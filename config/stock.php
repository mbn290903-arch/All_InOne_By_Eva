<?php

return [
    // Valeur par défaut si la clé "stock_consommation_prestations" n'existe pas dans parametres_salon.
    'consommation_prestations' => false,

    // Presque épuisé = quantité <= seuil_alerte * ce ratio.
    'ratio_presque_epuise' => 0.5,

    'roles_gestion' => ['admin'],                       // créer / modifier / approvisionner
    'roles_sortie'  => ['admin', 'receptionniste'],     // enregistrer une sortie
    'roles_lecture' => ['admin', 'receptionniste'],
];