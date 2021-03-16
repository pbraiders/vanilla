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
 * description: build and display the day print page.
 *         GET: rey=year, rem=month, red=day
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.2.1');
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
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/cpaging.php');
    $pDate = new CDate();
    $pPaging = new CPaging();

    /** Read input parameters
     ************************/

    // Read date values
    if( $pDate->ReadInput( INPUT_GET, TRUE )===FALSE )
    {
        // mandatory parameters are not valid
        unset( $pDate, $pPaging);
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'date invalide', E_USER_WARNING, FALSE);
        RedirectError( -2, __FILE__, __LINE__ );
        exit;
    }//if( $pDate->ReadInput(...

    /** Build the page
     *****************/

    // Get the reservations count
    require(PBR_PATH.'/includes/db/function/rentsgetcount.php');
    $iReturn = RentsGetCount( CAuth::GetInstance()->GetUsername()
                            , CAuth::GetInstance()->GetSession()
                            , GetIP().GetUserAgent()
                            , $pDate );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<0) )
    {
        unset( $pDate, $pPaging);
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<0) )

    // Get rents
    require(PBR_PATH.'/includes/db/function/rentsget.php');
    $tRecordset = RentsGet( CAuth::GetInstance()->GetUsername()
                          , CAuth::GetInstance()->GetSession()
                          , GetIP().GetUserAgent()
                          , $pDate
                          , $pPaging );

    // Error
    if( !is_array($tRecordset) )
    {
        unset( $pDate, $pPaging);
        RedirectError( $tRecordset, __FILE__, __LINE__ );
        exit;
    }//if( !is_array($tRecordset) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sFormTitle  = $pDate->GetRequestDay().' ';
    $sFormTitle .= $pDate->GetMonthName( $pDate->GetRequestMonth() ).' ';
    $sFormTitle .= $pDate->GetRequestYear();
    $pHeader->SetNoCache();
    $pHeader->ToPrint();
    $pHeader->SetTitle($sFormTitle);
    $pHeader->SetDescription($sFormTitle);
    $pHeader->SetKeywords($sFormTitle);
    $pHeader->SetTitle('Imprimer');
    $pHeader->SetDescription('Imprimer');
    $pHeader->SetKeywords('imprimer,print');

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/dayprint.php');

    /** Delete objects
     *****************/
    unset( $pDate, $pPaging);
    include(PBR_PATH.'/includes/init/clean.php');
