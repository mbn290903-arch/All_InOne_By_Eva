<?php

namespace App\Support;

class WhatsApp
{
    /** Lien WhatsApp avec message prérempli (null si pas de numéro). */
    public static function lien(?string $numero, string $message): ?string
    {
        $chiffres = preg_replace('/\D/', '', (string) $numero);

        if ($chiffres === '') {
            return null;
        }

        if (strlen($chiffres) === 9) {
            $chiffres = '221' . $chiffres; // indicatif du Sénégal
        }

        return 'https://wa.me/' . $chiffres . '?text=' . rawurlencode($message);
    }
}