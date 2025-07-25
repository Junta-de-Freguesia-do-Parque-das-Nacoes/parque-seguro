<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject(__('auth.reset_password_notification')) // Notificação de Redefinição de Senha
        ->greeting(__('auth.hello')) // Olá!
        ->line(__('auth.received_email_because')) // Mensagem informativa
        ->action(__('auth.reset_password'), $this->resetUrl($notifiable)) // Botão de redefinição
        ->line(__('auth.password_reset_expiry', [
            'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
        ])) // Tempo de expiração do link
        ->line(__('auth.no_action_needed')) // Caso não tenha solicitado
        ->salutation(__('auth.Cumprimentos') . "\n" . __('auth.team')) // Cumprimentos + PARQUE-SEGURO
        ->line(__('auth.trouble_clicking', ['actionText' => __('auth.reset_password')])); // Mensagem de erro ao clicar
}

    protected function resetUrl($notifiable)
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
