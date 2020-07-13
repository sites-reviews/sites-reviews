<?php

use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sites = factory(\App\Site::class, 20)
            ->states('with_preview')
            ->create()
            ->each(function ($site) {

                for ($i = 0; $i < 5; $i++)
                {
                    $review = factory(\App\Review::class)
                        ->create(['site_id' => $site->id]);
                }
            });

        $sites = factory(\App\Site::class, 3)
            ->states('with_preview')
            ->create()
            ->each(function ($site) {

                for ($i = 0; $i < 2; $i++)
                {
                    $review = factory(\App\Review::class)
                        ->create(['site_id' => $site->id]);

                    $comment = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id]);

                    $subComment = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id, 'parent' => $comment]);

                    $subComment2 = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id, 'parent' => $subComment]);

                    $comment = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id]);

                    $subComment = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id, 'parent' => $comment]);

                    $subComment2 = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id, 'parent' => $subComment]);

                    $comment = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id]);

                    $subComment = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id, 'parent' => $comment]);

                    $subComment2 = factory(\App\Comment::class)
                        ->create(['review_id' => $review->id, 'parent' => $subComment]);
                }
            });

        for ($rating = 1; $rating <= 5; ($rating + 0))
        {
            $sites = factory(\App\Site::class)
                ->states('with_preview')
                ->create(['rating' => $rating]);

            $rating = $rating + 0.5;
        }
    }
}
