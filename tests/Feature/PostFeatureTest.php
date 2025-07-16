<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class PostFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_post()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->count(2)->create(['user_id' => $user->id]);
        $image = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($user)
            ->post(route('post.store'), [
                'category' => $category->pluck('id')->toArray(),
                'description' => 'テスト投稿本文',
                'image' => $image,
            ]);

        $response->assertRedirect(route('index'));
        $this->assertDatabaseHas('posts', [
            'description' => 'テスト投稿本文',
            'user_id' => $user->id,
        ]);
    }

    public function test_post_store_validation_error()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->post(route('post.store'), [
                'category' => [],
                'description' => '',
                'image' => null,
            ]);
        $response->assertSessionHasErrors(['category', 'description', 'image']);
    }
}