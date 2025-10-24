<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class PublicViewController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Show public viewing page
     */
    public function show(string $token)
    {
        $event = Event::where('public_viewing_token', $token)
            ->with(['contestants', 'criterias', 'rounds', 'eventJudges'])
            ->firstOrFail();

        // Check if event is active
        if (!$event->is_active) {
            abort(403, 'This event is not currently active.');
        }

        $config = $event->public_viewing_config;
        $results = $this->scoringService->calculateFinalScores($event);
        $judgeSummary = $this->scoringService->getJudgeScoringSummary($event);
        $statistics = $this->getEventStatistics($event);

        return view('public.event', compact(
            'event',
            'config',
            'results',
            'judgeSummary',
            'statistics'
        ));
    }

    /**
     * Get live results as JSON (for AJAX/real-time updates)
     */
    public function getLiveResults(string $token)
    {
        $event = Event::where('public_viewing_token', $token)->firstOrFail();

        if (!$event->is_active) {
            return response()->json(['error' => 'Event is not active'], 403);
        }

        $results = $this->scoringService->calculateFinalScores($event);
        $judgeSummary = $this->scoringService->getJudgeScoringSummary($event);
        $statistics = $this->getEventStatistics($event);

        return response()->json([
            'results' => $results,
            'judge_summary' => $judgeSummary,
            'statistics' => $statistics,
            'last_updated' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get contestant detailed breakdown
     */
    public function getContestantBreakdown(string $token, int $contestantId)
    {
        $event = Event::where('public_viewing_token', $token)->firstOrFail();

        if (!$event->is_active) {
            return response()->json(['error' => 'Event is not active'], 403);
        }

        // Check if detailed breakdown is allowed
        if (!$event->canShowPublic('show_criteria_breakdown') && !$event->canShowPublic('show_round_breakdown')) {
            return response()->json(['error' => 'Detailed breakdown is not available'], 403);
        }

        $contestant = $event->contestants()->findOrFail($contestantId);
        $breakdown = $this->scoringService->getContestantScoringBreakdown($event, $contestant);

        return response()->json([
            'contestant' => $contestant,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get event statistics
     */
    protected function getEventStatistics(Event $event): array
    {
        $totalContestants = $event->contestants()->count();
        $totalScores = $event->scores()->count();

        // Different statistics for quiz bee vs judge-based events
        if ($event->isQuizBeeType()) {
            // Quiz bee: no judges, count questions instead
            $totalQuestions = $event->rounds()->sum('total_questions');
            $totalPossible = $totalContestants * $totalQuestions;
            
            // Count unique question scores using raw query
            $answeredQuestions = \DB::table('scores')
                ->where('event_id', $event->id)
                ->whereNotNull('question_number')
                ->distinct()
                ->count(\DB::raw('CONCAT(contestant_id, "-", round_id, "-", question_number)'));
            
            $completionPercentage = $totalPossible > 0 ? ($answeredQuestions / $totalPossible) * 100 : 0;
            
            return [
                'total_judges' => 0,
                'active_judges' => 0,
                'total_contestants' => $totalContestants,
                'total_scores' => $answeredQuestions,
                'completion_percentage' => round($completionPercentage, 2),
                'is_quiz_bee' => true,
                'total_questions' => $totalQuestions,
            ];
        } else {
            // Judge-based events
            $totalJudges = $event->eventJudges()->count();
            $activeJudges = $event->eventJudges()->where('status', 'accepted')->count();

            if ($event->judging_type === 'criteria') {
                $criteria = $event->criterias()->count();
                $totalPossible = $totalJudges * $totalContestants * $criteria;
            } else {
                $rounds = $event->rounds()->count();
                $totalPossible = $totalJudges * $totalContestants * $rounds;
            }

            $completionPercentage = $totalPossible > 0 ? ($totalScores / $totalPossible) * 100 : 0;

            return [
                'total_judges' => $totalJudges,
                'active_judges' => $activeJudges,
                'total_contestants' => $totalContestants,
                'total_scores' => $totalScores,
                'completion_percentage' => round($completionPercentage, 2),
                'is_quiz_bee' => false,
            ];
        }
    }
}
