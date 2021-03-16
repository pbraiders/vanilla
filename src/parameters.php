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
 * description: build and display the parameters page.
 *        POST: update=update case, paX=<parameter value>
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
require(PBR_PATH . '/includes/init/authadmin.php');

/** Initialize
 *************/
require(PBR_PATH . '/includes/class/cmaxrentpermonthlist.php');
require(PBR_PATH . '/includes/class/cdate.php');
$pDate = new CDate();
$pMax = null;
$iMessageCode = 0;

/** Read input parameters
 ************************/

// Update
if (filter_has_var(INPUT_POST, 'update')) {
    // Get the month parameters
    $pMax = new CMaxRentPerMonthList();
    $pMax->ReadInput();
    if ($pMax->GetCount() != 12) {
        unset($pMax);
        $iMessageCode = 1;
    } //if( $pMax->GetCount()!=12 )
} else {
    // Get the message code
    $iMessageCode = GetMessageCode();
} //if( filter_has_var( ...

/** Update
 *********/
if (isset($pMax)) {
    require(PBR_PATH . '/includes/db/function/maxupdate.php');
    $iReturn = MaxUpdate(
        CAuth::GetInstance()->GetUsername(),
        CAuth::GetInstance()->GetSession(),
        GetIP() . GetUserAgent(),
        $pMax
    );

    unset($pMax);

    // Failed
    if (($iReturn === FALSE) || ($iReturn < 0)) {
        RedirectError($iReturn, __FILE__, __LINE__);
        exit;
    } //if( ($iReturn===FALSE) || ($iReturn<0) )

    // Succeeded
    $iMessageCode = 4;
} //if( isset($pMax) )

/** Read the parameters
 **********************/
require(PBR_PATH . '/includes/db/function/maxget.php');
$tRecordset = MaxGet(
    CAuth::GetInstance()->GetUsername(),
    CAuth::GetInstance()->GetSession(),
    GetIP() . GetUserAgent()
);

if (!is_array($tRecordset)) {
    // Error
    RedirectError($tRecordset, __FILE__, __LINE__);
    exit;
} //if( !is_array($tRecordset) )

/** Read the database status
 ***************************/
require(PBR_PATH . '/includes/db/function/dbstatus.php');
$tRecordsetDB = DBStatus(
    CAuth::GetInstance()->GetUsername(),
    CAuth::GetInstance()->GetSession(),
    GetIP() . GetUserAgent()
);

if (!is_array($tRecordsetDB)) {
    $tRecordsetDB = array('records' => 0, 'size' => 0);
} //if( !is_array($tRecordsetDB) )

/** Build header
 ***************/
require(PBR_PATH . '/includes/class/cheader.php');
$pHeader = new Cheader();
$sBuffer = 'Paramètres';
$pHeader->SetNoCache();
$pHeader->SetTitle($sBuffer);
$pHeader->SetDescription($sBuffer);
$pHeader->SetKeywords($sBuffer);

/** Display
 **********/
require(PBR_PATH . '/includes/display/header.php');
require(PBR_PATH . '/includes/display/parameters.php');
require(PBR_PATH . '/includes/display/footer.php');

/** Delete objects
 *****************/
unset($pDate);
unset($pHeader);
include(PBR_PATH . '/includes/init/clean.php');
