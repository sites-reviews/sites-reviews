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

class CommentEditTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEditIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = $comment->create_user;

        $this->actingAs($user)
            ->get(route('comments.edit', ['comment' => $comment]))
            ->assertOk()
            ->assertViewHas('comment', $comment);
    }

    public function testUpdateIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = $comment->create_user;

        $commentNew = factory(Comment::class)
            ->make();

        $response = $this->actingAs($user)
            ->patch(route('comments.update', ['comment' => $comment]), [
                'text' => $commentNew->text
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas(['success' => __('The comment was edited successfully')]);

        $comment->refresh();

        $this->assertEquals($commentNew->text, $comment->text);
    }

    public function testUpdateWithAjaxIsOk()
    {
        $comment = factory(Comment::class)
            ->create();

        $user = $comment->create_user;

        $commentNew = factory(Comment::class)
            ->make();

        $response = $this->actingAs($user)
            ->patch(route('comments.update', ['comment' => $comment]), [
                'text' => $commentNew->text
            ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $comment->refresh();

        $this->assertEquals($commentNew->text, $comment->text);

        $response->assertJson($comment->toArray());
    }
}
