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
 * description: build and display the database log page.
 *         GET: error=<error code>, pag=<page>
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
    require(PBR_PATH.'/includes/init/authadmin.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/class/cpaging.php');
    $pPaging = null;
    $iMessageCode = 0;

    /** Read input parameters
     ************************/

    // Read the message code
    $iMessageCode = GetMessageCode();

    // Read the page
    $pPaging = new CPaging();
    $pPaging->ReadInput();

    /** Build Page
     *************/

    // Get log count
    require(PBR_PATH.'/includes/db/function/logsgetcount.php');
    $iReturn = LogsGetCount( CAuth::GetInstance()->GetUsername()
                           , CAuth::GetInstance()->GetSession()
                           , GetIP().GetUserAgent() );

    if( ($iReturn===FALSE) || ($iReturn<0) )
    {
        // Error
        unset($pPaging);
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<0) )

    // Succeeded
    $pPaging->Compute( PBR_PAGE_LOGS, $iReturn );

    // Get log list
    require(PBR_PATH.'/includes/db/function/logsget.php');
    $tRecordset = LogsGet( CAuth::GetInstance()->GetUsername()
                         , CAuth::GetInstance()->GetSession()
                         , GetIP().GetUserAgent()
                         , $pPaging );

    if( !is_array($tRecordset) )
    {
        // Error
        unset($pPaging);
        RedirectError( $tRecordset, __FILE__, __LINE__ );
        exit;
    }//if( !is_array($tRecordset) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sBuffer = 'Logs';
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords($sBuffer);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/logs.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pPaging);
    unset($pHeader);
    include(PBR_PATH.'/includes/init/clean.php');
