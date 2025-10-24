# Generic Scoring System

A comprehensive, **token-based** scoring system built with Laravel and Filament that supports two types of judging:

1. **Criteria-based judging** (e.g., Beauty Pageants) - Judges rate contestants based on multiple weighted criteria
2. **Rounds-based judging** (e.g., Quiz Bees) - Contestants compete in multiple rounds with configurable scoring modes

## 🌟 Key Features

### 🎯 Token-Based Access (No Login Required!)
- **Unique Judge Links**: Each judge gets a secure, token-based link
- **No Account Creation**: Judges can score immediately without registration
- **QR Codes**: Easy sharing via QR codes for in-person events
- **Public Viewing Link**: Separate link for audience with configurable visibility

### 🎭 Dual Judging Systems

### 🎭 Dual Judging Systems
- **Pageant/Criteria Mode**: Multiple weighted criteria with customizable max scores
- **Quiz Bee/Rounds Mode**: Multiple rounds/categories with two scoring options:
  - **Boolean Mode**: Check correct/incorrect, points auto-calculated
  - **Manual Mode**: Enter custom scores per round

### 📊 Real-Time Public Viewing
- **Live Leaderboard**: Auto-updates every 5 seconds
- **Configurable Visibility**: Control what information is shown publicly
  - Rankings with medal icons (🥇🥈🥉)
  - Final scores
  - Judge names
  - Individual judge scores
  - Detailed breakdowns
  - Judge progress
- **No Login Required**: Anyone with the link can view

### 🎨 Comprehensive Admin Panel
- **Event Management**: Complete CRUD with enhanced configuration
- **Link Management Dashboard**: 
  - Generate unlimited judge slots
  - View/copy individual judge links
  - Display QR codes for sharing
  - Regenerate tokens for security
  - Monitor real-time statistics
- **Public Viewing Configuration**: 7+ visibility toggles
- **Judge Progress Tracking**: See completion percentage per judge

### Core Functionality
- **Event Management**: Create and manage events with different judging types
- **Contestant Management**: Add and manage contestants for each event
- **Judge Management**: Invite and manage judges for events
- **Scoring System**: Flexible scoring for both criteria and rounds-based events
- **Results & Rankings**: Automatic calculation of final scores and rankings
- **Social Authentication**: Login with Google, Facebook, or GitHub

### Admin Panel (Filament)
- Complete CRUD operations for all entities
- User-friendly interface for event organizers
- Real-time data management
- Export capabilities

### Judge Interface
- Dedicated scoring interface for judges
- Real-time score saving
- Results viewing
- Progress tracking

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd generic-scoring-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

5. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 🚀 Quick Start (5 Minutes)

See [QUICKSTART.md](QUICKSTART.md) for a complete walkthrough from zero to your first live event!

## 📚 Documentation

- **[QUICKSTART.md](QUICKSTART.md)** - Get started in 5 minutes
- **[IMPLEMENTATION.md](IMPLEMENTATION.md)** - Complete technical documentation
- **[WORKFLOW.md](WORKFLOW.md)** - Visual diagrams and workflows
- **[TESTING-CHECKLIST.md](TESTING-CHECKLIST.md)** - Comprehensive testing guide
- **[SUMMARY.md](SUMMARY.md)** - Feature overview and status

## Usage

### For Event Organizers

1. **Access Application**: Visit the root URL and login
2. **Create Event**: 
   - Go to Events → Create Event
   - Choose judging type (Criteria-based or Rounds-based)
   - For Quiz Bee, select scoring mode (Boolean or Manual)
   - Configure public viewing visibility settings
3. **Set up Structure**:
   - For Criteria-based: Define criteria with weights and max scores
   - For Rounds-based: Define rounds with points configuration
4. **Add Contestants**: Add all participants
5. **Generate Judge Links**:
   - Go to Events → Click "Manage Links" on your event
   - Click "Add Judges" and specify quantity
   - Copy individual links or display QR codes
6. **Share Links**:
   - Send judge links via WhatsApp/Email/Print QR codes
   - Share public viewing link for audience
7. **Monitor Live**: Track progress and view real-time statistics

### For Judges (Token-Based Access)

1. **Open Your Unique Link**: No login required!
2. **View Scoring Interface**: See all contestants and criteria/rounds
3. **Enter Scores**:
   - **Criteria Mode**: Enter score per criterion for each contestant
   - **Quiz Boolean**: Check boxes for correct answers
   - **Quiz Manual**: Enter points earned per round
4. **Save**: Scores auto-save and persist
5. **View Results**: Click "View Results" to see current rankings

### For Public Viewers

1. **Access Public Link**: No authentication needed
2. **View Live Results**: 
   - See real-time leaderboard (updates every 5 seconds)
   - Watch rankings with medal icons
   - Monitor judge progress
   - View event statistics
3. **Auto-Refresh**: Page updates automatically

## 🎯 Use Cases

- **Beauty Pageants**: Criteria-based with multiple judges
- **Quiz Bees**: Rounds-based with boolean/manual scoring
- **Talent Shows**: Criteria-based with weighted categories
- **Academic Competitions**: Rounds-based with point accumulation
- **Sports Judging**: Any format with configurable criteria
- **Corporate Events**: Team competitions with scoring

## System Architecture

### Key URLs

```
/                                         - Main application (Filament)
/resources/events/{id}/manage-access     - Link management dashboard

/score/{token}                            - Judge scoring interface (no auth)
/score/{token}/results                    - Judge results view (no auth)

/admin/score/{token}                      - Admin scoring for Quiz Bee (shared access)

/public/event/{token}                     - Public viewing page (no auth)
/public/event/{token}/live                - Live results API (JSON)
```

