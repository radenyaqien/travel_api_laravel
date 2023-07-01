<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_cannot_crete_travels(): void
    {
        $response = $this->postJson('/api/v1/admin/travels');

        $response->assertStatus(401);
    }
    public function test_non_Admin_user_cannot_crete_travels(): void
    {

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();

        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        $response->assertStatus(403);
    }
    public function test_saves_travels_successfully_with_valid_data(): void
    {

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();

        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel Name'
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel Name',
            'is_public' => 1,
            'description' => "sbakdbkj",
            "number_of_days" => 1
        ]);

        $response->assertStatus(201);

        $this->get('api/v1/travels');
        $response->assertJsonFragment(['description' => 'sbakdbkj']);
    }

    public function test_update_travels_successfully_with_valid_data(): void
    {

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();

        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Travel Name'
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Travel Name Updated',
            'is_public' => 1,
            'description' => "sbakdbkj",
            "number_of_days" => 1
        ]);

        $response->assertStatus(200);

        $this->get('api/v1/travels');
        $response->assertJsonFragment(['name' => 'Travel Name Updated']);
    }
}
