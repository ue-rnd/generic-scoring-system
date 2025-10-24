# Dual Scoring System Implementation Summary

This document summarizes the implementation of **two independent scoring systems** in the Generic Scoring System application.

## Overview

The application now supports two distinct scoring models:

### 1. **Criteria-Based Scoring** (Pageant Style)
- Multiple judges score contestants independently
- Judging based on multiple criteria with weights
- Each judge has a unique token and private scoring interface
- Results calculated as weighted average across all judges
- Traditional judge-based workflow

### 2. **Quiz Bee Scoring** (Rounds-Based)
- Collaborative scoring with shared admin interface
- Question-level tracking (not judge-based)
- Boolean mode (correct/incorrect checkboxes) or manual mode
- Admin token provides access to entire scoring grid
- All moderators use the same interface simultaneously

---

## System Architecture

### Database Schema

**Key Change**: Added `question_number` column to `scores` table

```sql
-- Migration: 2025_10_23_075408_add_question_number_to_scores_table.php

ALTER TABLE scores ADD COLUMN question_number INTEGER UNSIGNED NULL;

-- Unique constraints updated:
-- Criteria-based: (event_id, contestant_id, event_judge_id, criteria_id)
-- Quiz bee: (event_id, contestant_id, round_id, question_number)
```

**Scores Table Fields**:
- `event_id` - Links to event
- `contestant_id` - Contestant being scored
- `judge_id` - Legacy, nullable
- `event_judge_id` - For criteria-based (nullable)
- `criteria_id` - For criteria-based (nullable)
- `round_id` - For rounds-based (nullable)
- `question_number` - For quiz bee (nullable, NEW)
- `score` - Numeric score
- `is_correct` - Boolean for correct/incorrect
- `comments` - Optional text

**How Systems Use Database Differently**:

| Field | Criteria-Based | Quiz Bee |
|-------|----------------|----------|
| `event_judge_id` | ✓ Required | NULL |
| `criteria_id` | ✓ Required | NULL |
| `round_id` | NULL | ✓ Required |
| `question_number` | NULL | ✓ Required |
| `score` | Judge's score | Auto-calculated or manual |
| `is_correct` | NULL | True/False (boolean mode) |

### Models

**Event Model** (`app/Models/Event.php`)

Added methods:
```php
public function isQuizBeeType(): bool
{
    return $this->judging_type === 'rounds';
}

public function getAdminScoringUrlAttribute(): string
{
    return url("/admin/score/{$this->admin_token}");
}
```

**Score Model** (`app/Models/Score.php`)

Added `question_number` to fillable fields:
```php
protected $fillable = [
    'event_id', 'contestant_id', 'judge_id', 'event_judge_id',
    'criteria_id', 'round_id', 'question_number',  // <-- NEW
    'score', 'is_correct', 'comments',
];
```

---

## Controllers

### 1. TokenScoringController (Criteria-Based)

**Purpose**: Handle individual judge scoring for criteria-based events

**Key Methods**:
- `showScoringInterface($token)` - Shows judge's private scoring page
  - **NEW**: Redirects quiz bee events to info page
- `store($token)` - Saves judge's scores
- `getScores($token)` - AJAX endpoint for existing scores
- `showResults($token)` - Shows results to judge

**Token Type**: `judge_token` (unique per judge per event)

**Access**: One judge = one token = one scoring interface

**View**: `resources/views/scoring/judge.blade.php`

### 2. AdminScoringController (Quiz Bee)

**Purpose**: Handle collaborative scoring for quiz bee events

**Key Methods**:
- `show($token)` - Shows admin scoring grid with all contestants and questions
- `store($token)` - Saves scores for multiple contestants/questions
- `getLive($token)` - Returns JSON of current scores for real-time updates

**Token Type**: `admin_token` (one per event, shared by all moderators)

**Access**: All moderators use same URL with same token

**View**: `resources/views/admin/scoring/quiz-bee.blade.php`

### 3. PublicViewController (Both Systems)

**Purpose**: Display live results to public audience

**Key Methods**:
- `show($token)` - Shows public viewing page
- `getLiveResults($token)` - AJAX endpoint for real-time updates
- `getContestantBreakdown($token, $contestantId)` - Detailed breakdown

**NEW**: Updated `getEventStatistics()` to handle both systems:
```php
if ($event->isQuizBeeType()) {
    // Calculate total questions, answered questions
    // Return quiz bee specific stats
} else {
    // Calculate judge progress, completion
    // Return judge-based stats
}
```

**Token Type**: `public_viewing_token` (one per event)

**Access**: Public, no authentication required

**View**: `resources/views/public/event.blade.php`

