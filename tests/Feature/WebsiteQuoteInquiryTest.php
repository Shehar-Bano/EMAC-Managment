<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactInquiry;
use App\Models\Region;
use App\Models\RegionalServicePrice;
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

        $photo1 = UploadedFile::fake()->image('leak1.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('leak2.png', 800, 600);
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

        $inquiry = ContactInquiry::latest('id')->first();
        $this->assertNotNull($inquiry);
        $this->assertEquals('John Customer', $inquiry->name);
        $this->assertEquals($region->id, $inquiry->region_id);
        $this->assertEquals($category->id, $inquiry->category_id);
        $this->assertEquals($subcategory->id, $inquiry->subcategory_id);
        $this->assertEquals('562.50', (string) $inquiry->estimated_price);
        $this->assertEquals('KYD', $inquiry->currency);
        $this->assertCount(2, $inquiry->photographs);
        $this->assertNotNull($inquiry->video);
    }
}
