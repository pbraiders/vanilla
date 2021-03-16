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
 * description: build and display the new contact page.
 *        POST: act=<new>, ctX=<contact info>
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
    $iMessageCode = 0;

    /** Read input parameters
     ************************/
    require(PBR_PATH.'/includes/class/caction.php');
    if( filter_has_var( INPUT_POST, 'new' ) )
    {
        // Read data
        $pContact->ReadInput(INPUT_POST);
        if( $pContact->MandatoriesAreFilled()===TRUE )
        {
            require(PBR_PATH.'/includes/db/function/contactadd.php');
            $iReturn = ContactAdd( CAuth::GetInstance()->GetUsername()
                                 , CAuth::GetInstance()->GetSession()
                                 , GetIP().GetUserAgent()
                                 , $pContact );

            // Error
            if( ($iReturn===FALSE) || ($iReturn<=0) )
            {
                unset($pContact);
                RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }//if( ($iReturn===FALSE) || ($iReturn<=0) )

            // Succeed
            $sBuffer  = PBR_URL.'contacts.php?'.CAction::ACTIONTAG.'=search';
            $sBuffer .= '&'.CContact::LASTNAMETAG.'='.$pContact->GetLastName(2);
            $sBuffer .= '&error=1';
            unset($pContact);
            include(PBR_PATH.'/includes/init/clean.php');
            header('Location: '.$sBuffer);
            exit;
        }
        else
        {
            // Missing values
            $iMessageCode = 1;
        }//if( $pContact->MandatoriesAreFilled()===TRUE )
    }//if( CAction::IsValid(...

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sBuffer = 'Nouveau contact';
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords($sBuffer);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/contactnew.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset( $pContact, $pHeader );
    include(PBR_PATH.'/includes/init/clean.php');
