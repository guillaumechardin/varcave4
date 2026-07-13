<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Varcave website Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'general' => [
        'opSuccess' => 'opération réussie',
        'opFailed' => 'operation échouée',
        'cancel' => 'annuler',
        'close' => 'fermer',
        'error' => 'erreur',
        'warning' => 'avertissement',
        'delete' => 'supprimer',
        'edit' => 'modifier',
        'enable' => 'activer',
        'disable' => 'désactiver',
        'yes' => 'oui',
        'no' => 'non',
        'action' => 'action',
        'choose' => 'choisir',
        'chooseFile' => 'sélectionnez un fichier',
        'choose_file_short' => 'Choisir fichier',
        'noFileSelected' => 'pas de fichier sélectionné',
        'save' => 'enregistrer',
        'unauthorized' => 'vous n\'êtes pas autorisé à accéder à cette page',
        'fileNotFound' => 'fichier non trouvé',
        'none' => 'Aucun',
        'ok' => 'ok',
        'change' => 'changer',
        'caveNotFound' => 'cavité non trouvée',
        'send' => 'envoyer',
        'never' => 'jamais',
        'unlock' => 'déverrouiller',
        'creation_date' => 'date de création',
        'download' => 'télécharger',
        'file_already_exists' => 'Le fichier existe déjà',
        'file_deleted' => 'Fichier supprimé',
        'create' => 'Créer',
        'add' => 'ajouter',
        'reminder' => 'rappel',
        
    ],

    'homepage' => [
        'title' => 'Page d\'accueil Varcave',
        'hometext' => 'Bienvenue sur la base de données des cavités du Var',
        'connectinfo' => 'Connectez vous accédez à plus d\'options',
        'homeAnnouncements' => 'Actualités',
        'lastCavesUpdates' => 'Dernières nouveautés',
        'featuredCave' => 'Cavité du jour',
        'create_new_cave' => 'Création de cavité',
    ],

    /**
     * Cave page
     */
    'caveshow' => [
        'caveFound' => 'cavité trouvée',
        'caveNotFound' => 'cavité non trouvée',
        'informations' => 'informations',
        'change_history'    => 'historique des modifications',
        'description' => 'description',
        'access' => 'accès',
        'caveMaps' => 'topographies',
        'photos' => 'photos de la cavité',
        'bibliography' => 'bibliographie',
        'nobiblio'  => 'Aucune bibliographie',
        'documents' => 'documents',
        'mainEntrance' => 'Entrée cavité',
        'nearCaves' => 'Cavités proches',
        'informChange' => 'Proposer une modification',
        'caveAddToFav' => 'cavité ajoutée aux favoris',
        'caveDelFav' => 'cavité supprimée des favoris',
        'cave-entrance' => 'entrée n°:nbr',
        'coord_copied' => 'copié ds le presse papier',
        'edited_by' => 'modifié par:',
        'rescue_info' => 'informations secours',
        'rescue_documents' => 'Documents info secours',
        'no_rescue_data' => 'pas de données secours',
        'coordinates' => 'coordonnées',
        'copy_cave' => 'Copier cavité',
        'edit_cave' => 'Modifier la cavité',
        'dl_gpx' => 'Téléchargement GPX',
        'add_favorites' => 'Ajouter aux cavités favorites',
        'dl_pdf' => 'Télécharger la fiche en PDF',
        'copy_name_hint' => 'Nouveau nom cavité',
        'copy_ref_hint' => 'Nouveau numéro cavité',
        'new_ref' => 'Nouveau numéro',
        'new_name' => 'Nouveau nom cavité',
        'copy_cave_modal_title' => 'Copie de: :cavename',
        'cave_copy_success' => 'Cavité copiée avec succès',
    ],

    /**
     * Cave udpate page
     */
    'cave_update' => [
        'save_fail' => 'Le paramètre n\'a pas été enregistré',
        'coord_deleted' => 'Les coordonnées ont été supprimées',
        'coord_not_deleted' => 'Les coordonnées n\'ont pas ont été supprimées',
        'new_coords' => 'Nouvelles coordonnées',
        'unlock_del_coords' => 'Autoriser la suppression des coordonnées',
        'unlock_del_files' => 'Autoriser la suppression des fichiers',
        'files' => 'fichiers',
        'editCave' => 'Modification de la cavité ":caveName"',
        'choose_category' => 'Choisir la catégorie',
        'add_new_file' => 'Ajouter un nouveau fichier',
        'add_file_note' => 'Ajouter une information',
        'file_note' => 'Note de fichier',
        'file_added' => 'fichier ajouté',
        'file_not_owned' => 'Le fichier spécifié n\'est pas lié à la cavité',
        'note_not_owned' => 'La note de fichier spécifiée n\'est pas lié à la cavité',
        'note_updated' => 'Note de fichier enregistrée.',
        'cave_created' => 'Cavité créée avec succès',
        'add_change_log' => 'Ajouter une note de modification',
        'add_note_reminder' => 'Pensez à mettre a jour l\'historique de modification',
        'add_edit_note' => 'Ajouter une note de modification',

        
    ],

    /**
     * Login page
     */
    'login' => [
        'loginFormTitle' => 'Connexion',
        'loginFormUser'  => 'Nom d\'utilisateur',
        'loginFormPwd'  => 'Mot de passe',
        'login' => 'Se connecter',
        'forgotten' => 'Mot de passe oublié.'
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
        'administration' => 'administration',
        'site_settings' => 'paramètres du site',
        'support_info' => 'informations technique',
        'users_mgmt' => 'gestion des utilisateurs',
        'statistics' => 'Statistiques cavités',
        'resources' => 'ressources',
        'files' => 'fichiers',
        'create_cave' => 'Créer une cavité',
    ],

    /**
     * My profile strings
     */
    'profile' => [
        'hello_user' => 'Bonjour :firstname :lastname',
        'current-password' => 'Mot de passe actuel',
        'new-password' => 'Nouveau mot de passe',
        'confirm-password' => 'Confirmez le mot de passe',
        'confirm-password2' => 'Confirmation de votre mot de passe',
        'changepassword' => 'Modification du mot de passe',
        'password-updated' => 'Mot de passe modifié avec succès',
        'settings' => 'paramètres',
        'bookmarks' => 'cavités enregistrées',
        'security' => 'sécurité',
        'others' => 'autres',
        'bookmark-deleted' => 'Cavité favorite supprimée',
        'not-bookmark-owner' => 'vous n\'etes pas propriétaire de ce signet',
        'roles' => 'roles',
        'your_roles' => 'vous disposez des roles suivants:',
        'show_eula_title' => 'Charte d\'utilisation',
        'accept_terms' => 'J\'accepte les terme de la charte',
        'eula_was_accepted_at' => 'Vous avez accepté la charte d\'utilisation le:',
        'eula_not_yet_accepted' => 'Vous n\'avez pas encore accepté la charte d\'utilisation.',

    ],

    /*
     * Specific auth messages
     */
    'auth' => [
        'user_email_fail' => 'Le couple :username/:email est invalide.',
        'reset_password' => 'Réinitialisation du mot de passe',
        'send_link' => 'Envoyer le lien de réinitialisation',
        'to_login' => 'Retour à la page de connexion',
        'change_pwd' => 'Changer le mot de passe',
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
        'is_location_protected' => "Protection coordonnées cavité",
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
            'high' => "FORTE",
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

    'coordinateSystems' => [
        'wgs84' => 'wgs84',
        'lambert3' => 'lambert 3',
        'lambert93' => 'lambert 93',
        'utm' =>   'utm',

    ],

    'settings' => [
        'title' => 'configuration du site',
        'settings_saved' => 'paramètre enregistré',
        'site_settings' => 'paramètres du site',
        'show_adv_opt' => 'afficher les options avancées',

        'loglevel_dsp' => 'Journalisation',
        'websiteFullName_dsp' => "Entête du site",
        'websiteFullName_hlp' => "Entête affichée en haut de chaque page",
        'disclaimer_dsp' => "Clause de nom responsabilité",
        'disclaimer_hlp' => "Disclaimer affiché en bas de chaque topo",
        'maxSearchResults_default_dsp' => "Nombre de résultat de recherche",
        'maxSearchResults_default_hlp' => "Nombre d'enregistrements maximum retournés dans la recherche",
        'stats_dsp' => "Statistiques",
        'stats_hlp' => "Activer ou non les statistique d'affichage de cavités",
        'displayedStats_dsp' => "Nombre de stat affichées",
        'displayedStats_hlp' => "Nombre de lignes affichées dans la page de statistiques",
        'welcomePageShowLastUpdate_dsp' => "Nombre de nouveautées (cavité) page accueil",
        'welcomePageShowLastUpdate_hlp' => "Le nombre de ligne de modification affiché en page d'accueil concernant les cavités",
        'adminIP_dsp' => "Adresses ip d'administrations",
        'adminIP_hlp' => "Liste des adresses IP utilisées par les administrateurs du site pour exclure des stats",
        'noAccessDisclaimer_dsp' => "Avertissement `inaccessible`",
        'noAccessDisclaimer_hlp' => "Avertissement affiché lorsque la cavité est inaccessible",
        
        'footerMsg_dsp' => "Message bas de page",
        'footerMsg_hlp' => "Message situé en bas de chaque page web",
        
        'excludedcopyfields_dsp' => "Champs exclus",
        'excludedcopyfields_hlp' => "Champs exclus lors d'une copie de cavité",
        
        'ol_zoom_map_lvl_dsp' => "Niveau de zoom carte openLayers",
        'ol_zoom_map_lvl_hlp' => "Défini le niveau de zoom par défaut lors de l'utilisation des API openlayers",
        'use_geoapi_dyn_map_img_pdf_dsp' => "Utiliser les geo API pour les PDF *unused*",
        'use_geoapi_dyn_map_img_pdf_hlp' => "Utilise les geo API API pour afficher les croquis de repérage dans les PDF",
        'static_map_service_url_dsp' => "Url static map *unused*",
        'static_map_service_url_hlp' => "Url à utiliser pour la génération des static map",
        
        
        'max_news_homepage_dsp' => "News affichées page accueil",
        'max_news_homepage_hlp' => "Détermine le nombre de news affichée sur la page d'accueil",
        'timezone_dsp' => "Fuseau horaire",
        'timezone_hlp' => "Identifiant de fuseau horaire voir (https://www.php.net/manual/fr/timezones.php)",
        'smtp_server_dsp' => "Serveur SMTP",
        'smtp_server_hlp' => "FQDN ou adresse IP du serveur SMTP à utiliser",
        'smtp_port_dsp' => "Port SMTP",
        'smtp_port_hlp' => "Port à utiliser pour la communication avec le serveur SMTP",
        'smtp_user_dsp' => "Utilisateur",
        'smtp_user_hlp' => "Compte utilisateur pour l'authentification",
        'smtp_userpwd_dsp' => "Mot de passe",
        'smtp_userpwd_hlp' => "Mot de passe du compte",
        'smtp_useauth_dsp' => "Authentification",
        'smtp_useauth_hlp' => "Spécifie si une authentification auprès du serveur doit être utilisée pour l'envoi des email",
        'smtp_sender_dsp' => "Adresse de l'expéditeur",
        'smtp_sender_hlp' => "Adresse d'expéditeur à utiliser pour l'envoi des messages",
        'smtp_max_attach_size_dsp' => "Taille max PJ",
        'smtp_max_attach_size_hlp' => "Taille max d'une PJ (en Ko)",
        'smtp_max_attach_global_size_dsp' => "Taille max globale PJ",
        'smtp_max_attach_global_size_hlp' => "Taille max globale des PJ dans un email",
        'smtp_cave_edit_recipients_dsp' => "Destinataire erreurs cavités",
        'smtp_cave_edit_recipients_hlp' => "Liste d'adresse email vers lequelles seront envoyés les emails du contact d'erreur cavité",
        'smtp_general_inquiry_recipient_dsp' => "Destinataires formulaire de contact",
        'smtp_general_inquiry_recipient_hlp' => "Liste d'adresse email vers lequelles seront envoyés les demandes issues du formulaire de contact",
        'smtp_server_debuglbvl_dsp' => "Niveau de journalisation",
        'smtp_server_debuglbvl_hlp' => "Niveau de journalisation SMTP (0 => désactivé, 4=maximum). Laisser à 0 en utilisation de production",
        'mail_use_captcha_dsp' => "Utilisation de captcha",
        'mail_use_captcha_hlp' => "Utilise captcha pour eviter les envois de mail non sollicités",
        'captcha_secret_key_dsp' => "Clef API captcha (secrete)",
        'captcha_secret_key_hlp' => "Clef API captcha personnelle",
        'captcha_public_key_dsp' => "Clef API captcha (publique)",
        'captcha_public_key_hlp' => "Clef API captcha publique (doit être enregistrée sur le domaine de ce site pour fonctionner correctement)",
        
        'pdf_coords_system_dsp' => "Système de coordonnées dans les PDF",
        'pdf_coords_system_hlp' => "Système de coordonnées utilisé pour l'affichage des coordonnées dans les fichiers PDF",
        'pdf_map_zoom_dsp' => 'Niveau de zoom carte',
        'pdf_map_zoom_hlp' => 'Niveau de zoom utilisé pour la génération des mini carte sur les PDF',
        'pdf_map_cache_delay_dsp' => 'Delai d\'expiration cache mini cartes',
        'pdf_map_cache_delay_hlp' => 'Delai d\'expiration du cache  des mini cartes (en heures) pour les PDF',
        'pdf_file_cache_delay_dsp' => 'Delai d\'expiration cache fichiers PDF',
        'pdf_file_cache_delay_hlp' => 'Delai d\'expiration de la mise en cache des fichiers PDF pour les fiches de cavité (en heures)',
        'pdf_minimap_service_dsp'  => 'Choix source mini carte',
        'pdf_minimap_service_hlp' => 'Sélection de la source pour les croquis d\'accès affiché dans les PDF',
        
        'legal_notice_dsp' => "Informations légales",
        'legal_notice_hlp' => "Affiche des informations dans le bloc d'informations légales en page d'accueil",
        'near_caves_max_radius_dsp' => "Rayon de recherche cavité proche",
        'near_caves_max_radius_hlp' => "Rayon de recherche des cavités proche d'une cavité affichée (en m)",
        'near_caves_max_number_dsp' => "Nombre max cavités proches",
        'near_caves_max_number_hlp' => "Nombre de cavités affiché lors de la recherches des cavités alentour",
        
        
        'user_must_accept_EULA_dsp' => 'Acceptation Charte d\'Utilisation obligatoire',
        'user_must_accept_EULA_hlp' => 'Indique si les utilisateurs doivent accepter la Charte d\'Utilisation avant accès au site.',
        'user_login_tip_dsp' => "Exemple nom d'utilisateur connexion",
        'user_login_tip_hlp' => "Nom d'utilisateur indiqué en exemple lors d'une connexion au site",
        'authorized_cave_file_type_dsp' => "Types de fichiers autorisés (cavités)",
        'authorized_cave_file_type_hlp' => "Liste des fichiers autorisés pour les téléversements dans les fiches de cavités",
        'authorized_resources_file_type_dsp' => "Types de fichiers autorisés (ressources)",
        'authorized_resources_file_type_hlp' => "Liste des fichiers autorisés pour les téléversements dans dans la page de resources",
        'include_GPX_details_dsp' => "Inclure des détails au GPX",
        'include_GPX_details_hlp' => "Permet d'ajouter des informations complémetaires au fichier GPX (présence photos, profondeur, devellopement, etc)",
        
        'featured_caves_delay_dsp' => 'Délai cavité mises en avant',
        'featured_caves_delay_hlp' => 'Délai maximum de changement de la cavité mise en avant en page d\'accueil (en secondes)',

        'category_name' => [
            'captcha' => 'google CAPTCHA',
            'general' => 'configuration générale',
            'config_email' => 'configuration messagerie',
            'pdf' => 'paramètres génération PDF',
            'geo_api' => 'Configuration GEOAPI',
            'config_site_stats' => 'Configuration statistique cavités'

        ],
    ],

    /**
     * Users administration page
     */
    'users' => [
        'users' => 'utilisateurs',
        'roles' => 'rôles',
        'import_data' => 'importation de données',
        'user_fetched' => 'Utilisateur récupéré',
        'edit_user' => 'Modification d\'un utilisateur',
        'deleted_title' => 'Utilisateur supprimé',
        'deleted_msg' => 'l\'utilisateur :username à été supprimé avec succès',
        'save_title' => 'Utilisateur enregistré',
        'save_msg' => 'l\'utilisateur :username à été enregistré avec succès',
        'unlock_delete' => 'déverrouiller la suppression',
        'no_expiry' => 'pas d\'expiration',
        'change_pwd' => 'modification du mot de passe',
        'role_saved' => ':username : les rôle sont enregistrés',
        'choose_role_add' => 'Choisir les rôles à ajouter',
        'choose_role_del' => 'Choisir des rôles à retirer',
        'role_add' => 'ajouter le rôle',
        'role_del' => 'retirer le rôle',
        'users_mgmt' => 'Gestion des utilisateurs',
        'import_results' => "Utilisateurs  importés avec succès.\n Resultats:\n  Lignes traité: :total\n  Ajouts: :added\n  mis à jour: :updated\n  Non traités: :failed",
        
        'table_users' => [
            'username' => 'nom d\'utilisateur',
            'firstname' => 'prénom',
            'lastname' => 'nom',
            'email' => 'adresse email',
            'caving_group' => 'club',
            'password' => 'mot de passe',
            'expires_at' => 'date d\'expiration',
            'eula_accepted' => 'Charte d\'utilisation acceptée',
        ],
    ],

    'statistics' => [
        'pageTitle' => 'Cavités les plus consultées',
        'position' => 'Position',
        'name' => 'Nom cavité',
        'views' => 'Nombre de vues'

    ],
    'resources' => [
        'page_title' => 'Fichiers / ressources',
        'pageTitle' => 'Fichiers ressources',
        'files' => 'Fichiers',
        'add_file' => 'Ajouter un nouveau fichier',
        'file_title' => 'Nom/titre du fichier',
        'file_title_phldr' => 'Nom du  fichier à partager',
        'create_group' => 'Créer nouveau groupe',
        'new_group' => 'Nouveau groupe',
        'new_group_phldr' => 'Créer nouveau groupe',
        'use_existing_group' => 'Utiliser groupe existant',
        'choose_group' => 'Choisir groupe',
        'choose' => 'choisir',
        'select_rights' => 'Définir les droits d\'accès',
        'add_rem_group' => 'SELECT/REMOVE GROUP',
        'description' => 'description',
        'rights_mgt' => 'Gestion des droits',
        'show_more' => 'Cliquez pour développer/ajouter',
    ],

    'roles' => [
            'admin' => 'Administrateur',
            'users' => 'Utilisateurs du site',
            'public' => '',
            'cave-editor' => '',
            'announcement-editor' => '',
            'resource-admin' => '',

    ],
    
    'pdf' => [
        'speleometry' => 'Spéléométrie',
        'access' => 'Information d\'accès',
        'coordinates' => 'Coordonnées',
    ],

    'cave_files' => [
        'cave_maps'         => 'topographies',
        'photos'            => 'photos',
        'sketch_access'     => 'croquis d\'accès',
        'biologyDocuments'  => 'document biologie',
        'documents'         => 'document généraux',
        'rescue_data'       => 'données secours',
    ],
    
];

