<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LandlordUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Hartwell',
                'bio' => 'Software engineer.',
            ],
            [
                'first_name' => 'Ben',
                'last_name' => 'Okafor',
                'bio' => 'Full-stack developer with a focus on Laravel and Vue. Enjoys building APIs.',
            ],
            [
                'first_name' => 'Clara',
                'last_name' => 'Mendes',
                'bio' => 'Product designer turned engineer. Passionate about accessible interfaces, clean typography, and design systems that scale across large product surfaces.',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Strauss',
                'bio' => 'Backend engineer specialising in search infrastructure. Has spent the last four years optimising PostgreSQL full-text search queries, building trigram indexes, and integrating vector similarity search using pgvector for a semantic product discovery platform.',
            ],
            [
                'first_name' => 'Elena',
                'last_name' => 'Voss',
                'bio' => 'DevOps and platform engineer with a decade of experience managing PostgreSQL clusters at scale. Writes extensively about database tuning, shared_preload_libraries configuration, and extension lifecycle management. Strong opinions on immutable infrastructure and GitOps workflows. Outside work, she mentors junior engineers and contributes to several open-source observability tools.',
            ],
            [
                'first_name' => 'François',
                'last_name' => 'Lemaire',
                'bio' => 'Senior engineering manager leading a distributed team of twelve across three time zones. Former principal engineer with deep expertise in text search pipelines — from Elasticsearch to Typesense to native PostgreSQL full-text search using tsvector, tsquery, and the pg_textsearch extension. Advocates for keeping search logic close to the database layer rather than delegating it to external services. Author of several internal technical standards on search relevance tuning, tokenisation strategies, and multilingual stemming. Currently focused on reducing infrastructure complexity while improving search recall across a SaaS product used by thousands of small businesses.',
            ],
            [
                'first_name' => 'Grace',
                'last_name' => 'Nkemdirim',
                'bio' => null,
            ],
            [
                'first_name' => 'Hugo',
                'last_name' => 'Dalton',
                'bio' => 'Freelance consultant.',
            ],
        ];

        foreach ($users as $data) {
            DB::connection('landlord')->table('users')->insertOrIgnore([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => Str::lower($data['first_name'].'.'.$data['last_name'].'@example.com'),
                'handle' => Str::slug($data['first_name'].'_'.$data['last_name'], '_'),
                'bio' => $data['bio'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
