# Testing Guide: Criteria-Based & Quiz Bee Scoring Systems

This guide covers comprehensive testing for both scoring systems in the Generic Scoring System.

## System Overview

The application supports **two distinct scoring models**:

1. **Criteria-Based (Pageant Style)**: Multiple judges score contestants on various criteria
2. **Quiz Bee (Rounds-Based)**: Collaborative scoring with question-level tracking, no individual judges

---

## Part 1: Criteria-Based Scoring (Pageant Style)

### Prerequisites
- Access to Filament admin panel
- At least 3 test judges
- At least 5 test contestants
- 3-5 criteria with different weights

### Test 1.1: Event Configuration

**Objective**: Verify criteria-based event setup

**Steps**:
1. Log into Filament admin panel
2. Navigate to Events → Create Event
3. Fill in event details:
   - Name: "Test Pageant 2025"
   - Description: "Testing criteria-based scoring"
   - Organization: Select appropriate org
   - Start Date: Today
   - End Date: Tomorrow
   - Is Active: ✓ Enabled
   - **Judging Type**: Select "Criteria-based (e.g., Beauty Pageants)"
   - Scoring Mode: Should not appear (criteria always use manual mode)

4. Configure Public Viewing:
   - Public: Show Rankings ✓
   - Public: Show Final Scores ✓
   - Public: Show Judge Names ✗ (Optional)
   - Public: Show Individual Judge Scores ✗ (Optional)
   - Public: Show Criteria Breakdown ✓
   - Public: Show Judge Progress ✓

5. Click Create

**Expected Result**: 
- Event created successfully
- Redirected to event edit page
- Public viewing token and admin token auto-generated

### Test 1.2: Add Contestants

**Objective**: Add multiple contestants

**Steps**:
1. On event edit page, click "Contestants" relation manager tab
2. Click "New Contestant"
3. Add contestants:
   - Name: "Contestant A", Description: "Team Red"
   - Name: "Contestant B", Description: "Team Blue"
   - Name: "Contestant C", Description: "Team Green"
   - Name: "Contestant D", Description: "Team Yellow"
   - Name: "Contestant E", Description: "Team Purple"

**Expected Result**: All contestants created and listed

### Test 1.3: Add Criteria

**Objective**: Configure judging criteria with weights

**Steps**:
1. Click "Criterias" relation manager tab
2. Click "New Criteria"
3. Add criteria:
   - Name: "Presentation", Max Score: 100, Min Score: 0, Weight: 30
   - Name: "Content", Max Score: 100, Min Score: 0, Weight: 40
   - Name: "Stage Presence", Max Score: 100, Min Score: 0, Weight: 20
   - Name: "Audience Impact", Max Score: 100, Min Score: 0, Weight: 10

**Expected Result**: 
- All criteria created
- Total weight = 100 (recommended)

### Test 1.4: Add Judges and Generate Tokens

**Objective**: Invite judges and generate scoring tokens

**Steps**:
1. Click "Judges" relation manager tab
2. Add judges:
   - Judge 1: Name "Judge Alpha", Email "alpha@test.com"
   - Judge 2: Name "Judge Beta", Email "beta@test.com"
   - Judge 3: Name "Judge Gamma", Email "gamma@test.com"

3. Navigate to Event → Actions → "Manage Access"
4. View judge tokens section
5. Copy each judge's unique scoring URL

**Expected Result**: 
- Each judge has unique `judge_token`
- URLs format: `/score/{token}`
- Display names shown in interface

### Test 1.5: Judge Scoring Interface

**Objective**: Test judge scoring workflow

**Steps**:
1. Open Judge Alpha's scoring URL in browser (incognito/private window)
2. Verify interface shows:
   - Event name and description
   - "Scoring as: Judge Alpha"
   - Stats cards: Event Type (Criteria), Scoring Mode (Manual), Contestants (5), Criteria (4)
   - Table with contestants in rows, criteria in columns
   - "View Results" button

3. Enter scores for Contestant A:
   - Presentation: 85
   - Content: 90
   - Stage Presence: 80
   - Audience Impact: 88