### Database Schema

- **Events**: Core event information with tokens and visibility config
  - `public_viewing_token`: Secure token for public access
  - `scoring_mode`: 'boolean' or 'manual' for quiz bees
  - `public_viewing_config`: JSON for visibility settings
- **Contestants**: Participant details
- **Judges**: Judge profiles (optional - token-based access doesn't require accounts)
- **EventJudges**: Judge slots with unique tokens
  - `judge_token`: Secure token for scoring access
  - `judge_name`: Display name (no account needed)
- **Criteria**: Judging criteria for criteria-based events
- **Rounds**: Competition rounds for rounds-based events
- **Scores**: Individual scores with boolean support
  - `is_correct`: For boolean scoring mode

### Key Services

- **EventAccessService**: Manages judge links, tokens, QR codes, and statistics
- **ScoringService**: Handles all scoring calculations with boolean mode support
- **TokenScoringController**: Judge interface (no authentication)
- **PublicViewController**: Public viewing with configurable visibility

## Scoring Logic

### Criteria-based Events (Pageants)
```
Final Score = Σ(average_score_per_criteria × weight) / total_weight
```
- Each criterion has a weight and max score
- Scores are averaged across all judges per criterion
- Final score is the weighted average

### Rounds-based Events (Quiz Bees)

**Manual Mode:**
```
Final Score = Σ(scores_per_round)
```
- Judges enter scores directly
- Final score is the sum of all rounds

**Boolean Mode:**
```
Round Score = correct_count × points_per_question
Final Score = Σ(round_scores)
```
- Judges mark correct/incorrect
- Points automatically calculated
- Final score is the sum of all rounds

## 🔐 Security Features

- **64-character cryptographically secure tokens**
- **Unique constraints** in database prevent duplicates
- **Token regeneration** capability for security
- **No password management** needed for judges
- **Server-side validation** on all score submissions
- **CSRF protection** on all forms
- **Event-specific access** isolation

## 🎨 Features Showcase

### Token-Based Access
✅ **No Login Required**: Judges access via unique links  
✅ **QR Codes**: Easy sharing for in-person events  
✅ **Secure**: 64-char random tokens  
✅ **Regenerable**: Invalidate old links anytime  

### Real-Time Updates
✅ **Auto-Refresh**: Public page updates every 5 seconds  
✅ **Live Leaderboard**: See rankings change in real-time  
✅ **Judge Progress**: Track completion percentage  
✅ **Statistics**: Live contestant and score counts  

### Flexible Configuration
✅ **7+ Visibility Options**: Control what public sees  
✅ **Dual Scoring Modes**: Boolean or Manual for Quiz Bees  
✅ **Weighted Criteria**: Custom weights for Pageants  
✅ **Unlimited Judges**: Generate as many slots as needed  

## API Endpoints

### Token-Based Judge Interface (No Authentication)
- `GET /score/{token}` - Scoring interface
- `POST /score/{token}` - Submit scores
- `GET /score/{token}/scores` - Get existing scores
- `GET /score/{token}/results` - View results

### Public Viewing (No Authentication)
- `GET /public/event/{token}` - Public viewing page
- `GET /public/event/{token}/live` - Live results JSON (for auto-refresh)
- `GET /public/event/{token}/contestant/{id}` - Detailed breakdown

### Admin Panel (Filament - Authentication Required)
- `/` - Main application dashboard
- `/resources/events` - Event management
- `/resources/events/{id}/manage-access` - Link management
- `/resources/events/{id}/score-quiz-bee` - Quiz bee scoring interface

### Legacy Judge Interface (Authentication Required - Preserved)
- `GET /judge/events` - List assigned events
- `GET /judge/events/{event}` - Show scoring interface
- `POST /judge/events/{event}/scores` - Submit scores
- `GET /judge/events/{event}/scores` - Get existing scores
- `GET /judge/events/{event}/results` - View results

## 🚀 Technology Stack

- **Backend**: Laravel 12
- **Admin Panel**: Filament 4
- **Frontend**: Tailwind CSS + Alpine.js
- **Authentication**: Token-based (judges), Laravel Auth (admin)
- **Database**: MySQL/PostgreSQL/SQLite
- **Real-time**: JavaScript polling (5s intervals)
- **QR Codes**: QR Server API integration

## 📱 Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 🐛 Troubleshooting

**Q: Judge links not working?**  
A: Verify tokens were generated. Try regenerating from the "Manage Links" page.

**Q: Public page not updating?**  
A: Check browser console for errors. Ensure event is marked as "Active".

**Q: Scores not saving?**  
A: Check validation errors. Verify score is within min/max range.

**Q: QR codes not showing?**  
A: Requires internet connection (uses external API).

See [TESTING-CHECKLIST.md](TESTING-CHECKLIST.md) for comprehensive troubleshooting.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 💬 Support

For support and questions:
- Check the documentation files in this repository
- Review [IMPLEMENTATION.md](IMPLEMENTATION.md) for technical details
- See [QUICKSTART.md](QUICKSTART.md) for setup help
- Create an issue in the repository

## 🎉 Acknowledgments

Built with:
- [Laravel](https://laravel.com/) - The PHP Framework
- [Filament](https://filamentphp.com/) - Admin Panel Framework
- [Tailwind CSS](https://tailwindcss.com/) - CSS Framework
- [Alpine.js](https://alpinejs.dev/) - JavaScript Framework

---

**Version 2.0** - Token-Based Edition  
**Last Updated**: October 21, 2025  

⭐ If you find this project useful, please consider giving it a star!