<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Follow;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile()
    {
        $user = User::factory()->create();
        $viewedUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.show', $viewedUser->id));

        $response->assertStatus(200);
        $response->assertViewIs('users.profile.show');
        $response->assertViewHas('user', $viewedUser);
    }

    public function test_user_can_view_own_profile_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('users.profile.edit');
        $response->assertViewHas('user', $user);
    }

    public function test_user_can_update_profile()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->patch(route('profile.update', $user->id), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'introduction' => 'Updated introduction',
                'avatar' => $image,
            ]);

        $response->assertRedirect(route('profile.show', $user->id));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'introduction' => 'Updated introduction',
        ]);
    }

    public function test_profile_update_validation_error()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('profile.update', $user->id), [
                'name' => '',
                'email' => 'invalid-email',
                'avatar' => UploadedFile::fake()->create('document.pdf', 2000),
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'avatar']);
    }

    public function test_user_can_view_followers()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        // Create follow relationship
        Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('profile.followers', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('users.profile.followers');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('followers');
    }

    public function test_user_can_view_following()
    {
        $user = User::factory()->create();
        $following = User::factory()->create();

        // Create follow relationship
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $following->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('profile.following', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('users.profile.following');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('following');
    }

    public function test_profile_show_returns_404_for_nonexistent_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.show', 99999));

        $response->assertStatus(404);
    }
}