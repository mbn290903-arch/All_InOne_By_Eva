<?php
namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Consulter la liste des clients.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Consulter une fiche client.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->is_active;
    }

    /**
     * Créer un client.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Modifier un client non archivé.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->is_active
            && $client->archived_at === null;
    }

    /**
     * Archiver ou restaurer un client.
     */
    public function archive(User $user, Client $client): bool
    {
        return $user->is_active
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.archive')
            );
    }

    /**
     * Exporter les clients.
     */
    public function export(User $user): bool
    {
        return $user->is_active
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.export')
            );
    }
}
