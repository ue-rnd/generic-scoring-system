<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Contestant;
use App\Models\Score;
use App\Models\Criteria;
use App\Models\Round;
use App\Models\Judge;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoringController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Show events assigned to the current judge
     */
    public function index()
    {
        $user = Auth::user();
        $judge = Judge::where('email', $user->email)->first();
        
        if (!$judge) {
            return view('judge.events', ['events' => collect()]);
        }
        
        $events = Event::whereHas('judges', function ($query) use ($judge) {
            $query->where('judge_id', $judge->id)
                  ->where('status', 'accepted');
        })->with(['contestants', 'criterias', 'rounds'])->get();

        return view('judge.events', compact('events'));
    }

    /**
     * Show scoring interface for a specific event
     */
    public function showEvent(Event $event)
    {
        $user = Auth::user();
        $judge = Judge::where('email', $user->email)->first();
        
        if (!$judge) {
            abort(403, 'You are not registered as a judge.');
        }
        
        // Check if user is assigned as judge for this event
        $isJudge = $event->judges()->where('judge_id', $judge->id)
            ->where('status', 'accepted')->exists();
            
        if (!$isJudge) {
            abort(403, 'You are not authorized to judge this event.');
        }

        $contestants = $event->contestants;
        $criterias = $event->criterias;
        $rounds = $event->rounds;

        return view('judge.scoring', compact('event', 'contestants', 'criterias', 'rounds'));
    }

    /**
     * Store or update scores
     */
    public function store(Request $request, Event $event)
    {
        $user = Auth::user();
        $judge = Judge::where('email', $user->email)->first();
        
        if (!$judge) {
            abort(403, 'You are not registered as a judge.');
        }
        
        // Check if user is assigned as judge for this event
        $isJudge = $event->judges()->where('judge_id', $judge->id)
            ->where('status', 'accepted')->exists();
            
        if (!$isJudge) {
            abort(403, 'You are not authorized to judge this event.');
        }

        $request->validate([
            'scores' => 'required|array',
            'scores.*.contestant_id' => 'required|exists:contestants,id',
            'scores.*.score' => 'required|numeric|min:0',
            'scores.*.criteria_id' => 'required_if:event.judging_type,criteria|exists:criterias,id',
            'scores.*.round_id' => 'required_if:event.judging_type,rounds|exists:rounds,id',
            'scores.*.comments' => 'nullable|string|max:1000',
        ]);

        foreach ($request->scores as $scoreData) {
            $score = Score::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'contestant_id' => $scoreData['contestant_id'],
                    'judge_id' => $judge->id,
                    'criteria_id' => $scoreData['criteria_id'] ?? null,
                    'round_id' => $scoreData['round_id'] ?? null,
                ],
                [
                    'score' => $scoreData['score'],
                    'comments' => $scoreData['comments'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Scores saved successfully!');
    }

    /**
     * Get existing scores for a judge and event
     */
    public function getScores(Event $event)
    {
        $user = Auth::user();
        $judge = Judge::where('email', $user->email)->first();
        
        if (!$judge) {
            return response()->json([]);
        }
        
        $scores = Score::where('event_id', $event->id)
            ->where('judge_id', $judge->id)
            ->get()
            ->keyBy(function ($score) {
                return $score->contestant_id . '_' . ($score->criteria_id ?? $score->round_id);
            });

        return response()->json($scores);
    }

    /**
     * Show results for an event (if judge has access)
     */
    public function showResults(Event $event)
    {
        $user = Auth::user();
        $judge = Judge::where('email', $user->email)->first();
        
        if (!$judge) {
            abort(403, 'You are not registered as a judge.');
        }
        
        // Check if user is assigned as judge for this event
        $isJudge = $event->judges()->where('judge_id', $judge->id)
            ->where('status', 'accepted')->exists();
            
        if (!$isJudge) {
            abort(403, 'You are not authorized to view results for this event.');
        }

        $results = $this->scoringService->calculateFinalScores($event);
        $judgeSummary = $this->scoringService->getJudgeScoringSummary($event);

        return view('judge.results', compact('event', 'results', 'judgeSummary'));
    }
}
