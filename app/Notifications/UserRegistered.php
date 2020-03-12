<?php

namespace App\Notifications;

use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class UserRegistered extends Notification
{
    use Queueable;

    private $firstName;
    private $username;
    private $password;
    private $url;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $password
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->firstName = $user->first_name;
        $this->username = $user->username;
        $this->password = $password;

        $this->url = url('/login');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Создан новый аккаунт в iOcto')
                    ->greeting("Здравствуйте, {$this->firstName}!")
                    ->line('Спасибо за регистрацию на портале iOcto.')
                    ->line([
                        "Ваш логин: <b>{$this->username}</b><br>", 
                        "Ваш пароль: <b>{$this->password}</b>"
                    ])
                    ->action('Войдите в кабинет', $this->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
