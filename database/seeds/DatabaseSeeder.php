<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SiteSeeder::class);

        $user = factory(\App\User::class)
            ->create([
                'name' => 'test',
                'email' => 'test@testsdf.test',
                'password' => 'test'
            ]);

        $user->notify(new \App\Notifications\WelcomeNotification($user));

        $review = factory(\App\Review::class)
            ->create([
                'create_user_id' => $user->id,
            ]);

        $rating = factory(\App\ReviewRating::class)
            ->create([
                'rateable_id' => $review->id,
            ]);

        $comment = factory(\App\Comment::class)
            ->create(['review_id' => $review->id]);
    }
}