---

## Services

### ScoringService (`app/Services/ScoringService.php`)

**Purpose**: Calculate scores, rankings, and breakdowns for both systems

**Updated Methods**:

1. **`getTotalScoreForRound()`**
   - Detects event type via `$event->isQuizBeeType()`
   - Quiz bee: Counts unique question numbers
   - Criteria: Counts judge submissions

2. **`getRoundsBreakdown()`**
   - Quiz bee: Groups by question_number
   - Criteria: Groups by event_judge_id

3. **`getJudgeScoringSummary()`**
   - Returns empty collection for quiz bee events
   - Calculates judge progress for criteria events

**Score Calculation**:

**Criteria-Based**:
```
Final Score = Σ (Average Score per Criteria × Weight) / Total Weight
```

**Quiz Bee**:
```
Boolean Mode: Final Score = Σ (Correct Questions × Points Per Question)
Manual Mode: Final Score = Σ (Entered Scores)
```

---

## Routes

### Criteria-Based Routes

```php
Route::prefix('score')->name('score.')->group(function () {
    Route::get('/{token}', [TokenScoringController::class, 'showScoringInterface']);
    Route::post('/{token}', [TokenScoringController::class, 'store']);
    Route::get('/{token}/scores', [TokenScoringController::class, 'getScores']);
    Route::get('/{token}/results', [TokenScoringController::class, 'showResults']);
});
```

### Quiz Bee Routes

```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/score/{token}', [AdminScoringController::class, 'show']);
    Route::post('/score/{token}', [AdminScoringController::class, 'store']);
    Route::get('/score/{token}/live', [AdminScoringController::class, 'getLive']);
});
```

### Public Viewing (Both)

```php
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/event/{token}', [PublicViewController::class, 'show']);
    Route::get('/event/{token}/live', [PublicViewController::class, 'getLiveResults']);
    Route::get('/event/{token}/contestant/{contestant}', [PublicViewController::class, 'getContestantBreakdown']);
});
```

---

## Views

### Criteria-Based Views

**1. Judge Scoring Interface**
- Path: `resources/views/scoring/judge.blade.php`
- Uses: Filament components
- Layout: CSS Grid for scoring table
- Features:
  - Contestant × Criteria grid
  - Number inputs for scores
  - Real-time validation (min/max scores)
  - Alpine.js for interactivity
  - Save button persists all scores

**2. Judge Results View**
- Path: `resources/views/scoring/results.blade.php`
- Shows rankings, judge summary, weighted scores

### Quiz Bee Views

**1. Admin Scoring Interface**
- Path: `resources/views/admin/scoring/quiz-bee.blade.php`
- Uses: Filament components, CSS Grid (NO Tailwind utilities)
- Layout:
  ```css
  grid-template-columns: 250px repeat(N, minmax(80px, 1fr)) 120px;
  /* Fixed contestant column | Question columns | Total column */
  ```
- Features:
  - Round tabs (Alpine.js)
  - Sticky header and first column
  - Boolean mode: Checkboxes per question
  - Manual mode: Number inputs per question
  - Real-time total calculation
  - Auto-save on form submit

**2. Quiz Bee Info/Redirect Page**
- Path: `resources/views/scoring/quiz-bee-redirect.blade.php`
- Shows when someone tries to access judge token for quiz bee event
- Explains quiz bee uses admin URL, provides link

**3. Filament Score Quiz Bee Page**
- Path: `resources/views/filament/resources/events/pages/score-quiz-bee.blade.php`
- Admin panel view showing:
  - Stats cards
  - Rounds configuration
  - Current standings with medals
  - Admin URL with copy button

### Public Viewing (Both Systems)

**Path**: `resources/views/public/event.blade.php`

**Updated**: Statistics section now adapts to event type:
```blade
@if($statistics['is_quiz_bee'] ?? false)
    {{-- Show total questions instead of judges --}}
    <div>Total Questions: {{ $statistics['total_questions'] }}</div>
@else
    {{-- Show active judges --}}
    <div>Active Judges: {{ $statistics['active_judges'] }}</div>
@endif
```

---

## Filament Admin Integration

### Event Configuration

**EventForm** (`app/Filament/Resources/Events/Schemas/EventForm.php`)

**Judging Type Selection**:
```php
Select::make('judging_type')
    ->options([
        'criteria' => 'Criteria-based (e.g., Beauty Pageants)',
        'rounds' => 'Rounds-based (e.g., Quiz Bees)',
    ])
    ->reactive()
```

