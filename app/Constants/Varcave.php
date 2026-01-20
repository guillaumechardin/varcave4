<?php

namespace app\Constants;

/**
 * Class Varcave
 *
 * Centralizes all reusable constants for the Varcave project.
 * Includes statuses, response modes, file types, and other global values.
 */
final class Varcave
{
    // ------------------------
    // General Status Constants
    // ------------------------
    public const SUCCESS = 'success';
    public const ERROR   = 'error';
    public const WARNING = 'warning';
    

    // ------------------------
    // Response Modes
    // ------------------------
    public const AS_JSON = 0;
    public const AS_VIEW = 1;

    // ------------------------
    // File Types
    // ------------------------
    public const AUTHORIZED_FILES = array(
        'jpg','jpeg','png', //image files
        'ini', 'txt', //text files
        
        'doc','docx', 'pdf', //document files
        'xls', 'xlsx', //document files
    );

    // Prevent instantiation
    private function __construct() {}
}