4. Enter scores for Contestant B:
   - Presentation: 92
   - Content: 85
   - Stage Presence: 90
   - Audience Impact: 87

5. Leave other contestants unscored
6. Click "Save Scores"

**Expected Result**: 
- Success message displayed
- Scores persist on page reload
- No validation errors
- Can modify scores and re-save

### Test 1.6: Multiple Judges Scoring

**Objective**: Verify independent judge scoring

**Steps**:
1. Open Judge Beta's scoring URL in different browser/profile
2. Score ALL contestants (including C, D, E)
3. Open Judge Gamma's scoring URL
4. Score only Contestants A, B, C

**Expected Result**: 
- Each judge sees only their own scores
- Judges cannot see other judges' scores
- All scores save independently

### Test 1.7: Live Results

**Objective**: Test judge results view

**Steps**:
1. From Judge Alpha's scoring page, click "View Results"
2. Verify results page shows:
   - Event name
   - Current rankings
   - Final scores (weighted average across criteria)
   - Judge summary (which judges have submitted scores)

3. Check ranking calculation:
   - Contestant B should rank high (if scored well by all judges)
   - Rankings update based on average scores across all judges
   - Weights applied correctly (Content = 40% weight)

**Expected Result**: 
- Results calculated correctly
- Weighted scoring applied
- Only contestants scored by at least one judge appear

### Test 1.8: Public Viewing

**Objective**: Test public results page

**Steps**:
1. Navigate to Manage Access page
2. Copy "Public Viewing URL" (format: `/public/event/{token}`)
3. Open in new browser window (no authentication)
4. Verify public page shows:
   - Event name and description
   - Live Updates indicator
   - Statistics cards:
     - Contestants: 5
     - Active Judges: 2-3 (judges who submitted scores)
     - Scores Submitted: Count
     - Completion percentage
   - Rankings leaderboard with medals (🥇🥈🥉)
   - Final scores (if enabled in config)
   - Criteria breakdown (if enabled)

5. Test real-time updates:
   - Keep public page open
   - Go back to judge scoring page
   - Update scores
   - Public page should auto-refresh (check Alpine.js polling)

**Expected Result**: 
- Public page accessible without auth
- Shows only configured information
- Real-time updates work
- No judge names shown (if disabled)

### Test 1.9: Validation & Edge Cases

**Objective**: Test validation rules

**Test Cases**:
1. Score above max_score (100) → Should show validation error
2. Score below min_score (0) → Should show validation error
3. Non-numeric input → Should validate
4. Decimal scores (85.5) → Should work
5. Leave all scores empty, click save → Should save empty (optional scoring)

**Expected Result**: Proper validation messages, no crashes

### Test 1.10: Access Control

**Objective**: Verify token-based access

**Test Cases**:
1. Access judge URL with invalid token → 404 error
2. Access public URL with invalid token → 404 error
3. Judge cannot access another judge's token URL → Sees different scores
4. Public URL works without authentication → ✓
5. Judge URL works without authentication → ✓

**Expected Result**: Token security working correctly

---

## Part 2: Quiz Bee Scoring (Rounds-Based)

### Prerequisites
- Access to Filament admin panel
- At least 5 test contestants
- 3 rounds with varying question counts

### Test 2.1: Quiz Bee Event Configuration

**Objective**: Configure rounds-based event

**Steps**:
1. Navigate to Events → Create Event
2. Fill in event details:
   - Name: "Quiz Bee Championship 2025"
   - Description: "Testing quiz bee scoring"
   - Organization: Select org
   - Start/End dates
   - Is Active: ✓
   - **Judging Type**: Select "Rounds-based (e.g., Quiz Bees)"
   - **Scoring Mode**: Select "Correct/Incorrect (Auto-calculate)"

3. Configure Public Viewing:
   - Show Rankings: ✓
   - Show Final Scores: ✓
   - Show Round Breakdown: ✓
   - Show Judge Progress: ✓

4. Click Create

**Expected Result**: 
- Event created
- Scoring mode dropdown visible only for rounds-based
- Admin token generated

### Test 2.2: Add Contestants

