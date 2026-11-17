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
        return $user->is_active
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.gerer')
            );
    }

    /**
     * Consulter une fiche client.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->is_active
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.gerer')
            );
    }

    /**
     * Créer un client.
     */
    public function create(User $user): bool
    {
        return $user->is_active
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.gerer')
            );
    }

    /**
     * Modifier un client non archivé.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->is_active
            && $client->archived_at === null
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.gerer')
            );
    }

    /**
     * Archiver ou restaurer un client.
     * Réservé à l'administratrice.
     */
    public function archive(User $user, Client $client): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    /**
     * Exporter les clients.
     */
    public function export(User $user): bool
    {
        return $user->is_active
            && (
                $user->isAdmin()
                || $user->hasPermission('clients.exporter')
            );
    }
}
