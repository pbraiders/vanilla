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
 * description: build and display the select page.
 *         GET: rey=year, rem=month, red=day, ctl=<contact name>, pag=<page>
 *              , opX=<option>
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

/** Initialize
 *************/
require(PBR_PATH . '/includes/class/coption.php');
require(PBR_PATH . '/includes/class/cdate.php');
require(PBR_PATH . '/includes/class/cpaging.php');
require(PBR_PATH . '/includes/class/ccontact.php');
$pOrder = new COption('1');
$pSort = new COption('2');
$pDate = new CDate();
$pPaging = new CPaging();
$pSearch = new CContact();

/** Read input parameters
 ************************/

// Read date values
if ($pDate->ReadInput(INPUT_GET, TRUE) === FALSE) {
    // mandatory parameters are not valid
    unset($pDate, $pSearch, $pPaging, $pOrder, $pSort);
    $sTitle = 'fichier: ' . basename(__FILE__) . ', ligne:' . __LINE__;
    ErrorLog(CAuth::GetInstance()->GetUsername(), $sTitle, 'date invalide', E_USER_WARNING, FALSE);
    RedirectError(-2, __FILE__, __LINE__);
    exit;
} //Read date values

// Read contact lastname
if (filter_has_var(INPUT_GET, CContact::LASTNAMETAG)) {
    $pSearch->ReadInputLastName(INPUT_GET);
} //Read contact lastname

// Read the page
$pPaging->ReadInput();

/** Get data
 ***********/

// Get contact count
require(PBR_PATH . '/includes/db/function/contactsgetcount.php');
$iReturn = ContactsGetCount(
    CAuth::GetInstance()->GetUsername(),
    CAuth::GetInstance()->GetSession(),
    GetIP() . GetUserAgent(),
    $pSearch
);

// Error
if (($iReturn === FALSE) || ($iReturn < 0)) {
    unset($pPaging, $pSearch, $pDate, $pOrder, $pSort);
    RedirectError($iReturn, __FILE__, __LINE__);
    exit;
} //if( ($iReturn===FALSE) || ($iReturn<0) )

// Succeeded
$pPaging->Compute(PBR_PAGE_CONTACTS, $iReturn);

// Get contact list
require(PBR_PATH . '/includes/db/function/contactsget.php');
$tRecordset = ContactsGet(
    CAuth::GetInstance()->GetUsername(),
    CAuth::GetInstance()->GetSession(),
    GetIP() . GetUserAgent(),
    $pSearch,
    $pPaging,
    $pOrder,
    $pSort
);

if (!is_array($tRecordset)) {
    // Error
    unset($pPaging, $pSearch, $pDate, $pOrder, $pSort);
    RedirectError($tRecordset, __FILE__, __LINE__);
    exit;
} //if( !is_array($tRecordset) )

/** Build header
 ***************/
require(PBR_PATH . '/includes/class/cheader.php');
$pHeader = new CHeader();
$sBuffer = 'Contacts';
$pHeader->SetNoCache();
$pHeader->SetTitle($sBuffer);
$pHeader->SetDescription($sBuffer);
$pHeader->SetKeywords($sBuffer);
if (strlen($pSearch->GetLastName()) > 0) {
    $pHeader->SetTitle($pSearch->GetLastName());
    $pHeader->SetDescription($pSearch->GetLastName());
    $pHeader->SetKeywords($pSearch->GetLastName());
} //if( strlen($pSearch->GetLastName())>0 )

/** Display
 **********/
require(PBR_PATH . '/includes/display/header.php');
require(PBR_PATH . '/includes/display/select.php');
require(PBR_PATH . '/includes/display/footer.php');

/** Delete objects
 *****************/
unset($pPaging, $pSearch, $pDate, $pHeader, $pOrder, $pSort);
include(PBR_PATH . '/includes/init/clean.php');
