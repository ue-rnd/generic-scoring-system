# Styling System Documentation

## Overview

This document describes the consistent styling system used across all public viewing and scoring pages in the Generic Scoring System, covering both pageant-style events and quiz bee events.

## Architecture

### Core Files

1. **`resources/css/scoring-system.css`** - Shared CSS classes and styles
2. **`resources/views/components/scoring-layout.blade.php`** - Base layout component
3. **`resources/views/components/event-header.blade.php`** - Reusable header component
4. **`resources/views/components/stat-card.blade.php`** - Statistics display cards
5. **`resources/views/components/info-banner.blade.php`** - Information/alert banners
6. **`resources/views/components/ranking-item.blade.php`** - Leaderboard ranking items

### Design System

#### Color Palette

- **Primary Blue**: `#3b82f6` - Event titles, primary actions
- **Success Green**: `#10b981` - Completion, correct answers
- **Warning Orange**: `#f59e0b` - Partial completion, alerts
- **Error Red**: `#ef4444` - Errors, minimal progress
- **Purple**: `#8b5cf6` - Quiz bee specific elements
- **Gray Scale**: `#f9fafb` (backgrounds) to `#111827` (text)

#### Typography

- **Primary Font**: System UI font stack
- **Title Size**: `1.875rem` (30px) to `3rem` (48px)
- **Body Text**: `1rem` (16px)
- **Small Text**: `0.875rem` (14px)
- **Label Text**: `0.75rem` (12px)

## Component Usage

### 1. Scoring Layout

Wrap your page content with the scoring layout component:

```blade
<x-scoring-layout 
    :title="'Event Name - Live Results'"
    body-class="scoring-page-body"
    container-class="scoring-container"
    :use-filament="false"
    :use-alpine="true">
    
    {{-- Your content here --}}
    
    <x-slot name="scripts">
        {{-- Custom scripts --}}
    </x-slot>
</x-scoring-layout>
```

**Props:**
- `title`: Page title (string)
- `body-class`: CSS class for body element (default: `scoring-page-body`)
- `container-class`: CSS class for main container (default: `scoring-container`)
- `use-filament`: Load Filament styles/scripts (default: `false`)
- `use-alpine`: Load Alpine.js (default: `true`)

### 2. Event Header

Display event information at the top of the page:

```blade
<x-event-header
    :title="$event->name"
    :description="$event->description"
    icon="heroicon-o-trophy"
    :use-filament="false">
    
    <x-slot name="actions">
        {{-- Action buttons --}}
    </x-slot>
    
    <x-slot name="badge">
        {{-- Optional badge content --}}
    </x-slot>
</x-event-header>
```

### 3. Stat Card

Display statistics in a grid:

```blade
<div class="stats-grid section-spacing">
    <x-stat-card
        icon="heroicon-o-user-group"
        label="Contestants"
        :value="$contestantCount"
        color="blue"
        :use-filament="false" />
</div>
```

**Colors**: `blue`, `green`, `purple`, `orange`, `red`

### 4. Info Banner

Show notifications or important information:

```blade
<x-info-banner
    type="info"
    icon="heroicon-o-information-circle"
    title="Quiz Bee Event"
    :dismissible="false">
    Your message content here
</x-info-banner>
```

**Types**: `info`, `success`, `warning`, `error`

### 5. Ranking Item

Display leaderboard entries:

```blade
<x-ranking-item
    :rank="1"
    :contestant="$contestant"
    :score="$totalScore"
    :show-score="true" />
```

## CSS Class Reference

### Layout Classes

| Class | Purpose |
|-------|---------|
| `.scoring-page-body` | Page background and padding |
| `.scoring-container` | Max-width container for scoring pages (1600px) |
| `.public-view-container` | Max-width container for public pages (1400px) |
| `.section-spacing` | Margin bottom for sections (1.5rem) |

### Header Classes

| Class | Purpose |
|-------|---------|
| `.event-header` | Flex container for event header |
| `.event-title` | Event title with icon |
| `.event-description` | Event description text |

### Statistics Classes

| Class | Purpose |
|-------|---------|
| `.stats-grid` | Responsive grid for stat cards |
| `.stat-card` | Individual stat card container |
| `.stat-icon` | Icon within stat card |
| `.stat-label` | Stat label text |
| `.stat-value` | Stat value display |

### Table Classes (Pageant)

| Class | Purpose |
|-------|---------|
| `.scoring-table` | Table container with overflow |
| `.contestant-cell` | Contestant display with avatar |
| `.contestant-name` | Contestant name text |
| `.contestant-desc` | Contestant description |

### Grid Classes (Quiz Bee)

| Class | Purpose |
|-------|---------|
| `.scoring-grid` | CSS Grid for question scoring |
| `.scoring-header` | Grid header row |
| `.scoring-row` | Grid data row |
| `.cell` | Individual grid cell |
| `.cell-header` | Header cell styling |
| `.cell-contestant` | Contestant name cell |
| `.cell-question` | Question input cell |
| `.cell-total` | Total score cell |
| `.sticky-col` | Sticky first column |
| `.sticky-header` | Sticky header row |

### Rankings Classes

| Class | Purpose |
|-------|---------|
| `.rankings-container` | Leaderboard container |
| `.rankings-title` | Leaderboard title |
| `.ranking-item` | Individual ranking row |
| `.rank-1`, `.rank-2`, `.rank-3` | Top 3 special styling |
| `.rank-display` | Rank number/emoji display |
| `.rank-emoji` | Emoji for top 3 |
| `.rank-number` | Numeric rank display |
| `.contestant-info` | Contestant details |
| `.contestant-info-name` | Contestant name in ranking |
| `.contestant-info-desc` | Contestant description in ranking |
| `.score-display` | Score display container |
| `.score-value` | Score number |
| `.score-label` | Score label text |

