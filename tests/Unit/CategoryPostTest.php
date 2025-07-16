<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CategoryPost;
use App\Models\Post;
use App\Models\Category;

class CategoryPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_post_belongs_to_post()
    {
        $post = Post::factory()->create(['description' => 'Test Post']);
        $category = Category::factory()->create();

        $categoryPost = CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Post::class, $categoryPost->post);
        $this->assertEquals('Test Post', $categoryPost->post->description);
    }

    public function test_category_post_belongs_to_category()
    {
        $post = Post::factory()->create();
        $category = Category::factory()->create(['name' => 'Test Category']);

        $categoryPost = CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $categoryPost->category);
        $this->assertEquals('Test Category', $categoryPost->category->name);
    }

    public function test_category_post_foreign_keys()
    {
        $post = Post::factory()->create();
        $category = Category::factory()->create();

        $categoryPost = CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        $this->assertEquals($post->id, $categoryPost->post_id);
        $this->assertEquals($category->id, $categoryPost->category_id);
    }

    public function test_category_post_relationship_with_soft_deleted_models()
    {
        $post = Post::factory()->create(['description' => 'Test Post']);
        $category = Category::factory()->create(['name' => 'Test Category']);

        $categoryPost = CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        // Soft delete the post and category
        $post->delete();
        $category->delete();

        // Should still be able to access the relationships
        $this->assertInstanceOf(Post::class, $categoryPost->post);
        $this->assertInstanceOf(Category::class, $categoryPost->category);
        $this->assertEquals('Test Post', $categoryPost->post->description);
        $this->assertEquals('Test Category', $categoryPost->category->name);
    }
}