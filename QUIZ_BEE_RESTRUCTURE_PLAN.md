# Quiz Bee Scoring System Restructure Plan

## 📋 Current Issues Analysis

### 1. **Conceptual Problems**
- ❌ Judge-based scoring doesn't fit quiz bee format
- ❌ Scores are tied to individual judges (judge_id/event_judge_id)
- ❌ Token-based access creates separate scoring instances
- ❌ No shared scoring UI for all moderators
- ❌ Round configuration incomplete (missing question-level tracking)

### 2. **Data Model Issues**
- ❌ `scores` table has `judge_id` and `event_judge_id` (judge-centric)
- ❌ No `question_number` field in scores table
- ❌ Rounds table has `total_questions` but no per-question tracking
- ❌ `is_correct` boolean but no manual score per question option
- ❌ Unique constraints assume one score per judge per round

### 3. **UI/UX Issues**
- ❌ Using custom Tailwind/HTML instead of Filament components
- ❌ Token-based access prevents shared scoring
- ❌ No real-time collaboration for moderators
- ❌ Layout uses Tailwind classes that don't work
- ❌ Not following Filament theming patterns

## 🎯 New Quiz Bee Requirements

### Scoring Model
For quiz bee events:
1. **No Judge Concept**: All authorized users score the same event
2. **Shared Scoring UI**: One source of truth, accessible by admin token
3. **Question-Level Scoring**: Each question in each round must be trackable
4. **Flexible Scoring**: Boolean (correct/incorrect) OR manual points per question
5. **Real-time Updates**: Multiple moderators can score simultaneously
6. **Contestant x Round x Question Matrix**: Display all at once

### Round Configuration
- Number of rounds (configurable)
- Questions per round (configurable per round)
- Points per question (configurable per round) OR manual scoring

## 📐 Technical Architecture Plan

### Phase 1: Database Schema Changes

#### 1.1 Add question_number to scores table
```sql
ALTER TABLE scores ADD COLUMN question_number INTEGER NULL AFTER round_id;
```

#### 1.2 Remove judge constraints
```sql
-- Drop unique constraints that include judge_id
DROP INDEX unique_criteria_score;
DROP INDEX unique_round_score;

-- Add new constraint for quiz bee: unique per contestant per round per question
CREATE UNIQUE INDEX unique_quizbee_score ON scores(event_id, contestant_id, round_id, question_number);
```

#### 1.3 Make judge fields nullable
- `judge_id` already nullable ✓
- `event_judge_id` already nullable ✓

### Phase 2: Model Changes

#### 2.1 Event Model
Add helper method:
```php
public function isQuizBeeType(): bool
{
    return $this->judging_type === 'rounds';
}

public function getAdminScoringUrl(): string
{
    return url("/admin/score/{$this->admin_token}");
}
```

#### 2.2 Score Model
Update fillable and relationships:
```php
protected $fillable = [
    'event_id',
    'contestant_id',
    'round_id',
    'question_number',  // NEW
    'score',
    'is_correct',
    'comments',
    // Remove judge_id, event_judge_id from required
];
```

### Phase 3: New Admin Scoring Routes

#### 3.1 Admin Scoring Controller
Create new controller for admin-based scoring:
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/score/{token}', [AdminScoringController::class, 'show'])->name('score.show');
    Route::post('/score/{token}', [AdminScoringController::class, 'store'])->name('score.store');
    Route::get('/score/{token}/live', [AdminScoringController::class, 'getLive'])->name('score.live');
});
```

### Phase 4: Filament-based Scoring UI

#### 4.1 Component Structure
```
resources/views/admin/scoring/
├── quiz-bee.blade.php (main scoring interface)
├── components/
│   ├── scoring-header.blade.php
│   ├── round-selector.blade.php
│   ├── contestant-row.blade.php
│   └── question-cell.blade.php
```

#### 4.2 Use Filament Components
- `<x-filament::section>` for layout containers
- `<x-filament::card>` for contestant cards
- `<x-filament::badge>` for scores and status
- `<x-filament::button>` for actions
- `<x-filament::input.checkbox>` for boolean scoring
- `<x-filament::input>` for manual scoring
- `<x-filament-tables::table>` for data grid
- `<x-filament::tabs>` for round navigation

#### 4.3 Layout Strategy
- **CSS Grid/Flexbox**: For positioning and layout structure
- **Filament Components**: For all UI elements
- **Inline Styles**: For specific layout properties (grid-template-columns, etc.)
- **NO Tailwind utility classes**: Avoid due to known issues

### Phase 5: Scoring Interface Design

#### 5.1 Layout Structure
```
┌─────────────────────────────────────────────────────┐
│ Event Header (Filament Section)                      │
│ - Event Name                                          │
│ - Quick Stats (Contestants, Rounds, Questions)       │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ Round Tabs (Filament Tabs)                          │
│ [Round 1] [Round 2] [Round 3] ...                   │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ Scoring Grid (CSS Grid + Filament Table)            │
│                                                       │
│        Q1    Q2    Q3    Q4    Q5   ...   Total     │
│ ┌─────┬────┬────┬────┬────┬────┬────┬────┐         │
│ │ C1  │ ☐  │ ☐  │ ☐  │ ☐  │ ☐  │... │ 0  │         │
│ │ C2  │ ☑  │ ☐  │ ☑  │ ☐  │ ☐  │... │ 20 │         │
│ │ C3  │ ☐  │ ☑  │ ☐  │ ☐  │ ☑  │... │ 20 │         │
│ └─────┴────┴────┴────┴────┴────┴────┴────┘         │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ Actions (Filament Buttons)                           │
│ [Save All] [Reset Round] [View Results]             │
└─────────────────────────────────────────────────────┘
```

#### 5.2 Scoring Modes

**Boolean Mode:**
- Checkbox for each question
- Checked = points_per_question
- Unchecked = 0
- Auto-calculate totals

**Manual Mode:**
- Input field for each question
- Min: 0, Max: points_per_question (or custom max)
- Manual totals calculation

### Phase 6: Filament Resource Integration

#### 6.1 Add Scoring Page to Event Resource
```php
public static function getPages(): array
{
    return [
        'index' => ListEvents::route('/'),
        'create' => CreateEvent::route('/create'),
        'edit' => EditEvent::route('/{record}/edit'),
        'manage-access' => ManageEventAccess::route('/{record}/manage-access'),
        'score' => ScoreEvent::route('/{record}/score'),  // NEW
    ];
}
```

#### 6.2 Create Filament Page
```php
class ScoreEvent extends Page
{
    protected static string $resource = EventResource::class;
    protected static string $view = 'filament.resources.events.pages.score-event';
    
