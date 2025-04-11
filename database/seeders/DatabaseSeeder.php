<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create test user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create locales
        $locales = Locale::factory()->createMany([
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
        ]);

        // Create tags
        $tags = Tag::factory()->createMany([
            ['name' => 'web'],
            ['name' => 'mobile'],
            ['name' => 'desktop'],
        ]);

        // Create translations
        Translation::factory()->count(100000)->create()->each(function ($translation) use ($tags) {
            $translation->tags()->attach(
                $tags->random(rand(0, 3))->pluck('id')->toArray()
            );
        });
    }
}