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
 * update: Olivier JULLIEN - 2010-06-15 - improvements
 * update: Olivier JULLIEN - 2017-04-12 - PHP7 fixes
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') )
    die('-1');

    /** Turn off PHP time limit
     **************************/
    @set_time_limit(0);

    /** Beginning time
     *****************/
    $fGlobalBeginningTime = GetTime();

    /** Update memory
     ****************/
    // Default value
    define('PBR_MEMORY_LIMIT', '64M');

    // Update memory
    if( function_exists('memory_get_usage')
        && ( (int) @ini_get('memory_limit') < abs(intval(PBR_MEMORY_LIMIT)) ) )
    {
        @ini_set('memory_limit', PBR_MEMORY_LIMIT);
    }// if( function_exists('memory_get_usage')

    /** Set error level
     ******************/
    error_reporting(E_ALL ^ E_NOTICE);
 //   error_reporting(E_ALL | E_STRICT);

    /** Error management
     *******************/
    define('PBR_LOG_DIR', 'log');
    require(PBR_PATH.'/includes/class/cdfmgmt.php');
    require(PBR_PATH.'/includes/class/clog.php');
    require(PBR_PATH.'/includes/class/cerrorlist.php');
    require(PBR_PATH.'/includes/function/errors.php');
    if( defined('PBR_LOG') && (PBR_LOG===1) )
    {
        $sDir = PBR_PATH.'/'.PBR_LOG_DIR;
        $bReturn = CLog::GetInstance()->Open( $sDir, CLog::LOGFILENAME );
        if( !$bReturn )
        {
            $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( 'install', $sTitle, 'impossible d\'ouvrir le fichier de log', E_USER_WARNING, FALSE);
        }//if( !$bReturn )
    }//if( defined('PBR_LOG') && (PBR_LOG===1) )

    /** Validate user
     *****************/
    require(PBR_PATH.'/includes/class/cauth.php');
    CAuth::GetInstance()->Invalidate();
    // Set username
    CAuth::GetInstance()->SetUsername('install');
    // Set session
    CAuth::GetInstance()->SetSession('2010');

?>
