<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegleFidelite extends Model
{
    protected $table = 'regles_fidelite';

    protected $fillable = [
        'libelle',
        'montant_par_tranche',
        'points_par_tranche',
        'date_debut',
        'date_fin',
        'is_active',
        'enregistre_par',
    ];

    protected function casts(): array
    {
        return [
            'montant_par_tranche' => 'integer',
            'points_par_tranche' => 'integer',
            'date_debut' => 'date',
            'date_fin' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    public function scopeActives($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEnVigueur($query)
    {
        return $query
            ->where('is_active', true)
            ->whereDate('date_debut', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', now()->toDateString());
            });
    }
}