**Scoring Mode** (only for rounds-based):
```php
Select::make('scoring_mode')
    ->options([
        'manual' => 'Manual Score Entry',
        'boolean' => 'Correct/Incorrect (Auto-calculate)',
    ])
    ->visible(fn ($get) => $get('judging_type') === 'rounds')
```

**Public Viewing Settings**:
- Criteria: Show criteria breakdown toggle
- Quiz Bee: Show round breakdown toggle
- Both: Rankings, scores, judge progress toggles

### Relation Managers

**Shown based on event type**:

| Relation Manager | Criteria-Based | Quiz Bee |
|------------------|----------------|----------|
| Contestants | ✓ | ✓ |
| Judges | ✓ | Optional (not recommended) |
| Criterias | ✓ | Hidden |
| Rounds | Hidden | ✓ |

### Event Actions

**ManageEventAccess Page**:
- Criteria: Shows judge tokens section
- Quiz bee: Shows prominent admin URL section at top
- Both: Shows public viewing URL

**Score Event Action**:
- Criteria: Opens ManageEventAccess (judge tokens)
- Quiz bee: Opens ScoreQuizBee page
- Visible only for quiz bee in EventsTable action

---

## Key Differences Summary

| Feature | Criteria-Based | Quiz Bee |
|---------|----------------|----------|
| **Scoring Model** | Judge-based | Question-based |
| **Access Token** | Judge token (per judge) | Admin token (shared) |
| **Scoring Interface** | Individual, private | Collaborative, shared |
| **Database Scoring** | One score per judge per criteria | One score per question |
| **URL Pattern** | `/score/{judge_token}` | `/admin/score/{admin_token}` |
| **Judge Concept** | Required, multiple judges | None, moderators only |
| **Configuration** | Criterias with weights | Rounds with questions |
| **Score Calculation** | Weighted average | Sum or count correct |
| **Real-time Total** | N/A (save to see) | Yes (Alpine.js) |
| **Concurrent Editing** | No (separate interfaces) | Yes (same interface) |
| **Public Stats** | Shows judge progress | Shows total questions |

---

## Design Principles

### 1. **System Isolation**
- Both systems use same database but different fields
- No mixing: criteria events can't have rounds, vice versa
- Controllers dedicated to one system each

### 2. **Token-Based Access**
- No authentication required for scoring or public viewing
- Security through unguessable tokens (64 hex characters)
- Tokens auto-generated on event creation

### 3. **UI Consistency**
- Both systems use **100% Filament components**
- No Tailwind utility classes (per user requirement)
- CSS Grid and Flexbox for layouts
- Alpine.js for interactivity

### 4. **Real-Time Updates**
- Public viewing auto-refreshes via Alpine.js
- Quiz bee scoring shows live totals
- Criteria scoring saves persist immediately

### 5. **Flexibility**
- Boolean vs manual scoring modes
- Configurable public viewing options
- Weighted criteria support
- Variable points per question

---

## Testing Status

### Criteria-Based System ✅
- [x] Event configuration
- [x] Criteria management with weights
- [x] Judge token generation
- [x] Independent judge scoring
- [x] Weighted score calculation
- [x] Judge results view
- [x] Public viewing
- [x] Access control
- [x] Validation

### Quiz Bee System ✅
- [x] Rounds-based configuration
- [x] Question-level scoring
- [x] Admin token access
- [x] Boolean mode (checkboxes)
- [x] Manual mode (number inputs)
- [x] Round tabs navigation
- [x] Real-time totals
- [x] Filament standings view
- [x] Public viewing adaptation
- [x] Judge token redirect

### Integration ✅
- [x] Systems coexist independently
- [x] Database handles both correctly
- [x] Public viewing adapts to type
- [x] ScoringService handles both
- [x] No conflicts or errors

---

## Files Modified/Created

### Database
- ✅ `database/migrations/2025_10_23_075408_add_question_number_to_scores_table.php`

### Models
- ✅ `app/Models/Event.php` - Added `isQuizBeeType()`, `getAdminScoringUrlAttribute()`
- ✅ `app/Models/Score.php` - Added `question_number` to fillable

### Controllers
- ✅ `app/Http/Controllers/AdminScoringController.php` (NEW)
- ✅ `app/Http/Controllers/TokenScoringController.php` - Added quiz bee redirect
- ✅ `app/Http/Controllers/PublicViewController.php` - Updated statistics

### Services
- ✅ `app/Services/ScoringService.php` - Updated for both systems

### Routes
- ✅ `routes/web.php` - Added admin scoring routes

### Views - Quiz Bee
- ✅ `resources/views/admin/scoring/quiz-bee.blade.php` (NEW)
- ✅ `resources/views/scoring/quiz-bee-redirect.blade.php` (NEW)
- ✅ `resources/views/filament/resources/events/pages/score-quiz-bee.blade.php` (NEW)

