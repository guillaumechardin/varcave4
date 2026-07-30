<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Varcave Website Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'general' => [
        'opSuccess' => 'operation successful',
        'opFailed' => 'operation failed',
        'cancel' => 'cancel',
        'close' => 'close',
        'error' => 'error',
        'warning' => 'warning',
        'delete' => 'delete',
        'edit' => 'edit',
        'enable' => 'enable',
        'disable' => 'disable',
        'yes' => 'yes',
        'no' => 'no',
        'action' => 'action',
        'choose' => 'choose',
        'chooseFile' => 'select a file',
        'choose_file_short' => 'Choose file',
        'noFileSelected' => 'no file selected',
        'save' => 'save',
        'unauthorized' => 'you are not authorized to access this page',
        'fileNotFound' => 'file not found',
        'none' => 'None',
        'ok' => 'ok',
        'change' => 'change',
        'caveNotFound' => 'cave not found',
        'send' => 'send',
        'never' => 'never',
        'unlock' => 'unlock',
        'creation_date' => 'creation date',
        'download' => 'download',
        'file_already_exists' => 'The file already exists',
        'file_deleted' => 'File deleted',
        'create' => 'Create',
        'add' => 'add',
        'reminder' => 'reminder',
        'information' => 'information',
        'email_sent' => 'Your message has been sent',
        'email_not_sent' => 'Your message could not be sent',
    ],

    'homepage' => [
        'title' => 'Varcave Home Page',
        'hometext' => 'Welcome to the Var Cave Database',
        'connectinfo' => 'Sign in to access more features',
        'homeAnnouncements' => 'News',
        'lastCavesUpdates' => 'Latest Updates',
        'featuredCave' => 'Featured Cave',
        'create_new_cave' => 'Create Cave',
        'current_cave_count' => 'The database currently contains :count caves',
    ],

    /**
     * Cave page
     */
    'caveshow' => [
        'pagetitle' => 'Cave Details: :cavename',
        'caveFound' => 'cave found',
        'caveNotFound' => 'cave not found',
        'informations' => 'information',
        'change_history' => 'change history',
        'description' => 'description',
        'access' => 'access',
        'caveMaps' => 'maps',
        'photos' => 'cave photos',
        'bibliography' => 'bibliography',
        'nobiblio' => 'No bibliography',
        'documents' => 'documents',
        'mainEntrance' => 'Cave entrance',
        'nearCaves' => 'Nearby caves',
        'informChange' => 'Suggest an update',
        'caveAddToFav' => 'cave added to favorites',
        'caveDelFav' => 'cave removed from favorites',
        'cave-entrance' => 'entrance #:nbr',
        'coord_copied' => 'copied to clipboard',
        'edited_by' => 'edited by:',
        'rescue_info' => 'rescue information',
        'rescue_documents' => 'Rescue documents',
        'no_rescue_data' => 'no rescue data',
        'coordinates' => 'coordinates',
        'copy_cave' => 'Copy cave',
        'edit_cave' => 'Edit cave',
        'dl_gpx' => 'Download GPX',
        'add_favorites' => 'Add to favorites',
        'dl_pdf' => 'Download PDF',
        'copy_name_hint' => 'New cave name',
        'copy_ref_hint' => 'New cave reference',
        'new_ref' => 'New reference',
        'new_name' => 'New cave name',
        'copy_cave_modal_title' => 'Copy of: :cavename',
        'cave_copy_success' => 'Cave copied successfully',
        'email_subject' => 'Cave update request: :caveName',
        'contact_form' => 'Contact form',
    ],

    /**
     * Cave update page
     */
    'cave_update' => [
        'save_fail' => 'The setting could not be saved',
        'coord_deleted' => 'Coordinates deleted',
        'coord_not_deleted' => 'Coordinates could not be deleted',
        'new_coords' => 'New coordinates',
        'unlock_del_coords' => 'Allow coordinate deletion',
        'unlock_del_files' => 'Allow file deletion',
        'files' => 'Attached documents',
        'editCave' => 'Editing cave ":caveName"',
        'choose_category' => 'Choose category',
        'add_new_file' => 'Add a new document',
        'add_file_note' => 'Add information',
        'file_note' => 'File note',
        'file_added' => 'file added',
        'file_not_owned' => 'The specified file is not linked to this cave',
        'note_not_owned' => 'The specified file note is not linked to this cave',
        'note_updated' => 'File note saved.',
        'cave_created' => 'Cave created successfully',
        'add_change_log' => 'Add a change log entry',
        'add_changelog_reminder' => 'Remember to update the change history',
        'add_changelog' => 'Add a change log entry',
        'changelog_added' => 'Change log entry added',
        'changelog_is_visible' => 'Visible on the home page',
        'changelog_deleted' => 'Change log entry deleted',
        'show_on_homepage' => 'Show entry on the home page',
        'hide_on_homepage' => 'Hide entry from the home page',
        'changelog_not_owned' => 'The specified change log entry is not linked to this cave',
        'changelog_updated' => 'The change log entry has been updated',
        'add_bibliography' => 'Add a bibliography entry',
        'enable-biblio-delete' => 'Allow bibliography deletion',
        'nonexistant_bibliography_id' => 'Bibliography ID does not exist for this cave',
        'bibliography_updated' => 'Bibliography updated',
    ],

    /**
     * Login page
     */
    'login' => [
        'loginFormTitle' => 'Sign In',
        'loginFormUser' => 'Username',
        'loginFormPwd' => 'Password',
        'login' => 'Sign in',
        'forgotten' => 'Forgot your password?',
        'account_expired' => 'Your account has expired',
        'account_disabled' => 'Your account is disabled',
    ],

    /**
     * Navigation bar
     */
        'navbar' => [
        'caves' => 'caves',
        'allcaves' => 'complete list',
        'search' => 'search',
        'home' => 'Home',
        'log-in' => 'Sign in',
        'logout' => 'Sign out',
        'account' => 'My account',
        'modeLight' => 'Light',
        'modeDark' => 'Dark',
        'modeSystem' => 'System',
        'findCave' => 'find a cave',
        'administration' => 'administration',
        'site_settings' => 'site settings',
        'support_info' => 'technical information',
        'users_mgmt' => 'user management',
        'statistics' => 'cave statistics',
        'resources' => 'resources',
        'files' => 'files',
        'create_cave' => 'Create a cave',
        'eula_edit' => 'Edit Terms of Use',
        'pagefields' => 'Page field management',
        'more_menu' => 'more',
        'about_page' => 'about this site',
    ],

    /**
     * My profile
     */
    'profile' => [
        'hello_user' => 'Hello :firstname :lastname',
        'current-password' => 'Current password',
        'new-password' => 'New password',
        'confirm-password' => 'Confirm password',
        'confirm-password2' => 'Confirm your password',
        'changepassword' => 'Change password',
        'password-updated' => 'Password updated successfully',
        'settings' => 'settings',
        'bookmarks' => 'saved caves',
        'security' => 'security',
        'others' => 'others',
        'bookmark-deleted' => 'Favorite cave removed',
        'not-bookmark-owner' => 'you do not own this bookmark',
        'roles' => 'roles',
        'your_roles' => 'you have the following roles:',
        'show_eula_title' => 'Terms of Use',
        'accept_terms' => 'I accept the Terms of Use',
        'eula_was_accepted_at' => 'You accepted the Terms of Use on:',
        'eula_not_yet_accepted' => 'You have not yet accepted the Terms of Use.',
    ],

    /*
     * Authentication-specific messages
     */
    'auth' => [
        'user_email_fail' => 'The :username/:email combination is invalid.',
        'reset_password' => 'Reset password',
        'send_link' => 'Send password reset link',
        'to_login' => 'Back to sign-in page',
        'change_pwd' => 'Change password',
    ],

    'table_cave' => [
        'uuid' => 'UUID',
        'name' => 'Name',
        'addendum' => 'Addendum',
        'annex' => 'Annex',
        'edit_year' => 'Publication year',
        'bibliography' => 'Bibliography',
        'map_name' => 'IGN map',
        'town' => 'Municipality',
        'CO2' => 'CO2',
        'access_text' => 'Location / Access',
        'airflow_date' => 'Airflow observation date',
        'explore_date' => 'Exploration date',
        'description' => 'Cave description',
        'document_of_origin' => 'Source documents',
        'length' => 'Length',
        'explorers' => 'Explorers',
        'geology' => 'Geology',
        'hydrology' => 'Hydrology',
        'inventor' => 'Discoverer',
        'place' => 'Locality',
        'mountain_range' => 'Mountain range',
        'airflow' => 'Airflow',
        'numero_arrondissement' => 'District number',
        'numero_commune' => 'Municipality number',
        'numero_departement' => 'Department number',
        'cave_ref' => 'Cave reference',
        'depth' => 'Depth',
        'max_depth' => 'Vertical range',
        'area' => 'Geographical area',
        'topographer' => 'Surveyor',
        'is_location_protected' => 'Protected cave coordinates',
        'json_coords' => 'Coordinates',
        'coords_GPS_checked' => 'Coordinates verified',
        'zone_natura_2000' => 'Natura 2000 area',
        'anchors' => 'Anchors',
        'no_access' => 'Inaccessible',
        'PNR_SB' => 'Sainte-Baume Regional Natural Park',
        'created_at' => 'Creation date',
        'updated_at' => 'Last updated',
        'deleted_at' => 'Deletion date',
        'ENS' => 'Sensitive Natural Area',
        'foret_domaniale' => 'State forest',
        'cave_type' => 'Cave type',
        'cave_type_lst0' => 'OTHER',
        'cave_type_lst1' => 'CAVE',
        'cave_type_lst2' => 'POTHOLE',
        'files' => 'Files',
        'biologyDocuments' => 'Biospeleology documents',
        'documents' => 'Documents',
        'cave_maps' => 'Cave maps',
        'sketch_access' => 'Access sketch',
        'rescue_data' => 'Cave rescue data',
        'pollution' => 'Cave pollution',
        'pollution_list' => [
            'none' => 'NONE',
            'low' => 'LOW',
            'medium' => 'MEDIUM',
            'high' => 'HIGH',
        ],
    ],

    'searchPage' => [
        'title' => 'advanced search',
        'topTitle' => 'Search',
        'results' => 'search results',
        'datatables' => [

            "decimal" => "",
            "emptyTable" => "No data available",
            "info" => "Showing caves _START_ to _END_ of _TOTAL_",
            "infoEmpty" => "Showing 0 to 0 of 0 entries",
            "infoFiltered" => "(filtered from _MAX_ entries)",
            "infoPostFix" => "",
            "thousands" => ",",
            "lengthMenu" => "Show _MENU_ entries",
            "loadingRecords" => "Loading records...",
            "processing" => "Loading...",
            "search" => "Search:",
            "zeroRecords" => "No matching records found",
            "paginate" => [
                "first" => "First",
                "last" => "Last",
                "next" => "Next",
                "previous" => "Previous",
            ],
            "aria" => [
                "orderable" => "Sort by this column",
                "orderableReverse" => "Reverse the order of this column",
            ],

        ],
    ],

    'coordinateSystems' => [
        'wgs84' => 'WGS84',
        'lambert3' => 'Lambert III',
        'lambert93' => 'Lambert 93',
        'utm' => 'UTM',
    ],
        'settings' => [
        'title' => 'site configuration',
        'settings_saved' => 'setting saved',
        'site_settings' => 'site settings',
        'show_adv_opt' => 'show advanced options',

        'loglevel_dsp' => 'Logging level',
        'loglevel_hlp' => 'Application logging level',

        'websiteFullName_dsp' => 'Website header',
        'websiteFullName_hlp' => 'Header displayed at the top of every page',

        'disclaimer_dsp' => 'Disclaimer',
        'disclaimer_hlp' => 'Disclaimer displayed at the bottom of each cave sheet',

        'maxSearchResults_default_dsp' => 'Maximum search results',
        'maxSearchResults_default_hlp' => 'Maximum number of records returned by searches',

        'stats_dsp' => 'Statistics',
        'stats_hlp' => 'Enable or disable cave view statistics',

        'displayedStats_dsp' => 'Displayed statistics',
        'displayedStats_hlp' => 'Number of statistics displayed on the statistics page',

        'welcomePageShowLastUpdate_dsp' => 'Latest cave updates on homepage',
        'welcomePageShowLastUpdate_hlp' => 'Number of recent cave updates displayed on the homepage',

        'adminIP_dsp' => 'Administrator IP addresses',
        'adminIP_hlp' => 'List of administrator IP addresses excluded from statistics',

        'noAccessDisclaimer_dsp' => 'Inaccessible cave warning',
        'noAccessDisclaimer_hlp' => 'Warning displayed when a cave is marked as inaccessible',

        'location_protected_message_dsp' => 'Protected cave warning',
        'location_protected_message_hlp' => 'Warning displayed when cave coordinates are protected',

        'footerMsg_dsp' => 'Footer message',
        'footerMsg_hlp' => 'Message displayed at the bottom of every page',

        'pdf_author_dsp' => 'PDF author',
        'pdf_author_hlp' => 'Author stored in generated PDF metadata',

        'datatables_items_selector_dsp' => 'DataTables page length selector',
        'datatables_items_selector_hlp' => 'Available page size values for DataTables',

        'datatables_max_items_dsp' => 'Default page length',
        'datatables_max_items_hlp' => 'Default number of items displayed in tables. It is recommended to use one of the values defined in datatables_items_selector.',

        'excludedcopyfields_dsp' => 'Excluded fields',
        'excludedcopyfields_hlp' => 'Fields excluded when copying a cave',

        'ol_zoom_map_lvl_dsp' => 'OpenLayers zoom level',
        'ol_zoom_map_lvl_hlp' => 'Default map zoom level on cave pages',

        'use_geoapi_dyn_map_img_pdf_dsp' => 'Use GEO API for PDF maps *unused*',
        'use_geoapi_dyn_map_img_pdf_hlp' => 'Use the GEO API to generate access sketches in PDFs',

        'static_map_service_url_dsp' => 'Static map service URL *unused*',
        'static_map_service_url_hlp' => 'URL used to generate static maps',

        'max_news_homepage_dsp' => 'Homepage news items',
        'max_news_homepage_hlp' => 'Number of news items displayed on the homepage',

        'timezone_dsp' => 'Time zone',
        'timezone_hlp' => 'PHP time zone identifier (see https://www.php.net/manual/en/timezones.php)',

        'smtp_server_dsp' => 'SMTP server',
        'smtp_server_hlp' => 'FQDN or IP address of the SMTP server',

        'smtp_port_dsp' => 'SMTP port',
        'smtp_port_hlp' => 'SMTP communication port',

        'smtp_user_dsp' => 'Username',
        'smtp_user_hlp' => 'SMTP authentication account',

        'smtp_userpwd_dsp' => 'Password',
        'smtp_userpwd_hlp' => 'SMTP account password',

        'smtp_useauth_dsp' => 'Authentication',
        'smtp_useauth_hlp' => 'Enable SMTP authentication',

        'smtp_sender_dsp' => 'Sender address',
        'smtp_sender_hlp' => 'Email address used as the sender',

        'smtp_max_attach_size_dsp' => 'Maximum attachment size',
        'smtp_max_attach_size_hlp' => 'Maximum size of a single attachment (KB)',

        'smtp_max_attach_global_size_dsp' => 'Maximum total attachment size',
        'smtp_max_attach_global_size_hlp' => 'Maximum combined size of all attachments',

        'smtp_cave_update_recipients_dsp' => 'Cave update recipients',
        'smtp_cave_update_recipients_hlp' => 'Email addresses receiving cave update requests',

        'smtp_general_inquiry_recipient_dsp' => 'Contact form recipients',
        'smtp_general_inquiry_recipient_hlp' => 'Email addresses receiving contact form submissions',

        'smtp_server_debuglbvl_dsp' => 'SMTP debug level',
        'smtp_server_debuglbvl_hlp' => 'SMTP logging level (0 = disabled, 4 = maximum). Leave at 0 in production.',

        'mail_use_captcha_dsp' => 'Enable CAPTCHA',
        'mail_use_captcha_hlp' => 'Use CAPTCHA to prevent unsolicited emails',

        'captcha_secret_key_dsp' => 'CAPTCHA secret key',
        'captcha_secret_key_hlp' => 'Private CAPTCHA API key',

        'captcha_public_key_dsp' => 'CAPTCHA public key',
        'captcha_public_key_hlp' => 'Public CAPTCHA API key registered for this domain',

        'pdf_coords_system_dsp' => 'PDF coordinate system',
        'pdf_coords_system_hlp' => 'Coordinate system used in generated PDFs',

        'pdf_map_zoom_dsp' => 'PDF map zoom level',
        'pdf_map_zoom_hlp' => 'Zoom level used for PDF mini-maps',

        'pdf_map_cache_delay_dsp' => 'Mini-map cache lifetime',
        'pdf_map_cache_delay_hlp' => 'Cache lifetime for PDF mini-maps (hours)',

        'pdf_file_cache_delay_dsp' => '*UNUSED* PDF cache lifetime',
        'pdf_file_cache_delay_hlp' => '*UNUSED* Cache lifetime for generated PDF cave sheets (hours)',

        'pdf_minimap_service_dsp' => 'Mini-map provider',
        'pdf_minimap_service_hlp' => 'Select the provider used to generate PDF access maps',

        'legal_notice_dsp' => 'Legal notice',
        'legal_notice_hlp' => 'Displays legal information on the homepage',

        'near_caves_max_radius_dsp' => 'Nearby cave search radius',
        'near_caves_max_radius_hlp' => 'Search radius for nearby caves (meters)',

        'near_caves_max_number_dsp' => 'Maximum nearby caves',
        'near_caves_max_number_hlp' => 'Maximum number of nearby caves displayed',

        'user_must_accept_EULA_dsp' => 'Mandatory Terms of Use acceptance',
        'user_must_accept_EULA_hlp' => 'Require users to accept the Terms of Use before accessing the website',

        'user_login_tip_dsp' => 'Login example',
        'user_login_tip_hlp' => 'Example username displayed on the login page',

        'authorized_cave_file_type_dsp' => 'Allowed cave file types',
        'authorized_cave_file_type_hlp' => 'Allowed file extensions for cave uploads',

        'authorized_resources_file_type_dsp' => 'Allowed resource file types',
        'authorized_resources_file_type_hlp' => 'Allowed file extensions for resource uploads',

        'include_GPX_details_dsp' => 'Include additional GPX information',
        'include_GPX_details_hlp' => 'Include extra information in generated GPX files (photos, depth, length, etc.)',

        'featured_caves_delay_dsp' => 'Featured cave rotation delay',
        'featured_caves_delay_hlp' => 'Maximum delay before changing the featured cave on the homepage (seconds)',

        'category_name' => [
            'captcha' => 'Google CAPTCHA',
            'general' => 'General configuration',
            'config_email' => 'Email configuration',
            'pdf' => 'PDF generation',
            'geo_api' => 'GEO API configuration',
            'config_site_stats' => 'Cave statistics',
        ],
    ],

        /**
     * User administration page
     */
    'users' => [
        'users' => 'users',
        'roles' => 'roles',
        'import_data' => 'data import',
        'user_fetched' => 'User retrieved',
        'edit_user' => 'Edit user',
        'deleted_title' => 'User deleted',
        'deleted_msg' => 'User :username has been successfully deleted',
        'save_title' => 'User saved',
        'save_msg' => 'User :username has been successfully saved',
        'unlock_delete' => 'unlock deletion',
        'no_expiry' => 'no expiration',
        'change_pwd' => 'change password',
        'role_saved' => ':username: roles have been saved',
        'choose_role_add' => 'Select roles to add',
        'choose_role_del' => 'Select roles to remove',
        'role_add' => 'add role',
        'role_del' => 'remove role',
        'users_mgmt' => 'User Management',
        'import_results' => "Users successfully imported.\nResults:\n  Processed rows: :total\n  Added: :added\n  Updated: :updated\n  Failed: :failed",
        'accnt_expiration_date' => 'Account expiration date',
        'import_help' => 'Complete the form, select an import file, then submit the form to add users.',
        'csv_format' => 'CSV format',
        'no_header' => 'No header row',
        'csv_encoding' => 'UTF-8 encoding',
        'field_format' => 'Field format',
        'import_settings' => 'Import settings',
        'select_file' => 'Select a file',
        'disable_account' => 'Disable account',
        'datatables' => [
            'info' => 'Showing users _START_ to _END_ of _TOTAL_',
            'search' => '- filtered from _MAX_',
        ],

        'table_users' => [
            'username' => 'username',
            'firstname' => 'first name',
            'lastname' => 'last name',
            'email' => 'email address',
            'caving_group' => 'club',
            'password' => 'password',
            'is_disabled' => 'Account disabled',
            'expires_at' => 'expiration date',
            'eula_accepted' => 'Terms of Use accepted',
        ],
    ],

    'statistics' => [
        'pageTitle' => 'Most viewed caves',
        'position' => 'Rank',
        'name' => 'Cave name',
        'views' => 'Views',
    ],

    'resources' => [
        'page_title' => 'Documents / Resources',
        'pageTitle' => 'Documents and Resources',
        'documents' => 'documents',
        'add_file' => 'Add a new document',
        'file_title' => 'File title',
        'file_title_phldr' => 'Name of the file to share',
        'create_group' => 'Create a new group',
        'new_group' => 'New group',
        'new_group_phldr' => 'Create a new group',
        'use_existing_group' => 'Use an existing group',
        'choose_group' => 'Choose a group',
        'choose' => 'choose',
        'select_rights' => 'Define access permissions',
        'add_rem_group' => 'SELECT / REMOVE GROUP',
        'description' => 'description',
        'rights_mgt' => 'Permission management',
        'sort_order' => 'Sort order',
        // 28/07/2026 'show_more' => 'Click to expand/add',
        'confirm-delete' => 'Do you want to delete this file?',
        'start_build_gpx_file' => 'Generate GPX',
        'gpx_file_description' => 'Var caves in GPX format',
        'create_file' => 'Add document',
        'build_gpx' => 'GPX generation',
    ],

    'roles' => [
        'admin' => 'Administrator',
        'user' => 'Website user',
        'public' => '',
        'cave-editor' => 'Cave editor',
        'announcement-editor' => 'Announcement editor',
        'resource-admin' => 'Resource administrator',
    ],

    'pdf' => [
        'speleometry' => 'Speleometry',
        'access' => 'Access information',
        'coordinates' => 'Coordinates',
        'pdf_subject' => 'Offline cave information',
    ],

    'cave_files' => [
        'cave_maps' => 'maps',
        'photos' => 'photos',
        'sketch_access' => 'access sketch',
        'biologyDocuments' => 'biospeleology documents',
        'documents' => 'general documents',
        'rescue_data' => 'rescue data',
    ],

    'contact_form' => [
        'enter_name' => 'Enter your name',
        'hint_enter_name' => 'First name LAST NAME',
        'email' => 'Your email address',
        'hint_email' => 'Enter a valid email address',
        'invalid_email' => 'The specified email address is invalid',
        'subject' => 'Message subject',
        'hint_subject' => 'The cave link will automatically be added to the subject',
        'message_body' => 'Message',
        'hint_message_body' => 'Describe the changes you would like to suggest (coordinates, name, etc.).',
        'send_copy' => 'Receive a copy of this message',
    ],

    'email' => [
        'caveUpdate' => [
            'welcomeTxt' => "Hello,\nThe following cave update has been submitted by :name.",
            'default_link' => 'Direct link to cave: :caveName',
        ]
    ],

    'eula' => [
        'edit_eula_title' => 'Edit Terms of Use',
        'eula_saved' => 'Terms of Use saved',
        'select_eula' => 'Select Terms of Use',
        'language' => 'Language',
        'eula_text' => 'Terms of Use',
        'accept' => 'Accept',
    ],

    'page_fields' => [
        'title' => 'Page field management',
        'visiblity_updated' => 'Field visibility updated',
        'sort_order_updated' => 'Field order saved',
        'page_choice' => 'Select page to edit',
        'field_page' => 'Fields for page: :pagename',
        'hide_field' => 'Hide field',
        'show_field' => 'Show field',
    ],

    'about_page' => [
        'title' => 'About this website',
    ],
];
