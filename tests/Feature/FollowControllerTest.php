<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Follow;

class FollowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user()
    {
        $user = User::factory()->create();
        $userToFollow = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('follow.store', $userToFollow->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('follows', [
            'follower_id' => $user->id,
            'following_id' => $userToFollow->id,
        ]);
    }

    public function test_user_can_unfollow_another_user()
    {
        $user = User::factory()->create();
        $userToUnfollow = User::factory()->create();

        // Create follow relationship first
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $userToUnfollow->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('follow.destroy', $userToUnfollow->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user->id,
            'following_id' => $userToUnfollow->id,
        ]);
    }

    public function test_user_cannot_follow_themselves()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('follow.store', $user->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user->id,
            'following_id' => $user->id,
        ]);
    }

    public function test_user_cannot_follow_same_user_twice()
    {
        $user = User::factory()->create();
        $userToFollow = User::factory()->create();

        // Create follow relationship first
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $userToFollow->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('follow.store', $userToFollow->id));

        $response->assertRedirect();

        // Should still have only one follow relationship
        $this->assertDatabaseCount('follows', 1);
    }

    public function test_unfollow_nonexistent_follow_relationship()
    {
        $user = User::factory()->create();
        $userToUnfollow = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('follow.destroy', $userToUnfollow->id));

        $response->assertRedirect();

        // Should not affect database
        $this->assertDatabaseCount('follows', 0);
    }
}