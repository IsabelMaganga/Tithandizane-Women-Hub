<?php

namespace Database\Seeders;

use App\Models\HarassmentReport as ModelsHarassmentReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class HarassmentReport extends Seeder
{
    public function run(): void
    {
        // Look up seeded users so IDs are always correct regardless of run order
        $grace  = User::where('email', 'grace.phiri@tithandizane.mw')->first();
        $amina  = User::where('email', 'martingulo28@gmail.com')->first();
        $thand  = User::where('email', 'thandeka.mwale@tithandizane.mw')->first();

        $reports = [

            // 1 — Anonymous, pending, no mentor yet
            [
                'incident_type'        => 'verbal',
                'incident_title'       => 'Verbal abuse from supervisor at work',
                'incident_description' => 'My supervisor regularly shouts at me and calls me names in front of colleagues. He says women do not belong in management and uses very demeaning language. This has been happening for three months and has severely affected my confidence and mental health.',
                'incident_date'        => now()->subDays(5)->toDateString(),
                'incident_location'    => 'Blantyre, Office building — Area 15',
                'perpetrator_info'     => 'Male supervisor, early 50s, works in the accounts department',
                'is_anonymous'         => true,
                'status'               => 'pending',
            ],

            // 2 — Identified, assigned to Grace with response
            [
                'incident_type'        => 'physical',
                'incident_title'       => 'Physical harassment on public transport',
                'incident_description' => 'I was groped by a man on a minibus while travelling from Lilongwe City Centre to Area 25. When I shouted for help, other passengers looked away. The conductor did nothing. I am afraid to use public transport now.',
                'incident_date'        => now()->subDays(10)->toDateString(),
                'incident_location'    => 'Lilongwe — minibus route City Centre to Area 25',
                'perpetrator_info'     => 'Unknown male passenger, approximately 30 years old, wearing a blue shirt',
                'is_anonymous'         => false,
                'victim_name'          => 'Chisomo Tembo',
                'victim_email'         => 'chisomo.tembo@example.mw',
                'victim_phone'         => '+265 991 234 567',
                'status'               => 'resolved',
                'assigned_mentor_id'   => $grace?->id,
                'admin_response'       => "Dear Chisomo,\n\nThank you for your courage in reporting this. What happened to you is completely unacceptable and is a criminal offence under the Gender Equality Act.\n\nI strongly encourage you to:\n• Report this to the police (dial 999) and request a female officer if it makes you more comfortable.\n• Contact the GBV helpline on 5600 for additional support.\n• Know that this was not your fault — you did nothing wrong.\n\nI am here if you need to talk further. Please use the in-app chat to reach me directly.\n\nWith support,\nDr. Grace Phiri",
                'responded_at'         => now()->subDays(8),
            ],

            // 3 — Anonymous, assigned to Thandeka, reviewing
            [
                'incident_type'        => 'cyber',
                'incident_title'       => 'Cyberbullying and threats on social media',
                'incident_description' => 'A group of students from my school created a fake Facebook profile using my photos. They posted humiliating content and shared my phone number publicly. I have been receiving threatening messages from strangers ever since. I have taken screenshots but I am too scared to report it to the school.',
                'incident_date'        => now()->subDays(3)->toDateString(),
                'incident_location'    => 'Online — Facebook and WhatsApp',
                'perpetrator_info'     => 'Believed to be classmates from Form 4 — names unknown',
                'is_anonymous'         => true,
                'status'               => 'reviewing',
                'assigned_mentor_id'   => $thand?->id,
            ],

            // 4 — Identified, linked to app user, assigned to Amina, with response
            [
                'incident_type'        => 'sexual',
                'incident_title'       => 'Sexual harassment by university lecturer',
                'incident_description' => 'My lecturer has been making inappropriate comments about my appearance during one-on-one tutorial sessions. Last week he suggested my grade could improve if I "spent more time with him outside class." I have refused and I am worried this will affect my results. Other female students have mentioned similar experiences with him.',
                'incident_date'        => now()->subDays(7)->toDateString(),
                'incident_location'    => 'University of Malawi — Polytechnic campus, lecturer office Block C',
                'perpetrator_info'     => 'Lecturer — teaches Engineering Mathematics, tall, bald, wears glasses',
                'is_anonymous'         => false,
                'victim_name'          => 'Tadala Mvula',
                'victim_email'         => 'user@tithandizane.mw',
                'victim_phone'         => '+265 888 100 200',
                'status'               => 'assigned',
                'assigned_mentor_id'   => $amina?->id,
                'admin_response'       => "Dear Tadala,\n\nWhat you have described is sexual harassment and a serious abuse of power. You have done the right thing by reporting it.\n\nPlease keep records of every incident — dates, what was said, and any witnesses. You have the right to:\n• File a formal complaint with the university's Student Affairs office.\n• Contact the Centre for Legal Aid (+265 1 750 700) for free legal guidance.\n• Report to the police if you feel unsafe.\n\nYou are not alone. I am assigned to your case and we can work through this together via the in-app chat.\n\nProf. Amina Banda",
                'responded_at'         => now()->subDays(6),
            ],

            // 5 — Anonymous, pending, no assignment yet
            [
                'incident_type'        => 'other',
                'incident_title'       => 'Forced marriage threat from family members',
                'incident_description' => 'My parents and uncle are pressuring me to drop out of school and marry a man I have never met. I am 17 years old. They say if I refuse they will send me away. I am scared and I do not know who else to talk to. I found this app and decided to reach out anonymously because I do not feel safe.',
                'incident_date'        => now()->subDays(1)->toDateString(),
                'incident_location'    => 'Mzimba District — home village',
                'perpetrator_info'     => 'Family members — parents and paternal uncle',
                'is_anonymous'         => true,
                'status'               => 'pending',
                'user_id'              => null,
            ],

            // 6 — Identified, dismissed with explanation
            [
                'incident_type'        => 'verbal',
                'incident_title'       => 'Argument with neighbour — possible misunderstanding',
                'incident_description' => 'My neighbour shouted at me after I accidentally parked in front of her gate. She said some hurtful things. I am not sure if this counts as harassment but I felt it was worth reporting.',
                'incident_date'        => now()->subDays(14)->toDateString(),
                'incident_location'    => 'Lilongwe — Area 43',
                'perpetrator_info'     => 'Female neighbour',
                'is_anonymous'         => false,
                'victim_name'          => 'Mercy Chirwa',
                'victim_email'         => 'mercy.chirwa@example.mw',
                'victim_phone'         => null,
                'status'               => 'dismissed',
                'admin_response'       => "Dear Mercy,\n\nThank you for reaching out. After reviewing your report, this appears to be an isolated neighbour dispute rather than a pattern of harassment.\n\nWe encourage you to speak with your neighbour calmly, or involve a community leader if needed. If the situation escalates or continues, please do not hesitate to submit a new report.\n\nWe are always here for you.",
                'responded_at'         => now()->subDays(12),
                'user_id'              => null,
            ],

        ];

        foreach ($reports as $data) {
            ModelsHarassmentReport::create($data);
        }
    }
}
