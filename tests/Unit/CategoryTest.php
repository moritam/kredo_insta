<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Post;
use App\Models\CategoryPost;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_category_posts()
    {
        $category = Category::factory()->create();
        $categoryPosts = CategoryPost::factory()->count(2)->create(['category_id' => $category->id]);
        $this->assertCount(2, $category->categoryPost);
    }

    public function test_category_can_access_posts()
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create();

        CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(1, $category->categoryPost);
        $this->assertEquals($post->id, $category->categoryPost->first()->post->id);
    }

    public function test_category_soft_deletes()
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $categoryId]);
        $this->assertDatabaseHas('categories', ['id' => $categoryId]);
    }

    public function test_category_can_be_restored()
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $category->delete();
        $category->restore();

        $this->assertDatabaseHas('categories', ['id' => $categoryId]);
        $this->assertNull($category->deleted_at);
    }

    public function test_category_deletion_removes_category_posts()
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create();

        CategoryPost::create([
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);

        $category->delete();

        // CategoryPost records should be deleted
        $this->assertDatabaseMissing('category_posts', [
            'category_id' => $category->id,
        ]);
    }

    public function test_category_with_multiple_posts()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        CategoryPost::create([
            'post_id' => $post1->id,
            'category_id' => $category->id,
        ]);
        CategoryPost::create([
            'post_id' => $post2->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(2, $category->categoryPost);
        $this->assertEquals($post1->id, $category->categoryPost->first()->post->id);
        $this->assertEquals($post2->id, $category->categoryPost->last()->post->id);
    }

    public function test_category_post_count()
    {
        $category = Category::factory()->create();
        $posts = Post::factory()->count(3)->create();

        foreach ($posts as $post) {
            CategoryPost::create([
                'post_id' => $post->id,
                'category_id' => $category->id,
            ]);
        }

        $this->assertEquals(3, $category->categoryPost->count());
    }

    public function test_category_with_no_posts()
    {
        $category = Category::factory()->create();

        $this->assertCount(0, $category->categoryPost);
    }
}