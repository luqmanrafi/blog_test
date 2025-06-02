<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Teknologi',
            'Tutorial',
            'Berita',
            'Olahraga',
            'Kesehatan',
            'Bisnis',
            'Hiburan',
            'Travel',
            'Kuliner',
            'Edukasi',
            'Sains',
            'Otomotif',
            'Fashion',
            'Properti',
            'Finansial'
        ];
        return [
            'category_name'=>$this->faker->unique()->randomElement($categories),
        ];
    }
}
