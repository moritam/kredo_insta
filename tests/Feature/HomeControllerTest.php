<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Follow;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_home()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewIs('users.home');
        $response->assertViewHas('home_posts');
        $response->assertViewHas('suggested_users');
    }

    public function test_home_shows_own_posts()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('home_posts');

        $homePosts = $response->viewData('home_posts');
        $this->assertCount(1, $homePosts);
        $this->assertEquals($post->id, $homePosts[0]->id);
    }

    public function test_home_shows_followed_user_posts()
    {
        $user = User::factory()->create();
        $followedUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $followedUser->id]);

        // Create follow relationship
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $followedUser->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('home_posts');

        $homePosts = $response->viewData('home_posts');
        $this->assertCount(1, $homePosts);
        $this->assertEquals($post->id, $homePosts[0]->id);
    }

    public function test_home_does_not_show_unfollowed_user_posts()
    {
        $user = User::factory()->create();
        $unfollowedUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $unfollowedUser->id]);

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('home_posts');

        $homePosts = $response->viewData('home_posts');
        $this->assertCount(0, $homePosts);
    }

    public function test_suggested_users_excludes_followed_users()
    {
        $user = User::factory()->create();
        $followedUser = User::factory()->create();
        $unfollowedUser = User::factory()->create();

        // Create follow relationship
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $followedUser->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('suggested_users');

        $suggestedUsers = $response->viewData('suggested_users');
        $this->assertCount(1, $suggestedUsers);
        $this->assertEquals($unfollowedUser->id, $suggestedUsers[0]->id);
    }

    public function test_suggested_users_excludes_self()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('suggested_users');

        $suggestedUsers = $response->viewData('suggested_users');
        $this->assertCount(1, $suggestedUsers);
        $this->assertEquals($otherUser->id, $suggestedUsers[0]->id);
    }

    public function test_home_with_no_posts()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('home_posts');

        $homePosts = $response->viewData('home_posts');
        $this->assertCount(0, $homePosts);
    }

    public function test_home_with_multiple_followed_users()
    {
        $user = User::factory()->create();
        $followedUser1 = User::factory()->create();
        $followedUser2 = User::factory()->create();

        $post1 = Post::factory()->create(['user_id' => $followedUser1->id]);
        $post2 = Post::factory()->create(['user_id' => $followedUser2->id]);

        // Create follow relationships
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $followedUser1->id,
        ]);
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $followedUser2->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewHas('home_posts');

        $homePosts = $response->viewData('home_posts');
        $this->assertCount(2, $homePosts);
    }
}