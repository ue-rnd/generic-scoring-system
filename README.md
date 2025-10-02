# Generic Scoring System

A comprehensive scoring system built with Laravel and Filament that supports two types of judging:

1. **Criteria-based judging** (e.g., Beauty Pageants) - Judges rate contestants based on multiple criteria with weights
2. **Rounds-based judging** (e.g., Quiz Bees) - Contestants compete in multiple rounds with point accumulation

## Features

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

6. **Configure social authentication** (optional)
   - Add your OAuth credentials to `.env`:
     ```
     GOOGLE_CLIENT_ID=your_google_client_id
     GOOGLE_CLIENT_SECRET=your_google_client_secret
     GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
     
     FACEBOOK_CLIENT_ID=your_facebook_client_id
     FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
     FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
     
     GITHUB_CLIENT_ID=your_github_client_id
     GITHUB_CLIENT_SECRET=your_github_client_secret
     GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback
     ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Usage

### For Event Organizers

1. **Access Admin Panel**: Visit `/admin` and login with your admin credentials
2. **Create Events**: 
   - Go to Events → Create Event
   - Choose judging type (Criteria-based or Rounds-based)
   - Set event details and schedule
3. **Add Contestants**: Add all participants for your event
4. **Set up Judging Structure**:
   - For Criteria-based: Define criteria with weights and max scores
   - For Rounds-based: Define rounds with point values
5. **Invite Judges**: Add judges and send invitations
6. **Monitor Progress**: Track judge completion and view results

### For Judges

1. **Login**: Use social authentication or admin credentials
2. **View Assigned Events**: Access `/judge/events` to see your assigned events
3. **Score Events**: Use the scoring interface to rate contestants
4. **View Results**: Check final rankings and detailed breakdowns

## System Architecture

### Database Schema

- **Events**: Core event information and judging type
- **Contestants**: Participant details
- **Judges**: Judge profiles and information
- **EventJudges**: Many-to-many relationship with invitation status
- **Criteria**: Judging criteria for criteria-based events
- **Rounds**: Competition rounds for rounds-based events
- **Scores**: Individual scores with relationships to all entities

### Key Components

- **ScoringService**: Handles all scoring calculations and rankings
- **ScoringController**: Manages judge interface and score submission
- **Filament Resources**: Admin panel for event management
- **Social Authentication**: OAuth integration for user login

## Scoring Logic

### Criteria-based Events
- Each criteria has a weight and max score
- Final score = Σ(average_score_per_criteria × weight) / total_weight
- Rankings based on weighted final scores

### Rounds-based Events
- Each round has a max possible score
- Final score = sum of all round scores
- Rankings based on total accumulated points

## API Endpoints

### Judge Interface
- `GET /judge/events` - List assigned events
- `GET /judge/events/{event}` - Show scoring interface
- `POST /judge/events/{event}/scores` - Submit scores
- `GET /judge/events/{event}/scores` - Get existing scores
- `GET /judge/events/{event}/results` - View results

### Authentication
- `GET /login` - Login page
- `GET /auth/{provider}` - Social login redirect
- `GET /auth/{provider}/callback` - Social login callback
- `POST /logout` - Logout

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please create an issue in the repository or contact the development team.