<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'subtitle'    => 'Quran Education',
                'title'       => 'MOST IMPORTANT FOR MANKIND',
                'description' => 'than any education in the world',
                'button_text' => 'Start Learning',
                'button_url'  => '/courses',
                'image'       => 'img/hero.jpg',
                'order'       => 1,
                'is_active'   => true,
            ],
            [
                'subtitle'    => 'Avoid Zinnah',
                'title'       => 'Prefer Marriage Between Ummah',
                'description' => 'Big negligency in Muslims Society',
                'button_text' => 'Find Match',
                'button_url'  => '/nikah/create',
                'image'       => 'img/hero1.jpg',
                'order'       => 2,
                'is_active'   => true,
            ],
            [
                'subtitle'    => 'Parental Coaching',
                'title'       => 'SAVE YOUR FAMILY GET COACHING',
                'description' => 'before any big trouble in life',
                'button_text' => 'Get Enrolled',
                'button_url'  => '#',
                'image'       => 'img/hero2.jpg',
                'order'       => 3,
                'is_active'   => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(['title' => $banner['title']], $banner);
        }
    }
}
