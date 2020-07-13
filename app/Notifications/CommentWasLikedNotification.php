<?php

namespace App\Notifications;

use App\CommentRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentWasLikedNotification extends Notification
{
    use Queueable;

    public $rating;

    /**
     * Create a new notification instance.
     *
     * @param CommentRating $rating
     * @return void
     */
    public function __construct(CommentRating $rating)
    {
        $this->rating = $rating;
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

        if ($this->rating->rating > 0)
        {
            if ($notifiable->notificationSetting->db_when_comment_was_liked)
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
            ->subject(__('Someone liked your comment'))
            ->line(__(':userName liked your comment', ['userName' => $this->rating->create_user->name]))
            ->action(__('Go to the comment'), $this->rating->rateable->getRedirectToUrl());
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
            'title' => __('Someone liked your comment'),
            'description' => __(':userName liked your comment', ['userName' => $this->rating->create_user->name]),
            'url' => $this->rating->rateable->getRedirectToUrl()
        ];
    }
}
