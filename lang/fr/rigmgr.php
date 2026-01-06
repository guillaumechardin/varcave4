<?php

return [
    'welcome' => 'Tu ne me vois pass 1431fff',
    'configof' => 'Configuration de :servername',
     /* rigmgr.general messages */
    'general' => [
        'opSuccess' => 'Opération terminée',
        'opFailed' => 'Operation échouée',
        'cancel' => 'Annuler',
        'error' => 'erreur',
        'warning' => 'Avertissement',
        'delete' => 'supprimer',
        'edit' => 'modifier',
        'enable' => 'activer',
        'disable' => 'désactiver',
        'yes' => 'oui',
        'no' => 'non',
        'action' => 'action',
        //'windows' => 'Windows', //do not translate
        //'linux' => 'Linux', //do not translate
        'otherOS' => 'Autre OS',
        'chooseFile' => 'Sélectionnez un fichier',
        'noFileSelected' => 'Pas de fichier sélectionné',
        'save' => 'enregistrer',
        'unauthorized' => 'Vous n\'êtes pas autorisé à accéder à cette page',
        'fileNotFound' => 'Fichier non trouvé',
        'none' => 'Aucun',
    ],

    /* rigmgr.errors */
    'errors' => [
        'save' => 'Erreur lors de la sauvegarde de la configuration',
        'delete' => 'Erreur lors de la suppression',
    ],

     'servers' => [
        'serverList' => 'Serveurs disponibles',
        'Rename' => 'Renommer',
        'renamesrv' => 'Nouveau nom',
        'propertyChanged' => 'Paramètre de serveur modifié',
        'deleteServ' => 'Suppression du serveur',
        'serverDeleted' => 'Serveur supprimé',
        'deleteError' => 'Échec de la suppression du serveur',
        'confirmDeleteSrv'=>'Êtes vous sur de vouloir supprimer le serveur:',
        'createSuccess' => 'Serveur créé avec succès, nom du nouveau serveur :newname',
        'LastContactSecondAgo' => 'Dernier contact il y à :timeStr.',
        
    ],
    
    /* in servershow.blade */
    'servershow' => [
        'configuration' => 'Configuration',
        'inputyourconf' => 'Saisissez votre configuration',
        'ipv4' => 'Addresse IP',
        'confignotes' => 'Notes de configuration',
        'inputyournote' => 'Vous pouvez annoter votre configuration ici.',
        'configSaved' => 'Configuration enregistrée',
        'miningTool' => 'Outil de minage',
        'selectTool' => '--Choisissez un outil--',
        'number' => 'Numéro',
        'configuration' => 'Configuration',
        'ipAdd' => 'Adresse IPv4',
        'note' => 'Description',
        'lastUpdate' => 'Dernière modif.',
        'action' => 'Action',
        'tab_settings' => 'Configurations',
        'tab_serverSettings' => 'Paramètres serveur',
        'serverSettingsTitle' => 'Paramètres du serveur',
        'tab_serverPerf' => 'Performances',
        'serverPerfTitle' => 'Performances Xmrig',
        'serverAvailConfigurations' => 'Configurations serveur dispo.',
        'deployoption' => 'Options de déploiement',
        'deployFeature' => [
            'deploy_miner_services' => 'Services',
            'deploy_crontabs' => 'Crontab',
        ],
        'selectDateRange' => 'Selectionnez une autre plage',
        'graphTitle' => 'Graphes de performance serveur',
        'values' => 'Valeurs',
        'time' => 'Temps',
    ],



    

        /* settings page strings */
    'settings' => [
        'title' => 'Paramètres de Rigmanager',
        'tab-miningtools' => 'Outils de minage',
        'tab-miningtools-services' => 'Services systemd',
        'tab-others' => 'Autres paramètres',
        'addtool' => 'Ajouter un nouvel outil',
        'selectOS' => 'Sélectionnez l\'OS',
        'addMinerSuccess' => 'Outil de minage ajouté avec succès.',
        'delMinerSuccess' => 'Outil de minage supprimé avec succès.',
        'addservice' => 'Ajouter un service',
        'servicedata' => 'Données du service',
        'selectminer' => 'Vous devez sélectionner un outil',
        'addServiceSuccess' => 'Service ajouté avec succès.',
        'deleteServiceSuccess' => 'Service supprimé avec succès.',
        'servicename'  => 'Nom du service',
        'setname' => 'Indiquez un nom pour le service.',
        'rigmgrSettings' => 'Paramètres généraux de rigmanager',

    ],

    /* localized colname */
    'db-colname' => [
        'mining_tools' => [
            'toolname' => 'Miner',
            'version' => 'Version',
            'arch' => 'Architecture',
            'path' => 'Chemin d\'accès',
            'sha1_checksum' => 'Somme de controle SHA1',
        ],
        'common' => [
            'update_at' => 'Mis à jour le',
            'created_at' => 'Date de creation',
        ],
    ],

    /* ServerPackageController */
    'packageconsolidate' => [
        'zipCreateErr' => 'Impossible de créer fichier zip temporaire :filePath',

    ],

    /* post update commands view form template */
    'postupdcmd' => [
        'postupdacmd' => 'Commandes de post mise à jour',
    ],

    

    

    'timeunits' => [
        'secondsAbr' => 'sec|secs',
        'minutesAbr' => 'min|mins',
        'hoursAbr' => 'hr|hrs',
        'daysAbr' => 'j|jrs',
        'yearsAbr' => 'an|ans',
    ],
];



?>