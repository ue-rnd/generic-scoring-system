# Styling System Quick Reference

## Common Patterns

### Page Layout Structure

```blade
<x-scoring-layout :title="Page Title">
    <!-- Header -->
    <x-event-header :title="$event->name" />
    
    <!-- Stats -->
    <div class="stats-grid section-spacing">
        <x-stat-card ... />
    </div>
    
    <!-- Main Content -->
    <div class="rankings-container section-spacing">
        <!-- content -->
    </div>
</x-scoring-layout>
```

### Common CSS Classes

#### Containers
- `.scoring-page-body` - Page wrapper
- `.scoring-container` - Main container (1600px max)
- `.public-view-container` - Public pages (1400px max)
- `.section-spacing` - Bottom margin (1.5rem)

#### Statistics
- `.stats-grid` - Responsive stat grid
- `.stat-card` - Individual stat
- `.stat-icon` - Stat icon (2rem)
- `.stat-label` - Stat label text
- `.stat-value` - Stat value (1.5rem)

#### Rankings
- `.rankings-container` - Leaderboard wrapper
- `.rankings-title` - Leaderboard title
- `.ranking-item` - Individual rank
- `.rank-1`, `.rank-2`, `.rank-3` - Top 3 styling
- `.score-display` - Score container
- `.score-value` - Score number

#### Progress
- `.progress-bar-container` - Bar wrapper
- `.progress-bar` - Bar fill
- `.progress-complete` - Green (100%)
- `.progress-partial` - Orange (50-99%)
- `.progress-minimal` - Red (<50%)

#### Tables (Pageant)
- `.scoring-table` - Table wrapper
- `.contestant-cell` - Contestant row
- `.contestant-name` - Name text
- `.score-input-wrapper` - Input container

#### Grids (Quiz Bee)
- `.scoring-grid` - CSS Grid layout
- `.cell-header` - Header cells
- `.cell-contestant` - Contestant cells
- `.cell-question` - Question cells
- `.cell-total` - Total cells
- `.sticky-col` - Sticky column
- `.sticky-header` - Sticky header

#### Tabs
- `.round-tabs` - Tab container
- `.tab-button` - Tab button
- `.tab-active` - Active tab
- `.tab-inactive` - Inactive tab

#### Banners
- `.info-banner` - Banner container
- `.info-banner.info` - Blue info
- `.info-banner.success` - Green success
- `.info-banner.warning` - Orange warning
- `.info-banner.error` - Red error

## Color Reference

| Color | Hex | Usage |
|-------|-----|-------|
| Primary Blue | `#3b82f6` | Titles, primary actions |
| Success Green | `#10b981` | Complete, correct |
| Warning Orange | `#f59e0b` | Partial, alerts |
| Error Red | `#ef4444` | Errors, minimal |
| Purple | `#8b5cf6` | Quiz bee accents |
| Gray (BG) | `#f9fafb` | Backgrounds |
| Gray (Text) | `#111827` | Text |
| Gray (Subtle) | `#6b7280` | Subtle text |

## Component Props Quick Reference

### scoring-layout
```blade
:title="string"
body-class="string" (optional)
container-class="string" (optional)
:use-filament="boolean" (default: false)
:use-alpine="boolean" (default: true)
```

### event-header
```blade
:title="string"
:description="string|null"
icon="string" (heroicon)
:use-filament="boolean"
Slots: actions, badge
```

### stat-card
```blade
icon="string" (heroicon)
label="string"
:value="mixed"
color="string" (blue|green|purple|orange|red)
:use-filament="boolean"
```

### info-banner
```blade
type="string" (info|success|warning|error)
icon="string" (heroicon)
title="string|null"
:dismissible="boolean"
Slot: default (message content)
```

### ranking-item
```blade
:rank="integer"
:contestant="object"
:score="float|null"
:show-score="boolean"
```

## Responsive Breakpoints

- **Mobile**: < 768px (single column, simplified)
- **Tablet**: 768px - 1024px (2 columns)
- **Desktop**: > 1024px (full layout)

## Typography Scale

- **Hero**: 3rem (48px)
- **Title**: 1.875rem (30px)
- **Subtitle**: 1.25rem (20px)
- **Body**: 1rem (16px)
- **Small**: 0.875rem (14px)
- **Label**: 0.75rem (12px)

## Spacing Scale

- **Tight**: 0.5rem (8px)
- **Normal**: 1rem (16px)
- **Relaxed**: 1.5rem (24px)
- **Loose**: 2rem (32px)

## Icon Sizes

- **Small**: 1rem (16px)
- **Medium**: 1.5rem (24px)
- **Large**: 2rem (32px)
- **XLarge**: 4rem (64px)

## Quick Troubleshooting

### Styles not showing
1. Run `npm run build`
2. Clear browser cache
3. Check dev console for errors

### Component not rendering
1. Check component file exists
2. Verify prop names
3. Check for typos

### Layout broken
1. Verify container classes
2. Check responsive classes
3. Test at different widths

## Build Commands

```bash
# Development
npm run dev

# Production
npm run build

# Watch mode
npm run dev -- --watch
```

## File Locations

```
resources/
├── css/
│   ├── app.css
│   └── scoring-system.css ← Shared styles
├── views/
│   ├── components/
│   │   ├── scoring-layout.blade.php
│   │   ├── event-header.blade.php
│   │   ├── stat-card.blade.php
│   │   ├── info-banner.blade.php
│   │   └── ranking-item.blade.php
│   ├── public/
│   │   └── event.blade.php ← Public viewing
│   ├── scoring/
│   │   ├── judge.blade.php ← Judge scoring
│   │   ├── results.blade.php ← Results
│   │   └── quiz-bee-redirect.blade.php
│   └── admin/
│       └── scoring/
│           └── quiz-bee.blade.php ← Admin scoring
```

## Common Patterns by Page Type

### Public Viewing Page
```blade
<x-scoring-layout :title="$event->name">
    <!-- Stats Grid -->
    <div class="stats-grid section-spacing">
        <!-- stat cards -->
    </div>
    
    <!-- Rankings -->
    <div class="rankings-container section-spacing">
        <!-- ranking items -->
    </div>
    
    <!-- Judge Progress -->
    <div class="stats-grid section-spacing">
        <!-- progress bars -->
    </div>
</x-scoring-layout>
```

### Scoring Input Page
```blade
<x-scoring-layout :title="$event->name" :use-filament="true">
    <!-- Header -->
    <x-filament::section>
        <!-- event info -->
    </x-filament::section>
    
    <!-- Scoring Form -->
    <x-filament::section>
        <form>
            <div class="scoring-table">
                <table>
                    <!-- scoring inputs -->
                </table>
            </div>
        </form>
    </x-filament::section>
</x-scoring-layout>
```

### Quiz Bee Scoring Page
```blade
<x-scoring-layout :title="$event->name" :use-filament="true">
    <!-- Round Tabs -->
    <div class="round-tabs">
        <!-- tab buttons -->
    </div>
    
    <!-- Scoring Grid -->
    <div class="scoring-grid">
        <!-- grid cells -->
    </div>
</x-scoring-layout>
```

## Tips

1. **Always** use layout component for consistency
2. **Prefer** CSS classes over inline styles
3. **Use** components for repeated patterns
4. **Test** responsive behavior
5. **Document** custom additions

## Need Help?

See `STYLING-GUIDE.md` for complete documentation.
