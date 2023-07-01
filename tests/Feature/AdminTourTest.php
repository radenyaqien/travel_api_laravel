<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_cannot_crete_tour(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(401);
    }
    public function test_non_Admin_user_cannot_crete_travels(): void
    {

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $travel = Travel::factory()->create();

        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(403);
    }
    public function test_saves_travels_successfully_with_valid_data(): void
    {

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $travel = Travel::factory()->create();

        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'name' => 'Tour Name'
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'name' => 'Tour Name',
            'starting_date' => now()->toDateString(),
            'ending_date' => now()->addDays()->toDateString(),
            "price" => 11
        ]);

        $response->assertStatus(201);

        $this->get('api/v1/travels/' . $travel->id . '/tours');
        $response->assertJsonFragment(['name' => 'Tour Name']);
    }
}
