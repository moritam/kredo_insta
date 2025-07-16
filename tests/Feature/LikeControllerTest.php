<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('like.store', $post->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_user_can_unlike_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // Create like first
        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('like.destroy', $post->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_user_cannot_like_same_post_twice()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // Create like first
        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('like.store', $post->id));

        $response->assertRedirect();

        // Should still have only one like
        $this->assertDatabaseCount('likes', 1);
    }

    public function test_unlike_nonexistent_like()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('like.destroy', $post->id));

        $response->assertRedirect();

        // Should not affect database
        $this->assertDatabaseCount('likes', 0);
    }

    public function test_post_like_count()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create();

        // Create likes
        Like::create([
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);
        Like::create([
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);

        $this->assertEquals(2, $post->likes->count());
    }

    public function test_post_is_liked_by_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // Create like
        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->actingAs($user);
        $this->assertTrue($post->isLiked());
    }

    public function test_post_is_not_liked_by_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user);
        $this->assertFalse($post->isLiked());
    }
}