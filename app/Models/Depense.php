<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    protected $fillable = [
        'categorie_depense_id', 'montant', 'date_depense',
        'description', 'justificatif_path', 'enregistre_par',
        'annule_le', 'annule_par', 'motif_annulation',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'integer',
            'date_depense' => 'date',
            'annule_le' => 'datetime',
        ];
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_depense_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    public function annulePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annule_par');
    }

    public function estAnnulee(): bool
    {
        return $this->annule_le !== null;
    }

    public function scopeValides(Builder $query): void
    {
        $query->whereNull('annule_le');
    }
}