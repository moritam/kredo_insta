<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Post;
use App\Models\Follow;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_posts()
    {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->posts);
        $this->assertInstanceOf(Post::class, $user->posts->first());
    }

    public function test_user_has_many_followers()
    {
        $user = User::factory()->create();
        $followers = User::factory()->count(2)->create();

        foreach ($followers as $follower) {
            Follow::create([
                'follower_id' => $follower->id,
                'following_id' => $user->id,
            ]);
        }

        $this->assertCount(2, $user->followers);
        $this->assertInstanceOf(Follow::class, $user->followers->first());
    }

    public function test_user_has_many_following()
    {
        $user = User::factory()->create();
        $following = User::factory()->count(2)->create();

        foreach ($following as $followedUser) {
            Follow::create([
                'follower_id' => $user->id,
                'following_id' => $followedUser->id,
            ]);
        }

        $this->assertCount(2, $user->following);
        $this->assertInstanceOf(Follow::class, $user->following->first());
    }

    public function test_is_followed_returns_true_when_followed()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
        ]);

        $this->actingAs($follower);
        $this->assertTrue($user->isFollowed());
    }

    public function test_is_followed_returns_false_when_not_followed()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);
        $this->assertFalse($user->isFollowed());
    }

    public function test_user_can_access_follower_user_data()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create(['name' => 'Test Follower']);

        Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
        ]);

        $this->assertEquals('Test Follower', $user->followers->first()->follower->name);
    }

    public function test_user_can_access_following_user_data()
    {
        $user = User::factory()->create();
        $following = User::factory()->create(['name' => 'Test Following']);

        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $following->id,
        ]);

        $this->assertEquals('Test Following', $user->following->first()->following->name);
    }

    public function test_user_soft_deletes()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertDatabaseHas('users', ['id' => $userId]);
    }

    public function test_user_can_be_restored()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();
        $user->restore();

        $this->assertDatabaseHas('users', ['id' => $userId]);
        $this->assertNull($user->deleted_at);
    }
}