<?php

namespace Database\Seeders;

use App\Models\SafetyGuide;
use Illuminate\Database\Seeder;

class SafetyGuideSeeder extends Seeder
{
    public function run(): void
    {
        $guides = [
            [
                'title' => 'Drop, Cover, and Hold On',
                'content' => 'DROP to your hands and knees. COVER your head and neck under a sturdy table or desk. HOLD ON to your shelter until the shaking stops. If no shelter is nearby, cover your head and neck with your arms.',
                'category' => 'during',
                'order' => 1,
            ],
            [
                'title' => 'Stay Indoors',
                'content' => 'Do not run outside during shaking. Most injuries occur when people try to move to a different location or are hit by falling objects. Stay where you are until the shaking stops.',
                'category' => 'during',
                'order' => 2,
            ],
            [
                'title' => 'Prepare an Emergency Kit',
                'content' => 'Keep a supply of water (1 gallon per person per day for 3 days), non-perishable food, flashlight, battery-powered radio, first aid kit, medications, and important documents in a waterproof container.',
                'category' => 'before',
                'order' => 1,
            ],
            [
                'title' => 'Secure Heavy Items',
                'content' => 'Anchor bookcases, refrigerators, water heaters, and other heavy furniture to wall studs. Store heavy items on lower shelves. Secure hanging items like mirrors and picture frames.',
                'category' => 'before',
                'order' => 2,
            ],
            [
                'title' => 'Check for Injuries',
                'content' => 'After an earthquake, check yourself and others for injuries. Provide first aid for minor injuries. Get medical help for serious injuries. Do not move seriously injured persons unless they are in immediate danger.',
                'category' => 'after',
                'order' => 1,
            ],
            [
                'title' => 'Inspect Your Home',
                'content' => 'Check for structural damage, gas leaks, and electrical damage. If you smell gas or suspect a leak, turn off the main gas valve and leave immediately. Do not use matches, lighters, or electrical switches.',
                'category' => 'after',
                'order' => 2,
            ],
            [
                'title' => 'Be Prepared for Aftershocks',
                'content' => 'Aftershocks can occur minutes, days, or even months after the main earthquake. They are usually less intense but can cause additional damage to weakened structures. Drop, Cover, and Hold On during aftershocks.',
                'category' => 'after',
                'order' => 3,
            ],
            [
                'title' => 'Create a Family Emergency Plan',
                'content' => 'Discuss with your family what to do during an earthquake. Choose a safe place in each room. Plan where to meet if separated. Keep emergency contact numbers handy. Practice earthquake drills regularly.',
                'category' => 'before',
                'order' => 3,
            ],
        ];

        foreach ($guides as $guide) {
            SafetyGuide::create($guide);
        }
    }
}
