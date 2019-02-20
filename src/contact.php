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
 * description: build and display the contact page.
 *  GET: cti=contact identifier, pag=<page>
 * POST: update=update case, ctX=<contact info>
 *       delete=delete case, cti=contact identifier
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
    require(PBR_PATH.'/includes/class/ccontact.php');
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/crent.php');
    require(PBR_PATH.'/includes/class/cpaging.php');
    $pContact = new CContact();
    $pPaging = new CPaging();
    $iMessageCode = 0;

    /** Read input parameters
     ************************/

    // Delete
    if( filter_has_var( INPUT_POST, 'del') )
    {
        // Read contact identifier
        $pContact->ReadInputIdentifier(INPUT_POST);

        // Redirect
        $sBuffer = CContact::IDENTIFIERTAG.'='.$pContact->GetIdentifier();
        unset( $pPaging, $pContact );
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'contactdelete.php?'.$sBuffer);
        exit;
    }
    // Update
    elseif( filter_has_var( INPUT_POST, 'upd') )
    {
        // Read contact data
        $pContact->ReadInput(INPUT_POST);

        // Update database
        if( $pContact->IsValid()===TRUE )
        {
            require(PBR_PATH.'/includes/db/function/contactupdate.php');
            $iReturn = ContactUpdate( CAuth::GetInstance()->GetUsername()
                                    , CAuth::GetInstance()->GetSession()
                                    , GetIP().GetUserAgent()
                                    , $pContact );

            // Error
            if( ($iReturn===FALSE) || ($iReturn<0) )
            {
                unset( $pPaging, $pContact );
                RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }//if( ($iReturn===FALSE) || ($iReturn<0) )

            // Succeeded
            $iMessageCode = 2;
        }
        else
        {
            // Missing values
            $iMessageCode = 1;
        }//if( $pContact->IsValid()===TRUE )

        // Clean the old data
        $iReturn = $pContact->GetIdentifier();
        $pContact->ResetMe();
        $pContact->SetIdentifier($iReturn);

    }
    else
    {
        // Read contact identifier
        $pContact->ReadInputIdentifier(INPUT_GET);

        // Read the message code
        $iMessageCode = GetMessageCode();

        // Read the page
        $pPaging->ReadInput();

    }//Read action

    /** Build the page
     *****************/

    // Get contact
    require(PBR_PATH.'/includes/db/function/contactget.php');
    $iReturn = ContactGet( CAuth::GetInstance()->GetUsername()
                         , CAuth::GetInstance()->GetSession()
                         , GetIP().GetUserAgent()
                         , $pContact );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<=0) )
    {
        unset( $pPaging, $pContact );
        if( $iReturn===0 )
        {
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
            $iReturn=-2;
        }//if( $iReturn==0 )
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<=0) )

    // Get the reservations count
    require(PBR_PATH.'/includes/db/function/contactrentsgetcount.php');
    $iReturn = ContactRentsGetCount( CAuth::GetInstance()->GetUsername()
                                   , CAuth::GetInstance()->GetSession()
                                   , GetIP().GetUserAgent()
                                   , $pContact );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<0) )
    {
        unset( $pPaging, $pContact );
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<0) )

    // Succeeded
    $pPaging->Compute( PBR_PAGE_RENTS, $iReturn );

    // Get rents
    require(PBR_PATH.'/includes/db/function/contactrentsget.php');
    $tRecordset = ContactRentsGet( CAuth::GetInstance()->GetUsername()
                                 , CAuth::GetInstance()->GetSession()
                                 , GetIP().GetUserAgent()
                                 , $pContact
                                 , $pPaging );

    // Error
    if( !is_array($tRecordset) )
    {
        unset( $pPaging, $pContact );
        RedirectError( $tRecordset, __FILE__, __LINE__ );
        exit;
    }//if( !is_array($tRecordset) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pDate = new CDate();
    $pHeader = new CHeader();
    $sBuffer = $pContact->GetLastName().' '.$pContact->GetFirstName();
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords($sBuffer);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/contact.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset( $pPaging, $pContact, $pHeader, $pDate );
    include(PBR_PATH.'/includes/init/clean.php');
?>