### Progress Bar Classes

| Class | Purpose |
|-------|---------|
| `.progress-bar-container` | Progress bar background |
| `.progress-bar` | Progress bar fill |
| `.progress-complete` | 100% completion (green) |
| `.progress-partial` | 50-99% completion (orange) |
| `.progress-minimal` | <50% completion (red) |

### Info Banner Classes

| Class | Purpose |
|-------|---------|
| `.info-banner` | Banner container |
| `.info-banner.info` | Info style (blue) |
| `.info-banner.success` | Success style (green) |
| `.info-banner.warning` | Warning style (orange) |
| `.info-banner.error` | Error style (red) |
| `.info-banner-icon` | Banner icon |
| `.info-banner-content` | Banner text content |
| `.info-banner-title` | Banner title |
| `.info-banner-text` | Banner message |

### Tab Classes (Quiz Bee Rounds)

| Class | Purpose |
|-------|---------|
| `.round-tabs` | Tab container with border |
| `.round-tabs-container` | Scrollable tab button container |
| `.tab-button` | Individual tab button |
| `.tab-active` | Active tab styling |
| `.tab-inactive` | Inactive tab styling |
| `.round-content` | Round content container |
| `.round-content.active` | Visible round content |

### Utility Classes

| Class | Purpose |
|-------|---------|
| `.live-indicator` | Live update status display |
| `.live-dot` | Pulsing dot animation |
| `.live-text` | Live update text |
| `.live-timestamp` | Last update time |
| `.round-info-card` | Round information display |
| `.badge-group` | Grouped badge display |

## Page-Specific Implementations

### Public Event Viewing (`resources/views/public/event.blade.php`)

**Features:**
- Live results with auto-refresh
- Rankings/leaderboard
- Statistics dashboard
- Judge progress (if enabled)
- Works for both pageant and quiz bee

**Key Classes:**
- `.public-view-container`
- `.rankings-container`
- `.live-indicator`

### Judge Scoring (`resources/views/scoring/judge.blade.php`)

**Features:**
- Criteria-based or rounds-based scoring
- Manual or boolean input modes
- Contestant avatars
- Score validation

**Key Classes:**
- `.scoring-container`
- `.scoring-table`
- `.form-actions`

### Quiz Bee Admin Scoring (`resources/views/admin/scoring/quiz-bee.blade.php`)

**Features:**
- Question-by-question grid
- Multiple rounds with tabs
- Real-time total calculation
- Collaborative scoring

**Key Classes:**
- `.scoring-grid`
- `.round-tabs`
- `.cell-*` classes

### Quiz Bee Redirect (`resources/views/scoring/quiz-bee-redirect.blade.php`)

**Features:**
- Information about quiz bee events
- Links to public viewing
- Simple, centered layout

**Key Classes:**
- `.info-banner`
- Custom button styles

## Responsive Design

All components are responsive with mobile-first approach:

### Breakpoints

- **Mobile**: < 768px
  - Single column layouts
  - Stacked components
  - Smaller font sizes
  - Simplified tables/grids

- **Tablet**: 768px - 1024px
  - 2-column grids where appropriate
  - Maintained spacing

- **Desktop**: > 1024px
  - Full multi-column layouts
  - Maximum widths applied
  - Optimized spacing

### Media Query Example

```css
@media (max-width: 768px) {
    .scoring-page-body {
        padding: 1rem 0.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
}
```

## Best Practices

### 1. Consistency

- Always use the shared layout component
- Use predefined CSS classes instead of inline styles where possible
- Follow the established color palette

### 2. Accessibility

- Maintain proper heading hierarchy
- Include ARIA labels where needed
- Ensure sufficient color contrast
- Support keyboard navigation

### 3. Performance

- CSS file is loaded once and cached
- Components are lightweight
- Minimal JavaScript dependencies

### 4. Maintainability

- Document custom styles
- Use component props for customization
- Keep inline styles for dynamic values only

## Adding New Pages

To add a new page with consistent styling:

1. **Use the layout component:**
   ```blade
   <x-scoring-layout :title="$pageTitle">
       <!-- content -->
   </x-scoring-layout>
   ```

2. **Structure your content:**
   ```blade
   <x-event-header :title="$title" />
   
   <div class="stats-grid section-spacing">
       <!-- stats -->
   </div>
   
   <div class="rankings-container section-spacing">
       <!-- rankings -->
   </div>
   ```

3. **Use existing components:**
   - `<x-stat-card>` for statistics
   - `<x-info-banner>` for messages
   - `<x-ranking-item>` for leaderboards

4. **Add custom CSS only if needed:**
   - Add to `scoring-system.css` for reusable styles
   - Use inline styles for page-specific dynamic values

## Troubleshooting

### Styles not applying

1. Check if `resources/css/scoring-system.css` is loaded
2. Run `npm run build` to compile assets
3. Clear browser cache

### Components not rendering

1. Verify component file exists in `resources/views/components/`
2. Check for typos in component names
3. Ensure all required props are passed

### Layout issues

1. Check viewport meta tag is present
2. Verify container classes are used correctly
3. Test responsive behavior at different breakpoints

## Future Enhancements

Planned improvements:
- Dark mode support
- Theme customization options
- Additional component variations
- Print stylesheet optimization
- Enhanced animations
