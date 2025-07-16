<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comment.store', $post->id), [
                'body' => 'Test comment',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'body' => 'Test comment',
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_comment_validation_error()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comment.store', $post->id), [
                'body' => '',
            ]);

        $response->assertSessionHasErrors(['body']);
    }

    public function test_user_can_delete_own_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('comment.destroy', $comment->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_others_comment()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('comment.destroy', $comment->id));

        $response->assertRedirect();

        // Comment should still exist
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_post_has_many_comments()
    {
        $post = Post::factory()->create();
        $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $this->assertCount(3, $post->comments);
        $this->assertInstanceOf(Comment::class, $post->comments->first());
    }

    public function test_comment_belongs_to_user()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals('Test User', $comment->user->name);
    }

    public function test_comment_belongs_to_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['description' => 'Test Post']);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertInstanceOf(Post::class, $comment->post);
        $this->assertEquals('Test Post', $comment->post->description);
    }
}