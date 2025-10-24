# Quick Reference: Criteria-Based vs Quiz Bee Scoring

## At a Glance

### When to Use Criteria-Based Scoring
✅ Beauty pageants  
✅ Talent competitions  
✅ Presentations with rubrics  
✅ Multi-judge evaluations  
✅ Subjective scoring with criteria  

### When to Use Quiz Bee Scoring
✅ Quiz competitions  
✅ Game shows  
✅ Knowledge tests  
✅ Question-answer contests  
✅ Collaborative scoring scenarios  

---

## Quick Setup Guide

### Criteria-Based Event (5 Steps)

1. **Create Event**
   ```
   Judging Type: Criteria-based
   Scoring Mode: (Auto-set to Manual)
   ```

2. **Add Contestants**
   ```
   Name + Description for each contestant
   ```

3. **Add Criteria**
   ```
   Name: "Presentation"
   Max Score: 100
   Weight: 30
   (Repeat for all criteria)
   ```

4. **Add Judges**
   ```
   Name + Email for each judge
   System auto-generates tokens
   ```

5. **Share Judge URLs**
   ```
   Event → Manage Access → Copy each judge's URL
   Format: /score/{judge_token}
   ```

### Quiz Bee Event (4 Steps)

1. **Create Event**
   ```
   Judging Type: Rounds-based
   Scoring Mode: Boolean or Manual
   ```

2. **Add Contestants**
   ```
   Name + Description for each contestant
   ```

3. **Add Rounds**
   ```
   Name: "Easy Round"
   Total Questions: 5
   Points Per Question: 1
   (Repeat for all rounds)
   ```

4. **Share Admin URL**
   ```
   Event → Manage Access → Copy Admin URL
   Format: /admin/score/{admin_token}
   ALL moderators use same URL
   ```

---

## Access URLs

### Criteria-Based
| URL Type | Format | Who Uses | Purpose |
|----------|--------|----------|---------|
| Judge Scoring | `/score/{judge_token}` | Individual judge | Private scoring interface |
| Judge Results | `/score/{judge_token}/results` | Individual judge | View current standings |
| Public Viewing | `/public/event/{public_token}` | Anyone | Live results |

### Quiz Bee
| URL Type | Format | Who Uses | Purpose |
|----------|--------|----------|---------|
| Admin Scoring | `/admin/score/{admin_token}` | All moderators | Shared scoring grid |
| Admin Live | `/admin/score/{admin_token}/live` | System | JSON endpoint |
| Public Viewing | `/public/event/{public_token}` | Anyone | Live results |

---

## Scoring Interfaces

### Criteria-Based: Judge View
```
┌─────────────────────────────────────────────────┐
│ Event: Beauty Pageant 2025                      │
│ Scoring as: Judge Alpha                         │
├─────────────────────────────────────────────────┤
│         │ Presentation │ Content │ Stage       │
│         │ Max: 100     │ Max: 100│ Max: 100    │
│         │ Weight: 30   │ W: 40   │ W: 20       │
├─────────┼──────────────┼─────────┼─────────────┤
│ Alice   │ [  85  ]     │ [  90 ] │ [  80  ]    │
│ Bob     │ [  92  ]     │ [  85 ] │ [  90  ]    │
│ Carol   │ [  88  ]     │ [  95 ] │ [  85  ]    │
└─────────┴──────────────┴─────────┴─────────────┘
              [Save Scores]
```

### Quiz Bee: Admin View (Boolean Mode)
```
┌─────────────────────────────────────────────────┐
│ Event: Quiz Bee Championship 2025               │
│ Round: [Easy Round] [Medium Round] [Difficult]  │
├─────────────────────────────────────────────────┤
│          │ Q1 │ Q2 │ Q3 │ Q4 │ Q5 │ Total      │
├──────────┼────┼────┼────┼────┼────┼────────────┤
│ Alice    │ ✓  │ ✓  │ ☐  │ ✓  │ ☐  │ 3 pts      │
│ Bob      │ ✓  │ ✓  │ ✓  │ ✓  │ ✓  │ 5 pts      │
│ Carol    │ ✓  │ ☐  │ ✓  │ ☐  │ ✓  │ 3 pts      │
└──────────┴────┴────┴────┴────┴────┴────────────┘
              [Save All Scores]
```

---

## Score Calculation

### Criteria-Based Formula
```
Final Score = Σ (Average per Criteria × Weight) / Total Weight

Example:
Criteria: Presentation (W:30), Content (W:40), Stage (W:20)

Judge 1: Presentation=85, Content=90, Stage=80
Judge 2: Presentation=90, Content=88, Stage=85
Judge 3: Presentation=88, Content=92, Stage=82

Contestant Final Score:
= ((85+90+88)/3 × 30 + (90+88+92)/3 × 40 + (80+85+82)/3 × 20) / 90
= (87.67×30 + 90×40 + 82.33×20) / 90
= (2630 + 3600 + 1646.6) / 90
= 87.52
```

### Quiz Bee Formula

**Boolean Mode:**
```
Final Score = Σ (Correct Questions × Points Per Question)

Example:
Round 1: 3 correct × 1 pt = 3 pts
Round 2: 7 correct × 2 pts = 14 pts
Round 3: 2 correct × 5 pts = 10 pts
Final Score = 3 + 14 + 10 = 27 pts
```

**Manual Mode:**
```
Final Score = Σ (Entered Scores)

Example:
Round 1, Q1: 0.8, Q2: 1.0, Q3: 0.5 = 2.3 pts
Round 2, Q1: 2.0, Q2: 1.5, Q3: 2.0 = 5.5 pts
Final Score = 2.3 + 5.5 = 7.8 pts
```

---

## Public Viewing Configuration

