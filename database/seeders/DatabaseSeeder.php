<?php

namespace Database\Seeders;

use App\Models\Entry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Marceli',
            'email' => 'm@marceli.to',
            'password' => Hash::make('vault2024'),
        ]);

        $entries = [
            ['title' => 'Build a CLI dashboard', 'content' => 'Create a terminal-based dashboard using blessed or ink. Could show system stats, calendar, and todos in one view. Think htop meets notion.', 'type' => 'idea', 'tags' => ['cli', 'tooling', 'side-project']],
            ['title' => null, 'content' => 'https://linear.app', 'url' => 'https://linear.app', 'type' => 'link', 'tags' => ['productivity', 'design-inspo']],
            ['title' => 'Meeting notes — Product sync', 'content' => "Discussed Q1 roadmap. Key takeaways:\n- Focus on API-first approach\n- New auth system needed by March\n- Design system v2 kicks off next week", 'type' => 'note', 'tags' => ['work', 'meetings']],
            ['title' => null, 'content' => '"The best way to predict the future is to invent it." — Alan Kay', 'type' => 'quote', 'tags' => ['inspiration']],
            ['title' => 'Tailwind CSS v4 migration guide', 'content' => 'Key changes in Tailwind v4: new engine, CSS-first config, native cascade layers. Need to migrate existing projects.', 'url' => 'https://tailwindcss.com/docs/upgrade-guide', 'type' => 'reference', 'tags' => ['css', 'tailwind', 'frontend']],
            ['title' => 'Voice-controlled home automation', 'content' => 'What if Jarvis could control smart home devices? Start with lights and music. Use HomeKit API or Home Assistant REST API.', 'type' => 'idea', 'tags' => ['jarvis', 'automation', 'iot'], 'is_pinned' => true],
            ['title' => null, 'content' => 'https://ui.shadcn.com', 'url' => 'https://ui.shadcn.com', 'type' => 'link', 'tags' => ['ui', 'components', 'vue']],
            ['title' => 'Debugging Laravel queue workers', 'content' => "When queue workers silently fail:\n1. Check `failed_jobs` table\n2. Run `php artisan queue:listen` for real-time output\n3. Add `tries` and `backoff` to job class\n4. Use Horizon for monitoring in production", 'type' => 'note', 'tags' => ['laravel', 'debugging', 'backend']],
            ['title' => null, 'content' => '"Simplicity is the ultimate sophistication." — Leonardo da Vinci', 'type' => 'quote', 'tags' => ['design', 'philosophy']],
            ['title' => 'SQLite performance tips', 'content' => "For better SQLite performance in Laravel:\n- Use WAL mode: `PRAGMA journal_mode=WAL`\n- Add proper indexes\n- Use `DB::transaction()` for bulk inserts\n- Consider `busy_timeout` for concurrent access", 'type' => 'reference', 'tags' => ['sqlite', 'performance', 'laravel'], 'is_pinned' => true],
        ];

        foreach ($entries as $i => $entry) {
            Entry::create(array_merge($entry, [
                'user_id' => $user->id,
                'created_at' => now()->subHours(rand(1, 720)),
            ]));
        }
    }
}
