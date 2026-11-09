<?php

namespace App\Models;

use App\Enums\StatutRendezVous;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $prenom
 * @property string $nom
 * @property string $telephone
 * @property string|null $whatsapp
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property int|null $archived_by
 */
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
        'consent_par',
        'preferences_prestations',
        'notes',
        'statut',
        'archived_at',
        'archived_by',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance'     => 'date',
            'date_inscription'   => 'date',
            'consent_promotions' => 'boolean',
            'consent_at'         => 'datetime',
            'archived_at'        => 'datetime',
        ];
    }

    protected function nomComplet(): Attribute
    {
        return Attribute::get(fn() => "{$this->prenom} {$this->nom}");
    }

    public function getCodeAttribute(): string
    {
        return 'CL-' . str_pad((string) $this->getKey(), 6, '0', STR_PAD_LEFT);
    }

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

    public function soldePoints(): int
    {
        return (int) $this->mouvementsPoints()->sum('points');
    }

    public function derniereVisite(): ?CarbonInterface
    {
        return $this->rendezVous()
            ->where('statut', StatutRendezVous::Termine)
            ->latest('debut')
            ->first()?->debut;
    }

    public function numeroWhatsApp(): string
    {
        return $this->whatsapp ?: $this->telephone;
    }

    public function scopeRecherche(Builder $query, ?string $terme): void
    {
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

    public function scopeNonArchives(Builder $query): void
    {
        $query->whereNull('archived_at');
    }

    /** Clients dont la dernière prestation terminée date de plus de N semaines. */
    public function scopeInactifsDepuis(Builder $query, int $semaines): void
    {
        $limite = now()->subWeeks($semaines);

        $query->nonArchives()
            ->whereHas('rendezVous', fn(Builder $q) => $q->where('statut', StatutRendezVous::Termine))
            ->whereDoesntHave('rendezVous', fn(Builder $q) => $q
                ->where('statut', StatutRendezVous::Termine)
                ->where('debut', '>=', $limite));
    }
}
