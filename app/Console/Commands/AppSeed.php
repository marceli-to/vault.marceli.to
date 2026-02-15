<?php

namespace App\Console\Commands;

use App\Models\Entry;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AppSeed extends Command
{
    protected $signature = 'app:seed';
    protected $description = 'Seed the database with production data';

    public function handle(): void
    {
        $entries = [
            [
                'title' => 'Second Brain System',
                'content' => 'Set up a second brain system: Store all texted ideas and links in memory. Build a searchable UI via prompt for easy retrieval. Stack: Laravel 12 + Vue 3 + Inertia + shadcn-vue + SQLite. Deployed at vault.marceli.to',
                'url' => null,
                'type' => 'idea',
                'tags' => ['vault', 'project', 'productivity'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'kimiyu.ch iOS App',
                'content' => 'Build a native SwiftUI iOS app for kimiyu.ch (expense splitting app). Native SwiftUI frontend, Laravel backend as API. Location: /Users/marceli.to/Jamon.digital/Webroot/kimiyu.ch.app/. Marcelito has minimal SwiftUI experience.',
                'url' => null,
                'type' => 'idea',
                'tags' => ['kimiyu', 'ios', 'swift', 'project'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'Livewire RCE Vulnerability',
                'content' => 'CVE: GHSA-29cq-5w36-x7w3 — RCE via hydration on keller.mmt-ag.ch. Fixed in Livewire v3.6.4 (July 2025). Site was running v3.6.1 (5 months behind). Lesson: Consider Dependabot or composer audit in CI to catch these.',
                'url' => null,
                'type' => 'note',
                'tags' => ['security', 'livewire', 'laravel'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'Moltbook — Social Network for AI Agents',
                'content' => 'Registered as Jarvis90210 on Moltbook. Profile: https://moltbook.com/u/Jarvis90210. Social network specifically for AI agents. Skill docs: https://www.moltbook.com/skill.md',
                'url' => 'https://moltbook.com/u/Jarvis90210',
                'type' => 'link',
                'tags' => ['ai', 'social', 'moltbook'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'AC Stipendium Correction Form',
                'content' => 'Built a post-deadline correction form for acstipendium.ch. Artists enter email, get token link (48h expiry), form pre-filled with existing data. Admin flow: logged-in CP users see dropdown of all applications, edit directly. Key decision: no separate collection — corrections update original entry directly.',
                'url' => null,
                'type' => 'note',
                'tags' => ['client-work', 'statamic', 'laravel'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'Computed Properties in Livewire Blade',
                'content' => 'Computed properties in Blade templates need $this-> prefix (e.g., $this->isSettled). Form Object nullable types: use ?string for optional fields. #[Url(as: "period", history: true)] for clean URL params with browser history.',
                'url' => null,
                'type' => 'note',
                'tags' => ['livewire', 'laravel', 'tip'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'Twitter Account',
                'content' => 'Twitter/X account: @jarvis90210. Method: browser automation (no CLI). Used for developer humor tweets and Moltbook verification.',
                'url' => 'https://x.com/jarvis90210',
                'type' => 'link',
                'tags' => ['social', 'twitter'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:32:56',
            ],
            [
                'title' => 'Alex Finn — OpenClaw tips for noobs and pros',
                'content' => 'Alex Finn (@AlexFinn): "Even if you\'re a total OpenClaw noob, I guarantee there\'s something in here for you." Video thread with OpenClaw tips and tricks.',
                'url' => 'https://x.com/AlexFinn/status/2022481501145927725',
                'type' => 'link',
                'tags' => ['openclaw', 'tips', 'twitter'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 22:52:26',
            ],
            [
                'title' => 'CSS details-content trick — jhey',
                'content' => 'jhey (@jh3yy) sharing a CSS trick: [open]::details-content { height: fit-content; } — neat way to animate details/summary elements with pure CSS.',
                'url' => 'https://x.com/jh3yy/status/1986545100466315475',
                'type' => 'link',
                'tags' => ['css', 'frontend', 'twitter'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 23:26:53',
            ],
            [
                'title' => 'Claude Code + Three.js — CloudAI-X',
                'content' => 'CloudAI-X (@cloudxdev): Claude Code will have the knowledge of how to steer Three.js. Interesting take on AI-assisted 3D development.',
                'url' => 'https://x.com/cloudxdev/status/2013358306392449499',
                'type' => 'link',
                'tags' => ['ai', 'threejs', 'claude', 'twitter'],
                'is_pinned' => false,
                'created_at' => '2026-02-14 23:27:41',
            ],
            [
                'title' => 'Auto Layout in Anime.js v4.3 — Julian Garnier',
                'content' => 'Julian Garnier (@JulianGarnier) introduces Auto Layout in Anime.js v4.3: animate display flex/grid/none, enter/leave animations, DOM position changes, interruptible animations, staggered children, any easing functions, dead simple API.',
                'url' => 'https://x.com/JulianGarnier/status/2013661582417375320',
                'type' => 'link',
                'tags' => ['animation', 'javascript', 'css', 'frontend', 'twitter'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 08:30:41',
            ],
            [
                'title' => 'Claude + Polymarket Trading Bot — frostikk',
                'content' => 'frostikk (@frostikkkk) shares an article on using Claude AI to build a trading bot on Polymarket — from zero coding knowledge to a money-making bot. Covers how newbies are tapping into Claude for prediction market automation.',
                'url' => 'https://x.com/frostikkkk/status/2015154001797390637',
                'type' => 'link',
                'tags' => ['ai', 'claude', 'trading', 'polymarket', 'twitter'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 08:40:36',
            ],
            [
                'title' => 'Polymarket Trading Bot — Feasibility Breakdown',
                'content' => "Prediction market bot using Claude AI + Polymarket API. Tech stack: Python py-clob-client, Polygon/USDC, Claude for news/sentiment analysis.\n\nWhat it does: Monitor markets for mispriced outcomes, AI analyzes news & probabilities, place buy/sell orders via CLOB API.\n\nNeeded: Polymarket account + USDC (\$50-100 to start), Polygon wallet, Python bot + strategy logic.\n\nEdge opportunities: Faster news analysis, better probability estimation on niche markets, arbitrage.\n\nRisks: Real money losses, geo-restrictions (Switzerland), wallet security.\n\nPlan: Start with paper trading bot — monitor markets, virtual trades, track performance. Go live only if consistent edge shows.",
                'url' => null,
                'type' => 'idea',
                'tags' => ['polymarket', 'trading', 'ai', 'claude', 'crypto', 'project'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 08:55:17',
            ],
            [
                'title' => 'TV Series Watchlist',
                'content' => "WATCHED:\n✅ Pluribus\n✅ The Penguin\n✅ Landman\n✅ Adolescence\n✅ The Bear\n✅ Baby Reindeer\n✅ Silo\n✅ True Detective: Night Country\n✅ Ripley\n✅ 3 Body Problem\n✅ The Gentlemen\n✅ Slow Horses\n✅ The Brothers Sun\n✅ Bodies\n✅ Presumed Innocent\n✅ Dark Matter\n✅ Eric\n✅ Sugar\n✅ Beef\n✅ American Primeval\n✅ Griselda\n✅ One Day\n✅ Masters of the Air\n✅ Berlin\n✅ Mr. & Mrs. Smith\n✅ Fallout\n✅ Last Frontier\n\nTO WATCH:\n⬜ House of the Dragon (Season 2)\n⬜ The Boys (Season 4)\n⬜ The Diplomat (Season 2)\n⬜ The Night Agent (Season 2)\n⬜ A Killer Paradox\n⬜ The Continental\n⬜ Avatar: The Last Airbender (Live Action)\n⬜ The Sympathizer\n⬜ Halo (Season 2)\n⬜ The Regime\n⬜ Lawmen: Bass Reeves\n⬜ The Acolyte\n⬜ X-Men '97\n⬜ Tracker\n⬜ Constellation\n⬜ Obliterated\n⬜ Reacher (Season 3)\n⬜ Tulsa King (Season 2)\n⬜ The New Look\n⬜ Criminal Record\n⬜ Knuckles\n⬜ Echo\n⬜ Monsieur Spade\n⬜ Fool Me Once",
                'url' => null,
                'type' => 'note',
                'tags' => ['tv', 'watchlist', 'entertainment'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 10:30:11',
            ],
            [
                'title' => 'ETFs to Explore',
                'content' => "VanEck Semiconductor UCITS \nETF (AIAI)",
                'url' => null,
                'type' => 'note',
                'tags' => ['finance', 'etf', 'investing'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 10:32:05',
            ],
            [
                'title' => 'AI-Ready Business Strategy (Switzerland 2026)',
                'content' => "# AI-Ready Business Strategy (Switzerland 2026)\n\nAuthor: Marcel Stadelmann\nStatus: Early Concept – Parking Lot\nLast updated: 2026-02\n\n---\n\n# Context\n\nWebMCP / AI-operable websites suggest a shift:\n\nFrom:\n- Human browsing websites\n\nTo:\n- AI agents interacting with businesses\n\nOpportunity: Not selling \"WebMCP implementation\"\nBut selling AI-operability for Swiss SMEs\n\n---\n\n# PART 1 — Concrete Offer for Switzerland (2026)\n\n## Positioning\n\n> Wir machen Ihr Unternehmen KI-fähig.\n\nNOT:\n- Protocol integration\n- AI hype\n- Experimental tech\n\nBUT:\n- Sichtbarkeit für KI-Assistenten\n- Automatisierte Anfragen\n- Strukturierte Daten\n- Effizientere Prozesse\n\n---\n\n## The \"AI-Ready Business Package\"\n\n### 1. AI Visibility Layer\n\nDeliverables:\n- Structured service data\n- Machine-readable product data\n- Clean semantic architecture\n- Action endpoints (book, request, contact)\n- Structured knowledge extraction\n\nGoal: AI agents can:\n- Summarize services reliably\n- Extract pricing\n- Initiate structured requests\n- Generate offers\n\n---\n\n### 2. AI Workflow Automation\n\nExamples:\n\n#### For Architects\n- Incoming inquiry → AI extracts:\n  - Location\n  - Project type\n  - Budget\n  - Timeline\n- Generates structured internal summary\n- Suggests response draft\n- Optional CRM integration\n\n#### For Cultural Institutions\n- AI trained on:\n  - Press releases\n  - Exhibitions\n  - Team bios\n- Generates:\n  - Media responses\n  - Grant drafts\n  - Social media content\n\n---\n\n### 3. Internal Knowledge Agent\n\nPrivate AI assistant:\n- Trained on company content\n- Secure\n- No public data exposure\n- Internal productivity boost\n\n---\n\n## Pricing Model (Swiss-Realistic)\n\nSetup: CHF 6'000 – 18'000\nMonthly: CHF 300 – 900\n\nTarget:\n- 5–10 premium clients\n- High trust\n- Long-term retainers\n\n---\n\n# PART 2 — Product Ideas Based on Current Client Base\n\n---\n\n## Product Idea A — AI Intake Engine for Professional Services\n\nTarget:\n- Architects\n- Engineers\n- Agencies\n- Law firms\n\nProblem: Unstructured email inquiries.\nSolution: Structured AI intake portal.\n\n### Core Features\n- Smart dynamic questionnaire\n- AI summarization\n- Risk scoring\n- PDF export\n- CRM push\n- Offer draft generation\n- Multi-language (DE / EN / FR)\n\n### Tech Stack (likely)\n- Laravel\n- Clean UI (Swiss-clean)\n- Optional AI provider abstraction layer\n\nMonetization:\n- White-label\n- SaaS subscription\n- Agency licensing\n\n---\n\n## Product Idea B — Agent-Ready CMS Add-On\n\nTarget: Statamic / Laravel sites\n\n### Features\n- Semantic content blocks\n- Structured machine-readable endpoints\n- AI action registry\n- Knowledge map generator\n- Internal Q&A assistant\n\nPositioning:\n> Make your website AI-operable in one week.\n\nPossible Model:\n- Plugin\n- SaaS backend\n- Agency toolkit\n\n---\n\n## Product Idea C — Swiss AI Middleware (Long-Term)\n\nConcept: Hosted service that:\n- Connects to company website\n- Extracts structured data\n- Builds AI action layer\n- Provides safe API for AI agents\n\nThink: \"Zapier for AI-business interaction.\"\n\nHigh upside. High uncertainty. Long-term exploration.\n\n---\n\n# Strategy Roadmap\n\n## Phase 1 — Consulting First\nSell: AI-readiness audits + workflow automation.\nLearn:\n- What clients actually pay for\n- Where friction happens\n- What repeats across projects\n\n---\n\n## Phase 2 — Modularize\nExtract:\n- Reusable intake system\n- Structured content modules\n- AI workflow components\n\n---\n\n## Phase 3 — Productize\nSpin out:\n- SaaS\n- Licensing\n- Agency tooling\n\n---\n\n# Risks\n- Market too early\n- Clients don't understand value\n- Overbuilding infrastructure before demand\n\nMitigation: Sell outcomes. Not protocols.\n\n---\n\n# Why This Fits Marcel\n\nStrengths:\n- Structured architecture thinking\n- CMS & multi-language experience\n- Swiss SME client base\n- Trust relationships\n- Clean system design mindset\n\nEdge: Most agencies are not thinking about AI-operability yet.\n\n---\n\n# Reality Check (2026)\n\nWill this explode immediately? No.\nWill early adopters pay? Yes.\nYou don't need mass adoption. You need 5–10 strong clients.\n\n---\n\n# Current Status\n\nRight now: Web dev demand is strong. Cashflow is good. No urgency.\n\n---\n\nThis document = strategic seed. Revisit when:\n- Market signals increase\n- Clients ask about AI workflows\n- WebMCP / agent standards mature\n\n---\n\n# Final Thought\n\nThe opportunity is not:\n\"I implement WebMCP.\"\n\nThe opportunity is:\n\"I make your business usable by AI.\"\n\nThat's a positioning shift.",
                'url' => null,
                'type' => 'note',
                'tags' => ['strategy', 'ai', 'business', 'switzerland'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 14:14:42',
            ],
            [
                'title' => 'markdown.new - Markdown for Agents',
                'content' => 'Converts any URL to clean Markdown using Cloudflare native text/markdown content type. 80% fewer tokens than raw HTML. Prepend markdown.new/ to any URL for instant conversion.',
                'url' => 'https://markdown.new/',
                'type' => 'link',
                'tags' => ['ai', 'tools', 'markdown'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 15:59:40',
            ],
            [
                'title' => 'AI Energy / Power Infrastructure — Investment Research',
                'content' => "## Deep Dive: Energy Stocks Benefiting from AI\n\n### Thesis\nEvery new AI model needs exponentially more compute → more data centers → more power. This is already happening — Microsoft, Google, Meta are signing massive power deals.\n\n### Top Picks\n\n**Constellation Energy (CEG) — \$272** ⭐ Best risk/reward\n- Largest US nuclear fleet (31,676 MW)\n- P/E: 31, Analyst target: \$400 (47% upside)\n- Already signing data center power deals\n\n**Cameco (CCJ) — \$113**\n- World largest uranium producer + owns Westinghouse\n- 1Y return: +128%, but near 52W highs\n\n**Vistra (VST) — \$160**\n- 41,000 MW diversified capacity\n- Goldman target: \$205\n\n**Vertiv (VRT) — \$235**\n- Data center cooling/power infrastructure\n- Q4: 37% EPS growth, 23% revenue growth\n\n**NextEra Energy (NEE) — \$94**\n- Safe, 2.4% dividend, largest renewables company\n\n**NuScale Power (SMR) — \$16** 🎲\n- Speculative moonshot, small modular reactors\n\n### ETFs\n- URNM (uranium miners), XLU (utilities), NUKZ (nuclear)\n\n*Research date: Feb 15, 2026. Not financial advice.*",
                'url' => null,
                'type' => 'note',
                'tags' => ['investing', 'energy', 'research', 'stocks', 'ai'],
                'is_pinned' => true,
                'created_at' => '2026-02-15 17:37:36',
            ],
            [
                'title' => 'Investment Plan — AI Energy / Power Infrastructure',
                'content' => "## Action Plan (Feb 2026)\n\nBroker: Swissquote (already active)\n\n### Top Picks\n\n**CEG — Constellation Energy (~\$272)**\n- Largest US nuclear fleet (31,676 MW)\n- P/E: 31, Analyst target: \$400 (47% upside)\n- Already signing data center power deals\n- Earnings: Feb 17, 2026\n\n**CCJ — Cameco (~\$113)**\n- World largest uranium producer + owns Westinghouse\n- 1Y return: +128%\n\n**URNU — Global X Uranium UCITS ETF (IE000NDWFGA5)**\n- TER: 0.65%, Accumulating, Ireland-domiciled\n- Broad uranium miners exposure\n\n**VRT — Vertiv (~\$235)**\n- Data center cooling/power infrastructure\n- Q4: 37% EPS growth, 23% revenue growth\n\n### Starter Portfolio (e.g. CHF 5,000)\n- 40% CEG (conviction pick)\n- 30% URNU ETF (diversified uranium)\n- 20% VRT (data center infra)\n- 10% cash / for dips\n\n### Notes\n- Swissquote fees: ~\$9-15 per US stock trade\n- US stocks: 15% withholding tax on dividends (reclaim via DA-1)\n- Buy in batches, not all at once\n- CEG earnings Feb 17 — wait for results before buying?\n\n### Also Researched\n- VanEck Semiconductor UCITS ETF (IE00BMC38736) — semis play\n- VanEck Rare Earth UCITS ETF (IE0002PG6CA6) — materials play\n- NEE, VST, SMR — other energy picks\n\n*Not financial advice. Research date: Feb 15, 2026.*",
                'url' => null,
                'type' => 'note',
                'tags' => ['investing', 'energy', 'plan', 'swissquote', 'portfolio', 'ai'],
                'is_pinned' => true,
                'created_at' => '2026-02-15 17:46:13',
            ],
            [
                'title' => 'Ken Wheeler tweet',
                'content' => 'Pinned tweet from @kenwheeler',
                'url' => 'https://x.com/kenwheeler/status/2022429445077045452',
                'type' => 'link',
                'tags' => ['twitter', 'pin'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 17:56:32',
            ],
            [
                'title' => 'Silk - Native-like swipeable sheets for the web',
                'content' => 'Advanced swipeable sheet component for React. Unstyled by default, fully customizable animations, mobile-first.',
                'url' => 'https://silkhq.com/',
                'type' => 'link',
                'tags' => ['webdev', 'sheets'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 19:28:27',
            ],
            [
                'title' => 'AI / Robotics ETF',
                'content' => "1. Global X Artificial Intelligence & Technology ETF (AIQ)\n* Assets Under Management (AUM): Approximately \$3.31 billion\n* Expense Ratio: 0.68%\n* 1-Year Return: 14.4%\n* Top Holdings: Tencent Holdings, Alibaba Group, Netflix\n* Overview: AIQ offers broad exposure to companies leveraging AI technologies across various sectors.\n\n2. Global X Robotics & Artificial Intelligence ETF (BOTZ)\n* AUM: Approximately \$2.88 billion\n* Expense Ratio: 0.68%\n* 1-Year Return: -2.4%\n* Top Holdings: Keyence Corp, Nvidia, Intuitive Surgical, ABB Ltd.\n* Overview: BOTZ focuses on companies involved in the development and production of robotics and AI technologies.\n\n3. Defiance Quantum ETF (QTUM)\n* AUM: Approximately \$1.17 billion\n* Expense Ratio: 0.40%\n* 1-Year Return: 28.34%\n* Top Holdings: Alibaba, D-Wave Quantum\n* Overview: QTUM invests in companies at the forefront of quantum computing and machine learning technologies.\n\n4. ARK Autonomous Technology & Robotics ETF (ARKQ)\n* AUM: Approximately \$972 million\n* Expense Ratio: 0.75%\n* 1-Year Return: 28.83%\n* Top Holdings: Tesla, UiPath\n* Overview: ARKQ targets companies involved in autonomous technology and robotics, emphasizing innovation and growth potential.\n\n5. Xtrackers Artificial Intelligence and Big Data ETF (XAIX)\n* Expense Ratio: 0.35%\n* Overview: XAIX offers a cost-effective approach to investing in AI and big data companies, with a forward-looking selection methodology based on patent activity.",
                'url' => null,
                'type' => 'note',
                'tags' => ['investing'],
                'is_pinned' => true,
                'created_at' => '2026-02-15 22:18:03',
            ],
            [
                'title' => 'Design Director Agent — Multi-agent design pipeline system prompt',
                'content' => 'YAML config for an AI Design Director Agent by @kloss_xyz. Defines sub-agents for visual identity, UI/UX, design systems, motion, typography, research, dev. Integrates Figma, Framer, Gemini. Automated pipeline: Ideation → Design → Dev.',
                'url' => 'https://pbs.twimg.com/media/HBObjtgaIAAq1qY?format=jpg&name=4096x4096',
                'type' => 'link',
                'tags' => ['ai-agents', 'system-prompt', 'design', 'prompt-engineering'],
                'is_pinned' => false,
                'created_at' => '2026-02-15 22:30:48',
            ],
        ];

        $tasks = [
            [
                'title' => 'Weberbrunner Team Page',
                'description' => null,
                'status' => 'done',
                'priority' => 'high',
                'due_date' => '2026-02-15',
                'tags' => [],
                'created_at' => '2026-02-15 12:55:35',
            ],
            [
                'title' => 'Weberbrunner Project Page',
                'description' => null,
                'status' => 'open',
                'priority' => 'high',
                'due_date' => '2026-02-19',
                'tags' => [],
                'created_at' => '2026-02-15 12:59:15',
            ],
            [
                'title' => "Weberbrunner 'wrap up the day'",
                'description' => null,
                'status' => 'done',
                'priority' => 'high',
                'due_date' => '2026-02-15',
                'tags' => [],
                'created_at' => '2026-02-15 13:11:03',
            ],
            [
                'title' => 'Check CEG (Constellation Energy) earnings results',
                'description' => 'Check earning results and stock price',
                'status' => 'open',
                'priority' => 'normal',
                'due_date' => '2026-02-17',
                'tags' => [],
                'created_at' => '2026-02-15 17:52:20',
            ],
        ];

        foreach ($entries as $entry) {
            Entry::firstOrCreate(
                ['title' => $entry['title'], 'user_id' => $user->id],
                [...$entry, 'user_id' => $user->id]
            );
        }

        foreach ($tasks as $task) {
            Task::firstOrCreate(
                ['title' => $task['title'], 'user_id' => $user->id],
                [...$task, 'user_id' => $user->id]
            );
        }

        $this->info('Seeded ' . count($entries) . ' entries and ' . count($tasks) . ' tasks.');
    }
}
