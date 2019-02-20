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
 * description: logout a connected user.
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION',' ');
    define('PBR_PATH',dirname(__FILE__));

    /** Include config
     *****************/
    require(PBR_PATH.'/config.php');

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize context
     *********************/
    require(PBR_PATH.'/includes/init/context.php');

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/authuser.php');

    /** Initialize
     *************/
    $iMessageCode = GetMessageCode();

    /** Logout
     *********/
    require(PBR_PATH.'/includes/db/function/sessionlogoff.php');
    SessionLogOff( CAuth::GetInstance()->GetUsername()
                 , CAuth::GetInstance()->GetSession()
                 , GetIP().GetUserAgent() );

    /** Erase cookie
     ***************/
    if( CCookie::GetInstance()->Write( CAuth::GetInstance()->GetUsername(), CAuth::DEFAULT_SESSION,CAuth::DEFAULT_LANGUAGE,FALSE)===FALSE )
    {
        $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible d\'écrire le cookie', E_USER_NOTICE, FALSE);
    }// if( CCookie::GetInstance()->Write(...

    /** Invalidate authentication
     ****************************/
    CAuth::GetInstance()->Invalidate();

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $pHeader->SetNoCache();
    $pHeader->SetTitle('Déconnexion');
    $pHeader->SetDescription('Déconnexion');
    $pHeader->SetKeywords('deconnection,deconnexion,logout');

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/logout.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pHeader);
    include(PBR_PATH.'/includes/init/clean.php');
?>
