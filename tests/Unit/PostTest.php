<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;
use App\Models\CategoryPost;
use App\Models\Comment;
use App\Models\Category;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_belongs_to_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    public function test_post_has_many_category_posts()
    {
        $post = Post::factory()->create();
        $categoryPosts = CategoryPost::factory()->count(2)->create(['post_id' => $post->id]);
        $this->assertCount(2, $post->categoryPost);
    }

    public function test_post_has_many_comments()
    {
        $post = Post::factory()->create();
        $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);
        $this->assertCount(3, $post->comments);
    }

    public function test_post_has_many_likes()
    {
        $post = Post::factory()->create();
        $likes = \App\Models\Like::factory()->count(2)->create(['post_id' => $post->id]);
        $this->assertCount(2, $post->likes);
    }

    public function test_post_is_liked_by_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        \App\Models\Like::create([
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

    public function test_post_soft_deletes()
    {
        $post = Post::factory()->create();
        $postId = $post->id;

        $post->delete();

        $this->assertSoftDeleted('posts', ['id' => $postId]);
        $this->assertDatabaseHas('posts', ['id' => $postId]);
    }

    public function test_post_can_be_restored()
    {
        $post = Post::factory()->create();
        $postId = $post->id;

        $post->delete();
        $post->restore();

        $this->assertDatabaseHas('posts', ['id' => $postId]);
        $this->assertNull($post->deleted_at);
    }

    public function test_post_with_categories()
    {
        $post = Post::factory()->create();
        $category = Category::factory()->create();

        CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(1, $post->categoryPost);
        $this->assertEquals($category->name, $post->categoryPost->first()->category->name);
    }

    public function test_post_without_categories_is_unrecognized()
    {
        $post = Post::factory()->create();

        $this->assertCount(0, $post->categoryPost);
    }

    public function test_post_with_deleted_category()
    {
        $post = Post::factory()->create();
        $category = Category::factory()->create();

        CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        // Delete the category
        $category->delete();

        // Should still be able to access the relationship
        $this->assertCount(1, $post->categoryPost);
        $this->assertInstanceOf(Category::class, $post->categoryPost->first()->category);
    }

    public function test_post_with_multiple_categories()
    {
        $post = Post::factory()->create();
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);

        CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category1->id,
        ]);
        CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category2->id,
        ]);

        $this->assertCount(2, $post->categoryPost);
        $this->assertEquals('Category 1', $post->categoryPost->first()->category->name);
        $this->assertEquals('Category 2', $post->categoryPost->last()->category->name);
    }
}