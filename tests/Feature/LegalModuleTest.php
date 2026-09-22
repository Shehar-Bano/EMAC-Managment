<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LegalModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;

    protected User $technician;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            [
                'name' => 'EMAC Super Admin',
                'phone' => '+1555000111',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        if ($superAdminRole) {
            $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        $technicianRole = Role::where('slug', 'technician')->first();
        $this->technician = User::firstOrCreate(
            ['email' => 'tech@emac.test'],
            [
                'name' => 'Field Technician',
                'phone' => '+1555000222',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        if ($technicianRole) {
            $this->technician->roles()->syncWithoutDetaching([$technicianRole->id]);
        }
    }

    /**
     * Test Super Admin can access Terms and Conditions management in dashboard.
     */
    public function test_super_admin_can_access_and_update_terms(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('dashboard.terms.index'))
            ->assertStatus(200)
            ->assertSee('Terms')
            ->assertSee('Document Metadata');

        $this->actingAs($this->superAdmin)
            ->get(route('dashboard.terms.edit'))
            ->assertStatus(200)
            ->assertSee('Edit Terms')
            ->assertSee('Version Number');

        $updateResponse = $this->actingAs($this->superAdmin)
            ->put(route('dashboard.terms.update'), [
                'title' => 'Updated Terms of Service 2026',
                'version' => '2.0',
                'effective_date' => '2026-10-01',
                'status' => 'active',
                'content' => '<h3>Updated Terms Clause</h3><p>New terms description.</p>',
            ]);

        $updateResponse->assertRedirect(route('dashboard.terms.index'));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('legal_documents', [
            'type' => 'terms',
            'title' => 'Updated Terms of Service 2026',
            'version' => '2.0',
        ]);
    }

    /**
     * Test Super Admin can access Privacy Policy management in dashboard.
     */
    public function test_super_admin_can_access_and_update_privacy(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('dashboard.privacy.index'))
            ->assertStatus(200)
            ->assertSee('Privacy Policy Management');

        $this->actingAs($this->superAdmin)
            ->get(route('dashboard.privacy.edit'))
            ->assertStatus(200)
            ->assertSee('Edit Privacy Policy');

        $updateResponse = $this->actingAs($this->superAdmin)
            ->put(route('dashboard.privacy.update'), [
                'title' => 'Updated Global Privacy Policy',
                'version' => '1.5',
                'effective_date' => '2026-10-01',
                'status' => 'active',
                'content' => '<h3>Updated Privacy Clause</h3><p>New privacy disclosures.</p>',
            ]);

        $updateResponse->assertRedirect(route('dashboard.privacy.index'));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('legal_documents', [
            'type' => 'privacy',
            'title' => 'Updated Global Privacy Policy',
            'version' => '1.5',
        ]);
    }

    /**
     * Test Public APIs for Terms and Privacy return 200 with standard envelope.
     */
    public function test_public_legal_apis_return_standard_json(): void
    {
        // 1. Terms API
        $termsResponse = $this->getJson('/api/v1/terms');
        $termsResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Terms and Conditions retrieved successfully.',
                'data' => [
                    'type' => 'terms',
                ],
            ]);

        // 2. Privacy API
        $privacyResponse = $this->getJson('/api/v1/privacy');
        $privacyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'message' => 'Privacy Policy retrieved successfully.',
                'data' => [
                    'type' => 'privacy',
                ],
            ]);

        // 3. /api/v1/legal/terms and /api/v1/legal/privacy aliases
        $this->getJson('/api/v1/legal/terms')->assertStatus(200);
        $this->getJson('/api/v1/legal/privacy')->assertStatus(200);
    }

    /**
     * Test Public Website views render dynamic database terms and privacy.
     */
    public function test_public_website_renders_terms_and_privacy_pages(): void
    {
        $this->get(route('terms'))
            ->assertStatus(200)
            ->assertSee('Terms and Conditions');

        $this->get(route('privacy'))
            ->assertStatus(200)
            ->assertSee('Privacy Policy');
    }
}
