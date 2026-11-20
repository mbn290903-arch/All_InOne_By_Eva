<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            return $user->isAdmin() ? true : null;
        });
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            $minutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Vous avez demandé à réinitialiser le mot de passe de votre compte All in One by Eva.')
                ->action('Choisir un nouveau mot de passe', $url)
                ->line("Ce lien est valable {$minutes} minutes et ne peut être utilisé qu'une fois.")
                ->line("Si vous n'êtes pas à l'origine de cette demande, ignorez ce message : votre mot de passe ne sera pas modifié.")
                ->salutation("L'équipe All in One by Eva");
        });
    }
}
