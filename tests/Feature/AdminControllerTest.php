<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\CategoryPost;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users()
    {
        $admin = User::factory()->create();
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.users'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('all_users');
    }

    public function test_admin_can_deactivate_user()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.deactivate', $user->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_can_activate_user()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $user->delete(); // Soft delete the user

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.activate', $user->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_admin_can_view_posts()
    {
        $admin = User::factory()->create();
        $posts = Post::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.posts'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.posts.index');
        $response->assertViewHas('all_posts');
    }

    public function test_admin_can_deactivate_post()
    {
        $admin = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.posts.deactivate', $post->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_admin_can_activate_post()
    {
        $admin = User::factory()->create();
        $post = Post::factory()->create();
        $post->delete(); // Soft delete the post

        $response = $this->actingAs($admin)
            ->patch(route('admin.posts.activate', $post->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_admin_can_view_categories()
    {
        $admin = User::factory()->create();
        $categories = Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.categories'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('all_categories');
        $response->assertViewHas('unrecognizedCount');
    }

    public function test_admin_can_create_category()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'New Category',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
        ]);
    }

    public function test_admin_can_update_category()
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->patch(route('admin.categories.update', $category->id), [
                'name' => 'Updated Category',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    public function test_admin_can_delete_category()
    {
        $admin = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_unrecognized_posts_count()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        // Create post without categories
        $postWithoutCategories = Post::factory()->create(['user_id' => $user->id]);

        // Create post with categories
        $postWithCategories = Post::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        CategoryPost::create([
            'post_id' => $postWithCategories->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.categories'));

        $response->assertStatus(200);
        $response->assertViewHas('unrecognizedCount', 1);
    }
}