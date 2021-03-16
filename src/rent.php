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
 * description: build and display the rent page.
 *         GET: rei=rent identifier
 *        POST: del=delete case, rei=rent identifier
 *              upd=update case, reX=rent data
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
    $pRent = new CRent();
    $iMessageCode = 0;

    /** Read input parameters
     ************************/

    // Delete
    if( filter_has_var( INPUT_POST, 'del') )
    {
        // Read rent identifier
        $pRent->ReadInputIdentifier(INPUT_POST);
        // Redirect
        $sBuffer = CRent::IDENTIFIERTAG.'='.$pRent->GetIdentifier();
        unset( $pRent );
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'rentdelete.php?'.$sBuffer);
        exit;
    }
    // Update
    elseif( filter_has_var( INPUT_POST, 'upd') )
    {
        // Read rent data
        $pRent->ReadInput(INPUT_POST);

        // Update database
        require(PBR_PATH.'/includes/db/function/rentupdate.php');
        $iReturn = RentUpdate( CAuth::GetInstance()->GetUsername()
                             , CAuth::GetInstance()->GetSession()
                             , GetIP().GetUserAgent()
                             , $pRent );

        // Error
        if( ($iReturn===FALSE) || ($iReturn<0) )
        {
            unset( $pRent );
            RedirectError( $iReturn, __FILE__, __LINE__ );
            exit;
        }//if( ($iReturn===FALSE) || ($iReturn<0) )

        // Succeeded
        $iMessageCode = 2;

        // Clean the old data
        $iReturn = $pRent->GetIdentifier();
        $pRent->ResetMe();
        $pRent->SetIdentifier($iReturn);

    }
    else
    {
        // Read rent identifier
        $pRent->ReadInputIdentifier(INPUT_GET);
        // Read the message code
        $iMessageCode = GetMessageCode();
    }//Read action

    /** Build the page
    ******************/
    $pDate = new CDate();
    $pContact = new CContact();

    // Get rent
    require(PBR_PATH.'/includes/db/function/rentget.php');
    $iReturn = RentGet( CAuth::GetInstance()->GetUsername()
                      , CAuth::GetInstance()->GetSession()
                      , GetIP().GetUserAgent()
                      , $pRent
                      , $pDate
                      , $pContact );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<=0) )
    {
        unset( $pRent, $pDate, $pContact );
        if( $iReturn===0 )
        {
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
            $iReturn=-2;
        }//if( $iReturn==0 )
        RedirectError( $iReturn, __FILE__, __LINE__ );
        exit;
    }//if( ($iReturn===FALSE) || ($iReturn<=0) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sBuffer = $pDate->GetRequestDay().' ';
    $sBuffer .= $pDate->GetMonthName( $pDate->GetRequestMonth() ).' ';
    $sBuffer .= $pDate->GetRequestYear().' - ';
    $sBuffer .= $pContact->GetLastName().' '.$pContact->GetFirstName();
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords($sBuffer);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/rent.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset( $pRent, $pDate, $pContact, $pHeader );
    include(PBR_PATH.'/includes/init/clean.php');
