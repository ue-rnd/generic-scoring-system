<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Contestant;
use App\Models\Score;
use Illuminate\Support\Collection;

class ScoringService
{
    /**
     * Calculate final scores for all contestants in an event
     */
    public function calculateFinalScores(Event $event): Collection
    {
        $contestants = $event->contestants;
        $results = collect();

        foreach ($contestants as $contestant) {
            $finalScore = $this->calculateContestantFinalScore($event, $contestant);
            $results->push([
                'contestant' => $contestant,
                'final_score' => $finalScore,
                'rank' => 0, // Will be calculated after sorting
            ]);
        }

        // Sort by final score (descending) and assign ranks
        $results = $results->sortByDesc('final_score')->values();
        $results->transform(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        return $results;
    }

    /**
     * Calculate final score for a specific contestant
     */
    public function calculateContestantFinalScore(Event $event, Contestant $contestant): float
    {
        if ($event->judging_type === 'criteria') {
            return $this->calculateCriteriaBasedScore($event, $contestant);
        } else {
            return $this->calculateRoundsBasedScore($event, $contestant);
        }
    }

    /**
     * Calculate criteria-based final score
     */
    protected function calculateCriteriaBasedScore(Event $event, Contestant $contestant): float
    {
        $criterias = $event->criterias;
        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($criterias as $criteria) {
            $averageScore = $this->getAverageScoreForCriteria($contestant, $criteria);
            $weightedScore = $averageScore * $criteria->weight;
            
            $totalWeightedScore += $weightedScore;
            $totalWeight += $criteria->weight;
        }

        return $totalWeight > 0 ? $totalWeightedScore / $totalWeight : 0;
    }

    /**
     * Calculate rounds-based final score
     */
    protected function calculateRoundsBasedScore(Event $event, Contestant $contestant): float
    {
        $rounds = $event->rounds;
        $totalScore = 0;

        foreach ($rounds as $round) {
            $roundScore = $this->getTotalScoreForRound($event, $contestant, $round);
            $totalScore += $roundScore;
        }

        return $totalScore;
    }

    /**
     * Get average score for a specific criteria across all judges
     */
    protected function getAverageScoreForCriteria(Contestant $contestant, $criteria): float
    {
        $scores = Score::where('contestant_id', $contestant->id)
            ->where('criteria_id', $criteria->id)
            ->pluck('score');

        return $scores->count() > 0 ? $scores->average() : 0;
    }

    /**
     * Get total score for a specific round
     */
    protected function getTotalScoreForRound(Event $event, Contestant $contestant, $round): float
    {
        if ($event->scoring_mode === 'boolean') {
            // For boolean mode, count correct answers and multiply by points per question
            $correctCount = Score::where('contestant_id', $contestant->id)
                ->where('round_id', $round->id)
                ->where('is_correct', true)
                ->count();
            
            return $correctCount * $round->points_per_question;
        } else {
            // For manual mode, sum the scores
            $scores = Score::where('contestant_id', $contestant->id)
                ->where('round_id', $round->id)
                ->pluck('score');

            return $scores->sum();
        }
    }

    /**
     * Get detailed scoring breakdown for a contestant
     */
    public function getContestantScoringBreakdown(Event $event, Contestant $contestant): array
    {
        if ($event->judging_type === 'criteria') {
            return $this->getCriteriaBreakdown($event, $contestant);
        } else {
            return $this->getRoundsBreakdown($event, $contestant);
        }
    }

    /**
     * Get criteria-based scoring breakdown
     */
    protected function getCriteriaBreakdown(Event $event, Contestant $contestant): array
    {
        $breakdown = [];
        $criterias = $event->criterias;

        foreach ($criterias as $criteria) {
            $scores = Score::where('contestant_id', $contestant->id)
                ->where('criteria_id', $criteria->id)
                ->with('judge')
                ->get();

            $breakdown[] = [
                'criteria' => $criteria,
                'scores' => $scores,
                'average_score' => $scores->count() > 0 ? $scores->avg('score') : 0,
                'weighted_score' => ($scores->count() > 0 ? $scores->avg('score') : 0) * $criteria->weight,
            ];
        }

        return $breakdown;
    }

    /**
     * Get rounds-based scoring breakdown
     */
    protected function getRoundsBreakdown(Event $event, Contestant $contestant): array
    {
        $breakdown = [];
        $rounds = $event->rounds;

        foreach ($rounds as $round) {
            $scores = Score::where('contestant_id', $contestant->id)
                ->where('round_id', $round->id)
                ->with('judge')
                ->get();

            if ($event->scoring_mode === 'boolean') {
                $correctCount = $scores->where('is_correct', true)->count();
                $totalScore = $correctCount * $round->points_per_question;
                
                $breakdown[] = [
                    'round' => $round,
                    'scores' => $scores,
                    'correct_count' => $correctCount,
                    'total_questions' => $round->total_questions,
                    'total_score' => $totalScore,
                    'max_possible' => $round->max_score,
                ];
            } else {
                $breakdown[] = [
                    'round' => $round,
                    'scores' => $scores,
                    'total_score' => $scores->sum('score'),
                    'max_possible' => $round->max_score,
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Get judge scoring summary for an event
     */
    public function getJudgeScoringSummary(Event $event): Collection
    {
        $judges = $event->judges;
        $summary = collect();

        foreach ($judges as $judge) {
            $scoresCount = Score::where('event_id', $event->id)
                ->where('judge_id', $judge->id)
                ->count();

            $summary->push([
                'judge' => $judge,
                'scores_count' => $scoresCount,
                'completion_percentage' => $this->calculateJudgeCompletionPercentage($event, $judge),
            ]);
        }

        return $summary;
    }

    /**
     * Calculate judge completion percentage
     */
    protected function calculateJudgeCompletionPercentage(Event $event, $judge): float
    {
        $contestants = $event->contestants;
        $totalPossibleScores = 0;
        $actualScores = 0;

        if ($event->judging_type === 'criteria') {
            $criterias = $event->criterias;
            $totalPossibleScores = $contestants->count() * $criterias->count();
            $actualScores = Score::where('event_id', $event->id)
                ->where('judge_id', $judge->id)
                ->whereNotNull('criteria_id')
                ->count();
        } else {
            $rounds = $event->rounds;
            $totalPossibleScores = $contestants->count() * $rounds->count();
            $actualScores = Score::where('event_id', $event->id)
                ->where('judge_id', $judge->id)
                ->whereNotNull('round_id')
                ->count();
        }

        return $totalPossibleScores > 0 ? ($actualScores / $totalPossibleScores) * 100 : 0;
    }
}


