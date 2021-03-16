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
 * description: build and display the contact delete page.
 *         GET: cti=contact identifier
 *        POST: can=cancel case
 *              con=confirm case, tok=token, cti=contact identifier
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
    $pContact = new CContact();

    /** Cancel
     *********/
    if( filter_has_var( INPUT_POST, 'can') )
    {
        // Read contact identifier
        $pContact->ReadInputIdentifier(INPUT_POST);
        $sBuffer = CContact::IDENTIFIERTAG.'='.$pContact->GetIdentifier();
        unset( $pContact );
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'contact.php?'.$sBuffer);
        exit;
    }//Cancel

    /** Create session
     *****************/
    require(PBR_PATH.'/includes/class/cphpsession.php');
    CPHPSession::CreateSession();

    /** Delete
     *********/
    if( filter_has_var( INPUT_POST, 'con') && (CPHPSession::GetInstance()->ValidInput(INPUT_POST)===TRUE) )
    {
        // Read contact identifier
        $pContact->ReadInputIdentifier(INPUT_POST);

        // Clean SESSION
        CPHPSession::CleanToken();
        CPHPSession::Clean();

        // Delete
        require(PBR_PATH.'/includes/db/function/contactdel.php');
        $iReturn = ContactDel( CAuth::GetInstance()->GetUsername()
                             , CAuth::GetInstance()->GetSession()
                             , GetIP().GetUserAgent()
                             , $pContact );

        unset($pContact);

        // Failed
        if( ($iReturn===FALSE) || ($iReturn<=0) )
        {
            if( $iReturn===0 )
            {
                $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
                ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'identifiant inconnu', E_USER_ERROR, TRUE);
                $iReturn=-2;
            }//if( $iReturn==0 )
            RedirectError( $iReturn, __FILE__, __LINE__ );
            exit;
        }//if( ($iReturn===FALSE) || ($iReturn<0) )

        // Succeeded
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'contacts.php?error=3');
        exit;

    }//Delete

    // Clean SESSION token
    CPHPSession::CleanToken();

    /** Generate and write SESSION token
     ***********************************/
    $sToken = CPHPSession::GetInstance()->WriteToken();
    if( $sToken===FALSE )
    {
        $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible de fixer le jeton de la session', E_USER_ERROR, TRUE);
        CPHPSession::CleanToken();
        CPHPSession::Clean();
        unset($pContact);
        RedirectError( 1, __FILE__, __LINE__ );
        exit;
    }//if( $sToken===FALSE )

    /** Get contact data
     *******************/
    $pContact->ReadInputIdentifier(INPUT_GET);
    require(PBR_PATH.'/includes/db/function/contactget.php');
    $iReturn = ContactGet( CAuth::GetInstance()->GetUsername()
                         , CAuth::GetInstance()->GetSession()
                         , GetIP().GetUserAgent()
                         , $pContact );

    // Error
    if( ($iReturn===FALSE) || ($iReturn<=0) )
    {
        unset( $pContact );
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
    $sBuffer='Supprimer '.$pContact->GetLastName().' '.$pContact->GetFirstName();
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords('delete,erase,effacer,supprimer,suppression,contact');

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/contactdelete.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset( $pContact );
    include(PBR_PATH.'/includes/init/clean.php');
