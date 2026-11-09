<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * All in One by Eva — schéma complet en une seule migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Sécurité : rôles, permissions, utilisateurs, sessions ─────────────────

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ── Tables techniques Laravel : cache ─────────────────────────────────────

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });

        // ── Tables techniques Laravel : files d'attente ───────────────────────────

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedSmallInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('connection');
            $table->string('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
            $table->index(['connection', 'queue', 'failed_at']);
        });

        // ── Paramètres du salon et journal d'audit ────────────────────────────────

        Schema::create('parametres_salon', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->text('valeur')->nullable();
            $table->timestamps();
        });

        Schema::create('historique_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->nullableMorphs('auditable');
            $table->json('anciennes_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // ── Clients (CRM) ─────────────────────────────────────────────────────────

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('public', 10)->nullable()->default(null);

            $table->string('telephone', 20)->unique();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable();

            $table->date('date_naissance')->nullable();
            $table->date('date_inscription');

            $table->string('canal_prefere', 20)->default('whatsapp');
            $table->boolean('consent_promotions')->default(false);
            $table->timestamp('consent_at')->nullable();

            $table->string('preferences_prestations')->nullable();
            $table->text('notes')->nullable();

            $table->string('statut', 20)->default('actif')->index();

            $table->timestamp('archived_at')->nullable()->index();
            $table->foreignId('archived_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['nom', 'prenom']);
        });

        // ── Catalogue et équipe ───────────────────────────────────────────────────

        Schema::create('categories_services', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_service_id')->constrained('categories_services')->restrictOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->unsignedInteger('prix');
            $table->unsignedSmallInteger('duree_minutes');
            $table->string('zone', 20)->default('mixte');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('forfaits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->unsignedInteger('prix');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('forfait_service', function (Blueprint $table) {
            $table->foreignId('forfait_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->primary(['forfait_id', 'service_id']);
        });

        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('prenom');
            $table->string('nom');
            $table->string('telephone', 20);
            $table->string('fonction');
            $table->string('specialites')->nullable();
            $table->decimal('taux_commission', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employe_service', function (Blueprint $table) {
            $table->foreignId('employe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->primary(['employe_id', 'service_id']);
        });

        Schema::create('disponibilites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('jour_semaine');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->timestamps();
            $table->unique(['employe_id', 'jour_semaine']);
        });

        // ── Rendez-vous ───────────────────────────────────────────────────────────

        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('employe_id')->constrained()->restrictOnDelete();
            $table->dateTime('debut');
            $table->dateTime('fin');
            $table->string('zone', 20)->default('mixte');
            $table->unsignedInteger('prix_prevu');
            $table->string('statut', 20)->default('en_attente')->index();
            $table->text('commentaire')->nullable();
            $table->foreignId('cree_par')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['employe_id', 'debut', 'fin']);
        });

        Schema::create('rendez_vous_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendez_vous_id')->constrained('rendez_vous')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('forfait_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('libelle');
            $table->unsignedInteger('prix');
            $table->unsignedSmallInteger('duree_minutes');
        });

        // ── Caisse et dépenses ────────────────────────────────────────────────────

        Schema::create('modes_paiement', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('rendez_vous_id')->nullable()->constrained('rendez_vous')->restrictOnDelete();
            $table->foreignId('mode_paiement_id')->constrained('modes_paiement')->restrictOnDelete();
            $table->unsignedInteger('montant_total');
            $table->unsignedInteger('montant_paye');
            $table->unsignedInteger('reste_a_payer');
            $table->dateTime('paye_le')->index();
            $table->string('reference')->nullable();
            $table->foreignId('enregistre_par')->constrained('users')->restrictOnDelete();
            $table->timestamp('annule_le')->nullable()->index();
            $table->foreignId('annule_par')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('motif_annulation')->nullable();
            $table->timestamps();
        });

        Schema::create('categories_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_depense_id')->constrained('categories_depenses')->restrictOnDelete();
            $table->unsignedInteger('montant');
            $table->date('date_depense')->index();
            $table->string('description');
            $table->string('justificatif_path')->nullable();
            $table->foreignId('enregistre_par')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        // ── Stock ─────────────────────────────────────────────────────────────────

        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('categorie')->nullable();
            $table->decimal('quantite', 10, 2)->default(0);
            $table->string('unite', 20);
            $table->unsignedInteger('prix_achat');
            $table->unsignedInteger('prix_vente')->nullable();
            $table->decimal('seuil_alerte', 10, 2)->default(0);
            $table->string('fournisseur')->nullable();
            $table->date('date_entree')->nullable();
            $table->timestamps();
        });

        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->restrictOnDelete();
            $table->string('type', 20);
            $table->decimal('quantite', 10, 2);
            $table->decimal('stock_apres', 10, 2);
            $table->string('motif');
            $table->foreignId('rendez_vous_id')->nullable()->constrained('rendez_vous')->nullOnDelete();
            $table->foreignId('enregistre_par')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('service_produit', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantite', 10, 2);
            $table->primary(['service_id', 'produit_id']);
        });

        // ── Fidélité, messages et anniversaires ───────────────────────────────────

        Schema::create('mouvements_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('paiement_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->string('type', 20);
            $table->integer('points');
            $table->string('motif');
            $table->foreignId('enregistre_par')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('recompenses', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->string('type', 30);
            $table->unsignedInteger('seuil_points');
            $table->unsignedInteger('valeur')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('utilisations_recompenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('recompense_id')->constrained('recompenses')->restrictOnDelete();
            $table->foreignId('mouvement_points_id')->constrained('mouvements_points')->restrictOnDelete();
            $table->foreignId('enregistre_par')->constrained('users')->restrictOnDelete();
            $table->timestamp('utilisee_le')->useCurrent();
        });

        Schema::create('modeles_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->unique();
            $table->string('titre');
            $table->text('contenu');
            $table->timestamps();
        });

        Schema::create('campagnes_messages', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->foreignId('modele_message_id')->nullable()->constrained('modeles_messages')->nullOnDelete();
            $table->text('message');
            $table->string('statut_suivi')->default('preparee');
            $table->foreignId('cree_par')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('destinataires_campagnes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_message_id')->constrained('campagnes_messages')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->text('message_personnalise');
            $table->timestamp('ouvert_le')->nullable();
            $table->string('suivi_manuel')->nullable();
            $table->unique(['campagne_message_id', 'client_id']);
        });

        Schema::create('anniversaires_traites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->string('offre')->nullable();
            $table->foreignId('traite_par')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['client_id', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('anniversaires_traites');
        Schema::dropIfExists('destinataires_campagnes');
        Schema::dropIfExists('campagnes_messages');
        Schema::dropIfExists('modeles_messages');
        Schema::dropIfExists('utilisations_recompenses');
        Schema::dropIfExists('recompenses');
        Schema::dropIfExists('mouvements_points');
        Schema::dropIfExists('service_produit');
        Schema::dropIfExists('mouvements_stock');
        Schema::dropIfExists('produits');
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('categories_depenses');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('modes_paiement');
        Schema::dropIfExists('rendez_vous_service');
        Schema::dropIfExists('rendez_vous');
        Schema::dropIfExists('disponibilites');
        Schema::dropIfExists('employe_service');
        Schema::dropIfExists('employes');
        Schema::dropIfExists('forfait_service');
        Schema::dropIfExists('forfaits');
        Schema::dropIfExists('services');
        Schema::dropIfExists('categories_services');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('historique_actions');
        Schema::dropIfExists('parametres_salon');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        Schema::enableForeignKeyConstraints();
    }
};