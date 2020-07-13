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

class CommentDeleteOrRestoreTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDeleteIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = $comment->create_user;

        $response = $this->actingAs($user)
            ->delete(route('comments.destroy', ['comment' => $comment]))
            ->assertRedirect(route('comments.go_to', $comment))
            ->assertSessionHas(['success' => __('The comment was successfully deleted')]);

        $comment->refresh();

        $this->assertTrue($comment->trashed());

        $this->assertEquals(0, $comment->review->children_count);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRestoreIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $comment->delete();

        $user = $comment->create_user;

        $response = $this->actingAs($user)
            ->delete(route('comments.destroy', ['comment' => $comment]))
            ->assertRedirect(route('comments.go_to', $comment))
            ->assertSessionHas(['success' => __('The comment was successfully restored')]);

        $comment->refresh();

        $this->assertFalse($comment->trashed());

        $this->assertEquals(1, $comment->review->children_count);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDeleteThroughAjax()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = $comment->create_user;

        $response = $this->actingAs($user)
            ->delete(route('comments.destroy', ['comment' => $comment]), [],
                ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $comment->refresh();

        $this->assertTrue($comment->trashed());
        $this->assertNotNull($response->decodeResponseJson()['deleted_at']);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRestoreThroughAjax()
    {
        $comment = factory(Comment::class)
            ->create();

        $comment->delete();

        $user = $comment->create_user;

        $response = $this->actingAs($user)
            ->delete(route('comments.destroy', ['comment' => $comment]), [],
                ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $comment->refresh();

        $this->assertFalse($comment->trashed());
        $this->assertNull($response->decodeResponseJson()['deleted_at']);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDeleteReply()
    {
        $comment = factory(Comment::class)
            ->states('with_reply')
            ->create()
            ->fresh();

        $this->assertEquals(1, $comment->children_count);

        $reply = Comment::descendants($comment->id)->first();

        $user = $reply->create_user;

        $response = $this->actingAs($user)
            ->delete(route('comments.destroy', ['comment' => $reply]))
            ->assertRedirect(route('comments.go_to', $reply));

        $reply->refresh();
        $comment->refresh();

        $this->assertTrue($reply->trashed());

        $this->assertEquals(0, $comment->children_count);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRestoreReply()
    {
        $comment = factory(Comment::class)
            ->states('with_reply')
            ->create()
            ->fresh();

        $this->assertEquals(1, $comment->children_count);

        $reply = Comment::descendants($comment->id)->first();

        $reply->delete();

        $user = $reply->create_user;

        $response = $this->actingAs($user)
            ->delete(route('comments.destroy', ['comment' => $reply]))
            ->assertRedirect(route('comments.go_to', $reply));

        $reply->refresh();
        $comment->refresh();

        $this->assertFalse($reply->trashed());

        $this->assertEquals(1, $comment->children_count);
    }
}
