<?php
namespace App\Models;

use App\Enums\StatutRendezVous;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'prenom',
        'nom',
        'public',
        'telephone',
        'whatsapp',
        'email',
        'date_naissance',
        'date_inscription',
        'canal_prefere',
        'consent_promotions',
        'consent_at',
        'preferences_prestations',
        'notes',
        'statut',
        'archived_at',
        'archived_by',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'date_inscription' => 'date',
            'consent_promotions' => 'boolean',
            'consent_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    /* --------------------------------------------------------------
     | ATTRIBUTS
     * --------------------------------------------------------------*/

    protected function nomComplet(): Attribute
    {
        return Attribute::get(
            fn() => "{$this->prenom} {$this->nom}"
        );
    }

    public function getCodeAttribute(): string
    {
        return 'CL-' . str_pad(
            (string) $this->getKey(),
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    /* --------------------------------------------------------------
     | RELATIONS
     * --------------------------------------------------------------*/

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function mouvementsPoints(): HasMany
    {
        return $this->hasMany(MouvementPoints::class);
    }

    public function anniversairesTraites(): HasMany
    {
        return $this->hasMany(AnniversaireTraite::class);
    }

    /* --------------------------------------------------------------
     | FIDÉLITÉ
     * --------------------------------------------------------------*/

    public function soldePoints(): int
    {
        return (int) $this->mouvementsPoints()->sum('points');
    }

    /* --------------------------------------------------------------
     | HISTORIQUE
     * --------------------------------------------------------------*/

    public function derniereVisite(): ?CarbonInterface
    {
        return $this->rendezVous()
            ->where('statut', StatutRendezVous::Termine)
            ->latest('debut')
            ->first()?->debut;
    }

    /* --------------------------------------------------------------
     | COMMUNICATION
     * --------------------------------------------------------------*/

    /**
     * Retourne le numéro WhatsApp enregistré,
     * ou le téléphone si les deux sont identiques.
     */
    public function numeroWhatsApp(): string
    {
        return $this->whatsapp ?: $this->telephone;
    }

    /**
     * Vérifie si le canal choisi est WhatsApp.
     */
    public function prefereWhatsApp(): bool
    {
        return $this->canal_prefere === 'whatsapp';
    }

    /**
     * Vérifie si le canal choisi est l'appel.
     */
    public function prefereAppel(): bool
    {
        return $this->canal_prefere === 'appel';
    }

    /**
     * Vérifie si le canal choisi est le SMS.
     */
    public function prefereSms(): bool
    {
        return $this->canal_prefere === 'sms';
    }

    /**
     * Retourne le libellé du canal préféré.
     */
    public function libelleCanalPrefere(): string
    {
        return match ($this->canal_prefere) {
            'whatsapp' => 'WhatsApp',
            'appel' => 'Appel téléphonique',
            'sms' => 'SMS',
            default => 'Non défini',
        };
    }

    /* --------------------------------------------------------------
     | RECHERCHE
     * --------------------------------------------------------------*/

    public function scopeRecherche(
        Builder $query,
        ?string $terme
    ): void {
        $terme = trim((string) $terme);

        if ($terme === '') {
            return;
        }

        $chiffres = preg_replace('/\D/', '', $terme);

        $query->where(function (Builder $q) use ($terme, $chiffres) {

            $q->where('prenom', 'like', "%{$terme}%")
                ->orWhere('nom', 'like', "%{$terme}%");

            if ($chiffres !== '') {
                $q->orWhere('telephone', 'like', "%{$chiffres}%")
                    ->orWhere('whatsapp', 'like', "%{$chiffres}%");
            }
        });
    }

    /* --------------------------------------------------------------
     | ARCHIVAGE
     * --------------------------------------------------------------*/

    public function scopeNonArchives(Builder $query): void
    {
        $query->whereNull('archived_at');
    }

    /* --------------------------------------------------------------
     | CLIENTS INACTIFS
     * --------------------------------------------------------------*/

    /**
     * Clients dont la dernière prestation terminée
     * date de plus de N jours.
     */
    public function scopeInactifsDepuis(
        Builder $query,
        int $jours
    ): void {
        $limite = now()->subDays($jours);

        $query->nonArchives()
            ->whereHas('rendezVous', fn(Builder $q) =>
                $q->where('statut', StatutRendezVous::Termine)
            )
            ->whereDoesntHave('rendezVous', fn(Builder $q) =>
                $q->where('statut', StatutRendezVous::Termine)
                    ->where('debut', '>=', $limite)
            );
    }
}
