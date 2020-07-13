<?php

namespace Tests\Feature\Comment;

use App\Notifications\CommentWasLikedNotification;
use App\Notifications\NewResponseToReviewNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentRateUpAndDownTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUp()
    {
        Notification::fake();

        $comment = factory(Comment::class)
            ->create();

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('comments.rate.up', $comment), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonFragment(['rateable' => ['rating' => 1]]);

        $comment->refresh();

        $rating = $comment->ratings()->first();

        $this->assertNotNull($rating);
        $this->assertEquals(1, $rating->rating);
        $this->assertTrue($user->is($rating->create_user));

        $this->assertEquals(1, $comment->rate_up);
        $this->assertEquals(0, $comment->rate_down);
        $this->assertEquals(1, $comment->rating);

        $notifiable = $comment->create_user;

        \Notification::assertSentTo($notifiable,
            CommentWasLikedNotification::class,
            function ($notification, $channels) use ($rating, $notifiable) {

                $this->assertContains('database', $channels);

                return $notification->rating->id == $rating->id;
            }
        );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDown()
    {
        Notification::fake();

        $comment = factory(Comment::class)
            ->create();

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('comments.rate.down', $comment), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonFragment(['rateable' => ['rating' => -1]]);

        $comment->refresh();

        $rating = $comment->ratings()->first();

        $this->assertNotNull($rating);
        $this->assertEquals(-1, $rating->rating);
        $this->assertTrue($user->is($rating->create_user));

        $this->assertEquals(0, $comment->rate_up);
        $this->assertEquals(1, $comment->rate_down);
        $this->assertEquals(-1, $comment->rating);

        $notifiable = $comment->create_user;

        \Notification::assertSentTo($notifiable,
            CommentWasLikedNotification::class,
            function ($notification, $channels) use ($rating, $notifiable) {

                $this->assertEquals([], $channels);

                return $notification->rating->id == $rating->id;
            }
        );
    }
}
