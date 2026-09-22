<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => 'Architectural Design',
                'description' => 'Comprehensive residential & commercial architectural design, 3D modeling, floor plans, construction documentation, and custom blueprints.',
                'icon' => '🏛️',
                'sort_order' => 1,
                'subcategories' => [
                    ['name' => 'Custom Home Design', 'icon' => '🏡', 'description' => 'Bespoke residential architectural concepts crafted to your lifestyle.'],
                    ['name' => 'Floor Plans & 3D Renderings', 'icon' => '📐', 'description' => 'Photorealistic visualisations, elevations, and structural schematics.'],
                    ['name' => 'Commercial Building Design', 'icon' => '🏢', 'description' => 'Architectural planning for retail, hospitality, and office spaces.'],
                    ['name' => 'Construction Documentation', 'icon' => '📄', 'description' => 'Detailed permit drawings, structural engineering, and MEP coordination.'],
                ],
            ],
            [
                'name' => 'Cayman House Plans',
                'description' => 'Exclusive ready-to-build residential house plans designed specifically for Cayman Islands climate, zoning, and building codes.',
                'icon' => '🌴',
                'sort_order' => 2,
                'subcategories' => [
                    ['name' => 'Single-Story Island Homes', 'icon' => '🏖️', 'description' => 'Elegant single-level villas optimized for Caribbean cross-ventilation.'],
                    ['name' => 'Two-Story Luxury Villas', 'icon' => '🏰', 'description' => 'Multi-level estates with expansive balconies, high ceilings, and pool decks.'],
                    ['name' => 'Custom Lot Adaptations', 'icon' => '🗺️', 'description' => 'Adapting stock house plans to specific lot dimensions and setbacks.'],
                    ['name' => 'Plan Modification Requests', 'icon' => '✏️', 'description' => 'Custom room reconfigurations, bathroom expansions, and garage alterations.'],
                ],
            ],
            [
                'name' => 'Construction & Contracting',
                'description' => 'Licensed General Contracting, turnkey new builds, structural alterations, commercial fit-outs, and luxury home renovations.',
                'icon' => '🏗️',
                'sort_order' => 3,
                'subcategories' => [
                    ['name' => 'General Contracting & Turnkey', 'icon' => '🔨', 'description' => 'End-to-end site management, supervision, and building execution.'],
                    ['name' => 'Structural Foundations & Concrete', 'icon' => '🧱', 'description' => 'Piling, hurricane-resistant concrete framing, and foundation engineering.'],
                    ['name' => 'Complete Home Renovations', 'icon' => '✨', 'description' => 'Kitchen remodels, master suite upgrades, and full property transformations.'],
                    ['name' => 'Commercial Fit-Outs', 'icon' => '🏪', 'description' => 'Office interiors, retail build-outs, and hospitality refurbishments.'],
                ],
            ],
            [
                'name' => 'Certified Plumbing Services',
                'description' => 'State-certified plumbing diagnostics, water heater installations, emergency pipe repairs, and mechanical water filtration.',
                'icon' => '🚰',
                'sort_order' => 4,
                'subcategories' => [
                    ['name' => 'Water Heater Repair & Install', 'icon' => '🔥', 'description' => 'Tankless and standard water heater diagnostics, repair, and replacements.'],
                    ['name' => 'Drain Cleaning & Hydro-Jetting', 'icon' => '🚿', 'description' => 'Clearing blocked sewer lines, grease traps, and mainline clogs.'],
                    ['name' => 'Pipe Leak Detection & Repair', 'icon' => '🔍', 'description' => 'Non-invasive electronic leak detection and slab leak repiping.'],
                    ['name' => 'Fixture Installations & Upgrades', 'icon' => '🪠', 'description' => 'Luxury faucets, toilets, custom shower valves, and filtration systems.'],
                ],
            ],
            [
                'name' => 'Handyman & Property Maintenance',
                'description' => 'Rapid-response handyman repairs, scheduled maintenance subscriptions, drywall, electrical fixes, and facility management.',
                'icon' => '🔧',
                'sort_order' => 5,
                'subcategories' => [
                    ['name' => 'Emergency Handyman Repairs', 'icon' => '⚡', 'description' => 'Same-day fixes for doors, locks, drywall holes, and minor electrical items.'],
                    ['name' => 'Recurring Maintenance Subscriptions', 'icon' => '📅', 'description' => 'Monthly property checkups for homeowners, landlords, and vacation rentals.'],
                    ['name' => 'Carpentry & Architectural Millwork', 'icon' => '🪚', 'description' => 'Custom cabinetry, shelving, baseboards, and trim installations.'],
                    ['name' => 'Drywall, Plaster & Painting', 'icon' => '🎨', 'description' => 'Interior and exterior painting, drywall patching, and texture matching.'],
                ],
            ],
        ];

        foreach ($categoriesData as $data) {
            $subcategories = $data['subcategories'] ?? [];
            unset($data['subcategories']);

            $category = Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'status' => 'active',
                    'sort_order' => $data['sort_order'],
                ]
            );

            foreach ($subcategories as $index => $sub) {
                Subcategory::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($sub['name']),
                    ],
                    [
                        'name' => $sub['name'],
                        'description' => $sub['description'] ?? "Subcategory services and operational tasks for {$sub['name']}.",
                        'icon' => $sub['icon'] ?? '📁',
                        'status' => 'active',
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