**Steps**:
1. Add 5 contestants (same as criteria test)

### Test 2.3: Configure Rounds

**Objective**: Add multiple rounds with different configurations

**Steps**:
1. Click "Rounds" relation manager tab
2. Add rounds:
   - **Round 1**: Name "Easy Round", Total Questions: 5, Points Per Question: 1, Max Score: 5
   - **Round 2**: Name "Medium Round", Total Questions: 10, Points Per Question: 2, Max Score: 20
   - **Round 3**: Name "Difficult Round", Total Questions: 3, Points Per Question: 5, Max Score: 15
   - **Round 4**: Name "Clincher", Total Questions: 1, Points Per Question: 10, Max Score: 10

**Expected Result**: 
- All rounds created
- Max score auto-calculated correctly (total_questions × points_per_question)

### Test 2.4: Access Admin Scoring URL

**Objective**: Verify admin scoring interface

**Steps**:
1. Navigate to Event → Actions → "Manage Access"
2. At the top, verify blue section titled "Quiz Bee Admin Scoring"
3. Shows admin URL: `/admin/score/{admin_token}`
4. Click "Copy URL" button
5. Click "Open Scoring Page" button OR paste URL in new window

**Expected Result**: 
- Admin scoring page opens without authentication
- Shows quiz bee interface (NOT judge interface)

### Test 2.5: Quiz Bee Scoring Interface (Boolean Mode)

**Objective**: Test question-level checkbox scoring

**Steps**:
1. On admin scoring page, verify interface shows:
   - Event header with stats (5 contestants, 4 rounds, Boolean mode)
   - Round tabs (Easy Round, Medium Round, Difficult Round, Clincher)
   - Current Round: "Easy Round" (5 questions visible)
   - Scoring grid:
     - Columns: Contestant name | Q1 | Q2 | Q3 | Q4 | Q5 | Total
     - Rows: One per contestant
     - Checkboxes for each question
     - Total column shows real-time calculation

2. Score Easy Round (Round 1):
   - Contestant A: Check Q1, Q2, Q3 (3 correct) → Total shows "3"
   - Contestant B: Check Q1, Q2, Q3, Q4, Q5 (5 correct) → Total shows "5"
   - Contestant C: Check Q1, Q3, Q5 (3 correct) → Total shows "3"
   - Contestant D: Check Q1, Q2 (2 correct) → Total shows "2"
   - Contestant E: Check Q1, Q4, Q5 (3 correct) → Total shows "3"

3. Click "Save All Scores" button
4. Verify success message

**Expected Result**: 
- Real-time total calculation works (Alpine.js)
- Checkboxes toggle correctly
- Scores save successfully
- Grid layout sticky (first column and header stick on scroll)

### Test 2.6: Multi-Round Scoring

**Objective**: Score multiple rounds

**Steps**:
1. Click "Medium Round" tab
2. Score 10 questions for each contestant:
   - Contestant A: 7 correct → Total shows "14" (7 × 2 points)
   - Contestant B: 9 correct → Total shows "18"
   - Contestant C: 8 correct → Total shows "16"
   - Contestant D: 6 correct → Total shows "12"
   - Contestant E: 7 correct → Total shows "14"

3. Click "Save All Scores"
4. Click back to "Easy Round" tab
5. Verify previous scores still there

6. Switch to "Difficult Round" tab
7. Score 3 questions:
   - Each correct = 5 points
   - Score mixed results for each contestant

8. Save scores

**Expected Result**: 
- Round tabs work correctly
- Scores persist across tab switches
- Each round scores independently
- Different points per question apply correctly

### Test 2.7: Manual Scoring Mode

**Objective**: Test manual score entry instead of checkboxes

**Steps**:
1. Go back to Filament admin
2. Edit the quiz bee event
3. Change Scoring Mode to "Manual Score Entry"
4. Save

5. Refresh admin scoring page
6. Verify interface now shows:
   - Number inputs instead of checkboxes
   - Each question accepts manual score entry
   - Max score validation per round's max_score

