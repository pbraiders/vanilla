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
 * description: build and display the contacts page.
 *         GET: act=search|export, ctl=<contact name>, error=<error number>
 *              , pag=<page>, opX=<option>
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
    require(PBR_PATH.'/includes/class/coption.php');
    require(PBR_PATH.'/includes/class/cpaging.php');
    require(PBR_PATH.'/includes/class/ccontact.php');
    require(PBR_PATH.'/includes/class/ccsv.php');
    require(PBR_PATH.'/includes/function/export.php');
    $pPaging = new CPaging();
    $pSearch = new CContact();
    $pOrder = new COption('1');
    $pSort = new COption('2');
    $pCCSV = null;
    $pHeader = null;
    $iMessageCode = 0;
    $bSended = FALSE;

	/** Read input parameters
     ************************/

    // Read the options
    $pOrder->ReadInput(INPUT_GET);
    $pSort->ReadInput(INPUT_GET);

    // Read action
    require(PBR_PATH.'/includes/class/caction.php');
    if( CAction::IsValid( INPUT_GET, 'export' ) === CAction::VALID )
    {
        // Search
        $pSearch->ReadInputLastName(INPUT_GET);
        // Export
        $pCCSV = new CCSV();
    }
    else
    {
        // Search
        if( CAction::IsValid( INPUT_GET, 'search' ) === CAction::VALID )
        {
            $pSearch->ReadInputLastName(INPUT_GET);
        }//if( (CAction::IsValid(...

        // Read the message code
        $iMessageCode = GetMessageCode();

        // Read the page
        $pPaging->ReadInput();

    }//if( CAction::IsValid(...

    /** Get data
     ***********/

    // Get contact count
    require(PBR_PATH.'/includes/db/function/contactsgetcount.php');
    $iReturn = ContactsGetCount( CAuth::GetInstance()->GetUsername()
                               , CAuth::GetInstance()->GetSession()
                               , GetIP().GetUserAgent()
                               , $pSearch );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<0) )
    {
        unset( $pPaging, $pSearch, $pCCSV, $pOrder, $pSort );
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<0) )

    /** Build page
     *************/

    if( isset($pCCSV) )
    {

        /** Export case
         **************/

        // Open file
        $iReturn = ExportInit( $pCCSV, array('nom','prénom','téléphone','email','adresse','ville','code postal','commentaire','date de création') );

        // Write contact list
        if( ($iReturn!==FALSE) && $pCCSV->IsOpen() )
        {
            require(PBR_PATH.'/includes/db/function/contactsgetexport.php');
            $iReturn = ContactsGetExport( CAuth::GetInstance()->GetUsername()
                                        , CAuth::GetInstance()->GetSession()
                                        , GetIP().GetUserAgent()
                                        , $pSearch
                                        , $pPaging
                                        , $pOrder
                                        , $pSort
                                        , $pCCSV );
        }//if( $pCCSV->IsOpen() )

        // Close the file
        if( $pCCSV->IsOpen() )
            $pCCSV->Close();

        // Error
        if( ($iReturn===FALSE) || ($iReturn<0) )
        {
            unset( $pPaging, $pSearch, $pCCSV, $pOrder, $pSort );
            RedirectError( $iReturn, __FILE__, __LINE__ );
            exit;
        }//if( ($iReturn===FALSE) || ($iReturn<0) )

        // Send
        if( ExportSend( $pCCSV )===FALSE )
        {
            require(PBR_PATH.'/includes/display/contactsexport.php');
        }//Send

    }
    else
    {

        /** Normal case
         **************/

        // Paging
    	$pPaging->Compute( PBR_PAGE_CONTACTS, $iReturn );

        // Get contact list
        require(PBR_PATH.'/includes/db/function/contactsget.php');
        $tRecordset = ContactsGet( CAuth::GetInstance()->GetUsername()
                                 , CAuth::GetInstance()->GetSession()
                                 , GetIP().GetUserAgent()
                                 , $pSearch
                                 , $pPaging
                                 , $pOrder
                                 , $pSort );

        if( !is_array($tRecordset) )
        {
            // Error
            unset( $pPaging, $pSearch, $pCCSV, $pOrder, $pSort );
            RedirectError( $tRecordset, __FILE__, __LINE__ );
            exit;
        }//if( !is_array($tRecordset) )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $pHeader = new CHeader();
        $sBuffer = 'Contacts';
        $pHeader->SetNoCache();
        $pHeader->SetTitle($sBuffer);
        $pHeader->SetDescription($sBuffer);
        $pHeader->SetKeywords($sBuffer);
        if( strlen($pSearch->GetLastName())>0 )
        {
            $pHeader->SetTitle($pSearch->GetLastName());
            $pHeader->SetDescription($pSearch->GetLastName());
            $pHeader->SetKeywords($pSearch->GetLastName());
        }//if( strlen($pSearch->GetLastName())>0 )

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/header.php');
        require(PBR_PATH.'/includes/display/contacts.php');
        require(PBR_PATH.'/includes/display/footer.php');

    }//Display

    /** Delete objects
     *****************/
    unset( $pPaging, $pSearch, $pCCSV, $pHeader, $pOrder, $pSort );
    include(PBR_PATH.'/includes/init/clean.php');

?>
