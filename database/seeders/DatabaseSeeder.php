<?php

namespace Database\Seeders;

use App\Models\Station;
use App\Models\TimeSlotTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@billboard.test'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('password'),
                'role'     => 'admin',
            ]
        );

        // Demo advertiser
        User::firstOrCreate(
            ['email' => 'advertiser@billboard.test'],
            [
                'name'    => 'Demo Advertiser',
                'company' => 'Demo Co.',
                'password' => bcrypt('password'),
                'role'    => 'advertiser',
            ]
        );

        // Demo stations
        $stations = [
            [
                'name'          => 'CBD Main Screen',
                'location_name' => 'Corner First & Main Street, CBD',
                'lat'           => -17.8292,
                'lng'           => 31.0522,
                'description'   => 'High-traffic screen at the city centre intersection. Visible to foot and vehicle traffic.',
                'screen_size'   => '65 inch',
                'screen_width'  => 3840,
                'screen_height' => 2160,
                'status'        => 'active',
            ],
            [
                'name'          => 'Shopping Mall Entrance',
                'location_name' => 'Eastgate Mall Entrance, Harare',
                'lat'           => -17.8219,
                'lng'           => 31.0769,
                'description'   => 'Premium placement at the main mall entrance. High dwell time audience.',
                'screen_size'   => '75 inch',
                'screen_width'  => 3840,
                'screen_height' => 2160,
                'status'        => 'active',
            ],
            [
                'name'          => 'Airport Arrivals Lounge',
                'location_name' => 'Robert Gabriel Mugabe International Airport',
                'lat'           => -17.9318,
                'lng'           => 31.0928,
                'description'   => 'Target business travellers and visitors at the airport arrivals area.',
                'screen_size'   => '55 inch',
                'screen_width'  => 1920,
                'screen_height' => 1080,
                'status'        => 'active',
            ],
        ];

        foreach ($stations as $stationData) {
            $station = Station::firstOrCreate(
                ['name' => $stationData['name']],
                array_merge($stationData, ['device_token' => Str::random(64)])
            );

            // Create time slot templates for each day
            $this->createTimeSlots($station);
        }
    }

    private function createTimeSlots(Station $station): void
    {
        if ($station->timeSlotTemplates()->exists()) {
            return;
        }

        $slots = [
            ['start_time' => '06:00:00', 'end_time' => '06:30:00', 'price' => 15.00],
            ['start_time' => '07:00:00', 'end_time' => '07:30:00', 'price' => 25.00],
            ['start_time' => '08:00:00', 'end_time' => '08:30:00', 'price' => 35.00],
            ['start_time' => '12:00:00', 'end_time' => '12:30:00', 'price' => 30.00],
            ['start_time' => '17:00:00', 'end_time' => '17:30:00', 'price' => 40.00],
            ['start_time' => '18:00:00', 'end_time' => '18:30:00', 'price' => 45.00],
            ['start_time' => '20:00:00', 'end_time' => '20:30:00', 'price' => 35.00],
        ];

        foreach (range(0, 6) as $dayOfWeek) {
            $dayMultiplier = in_array($dayOfWeek, [0, 6]) ? 1.5 : 1.0; // Weekend premium
            foreach ($slots as $slot) {
                TimeSlotTemplate::create([
                    'station_id'       => $station->id,
                    'day_of_week'      => $dayOfWeek,
                    'start_time'       => $slot['start_time'],
                    'end_time'         => $slot['end_time'],
                    'duration_seconds' => 1800,
                    'price'            => $slot['price'] * $dayMultiplier,
                    'is_active'        => true,
                ]);
            }
        }
    }
}
