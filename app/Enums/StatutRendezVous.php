<?php

namespace App\Enums;

enum StatutRendezVous: string
{
    case EnAttente = 'en_attente';
    case Confirme = 'confirme';
    case ClientArrive = 'client_arrive';
    case EnCours = 'en_cours';
    case Termine = 'termine';
    case Annule = 'annule';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Confirme => 'Confirmé',
            self::ClientArrive => 'Client arrivé',
            self::EnCours => 'En cours',
            self::Termine => 'Terminé',
            self::Annule => 'Annulé',
            self::Absent => 'Client absent',
        };
    }

public function tone(): string
{
    return match ($this) {
        self::EnAttente => 'amber',
        self::Confirme => 'sky',
        self::ClientArrive => 'indigo',
        self::EnCours => 'violet',
        self::Termine => 'emerald',
        self::Annule, self::Absent => 'stone',
    };
}

    /** Classes Tailwind du bloc dans le calendrier (écrites en entier, obligatoire). */
    public function classes(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-amber-50 border-amber-500',
            self::Confirme => 'bg-sky-50 border-sky-600',
            self::ClientArrive => 'bg-indigo-50 border-indigo-600',
            self::EnCours => 'bg-violet-50 border-violet-600',
            self::Termine => 'bg-emerald-50 border-emerald-600',
            self::Annule => 'bg-stone-100 border-stone-400 line-through opacity-60',
            self::Absent => 'bg-stone-100 border-stone-400 opacity-60',
        };
    }

    /** Statuts qui libèrent le créneau du professionnel. */
    public function libereLeCreneau(): bool
    {
        return in_array($this, [self::Annule, self::Absent], true);
    }

    /** @return list<self> */
    public static function bloquants(): array
    {
        return array_values(array_filter(self::cases(), fn (self $s) => ! $s->libereLeCreneau()));
    }

    /** @return list<self> Statuts vers lesquels on peut passer depuis celui-ci. */
/** @return list<self> Statuts vers lesquels on peut passer depuis celui-ci. */

public function suivants(): array
{
    return match ($this) {
        self::EnAttente => [self::Confirme, self::Annule],
        self::Confirme => [self::ClientArrive, self::Annule, self::Absent],
        self::ClientArrive => [self::EnCours, self::Annule],
        self::EnCours => [self::Termine],
        self::Termine, self::Annule, self::Absent => [],
    };
}

public function peutPasserA(self $nouveau): bool
{
    return in_array($nouveau, $this->suivants(), true);
}

/** Boutons à afficher dans le modal (envoyés au JavaScript). */
public function actions(): array
{
    return array_map(
        fn (self $s) => ['value' => $s->value, 'label' => $s->label()],
        $this->suivants()
    );
}
/** Statuts pour lesquels on peut choisir une nouvelle date. */
public function peutEtreDeplace(): bool
{
    return in_array($this, [self::EnAttente, self::Confirme, self::Annule, self::Absent], true);
}
    /** Classes Tailwind du badge de statut (dashboard). */
    public function badge(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-[#fff1df] text-[#b87925]',
            self::Confirme => 'bg-[#e8f0f8] text-[#4c719b]',
            self::ClientArrive => 'bg-[#f1e8f5] text-[#79518b]',
            self::EnCours => 'bg-violet-100 text-violet-700',
            self::Termine => 'bg-emerald-100 text-emerald-700',
            self::Annule, self::Absent => 'bg-stone-100 text-stone-500',
        };
    }
}