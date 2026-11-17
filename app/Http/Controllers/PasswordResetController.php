<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /** Formulaire « Mot de passe oublié ». */
    public function demande()
    {
        return view('auth.forgot-password');
    }

    /** Envoi du lien par e-mail. */
    public function envoyerLien(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Saisissez votre adresse e-mail.',
            'email.email' => "Cette adresse e-mail n'est pas valide.",
        ]);

        // Seuls les comptes actifs peuvent recevoir un lien
        $statut = Password::sendResetLink([
            'email' => $request->email,
            'is_active' => true,
        ]);

        if ($statut === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Veuillez patienter une minute avant de refaire une demande.']);
        }

        // Même message dans tous les autres cas : on ne révèle pas si le compte existe
        return back()->with(
            'success',
            "Si cette adresse correspond à un compte actif, un lien de réinitialisation vient d'être envoyé. "
            . 'Pensez à vérifier vos courriers indésirables.'
        );
    }

    /** Formulaire de nouveau mot de passe (ouvert depuis l'e-mail). */
    public function formulaire(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /** Enregistrement du nouveau mot de passe. */
    public function reinitialiser(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ], [
            'email.required' => 'Saisissez votre adresse e-mail.',
            'email.email' => "Cette adresse e-mail n'est pas valide.",
            'password.required' => 'Saisissez un nouveau mot de passe.',
            'password.confirmed' => 'Les deux mots de passe ne sont pas identiques.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
        ]);

        $statut = Password::reset(
            [
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
                'is_active' => true,
            ],
            function (User $user, string $motDePasse) {
                $user->forceFill([
                    'password' => Hash::make($motDePasse),
                    'remember_token' => Str::random(60),
                ])->save();

                // Ferme les sessions déjà ouvertes avec l'ancien mot de passe
                DB::table('sessions')->where('user_id', $user->id)->delete();

                event(new PasswordReset($user));
            }
        );

        if ($statut === Password::PASSWORD_RESET) {
            return redirect()->route('login.show')
                ->with('success', 'Votre mot de passe a été modifié. Vous pouvez maintenant vous connecter.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Ce lien est invalide ou a expiré. Refaites une demande de réinitialisation.']);
    }
}