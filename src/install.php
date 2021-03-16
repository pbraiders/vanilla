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
 * description: build and display the main page.
 *        POST: act=install, pwd=<password>, pwdc=<password>, usr=<username>
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2017-12-04, mysql_get_client_info does not
 *                                       exist anymore on php7.0
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.3.0');
    define('PBR_PATH',dirname(__FILE__));

    /** Include config
     *****************/
    require(PBR_PATH.'/config.php');

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize context
     *********************/
    require(PBR_PATH.'/includes-install/context.php');
   $tMessageCode = array();

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes-install/authdb.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/class/cuser.php');
    $pUser = new CUser();
    $sPHPVersionRequired   = '5.2';
    $sMYSQLVersionRequired = '5.0';
    $sPHPVersion   = phpversion();
    if( function_exists( 'mysql_get_client_info' )) {
        $sMYSQLVersion = mysql_get_client_info();
    } else {
        $sMYSQLVersion = '5.0';
    }

    /** Prerequiste test
     ******************/
    if( !version_compare( $sPHPVersion, $sPHPVersionRequired, '>=')
     && !version_compare( $sMYSQLVersion, $sMYSQLVersionRequired, '>=') )
    {
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'les versions ne sont pas valides', E_USER_ERROR, FALSE);
        $tMessageCode[] = 3;
    }//if( !version_compare( $sPHPVersion, $sPHPVersionRequired, '>=') )

    /** Read input parameters
     ************************/
    if( filter_has_var( INPUT_POST, 'install') )
    {
        $pUser->ReadInput(INPUT_POST);
        if( $pUser->IsValidNew()===FALSE )
        {
            // Parameters are not valid
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'les paramètres ne sont pas valides', E_USER_ERROR, FALSE);
            $tMessageCode[] = 4;
        }//if( $pUser->IsValidNew()===FALSE )
    }//if( ($iMessageCode==0) && filter_has_var( INPUT_POST, 'install') )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $pHeader->SetNoCache();
    $pHeader->SetTitle('Installation de PBRaiders '.PBR_VERSION);
    $pHeader->SetDescription('Installation de PBRaiders '.PBR_VERSION);
    $pHeader->SetKeywords('install,installation,installer');

    /** Display or install
     *********************/
    require(PBR_PATH.'/includes/display/header.php');
    if( (count($tMessageCode)==0) && ($pUser->IsValidNew()===TRUE) )
    {
        // Install
        require(PBR_PATH.'/includes-install/install.php');
    }
    else
    {
        // Display
        require(PBR_PATH.'/includes-install/welcome.php');
    }//if( (...
    require(PBR_PATH.'/includes/display/footer.php');

   /** Delete objects
    *****************/
    unset($pUser,$pHeader);
    include(PBR_PATH.'/includes/init/clean.php');
