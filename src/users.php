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
 * description: build and display the user page.
 *         GET: act=select, usi=<user identifier>
 *        POST: act=new, usr=<username>, pwd=<password>, pwdc=<password>
 *        POST: act=update, usi=<user identifier>, pwd=<password>, pwdc=<password>, sta=<state>
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-11 - add password check
 *                                      - fixed minor bug
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
    require(PBR_PATH.'/includes/class/cuser.php');
    $pUser = null;
    $iMessageCode = 0;

    /** Read input parameters
     ************************/
    require(PBR_PATH.'/includes/class/caction.php');
    if( CAction::IsValid( INPUT_POST, 'new')===CAction::VALID )
    {
        /** Add new user
         ***************/
        $pUser = new CUser();
        $pUser->ReadInput(INPUT_POST);
        if( $pUser->IsValidNew()===FALSE )
        {
            // Not valid for creation
            $iMessageCode = 1;
        }
        else
        {
            require(PBR_PATH.'/includes/db/function/useradd.php');
            $iReturn = UserAdd( CAuth::GetInstance()->GetUsername()
                              , CAuth::GetInstance()->GetSession()
                              , GetIP().GetUserAgent()
                              , $pUser );
            if( $iReturn>0 )
            {
                // Succeeded
                $iMessageCode = 2;
                unset($pUser);
            }
            elseif( $iReturn==-4 )
            {
                // Failed: duplicate user
                $iMessageCode = 3;
            }
            else
            {
                // Failed
                unset($pUser);
                RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }//if( $iReturn>0 )
        }//if( $pUser->IsValidNew()===FALSE )
    }
    elseif( CAction::IsValid( INPUT_POST, 'update')===CAction::VALID )
    {
        /** Update user
         **************/
        $pUser = new CUser();
        $pUser->ReadInput(INPUT_POST);
        if( $pUser->IsValidUpdate()===FALSE )
        {
            // Not valid for update
            $iMessageCode = 1;
        }
        else
        {
            require(PBR_PATH.'/includes/db/function/userupdate.php');
            $iReturn = UserUpdate( CAuth::GetInstance()->GetUsername()
                                 , CAuth::GetInstance()->GetSession()
                                 , GetIP().GetUserAgent()
                                 , $pUser );
            unset($pUser);

            // Failed
            if( ($iReturn===FALSE) || ($iReturn<0) )
            {
                RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }// if( ($iReturn===FALSE) || ($iReturn<0) )

            // Succeeded
            $iMessageCode = 2;

        }//if( $pUser->IsValidUpdate()===FALSE )
    }
    elseif( CAction::IsValid( INPUT_GET, 'select')===CAction::VALID )
    {
        // Read the user identifier
        $pUser = new CUser();
        $pUser->ReadInput(INPUT_GET);
    }
    else
    {
        // Read the message code
        $iMessageCode = GetMessageCode();
    }//if( CAction::IsValid(...

    /** Get the user
    ****************/
    if( isset($pUser) && ($pUser->GetIdentifier()>0) )
    {
        require(PBR_PATH.'/includes/db/function/userget.php');
        $iReturn = UserGet( CAuth::GetInstance()->GetUsername()
                          , CAuth::GetInstance()->GetSession()
                          , GetIP().GetUserAgent()
                          , $pUser );
        if( ($iReturn===FALSE) || ($iReturn<=0) )
        {
            // Failed
            unset($pUser);
            if( $iReturn===0 )
            {
                $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
                ErrorLog(CAuth::GetInstance()->GetUsername(),$sTitle,'identifiant inconnu',E_USER_ERROR,TRUE);
                $iReturn=-2;
            }//if( $iReturn==0 )
            RedirectError( $iReturn, __FILE__, __LINE__ );
            exit;
        }//if( ($iReturn===FALSE) || ($iReturn<=0) )
    }//if( isset($pUser) && ($pUser->GetIdentifier()>0) )

    /** Get the users
     ****************/
    require(PBR_PATH.'/includes/db/function/usersget.php');
    $tRecordset = UsersGet( CAuth::GetInstance()->GetUsername()
                          , CAuth::GetInstance()->GetSession()
                          , GetIP().GetUserAgent());
    if( !is_array($tRecordset) )
    {
        // Failed
        if( isset($pUser) ) unset($pUser);
        RedirectError( $tRecordset, __FILE__, __LINE__ );
        exit;
    }//if( !is_array($tRecordset) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $sBuffer = 'Utilisateurs';
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords($sBuffer);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/users.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pHeader,$pUser);
    include(PBR_PATH.'/includes/init/clean.php');
