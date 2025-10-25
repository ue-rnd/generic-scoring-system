# System Architecture - Views Flow Diagram

## Event Types and Access Patterns

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         GENERIC SCORING SYSTEM                          │
│                      Custom Views (No Filament Prefix)                  │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                           EVENT TYPES                                    │
├─────────────────────────────────┬───────────────────────────────────────┤
│     PAGEANT STYLE               │      QUIZ BEE STYLE                   │
│   (Criteria-Based)              │    (Rounds-Based)                     │
├─────────────────────────────────┼───────────────────────────────────────┤
│ judging_type: 'criteria'        │ judging_type: 'rounds'                │
│ Multiple judges, unique tokens  │ Single admin token                    │
│ Weighted criteria scoring       │ Question-level scoring                │
│ Judge-isolated scoring          │ Centralized scoring                   │
└─────────────────────────────────┴───────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════════════
                          PAGEANT EVENT FLOW
═══════════════════════════════════════════════════════════════════════════

┌─────────────┐
│  ORGANIZER  │
│  (Filament) │
└──────┬──────┘
       │ Creates event
       │ Adds contestants & criteria
       │ Invites judges (auto-generates tokens)
       ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                            EVENT CREATED                                 │
│  public_viewing_token: xyz789...  (for public)                          │
│  EventJudge records created with unique judge_token per judge           │
└─────────────────────────────────────────────────────────────────────────┘
       │
       ├────────────────────┬────────────────────┬─────────────────────────┐
       ↓                    ↓                    ↓                         ↓
┌─────────────┐      ┌─────────────┐     ┌─────────────┐          ┌──────────────┐
│  JUDGE #1   │      │  JUDGE #2   │     │  JUDGE #3   │          │   PUBLIC     │
└──────┬──────┘      └──────┬──────┘     └──────┬──────┘          └──────┬───────┘
       │                    │                    │                        │
       │ /score/abc123...   │ /score/def456...   │ /score/ghi789...      │ /public/event/xyz789...
       ↓                    ↓                    ↓                        ↓
┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐  ┌─────────────────┐
│ scoring/         │ │ scoring/         │ │ scoring/         │  │ public/         │
│ judge.blade.php  │ │ judge.blade.php  │ │ judge.blade.php  │  │ event.blade.php │
├──────────────────┤ ├──────────────────┤ ├──────────────────┤  ├─────────────────┤
│ • All contestants│ │ • All contestants│ │ • All contestants│  │ • Leaderboard   │
│ • All criteria   │ │ • All criteria   │ │ • All criteria   │  │ • Live rankings │
│ • Score inputs   │ │ • Score inputs   │ │ • Score inputs   │  │ • Statistics    │
│ • Comments       │ │ • Comments       │ │ • Comments       │  │ • Judge progress│
│ • Submit button  │ │ • Submit button  │ │ • Submit button  │  │ • Auto-refresh  │
└────────┬─────────┘ └────────┬─────────┘ └────────┬─────────┘  └─────────────────┘
         │                    │                    │                      ↑
         │ POST /score/token  │                    │                      │
         ↓                    ↓                    ↓                      │
┌─────────────────────────────────────────────────────────────────────────┤
│              SCORES TABLE (isolated by event_judge_id)                  │
│  • Judge #1 scores for each contestant-criteria pair                    │
│  • Judge #2 scores for each contestant-criteria pair                    │
│  • Judge #3 scores for each contestant-criteria pair                    │
└─────────────────────────────────────────────────────────────────────────┘
         │                                                                │
         │ ScoringService calculates weighted averages                   │
         ↓                                                                │
┌─────────────────────────────────────────────────────────────────────────┤
│                        FINAL RESULTS                                     │
│  • Weighted average per contestant across all judges                    │
│  • Rankings calculated                                                   │
└──────────────────────────────────────────────────────────────────────────┘
         │                                                                
         ↓                                                                
┌──────────────────────┐                                                  
│ scoring/             │  View results (any judge)                        
│ results.blade.php    │  /score/{token}/results                          
├──────────────────────┤                                                  
│ • Final rankings     │                                                  
│ • Score breakdowns   │                                                  
│ • Judge completion   │                                                  
└──────────────────────┘                                                  


═══════════════════════════════════════════════════════════════════════════
                          QUIZ BEE EVENT FLOW
═══════════════════════════════════════════════════════════════════════════

