<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    protected $fillable = ['nom', 'categorie', 'quantite', 'unite', 'prix_achat', 'prix_vente', 'seuil_alerte', 'fournisseur', 'date_entree'];

    /**
     * Statuts et styles d'affichage.
     * Les 4 premiers sont utilisés par le module Stock ; les 3 derniers sont conservés
     * pour compatibilité avec le code existant.
     */
    public const STATUTS = [
        'epuise'         => ['label' => 'Épuisé',         'classes' => 'bg-red-50 text-red-700'],
        'presque_epuise' => ['label' => 'Presque épuisé', 'classes' => 'bg-orange-50 text-orange-700'],
        'faible'         => ['label' => 'Stock faible',   'classes' => 'bg-[#fff2df] text-[#b77926]'],
        'ok'             => ['label' => 'Disponible',     'classes' => 'bg-emerald-50 text-emerald-700'],

        'en_stock' => ['label' => 'En stock',  'classes' => 'bg-green-100 text-green-800'],
        'alerte'   => ['label' => 'Stock bas', 'classes' => 'bg-yellow-100 text-yellow-800'],
        'rupture'  => ['label' => 'Rupture',   'classes' => 'bg-red-100 text-red-800'],
    ];

    protected function casts(): array
    {
        return [
            'quantite'     => 'decimal:2',
            'seuil_alerte' => 'decimal:2',
            'prix_achat'   => 'integer',
            'prix_vente'   => 'integer',
            'date_entree'  => 'date',
        ];
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class)->latest('created_at');
    }

    public function estEnAlerte(): bool
    {
        return (float) $this->quantite <= (float) $this->seuil_alerte;
    }

    /** Statut calculé : epuise, presque_epuise, faible ou ok. */
    public function getStatutAttribute(): string
    {
        $quantite = (float) $this->quantite;
        $seuil = (float) $this->seuil_alerte;
        $ratio = config('stock.ratio_presque_epuise', 0.5);

        if ($quantite <= 0) {
            return 'epuise';
        }
        if ($seuil > 0 && $quantite <= $seuil * $ratio) {
            return 'presque_epuise';
        }
        if ($quantite <= $seuil) {
            return 'faible';
        }
        return 'ok';
    }

    // --- Scopes ---

    public function scopeStocksFaibles(Builder $query): void
    {
        $query->whereColumn('quantite', '<=', 'seuil_alerte');
    }

    /** Alias utilisé par le tableau de bord. */
    public function scopeEnAlerte(Builder $query): void
    {
        $query->whereColumn('quantite', '<=', 'seuil_alerte');
    }

    /** Presque épuisé = quantité > 0 et <= 50 % du seuil (ratio réglable dans config/stock.php). */
    public function scopePresqueEpuises(Builder $query): void
    {
        $ratio = config('stock.ratio_presque_epuise', 0.5);

        $query->where('quantite', '>', 0)
              ->where('seuil_alerte', '>', 0)
              ->whereRaw('quantite <= seuil_alerte * ?', [$ratio]);
    }

    public function scopeEpuises(Builder $query): void
    {
        $query->where('quantite', '<=', 0);
    }
}