<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Varcave website Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'general' => [
        'opSuccess' => 'opération terminée',
        'opFailed' => 'operation échouée',
        'cancel' => 'annuler',
        'error' => 'erreur',
        'warning' => 'avertissement',
        'delete' => 'supprimer',
        'edit' => 'modifier',
        'enable' => 'activer',
        'disable' => 'désactiver',
        'yes' => 'oui',
        'no' => 'non',
        'action' => 'action',
        'chooseFile' => 'sélectionnez un fichier',
        'noFileSelected' => 'pas de fichier sélectionné',
        'save' => 'enregistrer',
        'unauthorized' => 'vous n\'êtes pas autorisé à accéder à cette page',
        'fileNotFound' => 'fichier non trouvé',
        'none' => 'Aucun',
        'ok' => 'ok',
        'change' => 'changer',
        'caveNotFound' => 'cavité non trouvée',
        'send' => 'envoyer',
        
    ],

    'homepage' => [
        'title' => 'Page d\'accueil Varcave',
        'hometext' => 'Bienvenue sur la base de données des cavités du Var',
        'connectinfo' => 'Connectez vous accédez à plus d\'options',
        'homeAnnouncements' => 'Actualités',
        'lastCavesUpdates' => 'Dernières nouveautés',
        'randomCave' => 'Une cavité au hasard',
    ],

    /**
     * Cave page
     */
    'caveshow' => [
        'caveFound' => 'cavité trouvée',
        'caveNotFound' => 'cavité non trouvée',
        'informations' => 'informations',
        'description' => 'description',
        'access' => 'accès',
        'caveMaps' => 'topographies',
        'bibliography' => 'bibliographie',
        'nobiblio'  => 'Aucune bibliographie',
        'documents' => 'documents',
        'mainEntrance' => 'Entrée cavité',
        'nearCaves' => 'Cavités proches',


    ],

    /**
     * Login page
     */
    'login' => [
        'loginFormTitle' => 'Connexion',
        'loginFormUser'  => 'Nom d\'utilisateur',
        'loginFormPwd'  => 'Mot de passe',
        'login' => 'Se connecter',
    ],

    /**
     * Nav bar translation
     */
    'navbar' => [
        'caves' => 'cavités',
        'allcaves' => 'liste complète',
        'search' => 'recherche',
        'home' => 'Accueil',
        'log-in' => 'Connexion',
        'logout' => 'Déconnexion',
        'account' => 'Mon compte',
        'modeLight' => 'Clair',
        'modeDark' => 'Sombre',
        'modeSystem' => 'Système',
        'findCave' => 'chercher une cavité',
    ],

    /**
     * My profile strings
     */
    'profile' => [
        'current-password' => 'Mot de passe actuel',
        'new-password' => 'Nouveau mot de passe',
        'confirm-password' => 'Confirmez le mot de passe',
        'changepassword' => 'Modification du mot de passe',
        'password-updated' => 'Mot de passe modifié avec succès',
        'settings' => 'paramètres',
        'security' => 'sécurité',
        'others' => 'autres',
    ],

    'table_cave' => [
        'name' => "Nom",
        'addendum' => "Additif",
        'annex' => "Annexe",
        'edit_year' => "Année d'édition",
        'bibliography' => "Bibliographie",
        'map_name' => "Carte IGN",
        'town' => "Commune",
        'CO2' => "CO2",
        'access_text' => "Situation/accès",
        'airflow_date' => "Date courant d'air",
        'explore_date' => "Date d'exploration",
        'description' => "Description de la cavité",
        'document_of_origin' => "Documents d'origine",
        'length' => "Développement",
        'explorers' => "Explorateurs",
        'geology' => "Géologie",
        'hydrology' => "Hydrologie",
        'inventor' => "Inventeur",
        'place' => "lieu-dit",
        'mountain_range' => "Massif",
        'airflow' => "Courant d'air",
        'numero_arrondissement' => "Numéro d'arrondissement",
        'numero_commune' => "Numéro de commune",
        'numero_departement' => "Numéro de département",
        'cave_ref' => "Numéro de cavité",
        'depth' => "Profondeur",
        'max_depth' => "Dénivelée",
        'area' => "Secteur géographique",
        'topographer' => "Topographe",
        'random_coordinates' => "Coordonnées masquées",
        'json_coords' => "Coordonnées",
        'coords_GPS_checked' => "Coordonnées vérifiées",
        'zone_natura_2000' => "Zone natura 2000",
        'anchors' => "Brochage",
        'no_access' => "Inaccessible",
        'PNR_SB' => "PNR Sainte-Baume",
        'created_at' => "Date de création",
        'updated_at' => "Date de modification",
        'deleted_at' => "Date de suppression",
        'ENS' => "Espace Naturel Sensible",
        'foret_domaniale' => "Forêt domaniale",
        'cave_type' => "Type cavité",
        'cave_type_lst0' => "AUTRE",
        'cave_type_lst1' => "GROTTE",
        'cave_type_lst2' => "AVEN",
        'files' => "Fichiers",
        'biologyDocuments' => "Documents biospeleo",
        'documents' => "Documents",
        'cave_maps' => "Topographies cavité",
        'sketch_access' => "Croquis d'accès",
        'rescue_data' => "Données secours spéléo",
        'pollution' => "Pollution cavité",
        'pollution_list' => [
            'none' => "NULLE",
            'low' => "FAIBLE",
            'medium' => "MOYENNE",
            'hight' => "FORTE",
        ],

    ],

    'searchPage' => [
        'title' => 'recherche avancée',
        'topTitle' => 'Recherche',
        'results' => 'résultats de recherche',
        'datatables' => [
            
                "decimal"=>        "",
                "emptyTable"=>    "Pas de données disponible",
                "info"=>          "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty"=>      "Showing 0 to 0 of 0 entries",
                "infoFiltered"=>   "(filtered from _MAX_ total entries)",
                "infoPostFix"=>    "",
                "thousands"=>      ",",
                "lengthMenu"=>    "Show _MENU_ entries",
                "loadingRecords"=> "Chargement des enregistrements...",
                "processing"=>     "Chargement...",
                "search"=>         "Search:",
                "zeroRecords"=>    "No matching records found",
                "paginate"=> [
                    "first"=>      "Premier",
                    "last"=>       "Dernier",
                    "next"=>       "Suivant",
                    "previous"=>   "Précédent"
                ],
                "aria"=> [
                    "orderable"=>  "Order by this column",
                    "orderableReverse"=> "Reverse order this column"
                ],
            
        ],
    ],

];
