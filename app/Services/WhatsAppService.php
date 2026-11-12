<?php

namespace App\Services;

use App\Models\ModeleMessage;
use Illuminate\Support\Str;

/**
 * WhatsApp sans API : numéros normalisés, liens wa.me, modèles de messages.
 * Rien n'est envoyé automatiquement : on prépare seulement un lien que l'utilisateur ouvre.
 */
class WhatsAppService
{
    /** Textes d'origine (cahier des charges), utilisés si un modèle est vide ou réinitialisé. */
    public const DEFAUTS = [
        'anniversaire' => "Bonjour [Prénom] ! Toute l'équipe d'All in One by Eva te souhaite un merveilleux anniversaire. Pour célébrer cette journée spéciale, nous serions ravis de t'accueillir à nouveau au salon. À très bientôt !",
        'promotion'    => "Bonjour [Prénom] ! All in One by Eva a une nouvelle offre beauté à te faire découvrir. Contacte-nous pour connaître les détails et réserver ton prochain moment de bien-être.",
        'rappel_rdv'   => "Bonjour [Prénom] ! Nous te rappelons ton rendez-vous chez All in One by Eva prévu le [date] à [heure] pour [prestation]. Nous avons hâte de t'accueillir.",
        'fidelisation' => "Bonjour [Prénom] ! Cela fait quelque temps que nous ne t'avons pas accueilli chez All in One by Eva. Nous serions heureux de te retrouver pour un nouveau moment de détente et de beauté.",
    ];

    /**
     * Numéro au format international WhatsApp (sans +), ou null s'il n'est pas valide.
     * Accepte : 77 654 21 08 · 776542108 · +221 77 654 21 08 · 221776542108 · 00221776542108
     * L'indicatif 221 n'est jamais ajouté en double.
     */
    public static function normaliser(?string $numero): ?string
    {
        $chiffres = preg_replace('/\D/', '', (string) $numero);

        if ($chiffres === '') {
            return null;
        }
        if (str_starts_with($chiffres, '00')) {
            $chiffres = substr($chiffres, 2);
        }

        if (str_starts_with($chiffres, '221') && strlen($chiffres) === 12) {
            $national = substr($chiffres, 3);
        } elseif (strlen($chiffres) === 9) {
            $national = $chiffres;
        } else {
            return null;
        }

        // Mobiles sénégalais : 70, 75, 76, 77, 78
        return preg_match('/^7[05678]\d{7}$/', $national) ? '221' . $national : null;
    }

    /** Lien https://wa.me/ avec message encodé (UTF-8), ou null si le numéro n'est pas valide. */
    public static function lien(?string $numero, string $message): ?string
    {
        $n = self::normaliser($numero);

        return $n ? 'https://wa.me/' . $n . '?text=' . rawurlencode($message) : null;
    }

    /** Texte du modèle (base de données, sinon texte d'origine). */
    public static function gabarit(string $type): string
    {
        $contenu = ModeleMessage::where('type', $type)->value('contenu');

        return filled($contenu) ? $contenu : (self::DEFAUTS[$type] ?? '');
    }

    /**
     * Remplace les variables [Prénom], [date], {prenom}… (accents et majuscules ignorés).
     * Une variable inconnue reste visible, pour que l'utilisateur la voie et la corrige.
     */
    public static function rendre(string $gabarit, array $valeurs): string
    {
        $table = [];
        foreach ($valeurs as $cle => $valeur) {
            $table[self::cle($cle)] = (string) $valeur;
        }

        $texte = preg_replace_callback('/\[\s*([^\[\]]+?)\s*\]|\{\s*([^{}]+?)\s*\}/u', function ($m) use ($table) {
            $nom = ($m[1] ?? '') !== '' ? $m[1] : ($m[2] ?? '');
            $cle = self::cle($nom);

            return array_key_exists($cle, $table) ? $table[$cle] : $m[0];
        }, $gabarit);

        return trim(preg_replace('/[ \t]{2,}/', ' ', $texte));
    }

    public static function contientVariable(string $gabarit, string $nom): bool
    {
        $g = mb_strtolower(Str::ascii($gabarit));

        return str_contains($g, '[' . $nom . ']') || str_contains($g, '{' . $nom . '}');
    }

    /** Message d'anniversaire. L'offre est insérée via [offre], ou ajoutée à la fin si le modèle n'a pas cette variable. */
    public static function anniversaire(string $prenom, ?string $offre): string
    {
        $gabarit = self::gabarit('anniversaire');
        $texte   = self::rendre($gabarit, ['prenom' => $prenom, 'offre' => $offre ?? '']);

        if ($offre && ! self::contientVariable($gabarit, 'offre')) {
            $texte .= ' Pour fêter ça, nous avons une attention pour vous : ' . rtrim($offre, ' .') . '.';
        }

        return $texte;
    }

    private static function cle(string $nom): string
    {
        return Str::of($nom)->ascii()->lower()->trim()->toString();
    }
}
