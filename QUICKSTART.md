# Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Run Migrations (Already Done! ✓)
```bash
php artisan migrate
```

### Step 2: Start the Development Server
```bash
php artisan serve
```

### Step 3: Access the Admin Panel
1. Navigate to: `http://localhost:8000/admin`
2. Login with your admin credentials

### Step 4: Create Your First Event

1. **Go to Events** → Click "Create"

2. **Fill in Basic Info:**
   - Name: "Sample Beauty Pageant" or "Quiz Bee 2025"
   - Description: Brief description of your event
   - Organizer: Select yourself
   - Dates: Set start and end dates

3. **Choose Judging Configuration:**
   - **For Pageant**: Select "Criteria-based"
   - **For Quiz Bee**: Select "Rounds-based"
     - Then choose "Boolean" (correct/incorrect) or "Manual" (enter scores)

4. **Configure Public Viewing** (scroll down):
   - Toggle what information you want visible to the public
   - Recommended: Enable "Show Rankings" and "Show Judge Progress"

5. Click "Create"

### Step 5: Set Up Judging Structure

#### For Pageant (Criteria-based):
1. Go to **Criterias** → Click "Create"
2. Add criteria like:
   - "Stage Presence" (Weight: 30, Max Score: 100)
   - "Intelligence" (Weight: 40, Max Score: 100)
   - "Beauty" (Weight: 30, Max Score: 100)

#### For Quiz Bee (Rounds-based):
1. Go to **Rounds** → Click "Create"
2. Add rounds like:
   - "Easy Round" (10 questions, 1 point each)
   - "Average Round" (10 questions, 2 points each)
   - "Difficult Round" (5 questions, 5 points each)

### Step 6: Add Contestants
1. Go to **Contestants** → Click "Create"
2. Add your contestants:
   - Name: "Contestant #1"
   - Select your event
   - Repeat for all contestants

### Step 7: Generate Judge Links
1. Go back to **Events**
2. Click "Manage Links" button for your event
3. Click "Add Judges" in the top right
4. Enter number of judges (e.g., 5)
5. Optionally add judge names
6. Click "Save"

### Step 8: Share Links

#### Judge Links:
1. Each judge now has a unique link displayed
2. **Copy the link** by clicking the "Copy" button
3. Share via:
   - WhatsApp/Messenger
   - Email
   - QR code (print or display)

#### Public Viewing Link:
1. Copy the "Public Viewing Link" at the top
2. Share with your audience
3. Display on a big screen during the event

### Step 9: Judges Start Scoring
1. Judges open their unique links (no login needed!)
2. They see the scoring interface
3. Score contestants:
   - **Pageant**: Enter scores for each criterion
   - **Quiz Boolean**: Check boxes for correct answers
   - **Quiz Manual**: Enter points earned
4. Click "Save Scores"

### Step 10: Watch Live Results
1. Open the Public Viewing Link
2. See the leaderboard update automatically
3. Monitor judge progress
4. Celebrate your winners! 🎉

---

## 🎯 Example Walkthrough: Beauty Pageant

```
1. Create Event:
   - Name: "Miss Universe 2025"
   - Type: Criteria-based
   - Scoring: Manual

2. Add Criteria:
   - Swimsuit (Weight: 25%, Max: 100)
   - Evening Gown (Weight: 25%, Max: 100)
   - Q&A (Weight: 50%, Max: 100)

3. Add Contestants:
   - Miss Philippines
   - Miss USA
   - Miss Brazil
   (etc.)

4. Add Judges:
   - Generate 7 judge slots
   - Name them: Judge Panel 1-7

5. Share:
   - Send judge links to panel members
   - Display public link on venue screens

6. Live Event:
   - Judges score on tablets/phones
   - Audience watches live rankings
   - Results auto-calculate
```

---

## 🏆 Example Walkthrough: Quiz Bee

```
1. Create Event:
   - Name: "Science Quiz Bee 2025"
   - Type: Rounds-based
   - Scoring: Boolean (Correct/Incorrect)

2. Add Rounds:
   - Easy Round (20 questions, 1 pt each)
   - Average Round (15 questions, 2 pts each)
   - Difficult Round (10 questions, 5 pts each)

3. Add Contestants:
   - Team Alpha
   - Team Beta
   - Team Gamma

4. Add Judges:
   - Just 1 "Quizmaster" judge

5. Share:
   - Give quizmaster the scoring link
   - Display public link for audience

6. Live Event:
   - Quizmaster checks correct/incorrect
   - Points auto-calculate
   - Leaderboard updates live
```

---

## 🔧 Troubleshooting

### Issue: Can't access admin panel
**Solution:** Make sure you created an admin user:
```bash
php artisan make:filament-user
```

### Issue: Judge links not working
**Solution:** 
- Check if tokens were generated (should be automatic)
- Try regenerating tokens from "Manage Links" page

### Issue: Public viewing not updating
**Solution:**
- Check browser console for errors
- Verify the event is marked as "Active"
- Refresh the page manually

### Issue: Scores not saving
**Solution:**
- Check validation errors in browser
- Verify min/max score constraints
- Ensure event is active

---

## 📱 Testing Checklist

- [ ] Create event ✓
- [ ] Add criteria/rounds ✓
- [ ] Add contestants ✓
- [ ] Generate judge links ✓
- [ ] Open judge link in browser ✓
- [ ] Submit test scores ✓
- [ ] Open public viewing link ✓
- [ ] Verify scores appear ✓
- [ ] Check real-time updates ✓
- [ ] Test on mobile device ✓

---

## 🎉 You're Ready!

Your generic scoring system is now fully configured and ready for production use!

**Key URLs:**
- Admin Panel: `/admin`
- Judge Scoring: `/score/{token}` (unique per judge)
- Public Viewing: `/public/event/{token}` (one per event)

**Pro Tips:**
- Test everything before your live event
- Have backup links ready
- Print QR codes for easy access
- Monitor the "Manage Links" page during events
- Keep public viewing open on a dedicated screen

Enjoy your event! 🚀
