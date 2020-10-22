<?php

namespace App\Notifications;

use App\CommentRating;
use App\Review;
use App\TempReview;
use App\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmationOfCreatingReviewNotification extends Notification
{
    use Queueable;

    public $review;

    /**
     * Create a new notification instance.
     *
     * @param Review $review
     * @return void
     */
    public function __construct(TempReview $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Confirmation of the review publication'))
            ->line(__('Please click on the button to publish a review'))
            ->action(__('Publish the review'), route('reviews.confirm', [
                'uuid' => $this->review->uuid,
                'token' =>  $this->review->token
            ]));
    }
}
