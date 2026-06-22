<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentorReview;
use App\Models\MentorshipSession;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $thandeka = User::where('email', 'thandeka.mwale@tithandizane.mw')->firstOrFail();
        $mentee   = User::where('email', 'user@tithandizane.mw')->firstOrFail();

        // mentorship_sessions columns confirmed:
        // id, mentor_id, mentee_id, topic, message, status,
        // scheduled_at, mentor_notes, requested_date,
        // requested_time_from, requested_time_to, created_at, updated_at
        $session = MentorshipSession::create([
            'mentor_id'           => $thandeka->id,
            'mentee_id'           => $mentee->id,
            'topic'               => 'Mental Health Support',
            'message'             => 'I would like guidance on managing stress.',
            'status'              => 'completed',
            'requested_date'      => now()->subDays(7)->toDateString(),
            'requested_time_from' => '10:00',
            'requested_time_to'   => '11:00',
            'scheduled_at'        => now()->subDays(7),
        ]);

        // mentor_reviews columns confirmed:
        // id, mentorship_session_id, reviewer_id, mentor_id,
        // rating, comment, created_at, updated_at
        MentorReview::create([
            'mentorship_session_id' => $session->id,
            'reviewer_id'           => $mentee->id,
            'mentor_id'             => $thandeka->id,
            'rating'                => 3,
            'comment'               => 'Helpful session with great advice.',
        ]);
    }
}