<?php
/*************************************************************************
 *                                                                       *
 * Copyright (C) 2010   Olivier JULLIEN - PBRAIDERS.COM                  *
 * Tous droits réservés - All rights reserved                            *
 *                                                                       *
 *************************************************************************
 *                                                                       *
 * Except if expressly provided in a dedicated License Agreement,you     *
 * are not authorized to:                                                *
 *                                                                       *
 * 1. Use,copy,modify or transfer this software component,module or      *
 * product,including any accompanying electronic or paper documentation  *
 * (together,the "Software").                                            *
 *                                                                       *
 * 2. Remove any product identification,copyright,proprietary notices    *
 * or labels from the Software.                                          *
 *                                                                       *
 * 3. Modify,reverse engineer,decompile,disassemble or otherwise         *
 * attempt to reconstruct or discover the source code,or any parts of    *
 * it,from the binaries of the Software.                                 *
 *                                                                       *
 * 4. Create derivative works based on the Software (e.g. incorporating  *
 * the Software in another software or commercial product or service     *
 * without a proper license).                                            *
 *                                                                       *
 * By installing or using the "Software",you confirm your acceptance     *
 * of the hereabove terms and conditions.                                *
 *                                                                       *
 *************************************************************************/
/*************************************************************************
 * file encoding: UTF-8
 * description: initialize web app:
 *              - set memory limit
 *              - turn register globals off
 *              - disable magic quotes
 *              - set error level
 *              - activate error managment
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

    /** Update memory
     ****************/
    define('PBR_MEMORY_LIMIT', '16M');
    if( function_exists('memory_get_usage')
        && ( (int) @ini_get('memory_limit') < abs(intval(PBR_MEMORY_LIMIT)) ) )
    {
        @ini_set('memory_limit', PBR_MEMORY_LIMIT);
    }// if( function_exists('memory_get_usage')

    /** Turn off PHP time limit
     **************************/
    @set_time_limit(0);

    /** Register global
     ******************/
    unset($sBuffer);
    $sBuffer = @ini_get('register_globals');
    if( isset($sBuffer) && ($sBuffer!=FALSE) && (strlen($sBuffer)>0) && ($sBuffer!='0') )
    {
        // register globals is ON
        // Variables that shouldn't be unset
        $tNoUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        // Merge All
        $tAll = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES
                            , isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
        // Parse All
        foreach( $tAll as $sKey=>$sValue )
        {
            // Unset duplicate GLOBALS param
            if( !in_array($sKey, $tNoUnset) && isset($GLOBALS[$sKey]) )
            {
                $GLOBALS[$sKey] = NULL;
                unset($GLOBALS[$sKey]);
                // Double unset to circumvent the zend_hash_del_key_or_index hole in PHP <4.4.3 and <5.1.4
                unset($GLOBALS[$sKey]);
            }//if( !in_array($sKey, $tNoUnset) && isset($GLOBALS[$sKey]) )
        }// foreach( $tAll as $sKey=>$sValue )
    }// if( isset($sBuffer) && ($sBuffer!=FALSE) && (strlen($sBuffer)>0) && ($sBuffer!='0') )
    unset($sBuffer);
    unset($tNoUnset);

    /** Disable magic quotes
     ***********************/
    $sPHPVersion=phpversion();
    $sPHPVersionRequired='5.3';
    if( (version_compare( $sPHPVersion, $sPHPVersionRequired, '<')) && (get_magic_quotes_runtime()==1) )
    {
        set_magic_quotes_runtime(0);
    }//if( (version_compare( $sPHPVersion, $sPHPVersionRequired, '<')) && (get_magic_quotes_runtime()==1) )
    @ini_set('magic_quotes_sybase', 0);

    // Strip slashes from GET/POST
    if ( get_magic_quotes_gpc() )
    {
        $_GET    = stripslashes_deep($_GET   );
        $_POST   = stripslashes_deep($_POST  );
    }// if ( get_magic_quotes_gpc() )

    /** Set error level
     ******************/
	error_reporting(E_ALL);

    /** Error managment
     ******************/
    define('PBR_LOG_DIR', 'log');
    require(PBR_PATH.'/includes/class/clog.php');
    require(PBR_PATH.'/includes/class/cerrorlist.php');
    require(PBR_PATH.'/includes/function/errors.php');

    /** Validate user
     *****************/
    require(PBR_PATH.'/includes/class/cuser.php');
    // Set username
    CUser::GetInstance()->SetUsername('install');
    // Set session
    CUser::GetInstance()->SetSession('2010');

?>
