<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\PaymentMethod;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed Payment Methods
        $paymentMethods = [
            [
                'name' => 'BRI',
                'type' => 'bank',
                'account_number' => '1234-5678-9012-3456',
                'account_name' => 'PT. Event Management',
                'is_active' => true
            ],
            [
                'name' => 'BCA',
                'type' => 'bank',
                'account_number' => '9876-5432-1098-7654',
                'account_name' => 'PT. Event Management',
                'is_active' => true
            ],
            [
                'name' => 'Mandiri',
                'type' => 'bank',
                'account_number' => '5678-9012-3456-7890',
                'account_name' => 'PT. Event Management',
                'is_active' => true
            ],
            [
                'name' => 'OVO',
                'type' => 'ewallet',
                'account_number' => '0812-3456-7890',
                'account_name' => 'PT. Event Management',
                'is_active' => true
            ],
            [
                'name' => 'DANA',
                'type' => 'ewallet',
                'account_number' => '0813-4567-8901',
                'account_name' => 'PT. Event Management',
                'is_active' => true
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }

        // Seed Events
        $events = [
            [
                'event_code' => 'bpevent-' . Str::random(8),
                'name' => 'Workshop Digital Marketing',
                'date' => now()->addDays(15)->format('Y-m-d'),
                'price' => 0,
                'location' => 'Online',
                'type' => 'online',
                'description' => 'Pelajari strategi digital marketing terbaru untuk mengembangkan bisnis Anda dengan pemateri berpengalaman.',
                'is_active' => true,
                'available_slots' => 100,
                'registered_count' => 45
            ],
            [
                'event_code' => 'bpevent-' . Str::random(8),
                'name' => 'Tech Conference 2023',
                'date' => now()->addDays(20)->format('Y-m-d'),
                'price' => 500000,
                'location' => 'Jakarta Convention Center',
                'type' => 'offline',
                'description' => 'Konferensi teknologi terbesar dengan pembicara dari perusahaan teknologi ternama.',
                'is_active' => true,
                'available_slots' => 200,
                'registered_count' => 120
            ],
            [
                'event_code' => 'bpevent-' . Str::random(8),
                'name' => 'Web Development Bootcamp',
                'date' => now()->addDays(30)->format('Y-m-d'),
                'price' => 1200000,
                'location' => 'Online',
                'type' => 'online',
                'description' => 'Pelajari full-stack web development dalam 3 minggu intensif dengan mentor berpengalaman.',
                'is_active' => true,
                'available_slots' => 50,
                'registered_count' => 25
            ],
            [
                'event_code' => 'bpevent-' . Str::random(8),
                'name' => 'Data Science Workshop',
                'date' => now()->addDays(25)->format('Y-m-d'),
                'price' => 750000,
                'location' => 'Bandung Tech Park',
                'type' => 'offline',
                'description' => 'Workshop intensif tentang data science dan machine learning untuk pemula hingga menengah.',
                'is_active' => true,
                'available_slots' => 80,
                'registered_count' => 35
            ],
            [
                'event_code' => 'bpevent-' . Str::random(8),
                'name' => 'UI/UX Design Summit',
                'date' => now()->addDays(35)->format('Y-m-d'),
                'price' => 0,
                'location' => 'Online & Onsite',
                'type' => 'hybrid',
                'description' => 'Summit tentang tren terbaru dalam desain UI/UX dengan praktisi dari perusahaan ternama.',
                'is_active' => true,
                'available_slots' => 150,
                'registered_count' => 90
            ],
            [
                'event_code' => 'bpevent-' . Str::random(8),
                'name' => 'Startup Networking Night',
                'date' => now()->addDays(40)->format('Y-m-d'),
                'price' => 300000,
                'location' => 'Kuningan City, Jakarta',
                'type' => 'offline',
                'description' => 'Networking event untuk startup founder, investor, dan profesional di ekosistem startup.',
                'is_active' => true,
                'available_slots' => 120,
                'registered_count' => 60
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }

        // Create some sample participants
        $participantEvents = Event::take(3)->get();
        
        foreach ($participantEvents as $event) {
            for ($i = 1; $i <= 5; $i++) {
                $event->participants()->create([
                    'transaction_code' => $event->event_code . '-' . Str::random(8),
                    'full_name' => 'Participant ' . $i . ' for ' . $event->name,
                    'email' => 'participant' . $i . '@example.com',
                    'phone' => '0812' . rand(1000000, 9999999),
                    'gender' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                    'nik' => rand(1000000000000000, 9999999999999999),
                    'address' => 'Jl. Contoh No.' . $i . ', Jakarta',
                    'payment_method' => ['BRI', 'BCA', 'Mandiri', 'OVO'][rand(0, 3)],
                    'payment_status' => ['pending', 'paid', 'verified'][rand(0, 2)],
                    'wa_notification_sent' => rand(0, 1),
                    'email_notification_sent' => rand(0, 1)
                ]);
            }
        }
    }
}