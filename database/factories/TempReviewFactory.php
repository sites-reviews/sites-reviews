<?php

namespace Database\Factories;

use App\Site;
use App\TempReview;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TempReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TempReview::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'site_id' => function () {
                return factory(Site::class)
                    ->create()
                    ->id;
            },
            'advantages' => $this->faker->realText(100),
            'disadvantages' => $this->faker->realText(100),
            'comment' => $this->faker->realText(100),
            'rate' => rand(1, 5),
            'email' => mb_strtolower(Str::random(8)).$this->faker->unique()->safeEmail,
            'token' => Str::random(20)
        ];
    }
}
