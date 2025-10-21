<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventJudge;
use App\Models\Judge;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventAccessService
{
    /**
     * Create judge slots for an event
     * 
     * @param Event $event
     * @param int $numberOfJudges
     * @param array $judgeNames Optional array of judge names
     * @return Collection
     */
    public function createJudgeSlots(Event $event, int $numberOfJudges, array $judgeNames = []): Collection
    {
        $eventJudges = collect();

        for ($i = 0; $i < $numberOfJudges; $i++) {
            $judgeName = $judgeNames[$i] ?? "Judge " . ($i + 1);
            
            $eventJudge = EventJudge::create([
                'event_id' => $event->id,
                'judge_id' => null, // No actual judge account needed
                'judge_name' => $judgeName,
                'status' => 'pending',
                'invited_at' => now(),
            ]);

            $eventJudges->push($eventJudge);
        }

        return $eventJudges;
    }

    /**
     * Add a specific judge to an event
     * 
     * @param Event $event
     * @param Judge|null $judge
     * @param string|null $judgeName
     * @return EventJudge
     */
    public function addJudge(Event $event, ?Judge $judge = null, ?string $judgeName = null): EventJudge
    {
        return EventJudge::create([
            'event_id' => $event->id,
            'judge_id' => $judge?->id,
            'judge_name' => $judgeName ?? $judge?->name,
            'status' => 'pending',
            'invited_at' => now(),
        ]);
    }

    /**
     * Get all judge links for an event
     * 
     * @param Event $event
     * @return Collection
     */
    public function getJudgeLinks(Event $event): Collection
    {
        return $event->eventJudges()->get()->map(function ($eventJudge) {
            return [
                'id' => $eventJudge->id,
                'judge_name' => $eventJudge->display_name,
                'token' => $eventJudge->judge_token,
                'url' => $eventJudge->scoring_url,
                'status' => $eventJudge->status,
                'qr_code_url' => $this->generateQrCodeUrl($eventJudge->scoring_url),
            ];
        });
    }

    /**
     * Get public viewing link for an event
     * 
     * @param Event $event
     * @return array
     */
    public function getPublicViewingLink(Event $event): array
    {
        return [
            'token' => $event->public_viewing_token,
            'url' => $event->public_viewing_url,
            'qr_code_url' => $this->generateQrCodeUrl($event->public_viewing_url),
            'config' => $event->public_viewing_config,
        ];
    }

    /**
     * Update public viewing configuration
     * 
     * @param Event $event
     * @param array $config
     * @return Event
     */
    public function updatePublicViewingConfig(Event $event, array $config): Event
    {
        $event->update([
            'public_viewing_config' => array_merge($event->public_viewing_config ?? [], $config)
        ]);

        return $event->fresh();
    }

    /**
     * Regenerate tokens for an event
     * 
     * @param Event $event
     * @param string $tokenType 'public' or 'admin' or 'judges'
     * @return Event|Collection
     */
    public function regenerateTokens(Event $event, string $tokenType = 'all')
    {
        if ($tokenType === 'public' || $tokenType === 'all') {
            $event->update(['public_viewing_token' => bin2hex(random_bytes(32))]);
        }

        if ($tokenType === 'admin' || $tokenType === 'all') {
            $event->update(['admin_token' => bin2hex(random_bytes(32))]);
        }

        if ($tokenType === 'judges' || $tokenType === 'all') {
            $event->eventJudges()->each(function ($eventJudge) {
                $eventJudge->update(['judge_token' => bin2hex(random_bytes(32))]);
            });
        }

        return $tokenType === 'judges' ? $event->eventJudges : $event->fresh();
    }

    /**
     * Generate QR code URL using a service (example: goqr.me)
     * 
     * @param string $url
     * @return string
     */
    protected function generateQrCodeUrl(string $url): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($url);
    }

    /**
     * Send email invitations to judges
     * 
     * @param Event $event
     * @param array $judgeIds EventJudge IDs
     * @return int Number of emails sent
     */
    public function sendJudgeInvitations(Event $event, array $judgeIds = []): int
    {
        $eventJudges = $judgeIds 
            ? $event->eventJudges()->whereIn('id', $judgeIds)->get()
            : $event->eventJudges;

        $sent = 0;

        foreach ($eventJudges as $eventJudge) {
            // TODO: Implement email sending using Laravel Mail
            // Mail::to($eventJudge->judge?->email ?? 'placeholder@example.com')
            //     ->send(new JudgeInvitationMail($event, $eventJudge));
            
            $sent++;
        }

        return $sent;
    }

    /**
     * Get event statistics
     * 
     * @param Event $event
     * @return array
     */
    public function getEventStatistics(Event $event): array
    {
        return [
            'total_judges' => $event->eventJudges()->count(),
            'active_judges' => $event->eventJudges()->where('status', 'accepted')->count(),
            'pending_judges' => $event->eventJudges()->where('status', 'pending')->count(),
            'total_contestants' => $event->contestants()->count(),
            'total_scores' => $event->scores()->count(),
            'completion_percentage' => $this->calculateOverallCompletion($event),
        ];
    }

    /**
     * Calculate overall scoring completion
     * 
     * @param Event $event
     * @return float
     */
    protected function calculateOverallCompletion(Event $event): float
    {
        $judges = $event->eventJudges()->count();
        $contestants = $event->contestants()->count();
        
        if ($judges === 0 || $contestants === 0) {
            return 0;
        }

        if ($event->judging_type === 'criteria') {
            $criteria = $event->criterias()->count();
            $totalPossible = $judges * $contestants * $criteria;
        } else {
            $rounds = $event->rounds()->count();
            $totalPossible = $judges * $contestants * $rounds;
        }

        if ($totalPossible === 0) {
            return 0;
        }

        $actualScores = $event->scores()->count();

        return round(($actualScores / $totalPossible) * 100, 2);
    }
}