┌─────────────┐
│  ORGANIZER  │
│  (Filament) │
└──────┬──────┘
       │ Creates event
       │ Adds contestants & rounds
       │ Sets scoring_mode (boolean/manual)
       ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                            EVENT CREATED                                 │
│  admin_token: def456...  (for admin scoring)                            │
│  public_viewing_token: xyz789...  (for public)                          │
│  NO individual judge tokens (centralized scoring)                       │
└─────────────────────────────────────────────────────────────────────────┘
       │
       ├────────────────────────────────┬─────────────────────────────────┐
       ↓                                ↓                                 ↓
┌──────────────────┐            ┌──────────────────┐           ┌──────────────┐
│  ADMIN/SCORER    │            │  JUDGE (Token)   │           │   PUBLIC     │
└────────┬─────────┘            └────────┬─────────┘           └──────┬───────┘
         │                               │                            │
         │ /admin/score/def456...        │ /score/abc123...           │ /public/event/xyz789...
         ↓                               ↓                            ↓
┌─────────────────────┐         ┌─────────────────────┐     ┌─────────────────┐
│ admin/scoring/      │         │ scoring/            │     │ public/         │
│ quiz-bee.blade.php  │         │ quiz-bee-redirect   │     │ event.blade.php │
├─────────────────────┤         ├─────────────────────┤     ├─────────────────┤
│ • Spreadsheet grid  │         │ • Info message      │     │ • Leaderboard   │
│ • All contestants   │         │ • Link to admin     │     │ • Live rankings │
│ • All rounds/Qs     │         │ • Link to public    │     │ • Round totals  │
│ • Real-time totals  │         │ • Event details     │     │ • Statistics    │
│ • Auto-save toggle  │         └─────────────────────┘     │ • Auto-refresh  │
│ • Checkboxes or     │                                     └─────────────────┘
│   number inputs     │                                              ↑
└──────────┬──────────┘                                              │
           │ POST /admin/score/token                                │
           ↓                                                         │
┌──────────────────────────────────────────────────────────────────────────┐
│                     SCORES TABLE                                         │
│  • One score per contestant-round-question                               │
│  • No judge_id (quiz bee has no individual judges)                       │
│  • is_correct (boolean mode) OR score (manual mode)                      │
│  • question_number field used                                            │
└──────────────────────────────────────────────────────────────────────────┘
           │                                                         │
           │ ScoringService sums scores per round                   │
           ↓                                                         │
┌──────────────────────────────────────────────────────────────────────────┤
│                        FINAL RESULTS                                     │
│  • Total score = sum of all round scores                                 │
│  • Rankings calculated                                                   │
└──────────────────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                        PUBLIC VIEW CONFIGURATION
═══════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────┐
│                  Event.public_viewing_config (JSON)                      │
├─────────────────────────────────────────────────────────────────────────┤
│  show_rankings: true/false          → Show rank numbers                 │
│  show_scores: true/false            → Display actual scores             │
│  show_judge_names: true/false       → Show judge names vs "Judge #1"    │
│  show_individual_scores: true/false → Show per-judge breakdown          │
│  show_criteria_breakdown: true/false→ Show criteria details             │
│  show_round_breakdown: true/false   → Show round details                │
│  show_judge_progress: true/false    → Show completion bars (pageant)    │
└─────────────────────────────────────────────────────────────────────────┘
                              ↓
                   Configurable via Filament
                              ↓
                    Applied in public/event.blade.php


═══════════════════════════════════════════════════════════════════════════
                         SCORING MODES (QUIZ BEE)
═══════════════════════════════════════════════════════════════════════════

┌───────────────────────────────────────┬─────────────────────────────────┐
│        BOOLEAN MODE                   │       MANUAL MODE               │
├───────────────────────────────────────┼─────────────────────────────────┤
│ scoring_mode: 'boolean'               │ scoring_mode: 'manual'          │
│                                       │                                 │
│ UI: Checkboxes (✓ = correct)         │ UI: Number inputs               │
│                                       │                                 │
│ Calculation:                          │ Calculation:                    │
│   if checked:                         │   score = user input            │
│     score = points_per_question       │   (validated against max)       │
│   else:                               │                                 │
│     score = 0                         │                                 │
│                                       │                                 │
│ is_correct field: TRUE/FALSE          │ is_correct field: NULL          │
│ score field: auto-calculated          │ score field: user-entered       │
└───────────────────────────────────────┴─────────────────────────────────┘


