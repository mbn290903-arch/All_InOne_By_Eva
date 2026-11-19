<?php
namespace App\Models;

use App\Enums\TypeMouvementStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'mouvements_stock';

    protected $fillable = ['produit_id', 'type', 'quantite', 'stock_apres', 'motif', 'rendez_vous_id', 'enregistre_par'];

    /**
     * Définition des motifs de mouvements de stock par type.
     */
    public const MOTIFS = [
        'entree' => [
            'stock_initial' => 'Stock initial',
            'achat_fournisseur' => 'Achat fournisseur',
            'retour_client' => 'Retour client',
            'autre_entree' => 'Autre entrée',
        ],
        'sortie' => [
            'vente' => 'Vente',
            'consommation_prestation' => 'Consommation / Prestation',
            'perte_casse' => 'Perte ou casse',
            'vol' => 'Vol',
            'autre_sortie' => 'Autre sortie',
        ],
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeMouvementStock::class, 
            'quantite' => 'decimal:2', 
            'stock_apres' => 'decimal:2'
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /**
     * Alias pour satisfaire l'appel à la relation "enregistreur" dans le contrôleur/vue.
     */
    public function enregistreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }
}