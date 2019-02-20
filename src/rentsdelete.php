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
 * description: build and display the rents delete page.
 *        POST: rey=year
 *              can=cancel case
 *              con=confirm case, tok=<token>, rey=year
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.2.0');
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
    require(PBR_PATH.'/includes/init/authadmin.php');

    /** Cancel
     *********/
    if( filter_has_var( INPUT_POST, 'can') )
    {
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'parameters.php');
        exit;
    }//Cancel

    /** Read input date
     ******************/
    require(PBR_PATH.'/includes/class/cdate.php');
    $pDate = new CDate();
    $iYear = 0;

    if( filter_has_var( INPUT_POST, CDate::YEARTAG)===TRUE )
    {
        $iYear = filter_input( INPUT_POST, CDate::YEARTAG, FILTER_VALIDATE_INT);
    }//if( filter_has_var(...

    if( is_null($iYear) || ($iYear===FALSE) || ($iYear>$pDate->GetCurrentYear()) || ($iYear<CDate::MINYEAR) )
    {
        // Date is not valid
        unset($pDate);
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'parameters.php?error=3');
        exit;
    }//if( $pDate->GetRequestYear()>=$pDate->GetCurrentYear() )

    /** Create session
     *****************/
    require(PBR_PATH.'/includes/class/cphpsession.php');
    CPHPSession::CreateSession();

    /** Delete
     *********/
    if( filter_has_var( INPUT_POST, 'con') && (CPHPSession::GetInstance()->ValidInput(INPUT_POST)===TRUE) )
    {
        // Clean SESSION
        CPHPSession::CleanToken();
        CPHPSession::Clean();
        // Delete
        require(PBR_PATH.'/includes/db/function/rentsdel.php');
        $iReturn = RentsDel( CAuth::GetInstance()->GetUsername()
                           , CAuth::GetInstance()->GetSession()
                           , GetIP().GetUserAgent()
                           , $iYear );

        unset($pDate);

        // Failed
        if( ($iReturn===FALSE) || ($iReturn<0) )
        {
            RedirectError( $iReturn, __FILE__, __LINE__ );
            exit;
        }//if( ($iReturn===FALSE) || ($iReturn<0) )
        // Succeeded
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'parameters.php?error=2');
        exit;
    }//Delete

    // Clean SESSION token
    CPHPSession::CleanToken();

    /** Generate and write SESSION token
     ***********************************/
    $sToken = CPHPSession::GetInstance()->WriteToken();
    if( $sToken===FALSE )
    {
        $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible de fixer le jeton de la session', E_USER_ERROR, TRUE);
        CPHPSession::CleanToken();
        CPHPSession::Clean();
        unset($pDate);
        RedirectError( 1, __FILE__, __LINE__ );
        exit;
    }//if( $sToken===FALSE )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sBuffer = 'Supprimer les anciennes réservations';
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords('delete,erase,effacer,supprimer,suppression,rent');

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/rentsdelete.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pHeader);
    unset($pDate);
    include(PBR_PATH.'/includes/init/clean.php');
?>
