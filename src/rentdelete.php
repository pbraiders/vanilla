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
 * description: build and display the rent delete page.
 *         GET: rei=rent identifier
 *        POST: can=cancel case, rei=rent identifier
 *              con=confirm case, tok=token, rei=rent identifier
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/

/** Defines
 **********/
define('PBR_VERSION', '1.3.2');
define('PBR_PATH', dirname(__FILE__));

/** Include config
 *****************/
require(PBR_PATH . '/config.php');

/** Include functions
 ********************/
require(PBR_PATH . '/includes/function/functions.php');

/** Initialize context
 *********************/
require(PBR_PATH . '/includes/init/context.php');

/** Authenticate
 ***************/
require(PBR_PATH . '/includes/init/authuser.php');

/** Read input identifier
 ************************/
require(PBR_PATH . '/includes/class/crent.php');
$pRent = new CRent();
if (($pRent->ReadInputIdentifier(INPUT_GET) === FALSE) && ($pRent->ReadInputIdentifier(INPUT_POST) === FALSE)) {
    //Error
    unset($pRent);
    $sTitle = 'fichier: ' . basename(__FILE__) . ', ligne:' . __LINE__;
    ErrorLog(CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
    RedirectError(-2, __FILE__, __LINE__);
    exit;
} //if( ($pRent->ReadInputIdentifier(

/** Initialize
 *************/
require(PBR_PATH . '/includes/class/ccontact.php');
require(PBR_PATH . '/includes/class/cdate.php');
$pContact = new CContact();
$pDate = new CDate();
$sCalendarHRef = '';

// Get rent
require(PBR_PATH . '/includes/db/function/rentget.php');
$iReturn = RentGet(
    CAuth::GetInstance()->GetUsername(),
    CAuth::GetInstance()->GetSession(),
    GetIP() . GetUserAgent(),
    $pRent,
    $pDate,
    $pContact
);

// Error
if (($iReturn === FALSE) || ($iReturn <= 0)) {
    unset($pRent, $pDate, $pContact);
    if ($iReturn === 0) {
        $sTitle = 'fichier: ' . basename(__FILE__) . ', ligne:' . __LINE__;
        ErrorLog(CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
        $iReturn = -2;
    } //if( $iReturn==0 )
    RedirectError($iReturn, __FILE__, __LINE__);
    exit;
} //if( ($iReturn===FALSE) || ($iReturn<=0) )

/** Cancel
 *********/
if (filter_has_var(INPUT_POST, 'can')) {
    $sBuffer = CRent::IDENTIFIERTAG . '=' . $pRent->GetIdentifier();
    unset($pRent, $pDate, $pContact);
    include(PBR_PATH . '/includes/init/clean.php');
    header('Location: ' . PBR_URL . 'rent.php?' . $sBuffer);
    exit;
} //Cancel

/** Create session
 *****************/
require(PBR_PATH . '/includes/class/cphpsession.php');
CPHPSession::CreateSession();

/** Delete
 *********/
if (filter_has_var(INPUT_POST, 'con') && (CPHPSession::GetInstance()->ValidInput(INPUT_POST) === TRUE)) {
    // Clean SESSION
    CPHPSession::CleanToken();
    CPHPSession::Clean();

    // Build href for calendar
    $sCalendarHRef  = PBR_URL . 'day.php';
    $sCalendarHRef .= '?' . CDate::YEARTAG . '=' . $pDate->GetRequestYear();
    $sCalendarHRef .= '&' . CDate::MONTHTAG . '=' . $pDate->GetRequestMonth();
    $sCalendarHRef .= '&' . CDate::DAYTAG . '=' . $pDate->GetRequestDay();

    /** Delete
     *********/
    require(PBR_PATH . '/includes/db/function/rentdel.php');
    $iReturn = RentDel(
        CAuth::GetInstance()->GetUsername(),
        CAuth::GetInstance()->GetSession(),
        GetIP() . GetUserAgent(),
        $pRent
    );

    unset($pRent, $pDate, $pContact);

    // Failed
    if (($iReturn === FALSE) || ($iReturn <= 0)) {
        if ($iReturn === 0) {
            $sTitle = 'fichier: ' . basename(__FILE__) . ', ligne:' . __LINE__;
            ErrorLog(CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
            $iReturn = -2;
        } //if( $iReturn==0 )
        RedirectError($iReturn, __FILE__, __LINE__);
        exit;
    } //if( ($iReturn===FALSE) || ($iReturn<0) )

    // Succeeded
    include(PBR_PATH . '/includes/init/clean.php');
    header('Location: ' . $sCalendarHRef . '&error=3');
    exit;
} //Delete

// Clean SESSION token
CPHPSession::CleanToken();

/** Generate and write SESSION token
 ***********************************/
$sToken = CPHPSession::GetInstance()->WriteToken();
if ($sToken === FALSE) {
    $sTitle = 'fichier: ' . basename(__FILE__) . ', ligne:' . __LINE__;
    ErrorLog(CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible de fixer le jeton de la session', E_USER_ERROR, TRUE);
    CPHPSession::CleanToken();
    CPHPSession::Clean();
    unset($pRent, $pDate, $pContact);
    RedirectError(1, __FILE__, __LINE__);
    exit;
} //if( $sToken===FALSE )

/** Build header
 ***************/
require(PBR_PATH . '/includes/class/cheader.php');
$pHeader = new CHeader();
$sBuffer = 'Supprimer une réservation';
$pHeader->SetNoCache();
$pHeader->SetTitle($sBuffer);
$pHeader->SetDescription($sBuffer);
$pHeader->SetKeywords('delete,erase,effacer,supprimer,suppression,rent,reservation');

/** Display
 **********/
require(PBR_PATH . '/includes/display/header.php');
require(PBR_PATH . '/includes/display/rentdelete.php');
require(PBR_PATH . '/includes/display/footer.php');

/** Delete objects
 *****************/
unset($pRent, $pDate, $pContact);
include(PBR_PATH . '/includes/init/clean.php');
