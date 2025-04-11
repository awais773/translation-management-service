<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Console\Command;

class GenerateTestTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:generate 
                            {count=1000 : Number of translations to generate} 
                            {--tags= : Comma-separated list of tag names to attach}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test translations for performance testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $tagNames = $this->option('tags') ? explode(',', $this->option('tags')) : [];
        
        // Get or create locales if they don't exist
        $locales = Locale::firstOrCreate(['code' => 'en'], ['name' => 'English']);
        $locales = Locale::firstOrCreate(['code' => 'fr'], ['name' => 'French']);
        $locales = Locale::all();
        
        // Get or create tags
        $tags = collect($tagNames)->map(function ($name) {
            return Tag::firstOrCreate(['name' => trim($name)]);
        });
        
        $this->info("Generating {$count} translations...");
        
        $bar = $this->output->createProgressBar($count);
        
        for ($i = 0; $i < $count; $i++) {
            $translation = Translation::create([
                'group' => $this->getRandomGroup(),
                'key' => 'key_' . uniqid(),
                'value' => 'Value for ' . uniqid(),
                'locale_id' => $locales->random()->id,
            ]);
            
            if ($tags->isNotEmpty()) {
                $translation->tags()->attach(
                    $tags->random(rand(1, $tags->count()))->pluck('id')->toArray()
                );
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\n{$count} translations generated successfully.");
        
        return Command::SUCCESS;
    }
    
    protected function getRandomGroup()
    {
        $groups = ['auth', 'validation', 'ui', 'messages', 'emails', 'notifications'];
        return $groups[array_rand($groups)];
    }
}