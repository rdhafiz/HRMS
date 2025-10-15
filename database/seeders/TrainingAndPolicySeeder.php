<?php

namespace Database\Seeders;

use App\Models\TrainingAndPolicy;
use App\Models\TrainingPolicyCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingAndPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories for assignment
        $technicalSkills = TrainingPolicyCategory::where('title', 'Technical Skills')->first();
        $softSkills = TrainingPolicyCategory::where('title', 'Soft Skills')->first();
        $compliance = TrainingPolicyCategory::where('title', 'Compliance & Safety')->first();
        $hrPolicies = TrainingPolicyCategory::where('title', 'HR Policies')->first();
        $itPolicies = TrainingPolicyCategory::where('title', 'IT Policies')->first();
        $operationalPolicies = TrainingPolicyCategory::where('title', 'Operational Policies')->first();

        $data = [
            // Trainings
            [
                'title' => 'Workplace Safety Basics',
                'type' => 'training',
                'description' => 'Comprehensive training on workplace safety protocols, emergency procedures, and hazard identification.',
                'category_id' => $compliance ? $compliance->id : null,
            ],
            [
                'title' => 'Data Protection and Privacy Training',
                'type' => 'training',
                'description' => 'Training on GDPR compliance, data handling procedures, and privacy protection measures.',
                'category_id' => $compliance ? $compliance->id : null,
            ],
            [
                'title' => 'Customer Service Excellence',
                'type' => 'training',
                'description' => 'Advanced customer service techniques, communication skills, and conflict resolution.',
                'category_id' => $softSkills ? $softSkills->id : null,
            ],
            [
                'title' => 'IT Security Awareness',
                'type' => 'training',
                'description' => 'Cybersecurity best practices, password management, and phishing prevention.',
                'category_id' => $technicalSkills ? $technicalSkills->id : null,
            ],
            [
                'title' => 'Leadership Development Program',
                'type' => 'training',
                'description' => 'Management skills, team building, and strategic thinking for emerging leaders.',
                'category_id' => $softSkills ? $softSkills->id : null,
            ],
            
            // Policies
            [
                'title' => 'Data Protection Policy',
                'type' => 'policy',
                'description' => 'Comprehensive policy outlining data collection, processing, storage, and protection procedures.',
                'category_id' => $itPolicies ? $itPolicies->id : null,
            ],
            [
                'title' => 'Anti-Discrimination Policy',
                'type' => 'policy',
                'description' => 'Policy prohibiting discrimination based on race, gender, age, religion, or other protected characteristics.',
                'category_id' => $hrPolicies ? $hrPolicies->id : null,
            ],
            [
                'title' => 'Remote Work Policy',
                'type' => 'policy',
                'description' => 'Guidelines and procedures for remote work arrangements, expectations, and equipment requirements.',
                'category_id' => $operationalPolicies ? $operationalPolicies->id : null,
            ],
            [
                'title' => 'Code of Conduct',
                'type' => 'policy',
                'description' => 'Expected standards of behavior, ethical guidelines, and professional conduct requirements.',
                'category_id' => $operationalPolicies ? $operationalPolicies->id : null,
            ],
            [
                'title' => 'Health and Safety Policy',
                'type' => 'policy',
                'description' => 'Comprehensive health and safety guidelines, emergency procedures, and reporting requirements.',
                'category_id' => $operationalPolicies ? $operationalPolicies->id : null,
            ],
        ];

        foreach ($data as $item) {
            TrainingAndPolicy::create($item);
        }
    }
}
