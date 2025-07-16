<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $commentBody = 'テストコメント';
        $response = $this->actingAs($user)
            ->post(route('comment.store', $post->id), [
                'comment_body' . $post->id => $commentBody,
            ]);
        $response->assertRedirect(route('post.show', $post->id));
        $this->assertDatabaseHas('comments', [
            'body' => $commentBody,
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_comment_store_validation_error()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user)
            ->post(route('comment.store', $post->id), [
                'comment_body' . $post->id => '',
            ]);
        $response->assertSessionHasErrors(['comment_body' . $post->id]);
    }
}