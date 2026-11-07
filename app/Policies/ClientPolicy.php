<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

/**
 * Hypothèse : la table users a une colonne `role` (admin, manager, reception…).
 * Adaptez à votre système (Spatie, enum, etc.).
 */
class ClientPolicy
{
    private const GESTION = ['admin', 'manager'];

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Client $client): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Client $client): bool { return $client->archived_at === null; }

    public function archive(User $user, Client $client): bool
    {
        return in_array($user->role, self::GESTION, true);
    }

    public function export(User $user): bool
    {
        return in_array($user->role, self::GESTION, true);
    }
}

/* =====================================================================
 |  À AJOUTER DANS app/Models/Client.php
 * ===================================================================*/
/*
protected $fillable = [
    'prenom', 'nom', 'public', 'telephone', 'whatsapp', 'email', 'date_naissance',
    'date_inscription', 'canal_prefere', 'consent_promotions', 'consent_at', 'consent_par',
    'notes', 'statut', 'archived_at', 'archived_by',
];

protected $casts = [
    'date_naissance'     => 'date',
    'date_inscription'   => 'date',
    'consent_promotions' => 'boolean',
    'consent_at'         => 'datetime',
    'archived_at'        => 'datetime',
];

// Garde-fou : impossible de supprimer un client ayant un historique financier.
// (Décommentez quand le modèle Paiement existe.)
protected static function booted(): void
{
    static::deleting(function (Client $c) {
        // if ($c->paiements()->exists()) {
        //     throw new \RuntimeException("Suppression interdite : ce client a un historique financier. Archivez-le.");
        // }
    });
}
*/

/* =====================================================================
 |  routes/web.php  (export AVANT les routes avec {client})
 * ===================================================================*/
/*
Route::middleware('auth')->group(function () {
    Route::get('clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::resource('clients', ClientController::class)->except(['destroy']);
    Route::post('clients/{client}/archive', [ClientController::class, 'archive'])->name('clients.archive');
    Route::post('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
});
*/