═══════════════════════════════════════════════════════════════════════════
                            DATA FLOW DIAGRAM
═══════════════════════════════════════════════════════════════════════════

                                ┌──────────┐
                                │ Database │
                                └────┬─────┘
                                     │
              ┌──────────────────────┼──────────────────────┐
              │                      │                      │
         ┌────▼────┐          ┌─────▼─────┐          ┌────▼────┐
         │ Events  │          │Contestants│          │  Scores │
         │─────────│          │───────────│          │─────────│
         │ tokens  │◄────────►│ event_id  │◄────────►│ scores  │
         │ config  │          │ name      │          │ judge_id│
         │ type    │          │ active    │          │ criteria│
         └────┬────┘          └───────────┘          │ round   │
              │                                       │ question│
              │                                       └─────────┘
              │
     ┌────────┴────────┐
     │                 │
┌────▼────┐       ┌───▼────┐
│Criteria │       │ Rounds │
│─────────│       │────────│
│ weights │       │ Qs     │
│ ranges  │       │ points │
└─────────┘       └────────┘
     │                 │
     │                 │
┌────▼─────────────────▼────┐
│   ScoringService          │
│───────────────────────────│
│ • calculateFinalScores()  │
│ • getContestantBreakdown()│
│ • getJudgeSummary()       │
└───────────────────────────┘
              │
              ↓
      ┌───────────────┐
      │  Controllers  │
      │───────────────│
      │ • Token       │
      │ • Admin       │
      │ • Public      │
      └───────┬───────┘
              │
              ↓
      ┌───────────────┐
      │  Views        │
      │───────────────│
      │ • Judge       │
      │ • Quiz Bee    │
      │ • Public      │
      │ • Results     │
      └───────────────┘


═══════════════════════════════════════════════════════════════════════════
                         FILE STRUCTURE OVERVIEW
═══════════════════════════════════════════════════════════════════════════

resources/views/
│
├── layouts/
│   └── app.blade.php                 ← Base layout (Tailwind + dark mode)
│
├── scoring/                          ← Judge-facing views
│   ├── judge.blade.php              ← Pageant judge scoring
│   ├── quiz-bee-redirect.blade.php  ← Quiz bee info/redirect
│   └── results.blade.php            ← Judge results view
│
├── admin/
│   └── scoring/
│       └── quiz-bee.blade.php       ← Admin quiz bee scoring grid
│
└── public/
    └── event.blade.php              ← Public scoreboard (both types)


app/Http/Controllers/
├── TokenScoringController.php       ← Judge scoring (pageant)
├── AdminScoringController.php       ← Admin scoring (quiz bee)
└── PublicViewController.php         ← Public scoreboard

app/Services/
└── ScoringService.php               ← Score calculations & rankings

routes/web.php                        ← All custom routes (no /admin prefix)


═══════════════════════════════════════════════════════════════════════════
                      URL PATTERNS SUMMARY
═══════════════════════════════════════════════════════════════════════════

PAGEANT EVENTS:
┌─────────────────────────────────┬─────────────────────────────────────┐
│ URL Pattern                     │ Purpose                             │
├─────────────────────────────────┼─────────────────────────────────────┤
│ /score/{judge_token}            │ Individual judge scoring interface  │
│ /score/{judge_token}/results    │ View event results                  │
│ /public/event/{public_token}    │ Public scoreboard                   │
└─────────────────────────────────┴─────────────────────────────────────┘

QUIZ BEE EVENTS:
┌─────────────────────────────────┬─────────────────────────────────────┐
│ URL Pattern                     │ Purpose                             │
├─────────────────────────────────┼─────────────────────────────────────┤
│ /admin/score/{admin_token}      │ Admin scoring grid                  │
│ /public/event/{public_token}    │ Public scoreboard                   │
│ /score/{judge_token}*           │ Redirects to quiz bee info page     │
└─────────────────────────────────┴─────────────────────────────────────┘
* Judge tokens still exist but redirect to info page for quiz bee events


═══════════════════════════════════════════════════════════════════════════

Legend:
  ┌──┐
  │  │  = Component/Page
  └──┘
  
   ↓   = Data/Control flow
   
  ◄─► = Relationship/Reference
  
  ═══ = Major section divider
