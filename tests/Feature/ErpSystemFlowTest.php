<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ErpSystemFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_website_pages_render_successfully(): void
    {
        $this->get(route('home'))->assertStatus(200);
        $this->get(route('about'))->assertStatus(200);
        $this->get(route('services'))->assertStatus(200);
        $this->get(route('faq'))->assertStatus(200);
        $this->get(route('contact'))->assertStatus(200);
        $this->get(route('privacy'))->assertStatus(200);
        $this->get(route('terms'))->assertStatus(200);
    }

    public function test_public_contact_form_submits_successfully(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'John Enterprise',
            'email' => 'john@enterprise.com',
            'market' => 'Florida',
            'phone' => '+1555000111',
            'message' => 'We would like to request an ERP enterprise demo for architectural projects.',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_super_admin_can_login_and_access_dashboard(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@emac.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticated();

        $this->get(route('dashboard.index'))->assertStatus(200);
        $this->get(route('dashboard.users.index'))->assertStatus(200);
        $this->get(route('dashboard.roles.index'))->assertStatus(200);
        $this->get(route('dashboard.permissions.index'))->assertStatus(200);
        $this->get(route('dashboard.categories.index'))->assertStatus(200);
        $this->get(route('dashboard.subcategories.index'))->assertStatus(200);
        $this->get(route('dashboard.settings.index'))->assertStatus(200);
    }

    public function test_dynamic_pagination_supports_presets_and_all(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'status' => 'active']
        );

        $this->actingAs($admin)
            ->get(route('dashboard.users.index', ['per_page' => 10]))
            ->assertStatus(200)
            ->assertSee('entries');

        $this->actingAs($admin)
            ->get(route('dashboard.users.index', ['per_page' => 'all']))
            ->assertStatus(200);
    }

    public function test_super_admin_can_create_edit_and_toggle_user(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $role = Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff', 'description' => 'Staff Role']);

        // 1. Create User
        $email = 'test.operator.'.uniqid().'@emac.test';
        $this->actingAs($admin)
            ->post(route('dashboard.users.store'), [
                'name' => 'Test Operator User',
                'email' => $email,
                'phone' => '+1 (555) 019-9999',
                'status' => 'active',
                'password' => 'password123',
                'roles' => [$role->id],
            ])
            ->assertRedirect(route('dashboard.users.index'))
            ->assertSessionHas('success');

        $user = User::where('email', $email)->firstOrFail();
        $this->assertTrue($user->hasRole('staff'));

        // 2. Edit User
        $this->actingAs($admin)
            ->put(route('dashboard.users.update', $user), [
                'name' => 'Updated Operator User',
                'email' => $email,
                'status' => 'active',
                'roles' => [$role->id],
            ])
            ->assertRedirect(route('dashboard.users.index'))
            ->assertSessionHas('success');

        // 3. Toggle Status
        $this->actingAs($admin)
            ->patch(route('dashboard.users.status', $user))
            ->assertSessionHas('success');

        $this->assertEquals('inactive', $user->fresh()->status);
    }

    public function test_category_and_subcategory_image_upload_and_crud(): void
    {
        Storage::fake('public');

        $admin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'status' => 'active']
        );

        $catImage = UploadedFile::fake()->create('architecture_banner.jpg', 150, 'image/jpeg');

        // 1. Create Category with Image
        $catResponse = $this->actingAs($admin)->post(route('dashboard.categories.store'), [
            'name' => 'Commercial Architecture '.uniqid(),
            'description' => 'Commercial architecture division',
            'image' => $catImage,
            'status' => 'active',
            'sort_order' => 1,
        ]);
        $catResponse->assertRedirect(route('dashboard.categories.index'));

        $category = Category::latest('id')->firstOrFail();
        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);

        // 2. Create Subcategory with Image
        $subImage = UploadedFile::fake()->create('blueprint_plan.png', 120, 'image/png');
        $subResponse = $this->actingAs($admin)->post(route('dashboard.subcategories.store'), [
            'category_id' => $category->id,
            'name' => 'Space Planning '.uniqid(),
            'description' => 'Interior layout blueprints',
            'image' => $subImage,
            'status' => 'active',
            'sort_order' => 1,
        ]);
        $subResponse->assertRedirect(route('dashboard.subcategories.index'));

        $subcategory = Subcategory::latest('id')->firstOrFail();
        $this->assertNotNull($subcategory->image);
        Storage::disk('public')->assertExists($subcategory->image);

        // 3. Test API resource output
        $apiResponse = $this->actingAs($admin)->getJson("/api/v1/categories/{$category->id}");
        $apiResponse->assertStatus(200)
            ->assertJsonPath('data.name', $category->name);
    }

    public function test_bulk_delete_users_action(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $u1 = User::factory()->create(['email' => 'temp1.'.uniqid().'@emac.test', 'status' => 'active']);
        $u2 = User::factory()->create(['email' => 'temp2.'.uniqid().'@emac.test', 'status' => 'active']);

        $response = $this->actingAs($admin)
            ->delete(route('dashboard.users.bulk-delete'), [
                'ids' => [$u1->id, $u2->id],
            ]);

        $response->assertRedirect(route('dashboard.users.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $u1->id]);
        $this->assertSoftDeleted('users', ['id' => $u2->id]);
    }

    public function test_role_management_crud(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $group = PermissionGroup::firstOrCreate(['slug' => 'system'], ['name' => 'System']);
        $perm = Permission::firstOrCreate(
            ['name' => 'system.view'],
            ['permission_group_id' => $group->id, 'label' => 'System View']
        );

        // 1. Create Role
        $roleName = 'Custom Auditor '.uniqid();
        $this->actingAs($admin)
            ->post(route('dashboard.roles.store'), [
                'name' => $roleName,
                'description' => 'Test auditor role',
                'permissions' => [$perm->id],
            ])
            ->assertRedirect(route('dashboard.roles.index'))
            ->assertSessionHas('success');

        $role = Role::where('name', $roleName)->firstOrFail();
        $this->assertTrue($role->hasPermission($perm->name));

        // 2. Delete Role
        $this->actingAs($admin)
            ->delete(route('dashboard.roles.destroy', $role))
            ->assertRedirect(route('dashboard.roles.index'))
            ->assertSessionHas('success');
    }

    public function test_api_v1_users_index_returns_json_resource(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@emac.test'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'status' => 'active']
        );

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'status',
                        'avatar_url',
                    ],
                ],
            ]);
    }
}