    public Event $record;
    
    public function mount(Event $record): void
    {
        // Verify record is quiz bee type
        if (!$record->isQuizBeeType()) {
            abort(403, 'This event is not a quiz bee type.');
        }
    }
}
```

## 📋 Implementation Checklist

### Database Layer
- [ ] 1. Create migration: add `question_number` to scores table
- [ ] 2. Create migration: drop old unique constraints
- [ ] 3. Create migration: add new unique constraint for quiz bee
- [ ] 4. Run migrations
- [ ] 5. Update Score model fillable array
- [ ] 6. Add helper methods to Event model

### Backend Layer
- [ ] 7. Create AdminScoringController
- [ ] 8. Add admin scoring routes
- [ ] 9. Create scoring service for quiz bee logic
- [ ] 10. Add validation for quiz bee scoring
- [ ] 11. Add real-time scoring methods

### Filament Integration
- [ ] 12. Create ScoreEvent page class
- [ ] 13. Add route to EventResource
- [ ] 14. Create page view file

### Frontend Layer
- [ ] 15. Create main quiz-bee.blade.php layout
- [ ] 16. Build scoring-header component
- [ ] 17. Build round-selector component
- [ ] 18. Build contestant-row component
- [ ] 19. Build question-cell component
- [ ] 20. Implement CSS Grid layout (no Tailwind)
- [ ] 21. Use Filament components for all UI elements
- [ ] 22. Add Alpine.js for interactivity
- [ ] 23. Add auto-save functionality
- [ ] 24. Add total calculation logic

### Testing & Polish
- [ ] 25. Test boolean scoring mode
- [ ] 26. Test manual scoring mode
- [ ] 27. Test with multiple rounds
- [ ] 28. Test with many contestants
- [ ] 29. Test auto-save
- [ ] 30. Test concurrent editing (multiple tabs)
- [ ] 31. Polish UI with Filament theming
- [ ] 32. Add loading states
- [ ] 33. Add success/error notifications

### Documentation
- [ ] 34. Update README with quiz bee scoring
- [ ] 35. Document admin token usage
- [ ] 36. Create user guide for moderators

## 🎨 Design Principles

1. **Filament First**: Use Filament components for all UI elements
2. **CSS for Layout**: Use CSS Grid/Flexbox for structure
3. **No Tailwind Utilities**: Avoid Tailwind classes due to issues
4. **Inline Styles**: Use inline styles for layout-specific properties
5. **Component-Based**: Modular blade components
6. **Real-time**: Auto-save and live updates
7. **Responsive**: Mobile-friendly grid layout
8. **Accessible**: Proper ARIA labels and keyboard navigation

## 🚀 Implementation Order

**Priority 1 (Critical):**
1. Database migrations (1-4)
2. Model updates (5-6)
3. Controller and routes (7-8)

**Priority 2 (Core Features):**
4. Filament page integration (12-14)
5. Main scoring UI (15)
6. Scoring grid with Filament components (18-19)

**Priority 3 (Enhancement):**
7. Components (16-17, 20)
8. Interactivity (22-24)

**Priority 4 (Testing & Polish):**
9. Testing (25-30)
10. UI polish (31-33)
11. Documentation (34-36)

---

**Estimated Time:** 6-8 hours
**Risk Level:** Medium (requires careful data migration)
**Complexity:** High (real-time scoring + Filament integration)
