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
 * description: build and display the day page.
 *         GET: rey=year, rem=month, red=day, cti=<contact identifier>, pag=<page>
 *        POST: new=new case, rey=year, rem=month, red=day, ctX=contact info
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
    require(PBR_PATH.'/includes/class/caction.php');
    $pDate = new CDate();
    $pContact = new CContact();
    $pRent = new CRent();
    $pPaging = new CPaging();
    $iMessageCode = 0;

    /** Read input parameters
     ************************/

    // Read date values
    if( $pDate->ReadInput( INPUT_POST, TRUE )===FALSE )
    {
        if( $pDate->ReadInput( INPUT_GET, TRUE )===FALSE )
        {
            // mandatory parameters are not valid
            unset( $pDate, $pContact, $pRent, $pPaging);
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'date invalide', E_USER_WARNING, FALSE);
            RedirectError( -2, __FILE__, __LINE__ );
            exit;
        }//if( $pDate->ReadInput(...
    }//Read date values

    // New rent (and contact) or normal case
    if( filter_has_var(INPUT_POST, 'new') )
    {
        // Read rent values
        $pRent->ReadInput(INPUT_POST);

        // Read contact values
        $pContact->ReadInput(INPUT_POST);

        // Save datas
        if( $pContact->GetIdentifier()>0 )
        {
            // Add rent to identified contact
            require(PBR_PATH.'/includes/db/function/rentadd.php');
            $iReturn = RentAdd( CAuth::GetInstance()->GetUsername()
                              , CAuth::GetInstance()->GetSession()
                              , GetIP().GetUserAgent()
                              , $pContact
                              , $pDate
                              , $pRent );

            // Error
            if( ($iReturn===FALSE) || ($iReturn<=0) )
            {
                unset( $pDate, $pContact, $pRent, $pPaging);
                RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }//if( ($iReturn===FALSE) || ($iReturn<=0) )

            // Succeeded
            $pContact->ResetMe();
            $pRent->ResetMe();
            $iMessageCode = 2;

        }
        elseif( $pContact->MandatoriesAreFilled()===TRUE )
        {
            // Add new contact and new rent
            require(PBR_PATH.'/includes/db/function/rentcontactadd.php');
            $iReturn = RentContactAdd( CAuth::GetInstance()->GetUsername()
                                     , CAuth::GetInstance()->GetSession()
                                     , GetIP().GetUserAgent()
                                     , $pContact
                                     , $pDate
                                     , $pRent );

            // Error
            if( ($iReturn===FALSE) || ($iReturn<=0) )
            {
                unset( $pDate, $pContact, $pRent, $pPaging);
                RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }//if( ($iReturn===FALSE) || ($iReturn<=0) )

            // Succeeded
            $pContact->ResetMe();
            $pRent->ResetMe();
            $iMessageCode = 2;

        }
        else
        {
            // Error
            $iMessageCode = 1;
        }//Save datas

    }
    else
    {
        // Read contact identifier
        $pContact->ReadInputIdentifier(INPUT_GET);

        // Read the message code
        $iMessageCode = GetMessageCode();

        // Read the page
        $pPaging->ReadInput();

    }//New rent (and contact) or normal case

    /** Build the page
     *****************/

    // Get contact
    if( $pContact->GetIdentifier()>0 )
    {
        require(PBR_PATH.'/includes/db/function/contactget.php');
        $iReturn = ContactGet( CAuth::GetInstance()->GetUsername()
                             , CAuth::GetInstance()->GetSession()
                             , GetIP().GetUserAgent()
                             , $pContact );

        // Error
        if( ($iReturn===FALSE) || ($iReturn<=0) )
        {
            unset( $pDate, $pContact, $pRent, $pPaging);
            if( $iReturn===0 )
            {
                $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
                ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
                $iReturn=-2;
            }//if( $iReturn==0 )
            RedirectError( $iReturn, __FILE__, __LINE__ );
            exit;
        }//if( ($iReturn===FALSE) || ($iReturn<=0) )
    }//Get contact

    // Get the reservations count
    require(PBR_PATH.'/includes/db/function/rentsgetcount.php');
    $iReturn = RentsGetCount( CAuth::GetInstance()->GetUsername()
                            , CAuth::GetInstance()->GetSession()
                            , GetIP().GetUserAgent()
                            , $pDate );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<0) )
    {
        unset( $pDate, $pContact, $pRent, $pPaging);
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<0) )

    // Succeeded
    $pPaging->Compute( PBR_PAGE_RENTS, $iReturn );

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
        unset( $pDate, $pContact, $pRent, $pPaging);
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
    $pHeader->SetTitle($sFormTitle);
    $pHeader->SetDescription($sFormTitle);
    $pHeader->SetKeywords($sFormTitle);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/day.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset( $pDate, $pContact, $pRent, $pPaging);
    include(PBR_PATH.'/includes/init/clean.php');
?>
