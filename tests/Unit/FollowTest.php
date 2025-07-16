<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Follow;
use App\Models\User;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_follow_belongs_to_follower()
    {
        $follower = User::factory()->create(['name' => 'Test Follower']);
        $following = User::factory()->create();

        $follow = Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->follower);
        $this->assertEquals('Test Follower', $follow->follower->name);
    }

    public function test_follow_belongs_to_following()
    {
        $follower = User::factory()->create();
        $following = User::factory()->create(['name' => 'Test Following']);

        $follow = Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->following);
        $this->assertEquals('Test Following', $follow->following->name);
    }

    public function test_follow_has_no_timestamps()
    {
        $follower = User::factory()->create();
        $following = User::factory()->create();

        $follow = Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $this->assertNull($follow->created_at);
        $this->assertNull($follow->updated_at);
    }

    public function test_follow_relationship_with_soft_deleted_users()
    {
        $follower = User::factory()->create(['name' => 'Test Follower']);
        $following = User::factory()->create(['name' => 'Test Following']);

        $follow = Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        // Soft delete the users
        $follower->delete();
        $following->delete();

        // Should still be able to access the relationships
        $this->assertInstanceOf(User::class, $follow->follower);
        $this->assertInstanceOf(User::class, $follow->following);
        $this->assertEquals('Test Follower', $follow->follower->name);
        $this->assertEquals('Test Following', $follow->following->name);
    }

    public function test_follow_relationship_foreign_keys()
    {
        $follower = User::factory()->create();
        $following = User::factory()->create();

        $follow = Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $this->assertEquals($follower->id, $follow->follower_id);
        $this->assertEquals($following->id, $follow->following_id);
    }
}