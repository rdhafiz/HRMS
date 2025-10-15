<?php

namespace Database\Seeders;

use App\Models\TrainingAndPolicy;
use App\Models\TrainingPolicyCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParentCategoryTrainingPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get parent categories
        $technicalSkills = TrainingPolicyCategory::where('title', 'Technical Skills')->first();
        $softSkills = TrainingPolicyCategory::where('title', 'Soft Skills')->first();
        $compliance = TrainingPolicyCategory::where('title', 'Compliance & Safety')->first();
        $hrPolicies = TrainingPolicyCategory::where('title', 'HR Policies')->first();
        $itPolicies = TrainingPolicyCategory::where('title', 'IT Policies')->first();
        $operationalPolicies = TrainingPolicyCategory::where('title', 'Operational Policies')->first();

        $data = [
            // Parent Category Trainings and Policies
            [
                'title' => 'General Technical Skills Assessment',
                'type' => 'training',
                'description' => 'Comprehensive assessment of basic technical skills required for all employees.',
                'category_id' => $technicalSkills ? $technicalSkills->id : null,
            ],
            [
                'title' => 'Core Communication Skills',
                'type' => 'training',
                'description' => 'Essential communication skills training for all team members.',
                'category_id' => $softSkills ? $softSkills->id : null,
            ],
            [
                'title' => 'General Safety Awareness',
                'type' => 'training',
                'description' => 'Basic safety awareness training for all employees.',
                'category_id' => $compliance ? $compliance->id : null,
            ],
            [
                'title' => 'Employee Handbook',
                'type' => 'policy',
                'description' => 'Comprehensive employee handbook covering all HR policies and procedures.',
                'category_id' => $hrPolicies ? $hrPolicies->id : null,
            ],
            [
                'title' => 'IT Acceptable Use Policy',
                'type' => 'policy',
                'description' => 'General IT acceptable use policy for all employees.',
                'category_id' => $itPolicies ? $itPolicies->id : null,
            ],
            [
                'title' => 'General Workplace Guidelines',
                'type' => 'policy',
                'description' => 'Basic workplace guidelines and expectations for all employees.',
                'category_id' => $operationalPolicies ? $operationalPolicies->id : null,
            ],
        ];

        foreach ($data as $item) {
            if ($item['category_id']) {
                TrainingAndPolicy::create($item);
            }
        }
    }
}