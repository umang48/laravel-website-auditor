<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use App\Models\Website;
use App\Models\Audit;
use App\Models\AuditPage;
use App\Models\AuditIssue;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // Create a specific admin user for yourself so you have known login credentials
        $admin = User::factory()->create([
            'name' => 'Umang Prajapati',
            'email' => 'admin@phptutorialpoints.in',
            'password' => bcrypt('password'), // default password for testing
        ]);

        // Generate 2 Random Users, each with 3 Websites, 2 Audits per Website, 
        // 5 Pages per Audit, and 3 Issues per Page
        User::factory(2)
            ->has(
                Website::factory(3)->has(
                    Audit::factory(2)->has(
                        AuditPage::factory(5)->has(
                            // We need to explicitly pass the audit_id to the issue
                            // so it matches the audit of the page it belongs to
                            AuditIssue::factory(3)->state(function (array $attributes, AuditPage $page) {
                                return ['audit_id' => $page->audit_id];
                            }), 'issues'
                        ), 'pages'
                    )
                )
            )->create();
    }
}
