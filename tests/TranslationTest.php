<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('api-token')->plainTextToken;

        Locale::factory()->create(['code' => 'en']);
        Tag::factory()->create(['name' => 'web']);
    }

    public function test_can_create_translation()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/translations', [
            'group' => 'auth',
            'key' => 'welcome',
            'value' => 'Welcome to our application',
            'locale_id' => 1,
            'tags' => [1]
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'group', 'key', 'value', 'locale_id']);
    }

    public function test_can_list_translations()
    {
        Translation::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_filter_translations_by_tag()
    {
        $tag = Tag::factory()->create(['name' => 'mobile']);
        $translation = Translation::factory()->create();
        $translation->tags()->attach($tag->id);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations?tags[]=mobile');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_export_translations()
    {
        Translation::factory()->create([
            'group' => 'auth',
            'key' => 'welcome',
            'value' => 'Welcome',
            'locale_id' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/translations/export');

        $response->assertStatus(200)
            ->assertJson([
                'en' => [
                    'auth' => [
                        'welcome' => 'Welcome'
                    ]
                ]
            ]);
    }
}