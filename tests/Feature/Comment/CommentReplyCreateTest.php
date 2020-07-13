<?php

namespace Tests\Feature\Comment;

use App\Notifications\NewResponseToReviewNotification;
use App\Notifications\NewResponseToYourCommentNotification;
use App\Review;
use App\Comment;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentReplyCreateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateRouteIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('comments.replies.create', ['comment' => $comment]))
            ->assertOk();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStoreIsOk()
    {
        $parentComment = factory(Comment::class)
            ->create();

        $review = $parentComment->review;

        $user = factory(User::class)
            ->create();

        $replyNew = factory(Comment::class)
            ->make();

        Notification::fake();

        $response = $this->actingAs($user)
            ->post(route('comments.replies.store', ['comment' => $parentComment]), [
                'text' => $replyNew->text
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment = Comment::descendants($parentComment->id)->first();

        $this->assertNotNull($comment);
        $this->assertNotNull($comment->text);
        $this->assertEquals($replyNew->text, $comment->text);
        $this->assertTrue($user->is($comment->create_user));
        $this->assertEquals(1, $comment->level);
        $this->assertTrue($parentComment->is($comment->getParent()));

        $response->assertRedirect(route('comments.go_to', $comment));

        $parentComment->refresh();

        $this->assertEquals(1, $parentComment->children_count);

        $review->refresh();

        $this->assertEquals(1, $review->children_count);

        $notifiable = $parentComment->create_user;

        Notification::assertSentTo(
            $notifiable,
            NewResponseToYourCommentNotification::class,
            function ($notification, $channels) use ($comment, $notifiable) {
                $this->assertContains('mail', $channels);
                $this->assertContains('database', $channels);

                $data = $notification->toArray($notifiable);

                $this->assertEquals(__('New response to your comment'), $data['title']);
                $this->assertEquals(__(':userName responded to your comment', ['userName' => $comment->create_user->name]), $data['description']);
                $this->assertEquals(route('comments.go_to', ['comment' => $comment]), $data['url']);

                return $notification->comment->id == $comment->id;
            }
        );

        Notification::assertNotSentTo($review->create_user, NewResponseToYourCommentNotification::class);
        Notification::assertNotSentTo($review->create_user, NewResponseToReviewNotification::class);
    }

    public function testStoreJsonIsOk()
    {
        $parentComment = factory(Comment::class)
            ->create();

        $user = factory(User::class)
            ->create();

        $replyNew = factory(Comment::class)
            ->make();

        $response = $this->actingAs($user)
            ->post(route('comments.replies.store', ['comment' => $parentComment]), [
                'text' => $replyNew->text
            ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment = Comment::descendants($parentComment->id)->first();

        $this->assertNotNull($comment);
        $response->assertJson($comment->toArray());
    }
}
