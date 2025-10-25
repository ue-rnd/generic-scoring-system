<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use App\Services\EventAccessService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ManageEventAccess extends ViewRecord
{
    use AuthorizesRequests;
    
    protected static string $resource = EventResource::class;

    protected string $view = 'filament.resources.events.pages.manage-event-access';

    public function getTitle(): string
    {
        return 'Manage Access & Tokens';
    }
    
    public array $judgeLinks = [];
    public array $publicViewingLink = [];
    public array $statistics = [];

    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->loadData();
    }
    
    protected function getAccessService(): EventAccessService
    {
        return app(EventAccessService::class);
    }

    protected function loadData(): void
    {
        $this->judgeLinks = $this->getAccessService()->getJudgeLinks($this->record)->toArray();
        $this->publicViewingLink = $this->getAccessService()->getPublicViewingLink($this->record);
        $this->statistics = $this->getAccessService()->getEventStatistics($this->record);
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        
        // Only show Add Judges action for criteria-based events
        if ($this->record->judging_type === 'criteria') {
            $actions[] = Action::make('addJudges')
                ->label('Add Judges')
                ->icon('heroicon-o-plus')
                ->form([
                    TextInput::make('number_of_judges')
                        ->label('Number of Judges')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(1)
                        ->required(),
                    Repeater::make('judge_names')
                        ->label('Judge Names (optional)')
                        ->schema([
                            TextInput::make('name')
                                ->label('Judge Name')
                                ->maxLength(255),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Add Judge Name'),
                ])
                ->action(function (array $data): void {
                    $judgeNames = collect($data['judge_names'] ?? [])
                        ->pluck('name')
                        ->filter()
                        ->toArray();
                    
                    $this->getAccessService()->createJudgeSlots(
                        $this->record,
                        $data['number_of_judges'],
                        $judgeNames
                    );
                    
                    $this->loadData();
                    
                    Notification::make()
                        ->title('Judges Added')
                        ->success()
                        ->send();
                });
        }
        
        $actions[] = Action::make('refreshData')
            ->label('Refresh')
            ->icon('heroicon-o-arrow-path')
            ->action(fn () => $this->loadData());
        
        return $actions;
    }

    public function copyToClipboard(string $text): void
    {
        $this->dispatch('copy-to-clipboard', text: $text);
        
        Notification::make()
            ->title('Copied to clipboard!')
            ->success()
            ->send();
    }

    public function regenerateJudgeTokens(): void
    {
        $this->getAccessService()->regenerateTokens($this->record, 'judges');
        $this->loadData();
        
        Notification::make()
            ->title('All judge tokens regenerated')
            ->warning()
            ->send();
    }

    public function regeneratePublicToken(): void
    {
        $this->getAccessService()->regenerateTokens($this->record, 'public');
        $this->loadData();
        
        Notification::make()
            ->title('Public viewing token regenerated')
            ->warning()
            ->send();
    }

    public function deleteJudge(int $judgeId): void
    {
        $this->record->eventJudges()->where('id', $judgeId)->delete();
        $this->loadData();
        
        Notification::make()
            ->title('Judge removed')
            ->success()
            ->send();
    }
}
