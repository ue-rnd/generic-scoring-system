<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Resources\Pages\ViewRecord;

class ScoreQuizBee extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected string $view = 'filament.resources.events.pages.score-quiz-bee';

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Verify this is a quiz bee (rounds-based) event
        if (!$this->record->isQuizBeeType()) {
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
            
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Not a Quiz Bee Event')
                ->body('This scoring interface is only available for quiz bee (rounds-based) events.')
                ->send();
        }
    }

    public function getTitle(): string
    {
        return 'Score Quiz Bee: ' . $this->record->name;
    }

    public static function getNavigationLabel(): string
    {
        return 'Score Event';
    }
}
