<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $prenom
 * @property string $nom
 * @property string $public
 * @property string $telephone
 * @property string|null $whatsapp
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $date_naissance
 * @property \Illuminate\Support\Carbon|null $date_inscription
 * @property string $canal_prefere
 * @property bool $consent_promotions
 * @property \Illuminate\Support\Carbon|null $consent_at
 * @property int|null $consent_par
 * @property string|null $notes
 * @property string $statut
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property int|null $archived_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */

class Client extends Model
{
    protected $fillable = [
        'prenom', 'nom', 'public', 'telephone', 'whatsapp', 'email',
        'date_naissance', 'date_inscription',
        'canal_prefere', 'consent_promotions', 'consent_at', 'consent_par',
        'notes', 'statut', 'archived_at', 'archived_by',
    ];

    protected $casts = [
        'date_naissance'     => 'date',
        'date_inscription'   => 'date',
        'consent_promotions' => 'boolean',
        'consent_at'         => 'datetime',
        'archived_at'        => 'datetime',
    ];

    // Garde-fou : on ne supprime jamais un client ayant un historique financier.
    // Décommentez quand le modèle Paiement et la relation paiements() existeront.
    protected static function booted(): void
    {
        static::deleting(function (Client $client) {
            // if ($client->paiements()->exists()) {
            //     throw new \RuntimeException('Suppression interdite : historique financier. Archivez ce client.');
            // }
        });
    }

    public function getCodeAttribute(): string
{
    return 'CL-' . str_pad((string) $this->getKey(), 6, '0', STR_PAD_LEFT);
}
    // public function paiements() { return $this->hasMany(Paiement::class); }
}
