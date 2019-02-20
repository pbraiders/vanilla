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
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.0.1');
    define('PBR_PATH',dirname(__FILE__));

    /** Include config
     *****************/
    require(PBR_PATH.'/config.php');

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/init/init.php');

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Logout
     *********/
    if( CUser::GetInstance()->IsAuthenticated() )
    {
        // Logout
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/sessionlogoff.php');
        $iReturn = SessionLogOff( CUser::GetInstance()->GetUsername()
                                , CUser::GetInstance()->GetSession()
                                , GetIP().GetUserAgent());
    }//if( CUser::GetInstance()->IsAuthenticated() )

    /** Erase cookie
     ***************/
    if( CUser::GetInstance()->IsValid() )
    {
        $sUsername=CUser::GetInstance()->GetUsername();
    }
    else
    {
        $sUsername=CUser::DEFAULT_USER;
    }//if( CUser::GetInstance()->IsValid() )
    if( CCookie::GetInstance()->Write( $sUsername, CUser::DEFAULT_SESSION) ===FALSE )
    {
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( $sUsername, $sTitle, 'impossible d\'écrire le cookie', E_USER_NOTICE, FALSE);
    }// if( CCookie::GetInstance()->Write(...

    /** Invalidate user
     ******************/
    CUser::GetInstance()->Invalidate();

    /** Build Message
     ****************/
    $iMessageCode=0;
    if( filter_has_var(INPUT_GET, 'error') )
    {
        $tFilter = array('options' => array('min_range' => 1, 'max_range' => 2));
        $iMessageCode=(integer)filter_input( INPUT_GET, 'error', FILTER_VALIDATE_INT, $tFilter);
    }//if( filter_has_var(INPUT_GET, 'error') )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    CHeader::GetInstance()->SetNoCache();
    CHeader::GetInstance()->SetTitle('Déconnexion');
    CHeader::GetInstance()->SetDescription('Déconnexion');
    CHeader::GetInstance()->SetKeywords('deconnection,deconnexion,logout');

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/displayheader.php');
    require(PBR_PATH.'/includes/display/displaylogout.php');
    require(PBR_PATH.'/includes/display/displayfooter.php');

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
