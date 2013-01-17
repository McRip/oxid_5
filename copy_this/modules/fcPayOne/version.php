<?php

if(is_array($_GET) && array_key_exists('key', $_GET) && md5($_GET['key']) == '5fce785e30dbf6e1181d452c6057bfd3') {
    if (!function_exists('getShopBasePath')) {
        /**
         * Returns shop base path.
         *
         * @return string
         */
        function getShopBasePath()
        {
            return dirname(__FILE__).'/../../';
        }
    }

    set_include_path(get_include_path() . PATH_SEPARATOR . getShopBasePath());

    /**
     * Returns true.
     *
     * @return bool
     */
    if ( !function_exists( 'isAdmin' )) {
        function isAdmin()
        {
            return true;
        }
    }

    error_reporting( E_ALL ^ E_NOTICE );

    // custom functions file
    include getShopBasePath() . 'modules/functions.php';
    // Generic utility method file
    require_once getShopBasePath() . 'core/oxfunctions.php';
    // Including main ADODB include
    require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

    echo fcpoRequest::getVersion();
}