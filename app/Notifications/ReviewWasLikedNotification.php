<?php

namespace App\Notifications;

use App\ReviewRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewWasLikedNotification extends Notification
{
    use Queueable;

    public $reviewVote;

    /**
     * Create a new notification instance.
     *
     * @param ReviewRating $reviewVote
     * @return void
     */
    public function __construct(ReviewRating $reviewVote)
    {
        $this->reviewVote = $reviewVote;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $array = [];

        if ($this->reviewVote->rating > 0)
        {
            if ($notifiable->notificationSetting->db_when_review_was_liked)
                array_push($array, 'database');

        }

        return $array;
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
            ->subject(__('Someone liked your review'))
            ->line(__(':userName liked your review', ['userName' => $this->reviewVote->create_user->name]))
            ->action(__('Go to the review'), route('reviews.go_to'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => __('Someone liked your review'),
            'description' => __(':userName liked your review', ['userName' => $this->reviewVote->create_user->name]),
            'url' => route('reviews.go_to', $this->reviewVote->rateable)
        ];
    }
}
