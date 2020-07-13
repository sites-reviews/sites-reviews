<?php

namespace App\Notifications;

use App\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewResponseToYourCommentNotification extends Notification
{
    use Queueable;

    public $comment;

    /**
     * Create a new notification instance.
     *
     * @param Comment $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) :array
    {
        $array = [];

        if ($notifiable->notificationSetting->email_response_to_my_comment)
            array_push($array, 'mail');

        if ($notifiable->notificationSetting->db_response_to_my_comment)
            array_push($array, 'database');

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
            ->subject(__('New response to your comment'))
            ->line(__(':userName responded to your comment', ['userName' => $this->comment->create_user->name]))
            ->action(__('Go to response'), route('comments.go_to', ['comment' => $this->comment]));
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
            'title' => __('New response to your comment'),
            'description' => __(':userName responded to your comment', ['userName' => $this->comment->create_user->name]),
            'url' => route('comments.go_to', ['comment' => $this->comment])
        ];
    }
}
