<?php

namespace Database\Seeders;

use App\Models\TrainingPolicyCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingPolicyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Training Categories
        $technicalSkills = TrainingPolicyCategory::create([
            'title' => 'Technical Skills',
            'type' => 'training',
        ]);

        $softwareTools = TrainingPolicyCategory::create([
            'parent_id' => $technicalSkills->id,
            'title' => 'Software Tools',
            'type' => 'training',
        ]);

        $programmingLanguages = TrainingPolicyCategory::create([
            'parent_id' => $technicalSkills->id,
            'title' => 'Programming Languages',
            'type' => 'training',
        ]);

        $softSkills = TrainingPolicyCategory::create([
            'title' => 'Soft Skills',
            'type' => 'training',
        ]);

        $communication = TrainingPolicyCategory::create([
            'parent_id' => $softSkills->id,
            'title' => 'Communication',
            'type' => 'training',
        ]);

        $leadership = TrainingPolicyCategory::create([
            'parent_id' => $softSkills->id,
            'title' => 'Leadership',
            'type' => 'training',
        ]);

        $compliance = TrainingPolicyCategory::create([
            'title' => 'Compliance & Safety',
            'type' => 'training',
        ]);

        $workplaceSafety = TrainingPolicyCategory::create([
            'parent_id' => $compliance->id,
            'title' => 'Workplace Safety',
            'type' => 'training',
        ]);

        $dataProtection = TrainingPolicyCategory::create([
            'parent_id' => $compliance->id,
            'title' => 'Data Protection',
            'type' => 'training',
        ]);

        // Policy Categories
        $hrPolicies = TrainingPolicyCategory::create([
            'title' => 'HR Policies',
            'type' => 'policy',
        ]);

        $leavePolicy = TrainingPolicyCategory::create([
            'parent_id' => $hrPolicies->id,
            'title' => 'Leave Policy',
            'type' => 'policy',
        ]);

        $recruitmentPolicy = TrainingPolicyCategory::create([
            'parent_id' => $hrPolicies->id,
            'title' => 'Recruitment Policy',
            'type' => 'policy',
        ]);

        $performancePolicy = TrainingPolicyCategory::create([
            'parent_id' => $hrPolicies->id,
            'title' => 'Performance Management',
            'type' => 'policy',
        ]);

        $itPolicies = TrainingPolicyCategory::create([
            'title' => 'IT Policies',
            'type' => 'policy',
        ]);

        $securityPolicy = TrainingPolicyCategory::create([
            'parent_id' => $itPolicies->id,
            'title' => 'Security Policy',
            'type' => 'policy',
        ]);

        $dataPolicy = TrainingPolicyCategory::create([
            'parent_id' => $itPolicies->id,
            'title' => 'Data Management Policy',
            'type' => 'policy',
        ]);

        $operationalPolicies = TrainingPolicyCategory::create([
            'title' => 'Operational Policies',
            'type' => 'policy',
        ]);

        $workplacePolicy = TrainingPolicyCategory::create([
            'parent_id' => $operationalPolicies->id,
            'title' => 'Workplace Policy',
            'type' => 'policy',
        ]);

        $remoteWorkPolicy = TrainingPolicyCategory::create([
            'parent_id' => $operationalPolicies->id,
            'title' => 'Remote Work Policy',
            'type' => 'policy',
        ]);

        $codeOfConduct = TrainingPolicyCategory::create([
            'parent_id' => $operationalPolicies->id,
            'title' => 'Code of Conduct',
            'type' => 'policy',
        ]);
    }
}
