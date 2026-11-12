<?php

namespace App\Models;

use App\Enums\TypeMouvementPoints;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MouvementPoints extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'mouvements_points';

    protected $fillable = [
        'client_id',
        'paiement_id',
        'regle_fidelite_id',
        'mouvement_origine_id',
        'montant_reference',
        'montant_par_tranche_snapshot',
        'points_par_tranche_snapshot',
        'type',
        'points',
        'motif',
        'enregistre_par',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeMouvementPoints::class,
            'points' => 'integer',
            'montant_reference' => 'integer',
            'montant_par_tranche_snapshot' => 'integer',
            'points_par_tranche_snapshot' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    public function regleFidelite(): BelongsTo
    {
        return $this->belongsTo(RegleFidelite::class, 'regle_fidelite_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    public function mouvementOrigine(): BelongsTo
    {
        return $this->belongsTo(self::class, 'mouvement_origine_id');
    }

    public function annulation(): HasOne
    {
        return $this->hasOne(self::class, 'mouvement_origine_id');
    }
}