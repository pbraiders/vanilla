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
 * description: build and display the login page.
 * POST: act=login, usr=<username>, pwd=<password>, tok=<token>
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION',' ');
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

    /** Create session
     *****************/
    require(PBR_PATH.'/includes/class/cphpsession.php');
    CPHPSession::CreateSession();

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/class/cuser.php');
    $pUser = new CUser();
    $iMessageCode = 0;

    /** Read input parameters
     ************************/
    require(PBR_PATH.'/includes/class/caction.php');
    if( (CAction::IsValid( INPUT_POST, 'login')===CAction::VALID)
     && (CPHPSession::GetInstance()->ValidInput(INPUT_POST)===TRUE) )
    {
        /** Login case
         *************/

        // Read and check user input parameters
        $pUser->ReadInput(INPUT_POST);
        if( $pUser->IsValidLogin() )
        {
            // Open database
            require(PBR_PATH.'/includes/db/class/cdblayer.php');
            if( CDBLayer::GetInstance()->Open( PBR_DB_DSN.PBR_DB_DBN, PBR_DB_USR, PBR_DB_PWD, $pUser->GetUsername() )===FALSE )
            {
                $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
                ErrorLog( $pUser->GetUsername(), $sTitle, 'impossible d\'ouvrir la base de données', E_USER_ERROR, FALSE);
            }
            else
            {
                // Generate a new session id
                $sSession = CPHPSession::GetInstance()->GenerateSessionId();

                // Login
                require(PBR_PATH.'/includes/db/function/sessionset.php');
                $iReturn = SessionSet( $pUser->GetUsername()
                                     , $sSession
                                     , $pUser->GetPassword()
                                     , GetIP().GetUserAgent() );
                if( $iReturn>0 )
                {
                    // Set cookie
                    if( CCookie::GetInstance()->Write( $pUser->GetUsername(), $sSession, CAuth::DEFAULT_LANGUAGE, FALSE  )===FALSE )
                    {
                        $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
                        ErrorLog( $pUser->GetUsername(), $sTitle, 'impossible d\'écrire le cookie', E_USER_WARNING, TRUE);
                    }//if(...

                    // Redirect
                    unset($pUser);
                    CPHPSession::CleanToken();
                    CPHPSession::Clean();
                    include(PBR_PATH.'/includes/init/clean.php');
                    header('Location: '.PBR_URL);
                    exit;

                }//if( $iReturn>0 )

                // Trace
                if( ($iReturn==-2)||($iReturn==-3) )
                {
                    $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
                    ErrorLog( $pUser->GetUsername(), $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
                }//if( ($iReturn==-2)||($iReturn==-3) )

            }//if( CDBLayer::GetInstance()->Open(PBR_DB_DSN,PBR_DB_USR,PBR_DB_PWD)===FALSE )
        }//if( $pUser->IsValidLogin() )

        // Set Message
        $iMessageCode = 1;

    }//Read input parameters

    /** Generate and write SESSION token
     ***********************************/
    $sToken = CPHPSession::GetInstance()->WriteToken();
    if( $sToken===FALSE )
    {
        $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( 'visitor', $sTitle, 'impossible de fixer le jeton de la session', E_USER_ERROR, FALSE);
    }//if( $sToken===FALSE )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new CHeader();
    $pHeader->SetTitle('Connexion');
    $pHeader->SetDescription('Connexion');
    $pHeader->SetKeywords('connection,connexion,login');

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/login.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pUser);
    unset($pHeader);
    include(PBR_PATH.'/includes/init/clean.php');