### Recommended Settings

**Pageant (High Privacy):**
```
✓ Show Rankings
✓ Show Final Scores
✗ Show Judge Names
✗ Show Individual Judge Scores
✗ Show Criteria Breakdown
✓ Show Judge Progress
```

**Quiz Bee (Full Transparency):**
```
✓ Show Rankings
✓ Show Final Scores
✓ Show Round Breakdown
✓ Show Judge Progress
```

**Competition (Moderate):**
```
✓ Show Rankings
✓ Show Final Scores
✗ Show Judge Names
✗ Show Individual Scores
✓ Show Criteria/Round Breakdown
✓ Show Progress
```

---

## Common Workflows

### Criteria-Based: Live Pageant

1. **Before Event**:
   - Create event, add contestants, criteria, judges
   - Test scoring with one judge
   - Share judge URLs via email
   - Display public viewing on screen

2. **During Event**:
   - Judges score on tablets/phones using their URLs
   - Public screen shows live rankings (auto-refresh)
   - Emcee can view results on separate device

3. **After Event**:
   - All judges finish scoring
   - Final rankings locked
   - Export results (future feature)

### Quiz Bee: Live Competition

1. **Before Event**:
   - Create event, add contestants, rounds
   - Test scoring with sample questions
   - Share admin URL with all moderators
   - Display public viewing on screen

2. **During Event**:
   - Moderator marks correct/incorrect as questions answered
   - Public screen shows live standings (auto-refresh)
   - Switch between rounds as competition progresses
   - Totals update in real-time

3. **After Event**:
   - All rounds completed
   - Final scores locked
   - Winner announced

---

## Troubleshooting

### "Scores not saving"
- ✅ Check internet connection
- ✅ Verify token URL is correct
- ✅ Try refreshing page
- ✅ Check browser console for errors

### "Can't see my scores"
- **Criteria**: Make sure you're using YOUR judge token
- **Quiz Bee**: All moderators see all scores (expected)

### "Wrong total showing"
- **Criteria**: Final score is weighted average, not sum
- **Quiz Bee**: Check points per question in round config

### "Public page not updating"
- ✅ Verify event is set to "Active"
- ✅ Check public viewing token is correct
- ✅ Wait 30 seconds (auto-refresh interval)
- ✅ Manually refresh page

### "Judge token doesn't work for quiz bee"
- ℹ️ Expected! Quiz bee uses admin URL, not judge tokens
- ℹ️ Find admin URL in "Manage Access" page

---

## Best Practices

### Criteria-Based
✅ Set clear min/max scores for each criteria  
✅ Use weights that total 100 for easy calculation  
✅ Test with one judge before sharing all URLs  
✅ Remind judges to save frequently  
✅ Keep judge URLs private (security)  

### Quiz Bee
✅ Configure all rounds before event starts  
✅ Use boolean mode for speed (checkbox faster than typing)  
✅ Use manual mode for partial credit scenarios  
✅ Test round switching before live event  
✅ Have backup device ready for admin scoring  

### Both Systems
✅ Enable public viewing for audience engagement  
✅ Test internet connection before live event  
✅ Have URLs bookmarked before event  
✅ Set event to "Active" when ready to start  
✅ Use descriptive contestant names (avoid numbers only)  

---

## Feature Comparison

| Feature | Criteria-Based | Quiz Bee |
|---------|----------------|----------|
| **Multiple Judges** | ✅ Yes | ❌ No (Moderators) |
| **Weighted Scoring** | ✅ Yes | ❌ No |
| **Question-Level** | ❌ No | ✅ Yes |
| **Real-time Totals** | ❌ No | ✅ Yes |
| **Concurrent Editing** | ❌ No | ✅ Yes |
| **Private Interfaces** | ✅ Yes | ❌ No (Shared) |
| **Boolean Mode** | ❌ No | ✅ Yes |
| **Manual Mode** | ✅ Yes | ✅ Yes |
| **Partial Credit** | ✅ Yes | ✅ Yes (Manual) |
| **Round Tabs** | ❌ No | ✅ Yes |
| **Criteria Tabs** | ❌ No | ❌ No |

---

## System Limits

### Performance Tested
- ✅ 50 contestants
- ✅ 10 criteria / 100 questions
- ✅ 7 judges
- ✅ 3,500 scores

### Recommended Limits
- Contestants: Up to 100
- Criteria: Up to 15
- Judges: Up to 20
- Rounds: Up to 10
- Questions per Round: Up to 50

### Mobile Support
- ✅ Responsive design
- ✅ Touch-friendly inputs
- ✅ Works on tablets
- ✅ Works on phones

---

## Quick Tips

💡 **Criteria**: Think "many judges scoring few criteria"  
💡 **Quiz Bee**: Think "one scorekeeper marking many questions"  

💡 **Criteria**: Each judge scores independently  
💡 **Quiz Bee**: Everyone sees the same grid  

💡 **Criteria**: Use for subjective judging  
💡 **Quiz Bee**: Use for objective right/wrong  

💡 **Criteria**: Final score = weighted average  
💡 **Quiz Bee**: Final score = sum/count  

💡 **Criteria**: Token per judge  
💡 **Quiz Bee**: One token for all  

---

## Getting Help

1. Check this guide first
2. Review TESTING_GUIDE.md for detailed scenarios
3. Check DUAL_SYSTEM_IMPLEMENTATION.md for technical details
4. Contact system administrator

---

## Version Info

- System Version: 1.0
- Last Updated: October 24, 2025
- Laravel: 12.31.1
- Filament: v4
- Database: SQLite

---

*For technical documentation, see DUAL_SYSTEM_IMPLEMENTATION.md*  
*For comprehensive testing, see TESTING_GUIDE.md*
