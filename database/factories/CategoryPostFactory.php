<?php

namespace Database\Factories;

use App\Models\CategoryPost;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryPostFactory extends Factory
{
    protected $model = CategoryPost::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'post_id' => Post::factory(),
        ];
    }
}