7. Enter scores manually:
   - Round 1, Contestant A, Q1: 0.5 (partial credit)
   - Round 1, Contestant A, Q2: 1.0
   - Round 1, Contestant A, Q3: 0.8
   - Total should show: 2.3

8. Save scores

**Expected Result**: 
- Number inputs work correctly
- Accepts decimal values
- Total calculates sum of entered values
- Validation prevents exceeding max_score per question

### Test 2.8: Concurrent Editing

**Objective**: Test multiple users scoring simultaneously

**Steps**:
1. Open admin scoring URL in Browser 1
2. Open same admin scoring URL in Browser 2 (different browser or incognito)
3. In Browser 1: Score Contestant A in Round 1
4. In Browser 2: Score Contestant B in Round 1
5. Both click Save
6. Refresh both browsers

**Expected Result**: 
- Both saves succeed without conflicts
- Database handles concurrent updates correctly
- Each contestant's scores independent

### Test 2.9: Quiz Bee Results in Filament

**Objective**: View standings in admin panel

**Steps**:
1. Navigate to Event → "Score Quiz Bee" page (from actions menu)
2. Verify page shows:
   - Quick stats cards
   - Info banner explaining quiz bee concept
   - Rounds configuration display (badges showing each round's config)
   - Current standings table:
     - Rank column with medals
     - Contestant names
     - Total scores (sum across all rounds)
     - Sorted by score descending

3. Click "Open Scoring Interface" button
4. Should open admin scoring page

**Expected Result**: 
- Standings calculated correctly
- Total = sum of all round scores
- Rankings correct (highest score = rank 1)

### Test 2.10: Quiz Bee Public Viewing

**Objective**: Test public results for quiz bee

**Steps**:
1. Copy public viewing URL from Manage Access page
2. Open in new window (no auth)
3. Verify public page shows:
   - Event name
   - Statistics:
     - Contestants: 5
     - **Total Questions: 19** (5+10+3+1)
     - Questions Answered: Count
     - Completion percentage
   - Rankings with medals
   - Final scores for each contestant

4. Verify round breakdown (if enabled):
   - Shows per-round scores for each contestant
   - Shows correct/incorrect counts for boolean mode
   - Shows manual scores for manual mode

**Expected Result**: 
- No judge information shown (quiz bees don't have judges)
- Total questions stat shown instead of active judges
- Rankings calculate correctly
- Round breakdown displays if enabled

### Test 2.11: Judge Token Redirect

**Objective**: Verify old judge tokens redirect for quiz bee events

**Steps**:
1. Try to add a judge to quiz bee event (should work, but not recommended)
2. Generate judge token
3. Try to access judge scoring URL: `/score/{judge_token}`
4. Should redirect to info page explaining:
   - "This is a quiz bee event"
   - "Quiz bee events use a shared admin scoring interface"
   - Link to public viewing
   - No individual judge scoring

**Expected Result**: 
- Judge token URLs don't work for quiz bee
- Info page displayed instead
- User guided to correct URL

---

## Part 3: Cross-System Tests

### Test 3.1: Switching Event Types

**Objective**: Verify systems are independent

**Test Cases**:
1. Create criteria event → Add criteria → Works ✓
2. Create quiz bee event → Add rounds → Works ✓
3. Criteria event cannot add rounds → Rounds tab hidden ✓
4. Quiz bee event cannot add criteria → Criteria tab hidden ✓

### Test 3.2: Database Integrity

**Objective**: Verify scores table handles both systems

**Checks**:
```sql
-- Criteria scores: have criteria_id, judge_id, no question_number
SELECT * FROM scores WHERE criteria_id IS NOT NULL;

-- Quiz bee scores: have round_id, question_number, no judge_id
SELECT * FROM scores WHERE question_number IS NOT NULL;

-- Unique constraint for quiz bee
-- Should prevent duplicate (event_id, contestant_id, round_id, question_number)

-- Verify scores are isolated between events
SELECT event_id, COUNT(*) FROM scores GROUP BY event_id;
```

### Test 3.3: Public Viewing for Both Types

**Objective**: Ensure public page adapts to event type

**Steps**:
1. Open criteria event public URL → Shows judge progress
2. Open quiz bee event public URL → Shows total questions
3. Both show rankings correctly
4. Both calculate scores correctly

---

## Part 4: Performance & Stress Tests

### Test 4.1: Large Dataset

**Objective**: Test with realistic data volumes

**Criteria Event**:
- 50 contestants
- 10 criteria
- 7 judges
- Total scores: 50 × 10 × 7 = 3,500

**Quiz Bee Event**:
- 30 contestants
- 5 rounds (20 questions each) = 100 questions
- Total scores: 30 × 100 = 3,000

**Expected Result**: 
- Pages load in < 3 seconds
- No timeout errors
- Public viewing updates smoothly

### Test 4.2: Concurrent Users

**Objective**: Test multiple simultaneous scorers

**Setup**:
- 5 judges scoring criteria event simultaneously
- 3 moderators scoring quiz bee simultaneously

**Expected Result**: No conflicts, all scores saved

---

## Part 5: Validation & Error Handling

### Test 5.1: Required Fields

**Objective**: Test form validation

**Test Cases**:
1. Create event without name → Error
2. Create event without judging type → Error
3. Add contestant without name → Error
4. Add criteria without max_score → Error
5. Add round without total_questions → Error

### Test 5.2: Invalid Data

**Test Cases**:
1. Negative scores → Error
2. Score exceeds max → Error
3. Non-numeric score → Error
4. Invalid tokens → 404

### Test 5.3: Edge Cases

**Test Cases**:
1. Event with 0 contestants → Should work, empty results
2. Event with 0 criteria/rounds → Can create but can't score
3. All judges score 0 → Rankings still work
4. Tie in scores → Same rank number
5. Incomplete scoring → Only scored contestants shown

---

## Test Execution Checklist

### Criteria-Based System
- [ ] Event configuration
- [ ] Add contestants (5+)
- [ ] Add criteria (4+)
- [ ] Add judges (3+)
- [ ] Generate judge tokens
- [ ] Judge scoring interface (all 3 judges)
- [ ] Weighted score calculation
- [ ] Judge results view
- [ ] Public viewing page
- [ ] Real-time updates
- [ ] Access control
- [ ] Validation

### Quiz Bee System
- [ ] Event configuration (rounds-based)
- [ ] Add contestants (5+)
- [ ] Add rounds (4+ with different configs)
- [ ] Admin token access
- [ ] Boolean scoring mode (checkboxes)
- [ ] Manual scoring mode (number inputs)
- [ ] Multi-round scoring
- [ ] Round tabs navigation
- [ ] Real-time total calculation
- [ ] Concurrent editing
- [ ] Filament standings view
- [ ] Public viewing (quiz bee specific)
- [ ] Judge token redirect

### Cross-System
- [ ] Systems are independent
- [ ] Database integrity
- [ ] Public viewing adapts to type
- [ ] No conflicts between event types

---

## Known Issues & Limitations

1. **Criteria System**:
   - Judges must manually refresh to see other judges' progress
   - No real-time collaboration between judges

2. **Quiz Bee System**:
   - Last save wins (no optimistic locking for concurrent edits)
   - No question text storage (questions numbered only)
   - No partial credit in boolean mode

3. **General**:
   - Public viewing auto-refresh interval fixed (not configurable)
   - Large datasets may slow rendering

---

## Success Criteria

### Criteria-Based ✅
- [x] Multiple judges can score independently
- [x] Weighted criteria calculation correct
- [x] Judge tokens secure and unique
- [x] Public viewing respects privacy settings
- [x] Results update in real-time

### Quiz Bee ✅
- [x] Question-level scoring works
- [x] No judge concept enforced
- [x] Admin token provides shared access
- [x] Round tabs function correctly
- [x] Boolean and manual modes work
- [x] Totals calculate accurately
- [x] Public viewing shows quiz bee stats

### Overall ✅
- [x] Both systems coexist without interference
- [x] Filament admin panel integration complete
- [x] All views use Filament components
- [x] CSS Grid layout works (no Tailwind utilities)
- [x] No console errors
- [x] Mobile responsive
