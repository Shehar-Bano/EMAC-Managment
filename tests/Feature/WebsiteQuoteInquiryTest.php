<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Region;
use App\Models\RegionalServicePrice;
use App\Models\ServiceRequest;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebsiteQuoteInquiryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_submit_website_quote_with_images_and_video(): void
    {
        Storage::fake('public');

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
            'icon' => '🚰',
            'status' => 'active',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Water Heater Replacement',
            'slug' => 'water-heater-replacement',
            'icon' => '🔥',
            'status' => 'active',
        ]);

        RegionalServicePrice::create([
            'region_id' => $region->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'price' => 562.50,
            'currency' => 'KYD',
            'status' => 'active',
        ]);

        $photo1 = UploadedFile::fake()->create('leak1.jpg', 800, 'image/jpeg');
        $photo2 = UploadedFile::fake()->create('leak2.png', 800, 'image/png');
        $video = UploadedFile::fake()->create('walkthrough.mp4', 5000, 'video/mp4');

        $response = $this->post('/contact', [
            'name' => 'John Customer',
            'email' => 'john.customer@example.com',
            'phone' => '+1 (345) 949-0000',
            'region_id' => $region->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'message' => 'Please replace my residential water heater as soon as possible.',
            'photographs' => [$photo1, $photo2],
            'video' => $video,
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');

        $serviceRequest = ServiceRequest::latest('id')->first();
        $this->assertNotNull($serviceRequest);
        $this->assertEquals($category->id, $serviceRequest->category_id);
        $this->assertEquals($subcategory->id, $serviceRequest->subcategory_id);
        $this->assertEquals('web', $serviceRequest->type);
        $this->assertCount(2, $serviceRequest->photographs);
        $this->assertCount(1, $serviceRequest->videos);
        $this->assertEquals('walkthrough.mp4', $serviceRequest->videos->first()->file_name);
    }

    public function test_can_submit_website_quote_via_ajax_with_video(): void
    {
        Storage::fake('public');

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
            'name' => 'Electrical',
            'slug' => 'electrical',
            'icon' => '⚡',
            'status' => 'active',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Panel Upgrade',
            'slug' => 'panel-upgrade',
            'icon' => '🔌',
            'status' => 'active',
        ]);

        $video = UploadedFile::fake()->create('circuit_breaker.webm', 8000, 'video/webm');

        $response = $this->postJson('/contact', [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '+1 (345) 949-1111',
            'region_id' => $region->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'message' => 'Need emergency breaker inspection and panel quote.',
            'video' => $video,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $serviceRequest = ServiceRequest::latest('id')->first();
        $this->assertNotNull($serviceRequest);
        $this->assertCount(1, $serviceRequest->videos);
        $this->assertEquals('circuit_breaker.webm', $serviceRequest->videos->first()->file_name);
    }
}
