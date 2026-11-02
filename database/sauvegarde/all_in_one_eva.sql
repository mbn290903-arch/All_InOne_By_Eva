-- =============================================================================
--  ALL IN ONE BY EVA — Schéma complet de la base de données (MySQL 8 / MariaDB 10.6+)
-- =============================================================================
--  Généré automatiquement à partir des migrations Laravel du projet (lot 1).
--  Contenu : 38 tables, clés étrangères, index, contraintes d'unicité,
--            données de référence et compte administratrice initial.
--
--  Exécution en une seule commande :
--      mysql -u root -p < all_in_one_eva.sql
--  ou via phpMyAdmin : onglet « Importer ».
--
--  ⚠ Le script SUPPRIME puis recrée la base `all_in_one_eva`.
--  ⚠ Compte créé : admin@allinone.sn / ChangezMoi2026!  → à changer immédiatement.
--
--  La table `migrations` est remplie : Laravel considère toutes les migrations
--  comme exécutées (php artisan migrate n'essaiera pas de recréer les tables).
-- =============================================================================

DROP DATABASE IF EXISTS `all_in_one_eva`;
CREATE DATABASE `all_in_one_eva` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `all_in_one_eva`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1; -- tables créées dans l'ordre des dépendances : contrôles actifs
SET time_zone = '+00:00';

-- Table de suivi des migrations Laravel
CREATE TABLE `migrations` (
    `id` int unsigned not null auto_increment primary key,
    `migration` varchar(255) not null,
    `batch` int not null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';


-- -----------------------------------------------------------------------------
-- 1. SÉCURITÉ : rôles, permissions, utilisateurs
-- -----------------------------------------------------------------------------
--   roles 1 ──< N users                (users.role_id → roles.id, RESTRICT)
--   roles N >──< N permissions         (table pivot permission_role, CASCADE)
--   sessions, password_reset_tokens : tables techniques Laravel

CREATE TABLE `roles` (
    `id` bigint unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `label` varchar(255) not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `roles` add unique `roles_name_unique`(`name`);

CREATE TABLE `permissions` (
    `id` bigint unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `label` varchar(255) not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `permissions` add unique `permissions_name_unique`(`name`);

CREATE TABLE `permission_role` (
    `permission_id` bigint unsigned not null,
    `role_id` bigint unsigned not null,
    primary key (`permission_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `permission_role` add constraint `permission_role_permission_id_foreign` foreign key (`permission_id`) references `permissions` (`id`) on delete cascade;
ALTER TABLE `permission_role` add constraint `permission_role_role_id_foreign` foreign key (`role_id`) references `roles` (`id`) on delete cascade;

CREATE TABLE `users` (
    `id` bigint unsigned not null auto_increment primary key,
    `role_id` bigint unsigned not null,
    `name` varchar(255) not null,
    `email` varchar(255) not null,
    `email_verified_at` timestamp null,
    `password` varchar(255) not null,
    `is_active` tinyint(1) not null default '1',
    `remember_token` varchar(100) null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `users` add constraint `users_role_id_foreign` foreign key (`role_id`) references `roles` (`id`) on delete restrict;
ALTER TABLE `users` add unique `users_email_unique`(`email`);

CREATE TABLE `password_reset_tokens` (
    `email` varchar(255) not null,
    `token` varchar(255) not null,
    `created_at` timestamp null,
    primary key (`email`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';

CREATE TABLE `sessions` (
    `id` varchar(255) not null,
    `user_id` bigint unsigned null,
    `ip_address` varchar(45) null,
    `user_agent` text null,
    `payload` longtext not null,
    `last_activity` int not null,
    primary key (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `sessions` add index `sessions_user_id_index`(`user_id`);
ALTER TABLE `sessions` add index `sessions_last_activity_index`(`last_activity`);

-- -----------------------------------------------------------------------------
-- 2. TABLES TECHNIQUES LARAVEL : cache
-- -----------------------------------------------------------------------------
--   cache, cache_locks : cache applicatif (CACHE_STORE=database)

CREATE TABLE `cache` (
    `key` varchar(255) not null,
    `value` mediumtext not null,
    `expiration` bigint not null,
    primary key (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `cache` add index `cache_expiration_index`(`expiration`);

CREATE TABLE `cache_locks` (
    `key` varchar(255) not null,
    `owner` varchar(255) not null,
    `expiration` bigint not null,
    primary key (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `cache_locks` add index `cache_locks_expiration_index`(`expiration`);

-- -----------------------------------------------------------------------------
-- 3. TABLES TECHNIQUES LARAVEL : files d'attente
-- -----------------------------------------------------------------------------
--   jobs, job_batches, failed_jobs : tâches asynchrones (QUEUE_CONNECTION=database)

CREATE TABLE `jobs` (
    `id` bigint unsigned not null auto_increment primary key,
    `queue` varchar(255) not null,
    `payload` longtext not null,
    `attempts` smallint unsigned not null,
    `reserved_at` int unsigned null,
    `available_at` int unsigned not null,
    `created_at` int unsigned not null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `jobs` add index `jobs_queue_index`(`queue`);

CREATE TABLE `job_batches` (
    `id` varchar(255) not null,
    `name` varchar(255) not null,
    `total_jobs` int not null,
    `pending_jobs` int not null,
    `failed_jobs` int not null,
    `failed_job_ids` longtext not null,
    `options` mediumtext null,
    `cancelled_at` int null,
    `created_at` int not null,
    `finished_at` int null,
    primary key (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';

CREATE TABLE `failed_jobs` (
    `id` bigint unsigned not null auto_increment primary key,
    `uuid` varchar(255) not null,
    `connection` varchar(255) not null,
    `queue` varchar(255) not null,
    `payload` longtext not null,
    `exception` longtext not null,
    `failed_at` timestamp not null default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `failed_jobs` add index `failed_jobs_connection_queue_failed_at_index`(`connection`, `queue`, `failed_at`);
ALTER TABLE `failed_jobs` add unique `failed_jobs_uuid_unique`(`uuid`);

-- -----------------------------------------------------------------------------
-- 4. PARAMÈTRES DU SALON ET JOURNAL D'AUDIT
-- -----------------------------------------------------------------------------
--   parametres_salon : clé / valeur (pause_minutes, fidelite_fcfa_par_point, inactivite_semaines…)
--   users 1 ──< N historique_actions    (historique_actions.user_id → users.id, SET NULL)
--   historique_actions.auditable_type/_id : relation polymorphe vers l'objet modifié

CREATE TABLE `parametres_salon` (
    `id` bigint unsigned not null auto_increment primary key,
    `cle` varchar(255) not null,
    `valeur` text null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `parametres_salon` add unique `parametres_salon_cle_unique`(`cle`);

CREATE TABLE `historique_actions` (
    `id` bigint unsigned not null auto_increment primary key,
    `user_id` bigint unsigned null,
    `action` varchar(255) not null,
    `auditable_type` varchar(255) null,
    `auditable_id` bigint unsigned null,
    `anciennes_valeurs` json null,
    `nouvelles_valeurs` json null,
    `ip` varchar(45) null,
    `created_at` timestamp not null default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `historique_actions` add constraint `historique_actions_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete set null;
ALTER TABLE `historique_actions` add index `historique_actions_auditable_type_auditable_id_index`(`auditable_type`, `auditable_id`);
ALTER TABLE `historique_actions` add index `historique_actions_created_at_index`(`created_at`);

-- -----------------------------------------------------------------------------
-- 5. CLIENTS (CRM)
-- -----------------------------------------------------------------------------
--   clients.telephone UNIQUE : stocké au format international (221771234567) → détection des doublons
--   clients.statut : actif | inactif | archive (pas de suppression physique)

CREATE TABLE `clients` (
    `id` bigint unsigned not null auto_increment primary key,
    `prenom` varchar(255) not null,
    `nom` varchar(255) not null,
    `telephone` varchar(20) not null,
    `whatsapp` varchar(20) null,
    `email` varchar(255) null,
    `date_naissance` date null,
    `date_inscription` date not null,
    `preference_communication` varchar(20) not null default 'whatsapp',
    `consentement_promo` tinyint(1) not null default '0',
    `consentement_modifie_le` timestamp null,
    `preferences_prestations` varchar(255) null,
    `notes` text null,
    `statut` varchar(20) not null default 'actif',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `clients` add index `clients_nom_prenom_index`(`nom`, `prenom`);
ALTER TABLE `clients` add unique `clients_telephone_unique`(`telephone`);
ALTER TABLE `clients` add index `clients_statut_index`(`statut`);

-- -----------------------------------------------------------------------------
-- 6. CATALOGUE ET ÉQUIPE
-- -----------------------------------------------------------------------------
--   categories_services 1 ──< N services        (services.categorie_service_id, RESTRICT)
--   forfaits N >──< N services                  (pivot forfait_service)
--   users 1 ──── 0..1 employes                  (employes.user_id UNIQUE, SET NULL)
--   employes N >──< N services                  (pivot employe_service : prestations proposées)
--   employes 1 ──< N disponibilites             (UNIQUE employe_id + jour_semaine, 1 = lundi … 7 = dimanche)

CREATE TABLE `categories_services` (
    `id` bigint unsigned not null auto_increment primary key,
    `nom` varchar(255) not null,
    `ordre` smallint unsigned not null default '0',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `categories_services` add unique `categories_services_nom_unique`(`nom`);

CREATE TABLE `services` (
    `id` bigint unsigned not null auto_increment primary key,
    `categorie_service_id` bigint unsigned not null,
    `nom` varchar(255) not null,
    `description` text null,
    `prix` int unsigned not null,
    `duree_minutes` smallint unsigned not null,
    `zone` varchar(20) not null default 'mixte',
    `is_active` tinyint(1) not null default '1',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `services` add constraint `services_categorie_service_id_foreign` foreign key (`categorie_service_id`) references `categories_services` (`id`) on delete restrict;
ALTER TABLE `services` add index `services_is_active_index`(`is_active`);

CREATE TABLE `forfaits` (
    `id` bigint unsigned not null auto_increment primary key,
    `nom` varchar(255) not null,
    `description` text null,
    `prix` int unsigned not null,
    `is_active` tinyint(1) not null default '1',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';

CREATE TABLE `forfait_service` (
    `forfait_id` bigint unsigned not null,
    `service_id` bigint unsigned not null,
    primary key (`forfait_id`, `service_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `forfait_service` add constraint `forfait_service_forfait_id_foreign` foreign key (`forfait_id`) references `forfaits` (`id`) on delete cascade;
ALTER TABLE `forfait_service` add constraint `forfait_service_service_id_foreign` foreign key (`service_id`) references `services` (`id`) on delete restrict;

CREATE TABLE `employes` (
    `id` bigint unsigned not null auto_increment primary key,
    `user_id` bigint unsigned null,
    `prenom` varchar(255) not null,
    `nom` varchar(255) not null,
    `telephone` varchar(20) not null,
    `fonction` varchar(255) not null,
    `specialites` varchar(255) null,
    `taux_commission` decimal(5, 2) null,
    `is_active` tinyint(1) not null default '1',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `employes` add constraint `employes_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete set null;
ALTER TABLE `employes` add unique `employes_user_id_unique`(`user_id`);

CREATE TABLE `employe_service` (
    `employe_id` bigint unsigned not null,
    `service_id` bigint unsigned not null,
    primary key (`employe_id`, `service_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `employe_service` add constraint `employe_service_employe_id_foreign` foreign key (`employe_id`) references `employes` (`id`) on delete cascade;
ALTER TABLE `employe_service` add constraint `employe_service_service_id_foreign` foreign key (`service_id`) references `services` (`id`) on delete cascade;

CREATE TABLE `disponibilites` (
    `id` bigint unsigned not null auto_increment primary key,
    `employe_id` bigint unsigned not null,
    `jour_semaine` tinyint unsigned not null,
    `heure_debut` time not null,
    `heure_fin` time not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `disponibilites` add constraint `disponibilites_employe_id_foreign` foreign key (`employe_id`) references `employes` (`id`) on delete cascade;
ALTER TABLE `disponibilites` add unique `disponibilites_employe_id_jour_semaine_unique`(`employe_id`, `jour_semaine`);

-- -----------------------------------------------------------------------------
-- 7. RENDEZ-VOUS
-- -----------------------------------------------------------------------------
--   clients  1 ──< N rendez_vous                (rendez_vous.client_id, RESTRICT)
--   employes 1 ──< N rendez_vous                (rendez_vous.employe_id, RESTRICT)
--   users    1 ──< N rendez_vous                (rendez_vous.cree_par : auteur)
--   rendez_vous 1 ──< N rendez_vous_service     (lignes : service OU forfait, prix et durée figés)
--   INDEX (employe_id, debut, fin) : recherche rapide des chevauchements
--   statut : en_attente | confirme | client_arrive | en_cours | termine | annule | absent

CREATE TABLE `rendez_vous` (
    `id` bigint unsigned not null auto_increment primary key,
    `client_id` bigint unsigned not null,
    `employe_id` bigint unsigned not null,
    `debut` datetime not null,
    `fin` datetime not null,
    `zone` varchar(20) not null default 'mixte',
    `prix_prevu` int unsigned not null,
    `statut` varchar(20) not null default 'en_attente',
    `commentaire` text null,
    `cree_par` bigint unsigned not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `rendez_vous` add constraint `rendez_vous_client_id_foreign` foreign key (`client_id`) references `clients` (`id`) on delete restrict;
ALTER TABLE `rendez_vous` add constraint `rendez_vous_employe_id_foreign` foreign key (`employe_id`) references `employes` (`id`) on delete restrict;
ALTER TABLE `rendez_vous` add constraint `rendez_vous_cree_par_foreign` foreign key (`cree_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `rendez_vous` add index `rendez_vous_employe_id_debut_fin_index`(`employe_id`, `debut`, `fin`);
ALTER TABLE `rendez_vous` add index `rendez_vous_statut_index`(`statut`);

CREATE TABLE `rendez_vous_service` (
    `id` bigint unsigned not null auto_increment primary key,
    `rendez_vous_id` bigint unsigned not null,
    `service_id` bigint unsigned null,
    `forfait_id` bigint unsigned null,
    `libelle` varchar(255) not null,
    `prix` int unsigned not null,
    `duree_minutes` smallint unsigned not null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `rendez_vous_service` add constraint `rendez_vous_service_rendez_vous_id_foreign` foreign key (`rendez_vous_id`) references `rendez_vous` (`id`) on delete cascade;
ALTER TABLE `rendez_vous_service` add constraint `rendez_vous_service_service_id_foreign` foreign key (`service_id`) references `services` (`id`) on delete restrict;
ALTER TABLE `rendez_vous_service` add constraint `rendez_vous_service_forfait_id_foreign` foreign key (`forfait_id`) references `forfaits` (`id`) on delete restrict;

-- -----------------------------------------------------------------------------
-- 8. CAISSE ET DÉPENSES
-- -----------------------------------------------------------------------------
--   modes_paiement 1 ──< N paiements            (Espèces, Wave, Orange Money, Carte…)
--   clients 1 ──< N paiements ; rendez_vous 1 ──< N paiements (paiements partiels)
--   users 1 ──< N paiements                     (enregistre_par, annule_par)
--   paiements.annule_le : annulation tracée, jamais de suppression (exclu des recettes)
--   categories_depenses 1 ──< N depenses
--   Montants en entiers (FCFA, sans décimales)

CREATE TABLE `modes_paiement` (
    `id` bigint unsigned not null auto_increment primary key,
    `libelle` varchar(255) not null,
    `is_active` tinyint(1) not null default '1',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `modes_paiement` add unique `modes_paiement_libelle_unique`(`libelle`);

CREATE TABLE `paiements` (
    `id` bigint unsigned not null auto_increment primary key,
    `client_id` bigint unsigned not null,
    `rendez_vous_id` bigint unsigned null,
    `mode_paiement_id` bigint unsigned not null,
    `montant_total` int unsigned not null,
    `montant_paye` int unsigned not null,
    `reste_a_payer` int unsigned not null,
    `paye_le` datetime not null,
    `reference` varchar(255) null,
    `enregistre_par` bigint unsigned not null,
    `annule_le` timestamp null,
    `annule_par` bigint unsigned null,
    `motif_annulation` varchar(255) null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `paiements` add constraint `paiements_client_id_foreign` foreign key (`client_id`) references `clients` (`id`) on delete restrict;
ALTER TABLE `paiements` add constraint `paiements_rendez_vous_id_foreign` foreign key (`rendez_vous_id`) references `rendez_vous` (`id`) on delete restrict;
ALTER TABLE `paiements` add constraint `paiements_mode_paiement_id_foreign` foreign key (`mode_paiement_id`) references `modes_paiement` (`id`) on delete restrict;
ALTER TABLE `paiements` add constraint `paiements_enregistre_par_foreign` foreign key (`enregistre_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `paiements` add constraint `paiements_annule_par_foreign` foreign key (`annule_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `paiements` add index `paiements_paye_le_index`(`paye_le`);
ALTER TABLE `paiements` add index `paiements_annule_le_index`(`annule_le`);

CREATE TABLE `categories_depenses` (
    `id` bigint unsigned not null auto_increment primary key,
    `nom` varchar(255) not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `categories_depenses` add unique `categories_depenses_nom_unique`(`nom`);

CREATE TABLE `depenses` (
    `id` bigint unsigned not null auto_increment primary key,
    `categorie_depense_id` bigint unsigned not null,
    `montant` int unsigned not null,
    `date_depense` date not null,
    `description` varchar(255) not null,
    `justificatif_path` varchar(255) null,
    `enregistre_par` bigint unsigned not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `depenses` add constraint `depenses_categorie_depense_id_foreign` foreign key (`categorie_depense_id`) references `categories_depenses` (`id`) on delete restrict;
ALTER TABLE `depenses` add constraint `depenses_enregistre_par_foreign` foreign key (`enregistre_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `depenses` add index `depenses_date_depense_index`(`date_depense`);

-- -----------------------------------------------------------------------------
-- 9. STOCK
-- -----------------------------------------------------------------------------
--   produits 1 ──< N mouvements_stock           (entree | sortie | consommation, stock_apres historisé)
--   rendez_vous 1 ──< N mouvements_stock        (consommation liée à une prestation, facultatif)
--   services N >──< N produits                  (pivot service_produit + quantité consommée)

CREATE TABLE `produits` (
    `id` bigint unsigned not null auto_increment primary key,
    `nom` varchar(255) not null,
    `categorie` varchar(255) null,
    `quantite` decimal(10, 2) not null default '0',
    `unite` varchar(20) not null,
    `prix_achat` int unsigned not null,
    `prix_vente` int unsigned null,
    `seuil_alerte` decimal(10, 2) not null default '0',
    `fournisseur` varchar(255) null,
    `date_entree` date null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';

CREATE TABLE `mouvements_stock` (
    `id` bigint unsigned not null auto_increment primary key,
    `produit_id` bigint unsigned not null,
    `type` varchar(20) not null,
    `quantite` decimal(10, 2) not null,
    `stock_apres` decimal(10, 2) not null,
    `motif` varchar(255) not null,
    `rendez_vous_id` bigint unsigned null,
    `enregistre_par` bigint unsigned not null,
    `created_at` timestamp not null default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `mouvements_stock` add constraint `mouvements_stock_produit_id_foreign` foreign key (`produit_id`) references `produits` (`id`) on delete restrict;
ALTER TABLE `mouvements_stock` add constraint `mouvements_stock_rendez_vous_id_foreign` foreign key (`rendez_vous_id`) references `rendez_vous` (`id`) on delete set null;
ALTER TABLE `mouvements_stock` add constraint `mouvements_stock_enregistre_par_foreign` foreign key (`enregistre_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `mouvements_stock` add index `mouvements_stock_created_at_index`(`created_at`);

CREATE TABLE `service_produit` (
    `service_id` bigint unsigned not null,
    `produit_id` bigint unsigned not null,
    `quantite` decimal(10, 2) not null,
    primary key (`service_id`, `produit_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `service_produit` add constraint `service_produit_service_id_foreign` foreign key (`service_id`) references `services` (`id`) on delete cascade;
ALTER TABLE `service_produit` add constraint `service_produit_produit_id_foreign` foreign key (`produit_id`) references `produits` (`id`) on delete cascade;

-- -----------------------------------------------------------------------------
-- 10. FIDÉLITÉ, MESSAGES ET ANNIVERSAIRES
-- -----------------------------------------------------------------------------
--   clients 1 ──< N mouvements_points           (solde = SUM(points), historique jamais réécrit)
--   paiements 1 ──── 0..1 mouvements_points     (paiement_id UNIQUE : points crédités une seule fois)
--   recompenses 1 ──< N utilisations_recompenses ──── 1 mouvements_points (débit)
--   modeles_messages 1 ──< N campagnes_messages 1 ──< N destinataires_campagnes N >── 1 clients
--   clients 1 ──< N anniversaires_traites       (UNIQUE client_id + annee : un seul traitement par an)

CREATE TABLE `mouvements_points` (
    `id` bigint unsigned not null auto_increment primary key,
    `client_id` bigint unsigned not null,
    `paiement_id` bigint unsigned null,
    `type` varchar(20) not null,
    `points` int not null,
    `motif` varchar(255) not null,
    `enregistre_par` bigint unsigned null,
    `created_at` timestamp not null default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `mouvements_points` add constraint `mouvements_points_client_id_foreign` foreign key (`client_id`) references `clients` (`id`) on delete restrict;
ALTER TABLE `mouvements_points` add constraint `mouvements_points_paiement_id_foreign` foreign key (`paiement_id`) references `paiements` (`id`) on delete restrict;
ALTER TABLE `mouvements_points` add constraint `mouvements_points_enregistre_par_foreign` foreign key (`enregistre_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `mouvements_points` add unique `mouvements_points_paiement_id_unique`(`paiement_id`);

CREATE TABLE `recompenses` (
    `id` bigint unsigned not null auto_increment primary key,
    `libelle` varchar(255) not null,
    `type` varchar(30) not null,
    `seuil_points` int unsigned not null,
    `valeur` int unsigned null,
    `is_active` tinyint(1) not null default '1',
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';

CREATE TABLE `utilisations_recompenses` (
    `id` bigint unsigned not null auto_increment primary key,
    `client_id` bigint unsigned not null,
    `recompense_id` bigint unsigned not null,
    `mouvement_points_id` bigint unsigned not null,
    `enregistre_par` bigint unsigned not null,
    `utilisee_le` timestamp not null default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `utilisations_recompenses` add constraint `utilisations_recompenses_client_id_foreign` foreign key (`client_id`) references `clients` (`id`) on delete restrict;
ALTER TABLE `utilisations_recompenses` add constraint `utilisations_recompenses_recompense_id_foreign` foreign key (`recompense_id`) references `recompenses` (`id`) on delete restrict;
ALTER TABLE `utilisations_recompenses` add constraint `utilisations_recompenses_mouvement_points_id_foreign` foreign key (`mouvement_points_id`) references `mouvements_points` (`id`) on delete restrict;
ALTER TABLE `utilisations_recompenses` add constraint `utilisations_recompenses_enregistre_par_foreign` foreign key (`enregistre_par`) references `users` (`id`) on delete restrict;

CREATE TABLE `modeles_messages` (
    `id` bigint unsigned not null auto_increment primary key,
    `type` varchar(30) not null,
    `titre` varchar(255) not null,
    `contenu` text not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `modeles_messages` add unique `modeles_messages_type_unique`(`type`);

CREATE TABLE `campagnes_messages` (
    `id` bigint unsigned not null auto_increment primary key,
    `titre` varchar(255) not null,
    `modele_message_id` bigint unsigned null,
    `message` text not null,
    `statut_suivi` varchar(255) not null default 'preparee',
    `cree_par` bigint unsigned not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `campagnes_messages` add constraint `campagnes_messages_modele_message_id_foreign` foreign key (`modele_message_id`) references `modeles_messages` (`id`) on delete set null;
ALTER TABLE `campagnes_messages` add constraint `campagnes_messages_cree_par_foreign` foreign key (`cree_par`) references `users` (`id`) on delete restrict;

CREATE TABLE `destinataires_campagnes` (
    `id` bigint unsigned not null auto_increment primary key,
    `campagne_message_id` bigint unsigned not null,
    `client_id` bigint unsigned not null,
    `message_personnalise` text not null,
    `ouvert_le` timestamp null,
    `suivi_manuel` varchar(255) null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `destinataires_campagnes` add constraint `destinataires_campagnes_campagne_message_id_foreign` foreign key (`campagne_message_id`) references `campagnes_messages` (`id`) on delete cascade;
ALTER TABLE `destinataires_campagnes` add constraint `destinataires_campagnes_client_id_foreign` foreign key (`client_id`) references `clients` (`id`) on delete restrict;
ALTER TABLE `destinataires_campagnes` add unique `destinataires_campagnes_campagne_message_id_client_id_unique`(`campagne_message_id`, `client_id`);

CREATE TABLE `anniversaires_traites` (
    `id` bigint unsigned not null auto_increment primary key,
    `client_id` bigint unsigned not null,
    `annee` smallint unsigned not null,
    `offre` varchar(255) null,
    `traite_par` bigint unsigned not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 collate 'utf8mb4_unicode_ci';
ALTER TABLE `anniversaires_traites` add constraint `anniversaires_traites_client_id_foreign` foreign key (`client_id`) references `clients` (`id`) on delete cascade;
ALTER TABLE `anniversaires_traites` add constraint `anniversaires_traites_traite_par_foreign` foreign key (`traite_par`) references `users` (`id`) on delete restrict;
ALTER TABLE `anniversaires_traites` add unique `anniversaires_traites_client_id_annee_unique`(`client_id`, `annee`);

-- =============================================================================
--  DONNÉES DE RÉFÉRENCE (identiques aux seeders RoleSeeder et SalonSeeder)
-- =============================================================================


-- roles
INSERT INTO `roles` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (1,'admin','Administratrice','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `roles` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (2,'receptionniste','Réceptionniste','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `roles` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (3,'professionnel','Professionnel(le)','2026-10-31 12:48:41','2026-10-31 12:48:41');

-- permissions
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (1,'clients.gerer','Gérer les clients','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (2,'clients.exporter','Exporter les clients','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (3,'catalogue.gerer','Gérer le catalogue','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (4,'employes.gerer','Gérer les employés','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (5,'rdv.gerer','Gérer les rendez-vous','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (6,'rdv.passe','Créer un rendez-vous dans le passé','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (7,'paiements.enregistrer','Enregistrer des paiements','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (8,'paiements.annuler','Annuler / corriger des paiements','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (9,'fidelite.corriger','Corriger les points de fidélité','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (10,'whatsapp.envoyer','Envoyer des messages WhatsApp','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (11,'finances.voir','Voir les finances et rapports','2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `permissions` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES (12,'parametres.gerer','Configurer le salon','2026-10-31 12:48:41','2026-10-31 12:48:41');

-- permission_role
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (1,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (1,2);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (2,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (3,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (4,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (5,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (5,2);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (6,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (7,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (7,2);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (8,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (9,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (10,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (10,2);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (11,1);
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES (12,1);

-- parametres_salon
INSERT INTO `parametres_salon` (`id`, `cle`, `valeur`, `created_at`, `updated_at`) VALUES (1,'pause_minutes','0','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `parametres_salon` (`id`, `cle`, `valeur`, `created_at`, `updated_at`) VALUES (2,'fidelite_fcfa_par_point','1000','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `parametres_salon` (`id`, `cle`, `valeur`, `created_at`, `updated_at`) VALUES (3,'inactivite_semaines','6','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `parametres_salon` (`id`, `cle`, `valeur`, `created_at`, `updated_at`) VALUES (4,'offre_anniversaire',NULL,'2026-10-31 12:48:42','2026-10-31 12:48:42');

-- categories_services
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (1,'Coiffure hommes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (2,'Coiffure femmes',2,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (3,'Coiffure enfants',3,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (4,'Tresses',4,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (5,'Coloration',5,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (6,'Soins capillaires',6,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (7,'Massages',7,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (8,'Manucure',8,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (9,'Pédicure',9,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (10,'Soins du visage',10,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_services` (`id`, `nom`, `ordre`, `created_at`, `updated_at`) VALUES (11,'Forfaits combinés',11,'2026-10-31 12:48:42','2026-10-31 12:48:42');

-- services
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (1,1,'Coupe homme',NULL,3000,30,'mixte',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (2,1,'Coupe + barbe',NULL,5000,45,'mixte',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (3,2,'Brushing',NULL,5000,45,'mixte',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (4,2,'Coupe femme',NULL,7000,60,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (5,3,'Coupe enfant',NULL,2000,20,'mixte',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (6,4,'Tresses simples',NULL,10000,120,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (7,4,'Box braids',NULL,25000,240,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (8,4,'Nattes collées',NULL,8000,90,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (9,5,'Coloration complète',NULL,15000,90,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (10,5,'Mèches',NULL,20000,120,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (11,6,'Soin profond',NULL,7000,45,'mixte',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (12,6,'Défrisage',NULL,10000,60,'femmes',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (13,7,'Massage relaxant 60 min',NULL,20000,60,'mixte',1,'2026-10-31 12:48:41','2026-10-31 12:48:41');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (14,7,'Massage aux pierres chaudes',NULL,30000,75,'mixte',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (15,8,'Manucure simple',NULL,5000,30,'mixte',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (16,8,'Pose de vernis semi-permanent',NULL,10000,45,'femmes',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (17,9,'Pédicure complète',NULL,8000,45,'mixte',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (18,10,'Soin du visage hydratant',NULL,15000,60,'mixte',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `services` (`id`, `categorie_service_id`, `nom`, `description`, `prix`, `duree_minutes`, `zone`, `is_active`, `created_at`, `updated_at`) VALUES (19,10,'Nettoyage de peau',NULL,12000,45,'mixte',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');

-- forfaits
INSERT INTO `forfaits` (`id`, `nom`, `description`, `prix`, `is_active`, `created_at`, `updated_at`) VALUES (1,'Duo détente','Massage relaxant et soin du visage',30000,1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `forfaits` (`id`, `nom`, `description`, `prix`, `is_active`, `created_at`, `updated_at`) VALUES (2,'Mains & pieds','Manucure et pédicure',11000,1,'2026-10-31 12:48:42','2026-10-31 12:48:42');

-- forfait_service
INSERT INTO `forfait_service` (`forfait_id`, `service_id`) VALUES (1,13);
INSERT INTO `forfait_service` (`forfait_id`, `service_id`) VALUES (1,18);
INSERT INTO `forfait_service` (`forfait_id`, `service_id`) VALUES (2,15);
INSERT INTO `forfait_service` (`forfait_id`, `service_id`) VALUES (2,17);

-- modes_paiement
INSERT INTO `modes_paiement` (`id`, `libelle`, `is_active`, `created_at`, `updated_at`) VALUES (1,'Espèces',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `modes_paiement` (`id`, `libelle`, `is_active`, `created_at`, `updated_at`) VALUES (2,'Wave',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `modes_paiement` (`id`, `libelle`, `is_active`, `created_at`, `updated_at`) VALUES (3,'Orange Money',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `modes_paiement` (`id`, `libelle`, `is_active`, `created_at`, `updated_at`) VALUES (4,'Carte bancaire',1,'2026-10-31 12:48:42','2026-10-31 12:48:42');

-- categories_depenses
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (1,'Achat de produits','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (2,'Loyer','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (3,'Électricité','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (4,'Eau','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (5,'Salaires et commissions','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (6,'Entretien du matériel','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `categories_depenses` (`id`, `nom`, `created_at`, `updated_at`) VALUES (7,'Autres dépenses','2026-10-31 12:48:42','2026-10-31 12:48:42');

-- modeles_messages
INSERT INTO `modeles_messages` (`id`, `type`, `titre`, `contenu`, `created_at`, `updated_at`) VALUES (1,'anniversaire','Message d\'anniversaire','Bonjour [Prénom] ! Toute l\'équipe d\'All in One by Eva te souhaite un merveilleux anniversaire. Pour célébrer cette journée spéciale, nous serions ravis de t\'accueillir à nouveau au salon. À très bientôt !','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `modeles_messages` (`id`, `type`, `titre`, `contenu`, `created_at`, `updated_at`) VALUES (2,'promotion','Message de promotion','Bonjour [Prénom] ! All in One by Eva a une nouvelle offre beauté à te faire découvrir. Contacte-nous pour connaître les détails et réserver ton prochain moment de bien-être.','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `modeles_messages` (`id`, `type`, `titre`, `contenu`, `created_at`, `updated_at`) VALUES (3,'rappel_rdv','Rappel de rendez-vous','Bonjour [Prénom] ! Nous te rappelons ton rendez-vous chez All in One by Eva prévu le [date] à [heure] pour [prestation]. Nous avons hâte de t\'accueillir.','2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `modeles_messages` (`id`, `type`, `titre`, `contenu`, `created_at`, `updated_at`) VALUES (4,'fidelisation','Message de fidélisation','Bonjour [Prénom] ! Cela fait quelque temps que nous ne t\'avons pas accueilli chez All in One by Eva. Nous serions heureux de te retrouver pour un nouveau moment de détente et de beauté.','2026-10-31 12:48:42','2026-10-31 12:48:42');

-- recompenses
INSERT INTO `recompenses` (`id`, `libelle`, `type`, `seuil_points`, `valeur`, `is_active`, `created_at`, `updated_at`) VALUES (1,'Réduction de 2 000 FCFA','reduction',50,2000,1,'2026-10-31 12:48:42','2026-10-31 12:48:42');
INSERT INTO `recompenses` (`id`, `libelle`, `type`, `seuil_points`, `valeur`, `is_active`, `created_at`, `updated_at`) VALUES (2,'Manucure offerte','prestation_offerte',100,NULL,1,'2026-10-31 12:48:42','2026-10-31 12:48:42');

-- migrations
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_01_01_000100_create_salon_settings_and_audit_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_01_01_000200_create_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_01_01_000300_create_catalogue_and_staff_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_01_01_000400_create_rendez_vous_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_01_01_000500_create_finance_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_01_01_000600_create_stock_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_01_01_000700_create_loyalty_and_communication_tables',1);

-- Compte administratrice initial (mot de passe : ChangezMoi2026! — à modifier dès la première connexion)
INSERT INTO `users` (`role_id`, `name`, `email`, `password`, `is_active`, `created_at`, `updated_at`)
SELECT `id`, 'Administratrice', 'admin@allinone.sn', '$2y$12$dD6.rI4dcvhgrwgKPZ86W.fQrPJtXoNOB.tL3R6dl6Dc3blUjJDaS', 1, NOW(), NOW() FROM `roles` WHERE `name` = 'admin';


-- Fin du script.
