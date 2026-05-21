<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Criterion;
use App\Models\Tourism;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Add Admin User
        User::factory()->create([
            'name' => 'Admin Ranca Upas',
            'email' => 'admin@rancaupas.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Add Dummy Pengunjung User
        User::factory()->create([
            'name' => 'Pengunjung Setia',
            'email' => 'user@rancaupas.com',
            'password' => bcrypt('password'),
            'role' => 'pengunjung',
        ]);

        // Seed Criteria for SAW
        $criteria = [
            [
                'name' => 'Anggaran (Cost)',
                'code' => 'C1',
                'type' => 'cost',
                'weight' => 0.3,
            ],
            [
                'name' => 'Fasilitas (Benefit)',
                'code' => 'C2',
                'type' => 'benefit',
                'weight' => 0.3,
            ],
            [
                'name' => 'Jarak / Aksesibilitas (Cost)',
                'code' => 'C3',
                'type' => 'cost',
                'weight' => 0.2, // 20%
            ],
            [
                'name' => 'Keseruan (Benefit)',
                'code' => 'C4',
                'type' => 'benefit',
                'weight' => 0.2, // 20%
            ],
        ];

        foreach ($criteria as $c) {
            Criterion::create($c);
        }

        // Seed Tourism Data based on standard Ranca Upas pricing
        $tourisms = [
            [
                'name' => 'Kunjungan Wisata Alam (Tiket Masuk)',
                'description' => 'Area utama menikmati pemandangan alam, penangkaran rusa, dan spot foto alami.',
                'price_wni' => 28000,
                'price_wna' => 45000,
                'category' => 'Alam',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15837.288597371192!2d107.3879949!3d-7.145453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e688b1b2f763955%3A0xe675ca33bcbc50cb!2sKampung%20Cai%20Ranca%20Upas!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid',
                'facilities_list' => 'Penangkaran Rusa, Toilet, Mushola, Area Parkir',
                'image' => 'https://images.unsplash.com/photo-1517409207122-6b3a32dc8fa4?auto=format&fit=crop&q=80',
                'score_anggaran' => 5,
                'score_fasilitas' => 3,
                'score_jarak' => 1,
                'score_keseruan' => 3,
            ],
            [
                'name' => 'Igloo Camp',
                'description' => 'Sensasi menginap di tenda berbentuk igloo di dekat danau eksotis dan private camp area.',
                'price_wni' => 850000,
                'price_wna' => 850000,
                'category' => 'Alam',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15837.288597371192!2d107.3879949!3d-7.145453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e688b1b2f763955%3A0xe675ca33bcbc50cb!2sKampung%20Cai%20Ranca%20Upas!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid',
                'facilities_list' => 'Kasur Busa, Selimut Tambahan, Kamar Mandi Air Panas, Sarapan Pagi',
                'image' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?auto=format&fit=crop&q=80',
                'score_anggaran' => 5,
                'score_fasilitas' => 5,
                'score_jarak' => 3,
                'score_keseruan' => 4,
            ],
            [
                'name' => 'ATV Ride',
                'description' => 'Memicu adrenalin dengan mengendarai ATV menjelajahi hutan Ranca Upas.',
                'price_wni' => 150000,
                'price_wna' => 200000,
                'category' => 'Buatan',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15837.288597371192!2d107.3879949!3d-7.145453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e688b1b2f763955%3A0xe675ca33bcbc50cb!2sKampung%20Cai%20Ranca%20Upas!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid',
                'facilities_list' => 'Helm, Pendamping, Track Hutan',
                'image' => 'https://images.unsplash.com/photo-1549405626-d66a68fdfb2a?auto=format&fit=crop&q=80',
                'score_anggaran' => 4,
                'score_fasilitas' => 2,
                'score_jarak' => 2,
                'score_keseruan' => 5,
            ],
            [
                'name' => 'Onsen Waterpark (Kolam Air Panas)',
                'description' => 'Kolam rendam air panas alami di tengah udara sejuk pegunungan.',
                'price_wni' => 35000,
                'price_wna' => 50000,
                'category' => 'Alam',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15837.288597371192!2d107.3879949!3d-7.145453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e688b1b2f763955%3A0xe675ca33bcbc50cb!2sKampung%20Cai%20Ranca%20Upas!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid',
                'facilities_list' => 'Kolam Rendam Beragam Suhu, Kamar Bilas, Loker',
                'image' => 'https://images.unsplash.com/photo-1582236113824-2c2621415d8f?auto=format&fit=crop&q=80',
                'score_anggaran' => 2,
                'score_fasilitas' => 4,
                'score_jarak' => 2,
                'score_keseruan' => 4,
            ],
            [
                'name' => 'Camping Ground (Bawa Tenda Sendiri)',
                'description' => 'Area luas untuk mendirikan tenda dan menikmati malam bertabur bintang.',
                'price_wni' => 15000,
                'price_wna' => 25000,
                'category' => 'Alam',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15837.288597371192!2d107.3879949!3d-7.145453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e688b1b2f763955%3A0xe675ca33bcbc50cb!2sKampung%20Cai%20Ranca%20Upas!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid',
                'facilities_list' => 'Lahan Kemah, Toilet Umum, Tempat Cuci Piring, Keamanan 24 Jam',
                'image' => 'https://images.unsplash.com/photo-1504280336637-975dc1bfe74b?auto=format&fit=crop&q=80',
                'score_anggaran' => 1, // Sangat Murah (jika bawa sendiri)
                'score_fasilitas' => 2, // Fasilitas dasar
                'score_jarak' => 3, // Pilihan blok camp bervariasi
                'score_keseruan' => 4, // Aktivitas alami
            ],
        ];

        foreach ($tourisms as $t) {
            Tourism::create($t);
        }
    }
}
