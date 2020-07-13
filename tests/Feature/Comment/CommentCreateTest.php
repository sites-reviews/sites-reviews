<?php

namespace Tests\Feature\Comment;

use App\Notifications\NewResponseToReviewNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentCreateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateRouteIsOk()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('reviews.comments.create', ['review' => $review]))
            ->assertOk();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStoreIsOk()
    {
        Notification::fake();

        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $commentNew = factory(Comment::class)
            ->make();

        $this->actingAs($user)
            ->post(route('reviews.comments.store', ['review' => $review]), [
                'text' => $commentNew->text
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment = $review->comments()->first();

        $this->assertNotNull($comment);
        $this->assertEquals($commentNew->text, $comment->text);
        $this->assertTrue($user->is($comment->create_user));
        $this->assertEquals($review->id, $comment->review_id);
        $this->assertEquals(0, $comment->level);

        $review->refresh();

        $this->assertEquals(1, $review->children_count);

        $notifiable = $review->create_user;

        Notification::assertSentTo(
            $notifiable,
            NewResponseToReviewNotification::class,
            function ($notification, $channels) use ($comment, $notifiable) {
                $this->assertContains('mail', $channels);
                $this->assertContains('database', $channels);

                $data = $notification->toArray($notifiable);

                $this->assertEquals(__('New response to your review'), $data['title']);
                $this->assertEquals(__(':userName responded to your review', ['userName' => $comment->create_user->name]), $data['description']);
                $this->assertEquals(route('comments.go_to', ['comment' => $comment]), $data['url']);

                return $notification->comment->id == $comment->id;
            }
        );
    }

    public function testAjaxStore()
    {
        $review = factory(Review::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $commentNew = factory(Comment::class)
            ->make();

        $response = $this->actingAs($user)
            ->post(route('reviews.comments.store', ['review' => $review]), [
                'text' => $commentNew->text
            ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment = $review->comments()->first();

        $this->assertNotNull($comment);
        $response->assertJson($comment->toArray());
    }
}
