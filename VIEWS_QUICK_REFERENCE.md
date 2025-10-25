# Views Quick Reference

## Quick Access URLs

### Pageant Events (Criteria-Based)
```
Judge Scoring:  /score/{judge_token}
Judge Results:  /score/{judge_token}/results
Public Display: /public/event/{public_viewing_token}
```

### Quiz Bee Events (Rounds-Based)
```
Admin Scoring:  /admin/score/{admin_token}
Public Display: /public/event/{public_viewing_token}
```

## View Files Location

```
resources/views/
├── layouts/app.blade.php              # Base layout
├── scoring/
│   ├── judge.blade.php                # Pageant judge scoring
│   ├── quiz-bee-redirect.blade.php    # Quiz bee redirect
│   └── results.blade.php              # Results page
├── admin/scoring/
│   └── quiz-bee.blade.php             # Quiz bee admin scoring
└── public/
    └── event.blade.php                # Public scoreboard
```

## Route Names

```php
// Judge scoring (pageant)
route('score.judge', $token)         // GET /score/{token}
route('score.store', $token)         // POST /score/{token}
route('score.results', $token)       // GET /score/{token}/results

// Admin scoring (quiz bee)
route('admin.score.show', $token)    // GET /admin/score/{token}
route('admin.score.store', $token)   // POST /admin/score/{token}

// Public viewing
route('public.view', $token)         // GET /public/event/{token}
route('public.live', $token)         // GET /public/event/{token}/live
```

## Event Types

### Pageant (Criteria-Based)
- `judging_type = 'criteria'`
- Multiple judges with individual tokens
- Weighted scoring across criteria
- Judges score all contestants

### Quiz Bee (Rounds-Based)
- `judging_type = 'rounds'`
- Single admin token for centralized scoring
- Question-by-question scoring
- Boolean (correct/incorrect) or manual scoring

## Key Features Per View

### Judge Scoring (`scoring/judge.blade.php`)
✓ Contestant-grouped scoring cards
✓ Criteria/round-based inputs
✓ Comments support
✓ Pre-filled existing scores
✓ Validation (min/max)
✓ Loading states

### Admin Quiz Bee (`admin/scoring/quiz-bee.blade.php`)
✓ Spreadsheet-style grid
✓ Real-time total calculation
✓ Auto-save toggle
✓ Keyboard shortcuts (Ctrl+S)
✓ Unsaved changes warning
✓ AJAX submission

### Public Scoreboard (`public/event.blade.php`)
✓ Live leaderboard
✓ Auto-refresh (30s)
✓ Statistics dashboard
✓ Configurable visibility
✓ Judge progress (pageant)
✓ Dark mode support

### Results Page (`scoring/results.blade.php`)
✓ Final rankings with medals
✓ Expandable score breakdowns
✓ Judge completion status
✓ Criteria/round details
✓ Navigation to scoring/public

## Common Blade Variables

### All Views
- `$event` - Event model instance
- `$event->judging_type` - 'criteria' or 'rounds'
- `$event->scoring_mode` - 'boolean' or 'manual'
- `$event->is_active` - Event active status

### Judge Scoring
- `$contestants` - Collection of contestants
- `$criterias` - Collection of criteria (pageant)
- `$rounds` - Collection of rounds (quiz bee)
- `$existingScores` - Keyed collection of scores
- `$judgeName` - Judge display name
- `$token` - Judge token

### Admin Quiz Bee
- `$existingScores` - Nested array: [contestant][round][question]
- `$event->contestants` - All contestants
- `$event->rounds` - All rounds (ordered)

### Public View
- `$results` - Ranked contestants with scores
- `$judgeSummary` - Judge completion info
- `$statistics` - Event statistics array
- `$config` - Visibility configuration

### Results View
- `$results` - Ranked results collection
- `$judgeSummary` - Judge progress (pageant)

## Styling Classes (Tailwind)

### Rank Medals
```html
<!-- Gold (1st) -->
<span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">

<!-- Silver (2nd) -->
<span class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">

<!-- Bronze (3rd) -->
<span class="bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300">
```