### Views - Criteria (No changes needed)
- ✅ `resources/views/scoring/judge.blade.php` (Existing, works correctly)
- ✅ `resources/views/scoring/results.blade.php` (Existing, works correctly)

### Views - Public (Updated)
- ✅ `resources/views/public/event.blade.php` - Adapted statistics section

### Filament
- ✅ `app/Filament/Resources/Events/Pages/ScoreQuizBee.php` (NEW)
- ✅ `app/Filament/Resources/Events/EventResource.php` - Added score-quiz-bee page
- ✅ `app/Filament/Resources/Events/Tables/EventsTable.php` - Added "Score Event" action
- ✅ `app/Filament/Resources/Events/Schemas/EventForm.php` (Existing, already correct)
- ✅ `resources/views/filament/resources/events/pages/manage-event-access.blade.php` - Added quiz bee section

### Documentation
- ✅ `QUIZ_BEE_RESTRUCTURE_PLAN.md` (Planning document)
- ✅ `QUIZ_BEE_IMPLEMENTATION.md` (Implementation guide)
- ✅ `TESTING_GUIDE.md` (Comprehensive testing guide, NEW)
- ✅ `DUAL_SYSTEM_IMPLEMENTATION.md` (This document, NEW)

---

## Usage Guide

### For Criteria-Based Events (Pageants)

1. **Create Event**:
   - Judging Type: "Criteria-based"
   - Configure public viewing settings

2. **Add Contestants**: Add all participants

3. **Add Criteria**: Define scoring criteria with weights

4. **Add Judges**: Invite judges, system generates tokens

5. **Distribute Judge URLs**: Share unique URLs with each judge

6. **Judges Score**: Each judge scores independently via their URL

7. **View Results**: Public viewing URL shows live rankings

### For Quiz Bee Events

1. **Create Event**:
   - Judging Type: "Rounds-based"
   - Scoring Mode: Boolean or Manual

2. **Add Contestants**: Add all participants

3. **Add Rounds**: Configure rounds with question counts and points

4. **Share Admin URL**: All moderators use same admin URL

5. **Collaborative Scoring**: Moderators score questions in real-time

6. **View Standings**: Admin panel shows current rankings

7. **Public Viewing**: Public URL shows live leaderboard

---

## Future Enhancements

### Potential Improvements

1. **Question Text Storage**: Store actual questions, not just numbers
2. **Optimistic Locking**: Prevent concurrent edit conflicts
3. **Real-time Collaboration**: WebSocket for live updates between moderators
4. **Audit Trail**: Track who scored what and when
5. **Scoring History**: View historical scores and changes
6. **Export Results**: PDF/Excel export of final results
7. **Mobile App**: Native mobile scoring interface
8. **Video Integration**: Link scores to video timestamps
9. **Analytics Dashboard**: Detailed scoring analytics and insights
10. **Custom Scoring Formulas**: User-defined calculation methods

---

## Troubleshooting

### Criteria-Based Issues

**Problem**: Judge scores not saving
- Check judge token is valid
- Verify criteria_id and event_judge_id present in request
- Check unique constraint not violated

**Problem**: Wrong final score calculation
- Verify criteria weights sum to expected total
- Check all judges have submitted scores
- Ensure weighted average formula correct

### Quiz Bee Issues

**Problem**: Totals not calculating
- Check Alpine.js loaded correctly
- Verify `calculateTotal()` function syntax
- Check browser console for JavaScript errors

**Problem**: Scores not saving
- Verify question_number present in request
- Check unique constraint on (event_id, contestant_id, round_id, question_number)
- Ensure admin_token valid

**Problem**: Judge token shows info page
- Expected behavior for quiz bee events
- Use admin URL instead

### General Issues

**Problem**: Public viewing not updating
- Check event `is_active` = true
- Verify public_viewing_token correct
- Check Alpine.js polling interval

**Problem**: Validation errors
- Check score within min/max range
- Verify required fields present
- Check data types match expectations

---

## Conclusion

The Generic Scoring System now supports **two completely independent scoring models**:

1. **Criteria-Based**: Traditional judge-based scoring for pageants and competitions
2. **Quiz Bee**: Collaborative question-level scoring for quiz bees and similar events

Both systems:
- ✅ Use Filament components exclusively
- ✅ Share the same database with different fields
- ✅ Have dedicated controllers and views
- ✅ Support public viewing with configurable privacy
- ✅ Are fully tested and documented

The implementation maintains **complete system isolation** while providing a **unified admin experience** through Filament.
