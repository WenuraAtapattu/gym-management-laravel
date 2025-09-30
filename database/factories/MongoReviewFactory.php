<?php

namespace Database\Factories;

use App\Models\MongoReview;
use App\Models\MongoProduct;
use App\Models\MongoUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class MongoReviewFactory extends Factory
{
    protected $model = MongoReview::class;

    public function definition()
    {
        return [
            'user_id' => MongoUser::factory(),
            'reviewable_id' => MongoProduct::factory(),
            'reviewable_type' => 'App\\Models\\MongoProduct',
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'guest_name' => $this->faker->optional()->name,
            'guest_email' => $this->faker->optional()->safeEmail,
        ];
    }

    public function approved()
    {
        return $this->state(['status' => 'approved']);
    }

    public function pending()
    {
        return $this->state(['status' => 'pending']);
    }

    public function guest()
    {
        return $this->state([
            'user_id' => null,
            'guest_name' => $this->faker->name,
            'guest_email' => $this->faker->safeEmail,
        ]);
    }
}