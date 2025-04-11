<?php

namespace Database\Factories;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        return [
            'group' => $this->faker->randomElement(['auth', 'validation', 'ui', 'messages']),
            'key' => 'key_' . $this->faker->unique()->uuid,
            'value' => $this->faker->sentence,
            'locale_id' => Locale::inRandomOrder()->first()->id,
        ];
    }
}