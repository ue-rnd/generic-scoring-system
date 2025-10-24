<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminScoringController extends Controller
{
    /**
     * Show admin scoring interface for quiz bee events
     */
    public function show(string $token)
    {
        $event = Event::where('admin_token', $token)->firstOrFail();
        
        // Verify this is a quiz bee (rounds-based) event
        if (!$event->isQuizBeeType()) {
            abort(403, 'This scoring interface is only for quiz bee events.');
        }
        
        // Load relationships
        $event->load(['contestants', 'rounds' => function($query) {
            $query->orderBy('order');
        }]);
        
        // Get existing scores grouped by contestant and round
        $existingScores = Score::where('event_id', $event->id)
            ->whereNotNull('round_id')
            ->whereNotNull('question_number')
            ->get()
            ->groupBy('contestant_id')
            ->map(function ($contestantScores) {
                return $contestantScores->groupBy('round_id')->map(function ($roundScores) {
                    return $roundScores->keyBy('question_number');
                });
            });
        
        return view('admin.scoring.quiz-bee', compact('event', 'existingScores'));
    }

    /**
     * Store or update quiz bee scores
     */
    public function store(Request $request, string $token)
    {
        $event = Event::where('admin_token', $token)->firstOrFail();
        
        if (!$event->isQuizBeeType()) {
            abort(403, 'This scoring interface is only for quiz bee events.');
        }

        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.contestant_id' => 'required|exists:contestants,id',
            'scores.*.round_id' => 'required|exists:rounds,id',
            'scores.*.question_number' => 'required|integer|min:1',
            'scores.*.is_correct' => 'nullable|boolean',
            'scores.*.score' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($event, $validated) {
            foreach ($validated['scores'] as $scoreData) {
                $data = [
                    'event_id' => $event->id,
                    'contestant_id' => $scoreData['contestant_id'],
                    'round_id' => $scoreData['round_id'],
                    'question_number' => $scoreData['question_number'],
                ];

                // Determine scoring based on mode
                if ($event->scoring_mode === 'boolean') {
                    $isCorrect = $scoreData['is_correct'] ?? false;
                    $round = $event->rounds()->find($scoreData['round_id']);
                    
                    $updateData = [
                        'is_correct' => $isCorrect,
                        'score' => $isCorrect ? $round->points_per_question : 0,
                    ];
                } else {
                    $updateData = [
                        'score' => $scoreData['score'] ?? 0,
                        'is_correct' => null,
                    ];
                }

                Score::updateOrCreate($data, $updateData);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Scores saved successfully',
            ]);
        }

        return redirect()->back()->with('success', 'Scores saved successfully!');
    }

    /**
     * Get live scores for real-time updates
     */
    public function getLive(string $token)
    {
        $event = Event::where('admin_token', $token)->firstOrFail();
        
        if (!$event->isQuizBeeType()) {
            abort(403);
        }

        $scores = Score::where('event_id', $event->id)
            ->whereNotNull('round_id')
            ->whereNotNull('question_number')
            ->with(['contestant', 'round'])
            ->get()
            ->groupBy('contestant_id')
            ->map(function ($contestantScores) {
                return [
                    'contestant' => $contestantScores->first()->contestant,
                    'total' => $contestantScores->sum('score'),
                    'by_round' => $contestantScores->groupBy('round_id')->map(function ($roundScores) {
                        return [
                            'round' => $roundScores->first()->round,
                            'total' => $roundScores->sum('score'),
                            'questions' => $roundScores->keyBy('question_number'),
                        ];
                    }),
                ];
            });

        return response()->json($scores);
    }
}
