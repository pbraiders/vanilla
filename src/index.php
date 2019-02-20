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
 *          POST: act=calendar, cuy=<current year>, cum=<current month>, rem=<requested month>,
 *                rey=<requested year>, go:goto date or pre:previous month or nex:next month
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
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
    require(PBR_PATH.'/includes/init/authuser.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/class/cdate.php');
    $pDate = new CDate();
    $bAdmin = FALSE;
    $bReturn = TRUE;

    /** Read input parameters
     ************************/
    if( filter_has_var(INPUT_POST, CDate::YEARTAG) )
    {
        // Read POST parameters
        $bReturn = $pDate->ReadInput(INPUT_POST);
    }
    elseif( filter_has_var(INPUT_GET, CDate::YEARTAG) )
    {
        // Read GET parameters
        $bReturn = $pDate->ReadInput(INPUT_GET);
    }//if( filter_has_var(
    if( !$bReturn )
    {
        // mandatory parameters are not valid
        unset($pDate);
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'action interdite', E_USER_WARNING, FALSE);
        RedirectError( -2, __FILE__, __LINE__ );
        exit;
    }//if( !$bReturn )

    /** Get the rent day infos
     *************************/
    require(PBR_PATH.'/includes/db/function/rentsmonthget.php');
    $tRecordset = RentsMonthGet( CAuth::GetInstance()->GetUsername()
                               , CAuth::GetInstance()->GetSession()
                               , GetIP().GetUserAgent()
                               , $pDate);

    if( !is_array($tRecordset) )
    {
        // Error
        RedirectError( $tRecordset, __FILE__, __LINE__ );
        exit;
    }//if( !is_array($tRecordset) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sBuffer = $pDate->GetMonthName( $pDate->GetRequestMonth() ).' '.$pDate->GetRequestYear();
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords($sBuffer);

    /** Admin case
	 *************/
    if( SessionValid( CAuth::GetInstance()->GetUsername(), CAuth::GetInstance()->GetSession(), 10, GetIP().GetUserAgent())>0 )
	{
	    $bAdmin = TRUE;
	}//admin case

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/calendar.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pDate);
    unset($pHeader);
    include(PBR_PATH.'/includes/init/clean.php');
?>
