<?php

namespace Database\Seeders;

use App\Models\Contestant;
use App\Models\Criteria;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Organization;
use App\Models\Round;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'rnd_admin@ue.edu.ph',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        // Create regular users
        $john = User::factory()->create([
            'name' => 'John Gab',
            'email' => 'johngab@ue.edu.ph',
            'password' => bcrypt('password'),
        ]);

        $maria = User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@ue.edu.ph',
            'password' => bcrypt('password'),
        ]);

        $pedro = User::factory()->create([
            'name' => 'Pedro Cruz',
            'email' => 'pedro@ue.edu.ph',
            'password' => bcrypt('password'),
        ]);

        // Create Organizations
        $csOrg = Organization::create([
            'name' => 'Computer Science Society',
            'description' => 'Official organization for CS students',
            'head_user_id' => $john->id,
            'is_active' => true,
        ]);

        $studentCouncil = Organization::create([
            'name' => 'Student Council',
            'description' => 'University-wide student government',
            'head_user_id' => $maria->id,
            'is_active' => true,
        ]);

        $artsClub = Organization::create([
            'name' => 'Arts & Culture Club',
            'description' => 'Promoting arts and cultural activities',
            'head_user_id' => $pedro->id,
            'is_active' => true,
        ]);

        // Attach users to organizations with roles
        // CS Org members
        $csOrg->users()->attach($john, ['role' => 'admin']);
        $csOrg->users()->attach($maria, ['role' => 'member']);
        
        // Student Council members
        $studentCouncil->users()->attach($maria, ['role' => 'admin']);
        $studentCouncil->users()->attach($john, ['role' => 'member']);
        $studentCouncil->users()->attach($pedro, ['role' => 'member']);
        
        // Arts Club members
        $artsClub->users()->attach($pedro, ['role' => 'admin']);

        // Create sample events for CS Org
        $quizBee = Event::create([
            'name' => 'CS Quiz Bee 2025',
            'description' => 'Annual computer science quiz competition',
            'organization_id' => $csOrg->id,
            'created_by_user_id' => $john->id,
            'judging_type' => 'rounds',
            'scoring_mode' => 'boolean',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(3),
            'is_active' => true,
        ]);

        // Create sample event for Student Council
        $mrAndMs = Event::create([
            'name' => 'Mr. & Ms. University 2025',
            'description' => 'Annual beauty pageant competition',
            'organization_id' => $studentCouncil->id,
            'created_by_user_id' => $maria->id,
            'judging_type' => 'criteria',
            'scoring_mode' => 'manual',
            'start_date' => now()->addDays(14),
            'end_date' => now()->addDays(14)->addHours(4),
            'is_active' => true,
        ]);

        // Create contestants for Quiz Bee
        $contestants = [
            'Team Alpha' => 'CS 4th Year',
            'Team Beta' => 'CS 3rd Year',
            'Team Gamma' => 'CS 2nd Year',
        ];

        foreach ($contestants as $name => $description) {
            Contestant::create([
                'name' => $name,
                'description' => $description,
                'event_id' => $quizBee->id,
                'organization_id' => $csOrg->id,
                'is_active' => true,
            ]);
        }

        // Create contestants for Pageant
        $pageantContestants = [
            'Candidate #1' => 'Jane Doe - BSBA',
            'Candidate #2' => 'John Smith - BSCS',
            'Candidate #3' => 'Mary Johnson - BSN',
        ];

        foreach ($pageantContestants as $name => $description) {
            Contestant::create([
                'name' => $name,
                'description' => $description,
                'event_id' => $mrAndMs->id,
                'organization_id' => $studentCouncil->id,
                'is_active' => true,
            ]);
        }

        // Create judges for Quiz Bee
        $judge1 = Judge::create([
            'name' => 'Dr. Robert Lee',
            'email' => 'robert.lee@ue.edu.ph',
            'specialization' => 'Computer Science',
            'bio' => 'Professor of Computer Science',
            'organization_id' => $csOrg->id,
            'is_active' => true,
        ]);

        $judge2 = Judge::create([
            'name' => 'Prof. Anna Garcia',
            'email' => 'anna.garcia@ue.edu.ph',
            'specialization' => 'Information Technology',
            'bio' => 'IT Department Head',
            'organization_id' => $csOrg->id,
            'is_active' => true,
        ]);

        // Attach judges to quiz bee
        $quizBee->judges()->attach([$judge1->id, $judge2->id], [
            'status' => 'accepted',
            'invited_at' => now(),
            'responded_at' => now(),
        ]);

        // Create rounds for Quiz Bee
        Round::create([
            'name' => 'Easy Round',
            'description' => 'Basic CS concepts',
            'total_questions' => 10,
            'points_per_question' => 1,
            'max_score' => 10,
            'event_id' => $quizBee->id,
            'organization_id' => $csOrg->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Round::create([
            'name' => 'Average Round',
            'description' => 'Intermediate programming',
            'total_questions' => 10,
            'points_per_question' => 2,
            'max_score' => 20,
            'event_id' => $quizBee->id,
            'organization_id' => $csOrg->id,
            'order' => 2,
            'is_active' => true,
        ]);

        Round::create([
            'name' => 'Difficult Round',
            'description' => 'Advanced algorithms',
            'total_questions' => 5,
            'points_per_question' => 3,
            'max_score' => 15,
            'event_id' => $quizBee->id,
            'organization_id' => $csOrg->id,
            'order' => 3,
            'is_active' => true,
        ]);

        // Create judges for Pageant
        $pageantJudge1 = Judge::create([
            'name' => 'Ms. Elizabeth Cruz',
            'email' => 'elizabeth.cruz@ue.edu.ph',
            'specialization' => 'Fashion & Design',
            'bio' => 'Fashion Design Expert',
            'organization_id' => $studentCouncil->id,
            'is_active' => true,
        ]);

        $pageantJudge2 = Judge::create([
            'name' => 'Mr. Michael Reyes',
            'email' => 'michael.reyes@ue.edu.ph',
            'specialization' => 'Performing Arts',
            'bio' => 'Theater Director',
            'organization_id' => $studentCouncil->id,
            'is_active' => true,
        ]);

        // Attach judges to pageant
        $mrAndMs->judges()->attach([$pageantJudge1->id, $pageantJudge2->id], [
            'status' => 'accepted',
            'invited_at' => now(),
            'responded_at' => now(),
        ]);

        // Create criteria for Pageant
        Criteria::create([
            'name' => 'Physical Beauty',
            'description' => 'Overall physical appearance',
            'weight' => 30,
            'max_score' => 100,
            'min_score' => 0,
            'event_id' => $mrAndMs->id,
            'organization_id' => $studentCouncil->id,
            'order' => 1,
            'is_active' => true,
        ]);

        Criteria::create([
            'name' => 'Intelligence',
            'description' => 'Q&A portion',
            'weight' => 40,
            'max_score' => 100,
            'min_score' => 0,
            'event_id' => $mrAndMs->id,
            'organization_id' => $studentCouncil->id,
            'order' => 2,
            'is_active' => true,
        ]);

        Criteria::create([
            'name' => 'Stage Presence',
            'description' => 'Confidence and charisma',
            'weight' => 30,
            'max_score' => 100,
            'min_score' => 0,
            'event_id' => $mrAndMs->id,
            'organization_id' => $studentCouncil->id,
            'order' => 3,
            'is_active' => true,
        ]);

        $this->command->info('✅ Sample data seeded successfully!');
        $this->command->info('');
        $this->command->info('📧 Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Super Admin:');
        $this->command->info('  Email: rnd_admin@ue.edu.ph');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Org Admin (CS Society):');
        $this->command->info('  Email: johngab@ue.edu.ph');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Org Admin (Student Council):');
        $this->command->info('  Email: maria@ue.edu.ph');
        $this->command->info('  Password: password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