### Status Badges
```html
<!-- Active -->
<span class="bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">

<!-- Inactive -->
<span class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
```

### Primary Buttons
```html
<button class="bg-primary-600 hover:bg-primary-700 text-white">
```

### Secondary Buttons
```html
<button class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700">
```

## JavaScript Functions

### Quiz Bee Admin
```javascript
calculateTotal(contestantId, roundId)  // Recalculate contestant-round total
scheduleAutoSave()                     // Schedule auto-save in 3s
saveScores()                           // AJAX save all scores
```

### Public View
```javascript
refreshScores()           // Fetch and update live scores
updateLastUpdateDisplay() // Update "X minutes ago" text
```

### Results View
```javascript
toggleDetails(contestantId) // Show/hide score breakdown
```

## Form Naming Convention

### Pageant Scores
```html
scores[{loop_index}][contestant_id]
scores[{loop_index}][criteria_id]
scores[{loop_index}][score]
scores[{loop_index}][comments]
```

### Quiz Bee Scores
```html
scores[c{contestantId}_r{roundId}_q{questionNum}][contestant_id]
scores[c{contestantId}_r{roundId}_q{questionNum}][round_id]
scores[c{contestantId}_r{roundId}_q{questionNum}][question_number]
scores[c{contestantId}_r{roundId}_q{questionNum}][is_correct]  // Boolean mode
scores[c{contestantId}_r{roundId}_q{questionNum}][score]       // Manual mode
```

## Public Viewing Configuration

```php
// In Event model
$event->public_viewing_config = [
    'show_rankings' => true,              // Show rank numbers
    'show_scores' => false,               // Show actual scores
    'show_judge_names' => false,          // Show judge names
    'show_individual_scores' => false,    // Show per-judge scores
    'show_criteria_breakdown' => false,   // Show criteria details
    'show_round_breakdown' => false,      // Show round details
    'show_judge_progress' => true,        // Show completion bars
];

// Check in blade
@if($event->canShowPublic('show_rankings'))
    <!-- show rankings -->
@endif
```

## Tips & Tricks

### Custom Page Title
```blade
@section('title', 'My Custom Title')
@section('page-title', 'Displayed in Header')
```

### Hide Header/Footer
```blade
@php
    $hideHeader = true;
    $hideFooter = true;
@endphp
```

### Add Header Actions
```blade
@section('header-actions')
    <button>Custom Action</button>
@endsection
```

### Flash Messages
```php
// In controller
return redirect()->back()->with('success', 'Saved!');
return redirect()->back()->with('error', 'Failed!');
```

### Loading Button State
```javascript
submitBtn.disabled = true;
submitBtn.innerHTML = '<svg class="animate-spin ...">...</svg>Saving...';
```

### Sticky Element
```html
<div class="sticky bottom-0 bg-white dark:bg-gray-800">
    <!-- Always visible at bottom -->
</div>
```

## Troubleshooting

### Scores not saving
1. Check CSRF token is present
2. Verify route exists
3. Check form name attributes
4. Inspect browser console for JS errors

### Public view not updating
1. Verify `is_active` is true
2. Check token is correct
3. Confirm scores exist in database
4. Test `/public/event/{token}/live` endpoint

### Dark mode not working
1. Ensure Tailwind config has `darkMode: 'class'`
2. Check `<html>` has dark class toggle
3. Verify all colors have dark: variants

### Styling issues
1. Run `npm run build` to compile assets
2. Clear browser cache
3. Check Tailwind classes are correct
4. Verify @vite directive in layout

## Development Commands

```bash
# Compile assets
npm run dev        # Development with hot reload
npm run build      # Production build

# Clear caches
php artisan view:clear
php artisan cache:clear

# Test routes
php artisan route:list | grep score
php artisan route:list | grep public
```

## Browser Testing

### Desktop
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)

### Mobile
- iOS Safari
- Android Chrome
- Responsive design (320px - 1920px)

### Features to Test
- [ ] Forms submit correctly
- [ ] Real-time calculations work
- [ ] Auto-save functions properly
- [ ] Dark mode toggles
- [ ] Responsive layout adapts
- [ ] Loading states appear
- [ ] Error messages display
