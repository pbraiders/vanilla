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
 * description: force classique display when mobile case.
 * GET: op1=0|1
 * author: Olivier JULLIEN - 2010-06-15
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
    require(PBR_PATH.'/includes/class/coption.php');
    $pOption = new COption('1', 0, 1);
    $bForceDesktop = FALSE;

    /** Read input parameters
     ************************/
    $pOption->ReadInput(INPUT_GET);
    if( $pOption->GetValue()==1 )
    {
        $bForceDesktop = TRUE;
    }

    /** Write cookie
     ***************/
    if( CCookie::GetInstance()->Write( CAuth::GetInstance()->GetUsername()
                                     , CAuth::GetInstance()->GetSession()
                                     , CAuth::GetInstance()->GetLanguage(), $bForceDesktop)===FALSE )
    {
        $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible d\'écrire le cookie', E_USER_NOTICE, FALSE);
    }// if( CCookie::GetInstance()->Write(...

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/clean.php');

    /** redirect to page
     *******************/
    header('Location: '.PBR_URL);
    exit;
