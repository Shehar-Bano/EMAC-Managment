<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Region;
use App\Models\RegionalServicePrice;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryCatalogApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_categories_catalog_with_subcategories_and_pricing(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $category = Category::create([
            'name' => 'Residential Cleaning',
            'slug' => 'residential-cleaning',
            'description' => 'Top notch home cleaning',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Deep Cleaning',
            'slug' => 'deep-cleaning',
            'description' => 'Detailed home deep cleaning',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $price = RegionalServicePrice::create([
            'region_id' => $region->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 150.00,
            'currency' => 'KYD',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/categories/catalog');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $category->id)
            ->assertJsonPath('data.0.name', 'Residential Cleaning')
            ->assertJsonPath('data.0.subcategories.0.id', $subcategory->id)
            ->assertJsonPath('data.0.subcategories.0.name', 'Deep Cleaning')
            ->assertJsonPath('data.0.subcategories.0.price', 150)
            ->assertJsonPath('data.0.subcategories.0.formatted_price', 'KYD 150.00')
            ->assertJsonPath('data.0.subcategories.0.currency', 'KYD')
            ->assertJsonPath('data.0.subcategories.0.regional_prices.0.id', $price->id);
    }

    public function test_categories_catalog_filters_by_region_id(): void
    {
        $region1 = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $region2 = Region::firstOrCreate(
            ['slug' => 'cayman-brac'],
            [
                'name' => 'Cayman Brac',
                'code' => 'CYB',
                'currency' => 'USD',
                'status' => 'active',
            ]
        );

        $category = Category::create([
            'name' => 'Gardening',
            'slug' => 'gardening',
            'status' => 'active',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Lawn Mowing',
            'slug' => 'lawn-mowing',
            'status' => 'active',
        ]);

        RegionalServicePrice::create([
            'region_id' => $region1->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 80.00,
            'currency' => 'KYD',
            'status' => 'active',
        ]);

        RegionalServicePrice::create([
            'region_id' => $region2->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 95.00,
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/categories/catalog?region_id='.$region2->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.subcategories.0.price', 95)
            ->assertJsonPath('data.0.subcategories.0.currency', 'USD')
            ->assertJsonPath('data.0.subcategories.0.region_id', $region2->id)
            ->assertJsonCount(1, 'data.0.subcategories.0.regional_prices');
    }

    public function test_soft_deleted_entities_are_excluded_from_catalog(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $activeCat = Category::create([
            'name' => 'Active Category',
            'slug' => 'active-category',
            'status' => 'active',
        ]);

        $deletedCat = Category::create([
            'name' => 'Deleted Category',
            'slug' => 'deleted-category',
            'status' => 'active',
        ]);
        $deletedCat->delete();

        $activeSub = Subcategory::create([
            'category_id' => $activeCat->id,
            'name' => 'Active Subcategory',
            'slug' => 'active-subcategory',
            'status' => 'active',
        ]);

        $deletedSub = Subcategory::create([
            'category_id' => $activeCat->id,
            'name' => 'Deleted Subcategory',
            'slug' => 'deleted-subcategory',
            'status' => 'active',
        ]);
        $deletedSub->delete();

        $response = $this->getJson('/api/v1/categories/catalog');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Active Category')
            ->assertJsonCount(1, 'data.0.subcategories')
            ->assertJsonPath('data.0.subcategories.0.name', 'Active Subcategory');
    }

    public function test_get_single_category_with_subcategories_and_pricing(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $category = Category::create([
            'name' => 'HVAC Maintenance',
            'slug' => 'hvac-maintenance',
            'status' => 'active',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'AC Duct Cleaning',
            'slug' => 'ac-duct-cleaning',
            'status' => 'active',
        ]);

        RegionalServicePrice::create([
            'region_id' => $region->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 200.00,
            'currency' => 'KYD',
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'HVAC Maintenance')
            ->assertJsonPath('data.subcategories.0.name', 'AC Duct Cleaning')
            ->assertJsonPath('data.subcategories.0.price', 200);
    }

    public function test_get_subcategories_api_with_pricing(): void
    {
        $region = Region::firstOrCreate(
            ['slug' => 'grand-cayman'],
            [
                'name' => 'Grand Cayman',
                'code' => 'GCM',
                'currency' => 'KYD',
                'status' => 'active',
            ]
        );

        $category = Category::create([
            'name' => 'Plumbing',
            'slug' => 'plumbing',
            'status' => 'active',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Pipe Leak Repair',
            'slug' => 'pipe-leak-repair',
            'status' => 'active',
        ]);

        RegionalServicePrice::create([
            'region_id' => $region->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 120.00,
            'currency' => 'KYD',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/subcategories');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $subcategory->id)
            ->assertJsonPath('data.0.name', 'Pipe Leak Repair')
            ->assertJsonPath('data.0.price', 120)
            ->assertJsonPath('data.0.currency', 'KYD')
            ->assertJsonPath('data.0.category.id', $category->id);
    }
}
