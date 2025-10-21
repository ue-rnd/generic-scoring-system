<?php

namespace App\Http\Controllers;

use App\Models\EventJudge;
use App\Models\Score;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class TokenScoringController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Show scoring interface using judge token
     */
    public function showScoringInterface(string $token)
    {
        $eventJudge = EventJudge::where('judge_token', $token)
            ->with(['event.contestants', 'event.criterias', 'event.rounds'])
            ->firstOrFail();

        $event = $eventJudge->event;
        $contestants = $event->contestants;
        $criterias = $event->criterias;
        $rounds = $event->rounds;
        $judgeName = $eventJudge->display_name;

        // Get existing scores for this judge
        $existingScores = Score::where('event_id', $event->id)
            ->where('event_judge_id', $eventJudge->id)
            ->get()
            ->keyBy(function ($score) {
                return $score->contestant_id . '_' . ($score->criteria_id ?? $score->round_id);
            });

        return view('scoring.judge', compact(
            'event',
            'eventJudge',
            'contestants',
            'criterias',
            'rounds',
            'judgeName',
            'existingScores',
            'token'
        ));
    }

    /**
     * Store or update scores via token
     */
    public function store(Request $request, string $token)
    {
        $eventJudge = EventJudge::where('judge_token', $token)
            ->with('event')
            ->firstOrFail();

        $event = $eventJudge->event;

        // Validation rules based on event type and scoring mode
        $rules = [
            'scores' => 'required|array',
            'scores.*.contestant_id' => 'required|exists:contestants,id',
        ];

        if ($event->judging_type === 'criteria') {
            $rules['scores.*.criteria_id'] = 'required|exists:criterias,id';
            $rules['scores.*.score'] = 'required|numeric|min:0';
        } else {
            $rules['scores.*.round_id'] = 'required|exists:rounds,id';
            
            if ($event->scoring_mode === 'boolean') {
                $rules['scores.*.is_correct'] = 'nullable|boolean';
            } else {
                $rules['scores.*.score'] = 'required|numeric|min:0';
            }
        }

        $rules['scores.*.comments'] = 'nullable|string|max:1000';

        $validated = $request->validate($rules);

        foreach ($validated['scores'] as $scoreData) {
            $data = [
                'event_id' => $event->id,
                'contestant_id' => $scoreData['contestant_id'],
                'event_judge_id' => $eventJudge->id,
                'criteria_id' => $scoreData['criteria_id'] ?? null,
                'round_id' => $scoreData['round_id'] ?? null,
            ];

            $updateData = [
                'comments' => $scoreData['comments'] ?? null,
            ];

            if ($event->judging_type === 'rounds' && $event->scoring_mode === 'boolean') {
                // Boolean mode: use is_correct and auto-calculate score
                $updateData['is_correct'] = $scoreData['is_correct'] ?? false;
                
                // Find the round to get points_per_question
                $round = $event->rounds()->find($scoreData['round_id']);
                $updateData['score'] = ($scoreData['is_correct'] ?? false) ? $round->points_per_question : 0;
            } else {
                // Manual mode: use provided score
                $updateData['score'] = $scoreData['score'];
                $updateData['is_correct'] = null;
            }

            Score::updateOrCreate($data, $updateData);
        }

        // Update eventJudge status to accepted if it's pending
        if ($eventJudge->status === 'pending') {
            $eventJudge->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Scores saved successfully!');
    }

    /**
     * Get existing scores for AJAX requests
     */
    public function getScores(string $token)
    {
        $eventJudge = EventJudge::where('judge_token', $token)->firstOrFail();

        $scores = Score::where('event_id', $eventJudge->event_id)
            ->where('event_judge_id', $eventJudge->id)
            ->get()
            ->keyBy(function ($score) {
                return $score->contestant_id . '_' . ($score->criteria_id ?? $score->round_id);
            });

        return response()->json($scores);
    }

    /**
     * Show results for this judge
     */
    public function showResults(string $token)
    {
        $eventJudge = EventJudge::where('judge_token', $token)
            ->with('event')
            ->firstOrFail();

        $event = $eventJudge->event;
        $results = $this->scoringService->calculateFinalScores($event);
        $judgeSummary = $this->scoringService->getJudgeScoringSummary($event);
        $judgeName = $eventJudge->display_name;

        return view('scoring.results', compact('event', 'results', 'judgeSummary', 'judgeName', 'token'));
    }
}
