<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\HygieneArticle;
use App\Models\GeneralGuide;
use App\Models\EmergencyContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@tithandizane.mw',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // user
        User::create([
            'name' => 'user',
            'email' => 'user@tithandizane.mw',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        // Sample Mentors
        $mentors = [
            [
                'name' => 'Dr. Grace Phiri',
                'email' => 'grace.phiri@tithandizane.mw',
                'password' => Hash::make('mentor123'),
                'role' => 'mentor',
                'bio' => 'Medical doctor specializing in women\'s health with 10 years experience.',
                'expertise_area' => 'Health & Menstrual Hygiene',
                'available_days' => json_encode(['Monday', 'Wednesday', 'Friday']),
                'available_time_start' => '09:00',
                'available_time_end' => '17:00',
                'is_available' => true,
            ],
            [
                'name' => 'Prof. Amina Banda',
                'email' => 'amina.banda@tithandizane.mw',
                'password' => Hash::make('mentor123'),
                'role' => 'mentor',
                'bio' => 'University lecturer and women empowerment advocate.',
                'expertise_area' => 'Education & Career',
                'available_days' => json_encode(['Tuesday', 'Thursday']),
                'available_time_start' => '14:00',
                'available_time_end' => '18:00',
                'is_available' => true,
            ],
            [
                'name' => 'Ms. Thandeka Mwale',
                'email' => 'thandeka.mwale@tithandizane.mw',
                'password' => Hash::make('mentor123'),
                'role' => 'mentor',
                'bio' => 'Counselor and mental health advocate helping young women build resilience.',
                'expertise_area' => 'Mental Health & Self-esteem',
                'available_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
                'available_time_start' => '10:00',
                'available_time_end' => '15:00',
                'is_available' => true,
            ],
        ];

        foreach ($mentors as $mentor) {
            User::create($mentor);
        }

        // Hygiene Articles
        $articles = [
            [
                'title' => 'Understanding Your Menstrual Cycle',
                'content' => 'Your menstrual cycle is a natural process that happens every month. A typical cycle lasts between 21 to 35 days. During this time, your body prepares for a possible pregnancy. When pregnancy does not occur, the uterus sheds its lining — this is your period. It usually lasts 3 to 7 days. Understanding your cycle helps you plan ahead and stay confident. Track your periods on a calendar or app so you always know when to expect them.',
                'category' => 'basics',
            ],
            [
                'title' => 'Choosing the Right Menstrual Product',
                'content' => 'There are several types of menstrual products available. Pads (sanitary napkins) are placed inside your underwear to absorb blood. They come in different sizes for light and heavy flow. Tampons are inserted into the vagina. They must be changed every 4-8 hours. Menstrual cups are reusable silicone cups inserted into the vagina. They can be used for up to 12 hours. Reusable cloth pads are washable and eco-friendly. Choose what works best for your body, lifestyle, and budget.',
                'category' => 'products',
            ],
            [
                'title' => 'Staying Clean During Your Period',
                'content' => 'Good hygiene during your period keeps you comfortable and healthy. Wash your hands before and after changing your pad or tampon. Change your pad every 4-6 hours even if it is not fully soaked. Bathe or shower daily using mild, unscented soap. Clean your vaginal area from front to back to prevent infections. Wear clean, breathable cotton underwear. Dispose of used pads properly — wrap in toilet paper and put in a bin.',
                'category' => 'health',
            ],
            [
                'title' => 'Managing Period at School',
                'content' => 'Having your period at school does not have to be stressful. Always carry extra pads or tampons in your school bag. Know where the nearest bathroom is. If you get your period unexpectedly, speak to a female teacher or school nurse in confidence. They are there to help you. Wearing darker clothing on heavy days can ease your anxiety. Remember: every girl goes through this. You are not alone.',
                'category' => 'school',
            ],
            [
                'title' => 'Common Myths About Menstruation',
                'content' => 'MYTH: You cannot exercise during your period. TRUTH: Light exercise like walking or yoga can actually reduce cramps. MYTH: You should not bathe during your period. TRUTH: Bathing is safe and important for hygiene. MYTH: Period blood is dirty. TRUTH: It is the same as regular blood mixed with uterine tissue. MYTH: Eating certain foods causes heavier periods. TRUTH: Diet does not directly cause heavier flow, but staying hydrated and eating iron-rich foods helps.',
                'category' => 'myths',
            ],
        ];

        foreach ($articles as $article) {
            HygieneArticle::create($article);
        }

        // General Guides
        $guides = [
            [
                'title' => 'Building Your Self-Esteem',
                'content' => 'Self-esteem is how you feel about yourself. High self-esteem means you believe in your worth and abilities. Here are ways to build yours: Celebrate small wins every day. Write down 3 things you like about yourself. Surround yourself with people who uplift you. Avoid comparing yourself to others on social media. Set achievable goals and celebrate when you reach them. Speak kindly to yourself — you deserve the same kindness you give others.',
                'category' => 'self-esteem',
                'icon' => 'star',
            ],
            [
                'title' => 'Handling Stress as a Young Woman',
                'content' => 'Stress is a normal part of life, but unmanaged stress can affect your health and happiness. Signs of stress include headaches, trouble sleeping, irritability, and difficulty concentrating. To manage stress: Take deep breaths when you feel overwhelmed. Talk to someone you trust. Break big tasks into small steps. Make time for activities you enjoy. Get enough sleep — 7 to 9 hours per night. Limit social media time if it increases anxiety.',
                'category' => 'stress',
                'icon' => 'heart',
            ],
            [
                'title' => 'Healthy Relationships',
                'content' => 'A healthy relationship — whether friendship, family, or romantic — is built on respect, trust, and open communication. Signs of a healthy relationship: Both people feel safe and respected. Disagreements are resolved calmly. You support each other\'s goals. No one feels pressured or controlled. Signs of an unhealthy relationship: Feeling afraid of the other person. Being controlled or isolated from friends/family. Being put down or humiliated. If you are in an unhealthy relationship, reach out for help through our platform.',
                'category' => 'relationships',
                'icon' => 'people',
            ],
            [
                'title' => 'Personal Development & Career',
                'content' => 'Investing in yourself is the best thing you can do. Personal development means working on your skills, knowledge, and mindset. Start by identifying your strengths and interests. Set goals for your education and future career. Read books, take online courses, and ask mentors for guidance. Keep a journal to track your growth. Do not be afraid to dream big — many successful women started with humble beginnings. Seek out mentors who inspire you and learn from their journeys.',
                'category' => 'personal_development',
                'icon' => 'rocket',
            ],
        ];

        foreach ($guides as $guide) {
            GeneralGuide::create($guide);
        }

        // Emergency Contacts (Malawi)
        $contacts = [
            ['name' => 'Malawi Police Service', 'phone' => '999', 'type' => 'police'],
            ['name' => 'Pogpogwa (GBV Helpline)', 'phone' => '5600', 'type' => 'counseling'],
            ['name' => 'Ministry of Gender Helpline', 'phone' => '1180', 'type' => 'women_affairs'],
            ['name' => 'Kamuzu Central Hospital', 'phone' => '+265 1 758 900', 'type' => 'health'],
            ['name' => 'Mzuzu Central Hospital', 'phone' => '+265 1 333 999', 'type' => 'health'],
            ['name' => 'Centre for Legal Aid', 'phone' => '+265 1 750 700', 'type' => 'counseling'],
        ];

        foreach ($contacts as $contact) {
            EmergencyContact::create($contact);
        }
    }
}
