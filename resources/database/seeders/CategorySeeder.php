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
                'name' => 'Handyman Services',
                'description' => 'Comprehensive handyman, property maintenance, minor carpentry, drywall, painting, and facility repair services.',
                'icon' => '🔧',
                'sort_order' => 1,
                'subcategories' => [
                    ['name' => 'General repairs', 'icon' => '🔧', 'description' => 'Comprehensive fix-it solutions for residential and commercial spaces.'],
                    ['name' => 'Drywall repair', 'icon' => '🧱', 'description' => 'Patching holes, cracks, water damage repair, and seamless texture blending.'],
                    ['name' => 'Painting', 'icon' => '🎨', 'description' => 'Interior and exterior precision painting, touch-ups, and surface finishing.'],
                    ['name' => 'Door repair', 'icon' => '🚪', 'description' => 'Fixing sticking doors, broken hinges, misalignments, and latch issues.'],
                    ['name' => 'Door installation', 'icon' => '🚪', 'description' => 'New interior and exterior door fitting, frames, and security hardware.'],
                    ['name' => 'Window repair', 'icon' => '🪟', 'description' => 'Repairing window latches, balances, seals, and operational mechanisms.'],
                    ['name' => 'Screen repair', 'icon' => '🛡️', 'description' => 'Fixing torn or damaged window screens, patio screens, and mesh replacements.'],
                    ['name' => 'Screen cleaning', 'icon' => '✨', 'description' => 'Deep cleaning and debris removal for window screens and patio enclosures.'],
                    ['name' => 'Pressure washing', 'icon' => '🌊', 'description' => 'High-pressure surface cleaning for driveways, siding, walkways, and decks.'],
                    ['name' => 'Window washing', 'icon' => '🪟', 'description' => 'Streak-free exterior and interior glass washing and track cleaning.'],
                    ['name' => 'Fixture installation', 'icon' => '💡', 'description' => 'Mounting light fixtures, ceiling fans, blinds, mirrors, and shelving.'],
                    ['name' => 'Furniture assembly', 'icon' => '🪑', 'description' => 'Professional flat-pack assembly for office, patio, and home furnishings.'],
                    ['name' => 'Caulking', 'icon' => '🧴', 'description' => 'Waterproof sealing for bathrooms, kitchens, sinks, tubs, and window perimeters.'],
                    ['name' => 'Trim repair', 'icon' => '📏', 'description' => 'Restoring and repairing baseboards, crown moulding, and architectural trim.'],
                    ['name' => 'Minor carpentry', 'icon' => '🪚', 'description' => 'Custom shelving, small woodwork fixes, deck boards, and framing repairs.'],
                    ['name' => 'Property maintenance', 'icon' => '📋', 'description' => 'Scheduled recurring checkups, seasonal upkeep, and preventative repairs.'],
                    ['name' => 'Other handyman services', 'icon' => '⚙️', 'description' => 'Custom requests, miscellaneous repairs, and specialized handyman tasks.'],
                ],
            ],
            [
                'name' => 'Plumbing Services',
                'description' => 'Certified residential and commercial plumbing diagnostics, pipe repairs, water heaters, and fixture installations.',
                'icon' => '🚰',
                'sort_order' => 2,
                'subcategories' => [
                    ['name' => 'Water heater replacement/installation', 'icon' => '🔥', 'description' => 'Tankless and traditional water heater diagnostics, installations, and swaps.'],
                    ['name' => 'Faucet replacement', 'icon' => '🚰', 'description' => 'Kitchen and bathroom faucet upgrades, removals, and new installations.'],
                    ['name' => 'Toilet repair/replacement', 'icon' => '🚽', 'description' => 'Fixing running toilets, seals, flush valves, and installing new units.'],
                    ['name' => 'Sink installation', 'icon' => '🥣', 'description' => 'Undermount, drop-in, and vessel sink fitting and drain connections.'],
                    ['name' => 'Garbage disposal installation', 'icon' => '♻️', 'description' => 'Replacing worn-out food waste disposers and electrical/drain hookups.'],
                    ['name' => 'Shower valve repair', 'icon' => '🚿', 'description' => 'Cartridge replacements, pressure balancing, and shower temperature fixes.'],
                    ['name' => 'Shower fixture replacement', 'icon' => '🚿', 'description' => 'Upgrading shower heads, handheld wands, and luxury fixture suites.'],
                    ['name' => 'Pipe repair', 'icon' => '🔧', 'description' => 'Fixing damaged, corroded, or rattling supply and drain lines.'],
                    ['name' => 'Broken pipe repair', 'icon' => '💥', 'description' => 'Emergency fixes for cracked, burst, or compromised piping systems.'],
                    ['name' => 'Leak repair', 'icon' => '💧', 'description' => 'Diagnosing hidden water leaks, dripping connections, and slab leaks.'],
                    ['name' => 'Drain issues', 'icon' => '🪠', 'description' => 'Clearing slow drains, blockages, grease clogs, and sewer backup diagnosis.'],
                    ['name' => 'Water line repair', 'icon' => '🌊', 'description' => 'Main supply line repairs, shutoff valves, and high-pressure line fixes.'],
                    ['name' => 'Plumbing fixture installation', 'icon' => '🛠️', 'description' => 'Installing utility sinks, bidets, hose bibs, and speciality fixtures.'],
                    ['name' => 'Plumbing maintenance', 'icon' => '🛡️', 'description' => 'System inspections, water pressure tests, flush checks, and drain care.'],
                    ['name' => 'Other plumbing services', 'icon' => '🚰', 'description' => 'Custom plumbing solutions, pipe re-routing, and general plumbing tasks.'],
                ],
            ],
        ];

        $activeCategorySlugs = [];

        foreach ($categoriesData as $data) {
            $subcategories = $data['subcategories'] ?? [];
            unset($data['subcategories']);

            $slug = Str::slug($data['name']);
            $activeCategorySlugs[] = $slug;

            $category = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'status' => 'active',
                    'sort_order' => $data['sort_order'],
                ]
            );

            $activeSubcategorySlugs = [];

            foreach ($subcategories as $index => $sub) {
                $subSlug = Str::slug($sub['name']);
                $activeSubcategorySlugs[] = $subSlug;

                Subcategory::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'slug' => $subSlug,
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

            // Remove subcategories no longer in the list for this category
            Subcategory::where('category_id', $category->id)
                ->whereNotIn('slug', $activeSubcategorySlugs)
                ->forceDelete();
        }

        // Remove all other categories and their subcategories not in the approved list
        $otherCategories = Category::withTrashed()->whereNotIn('slug', $activeCategorySlugs)->get();
        foreach ($otherCategories as $oldCat) {
            Subcategory::withTrashed()->where('category_id', $oldCat->id)->forceDelete();
            $oldCat->forceDelete();
        }

        // Remove any orphan subcategories
        Subcategory::withTrashed()->whereNotIn('category_id', Category::pluck('id'))->forceDelete();
    }
}
