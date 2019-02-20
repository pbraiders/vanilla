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
 *   description: build and display the login page.
 *          POST: act=login, usr=<username>, pwd=<password>, tok=<token>
 *        author: Olivier JULLIEN - 2010-02-04
 *        update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.0.1');
    define('PBR_PATH',dirname(__FILE__));

    /** Include config
     *****************/
    require(PBR_PATH.'/config.php');

    /** Create session
     *****************/
    require(PBR_PATH.'/includes/class/csession.php');
    CSession::CreateSession();

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/init/init.php');
    $sUsername=NULL;
    $sPassword=NULL;
    $sAction=NULL;
    $sToken=NULL;
    $iMessageCode=0;

    /** Read input parameters
     ************************/
    if( filter_has_var(INPUT_POST, 'act')
        && filter_has_var(INPUT_POST, 'usr')
        && filter_has_var(INPUT_POST, 'pwd')
        && filter_has_var(INPUT_POST, 'tok') )
    {
		// Get action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get token
        $sToken = trim(filter_input( INPUT_POST, 'tok', FILTER_SANITIZE_SPECIAL_CHARS));
        // Verify action and token
        if( ($sAction=='login') && ($sToken==CSession::GetToken()) )
        {
            // Unset token
            CSession::CleanToken();
			// Get username
			$sUsername = trim(filter_input(INPUT_POST,'usr',FILTER_UNSAFE_RAW));
			// Get password
			$sPassword = sha1(trim(filter_input(INPUT_POST,'pwd',FILTER_UNSAFE_RAW)));
        }
        else
        {
			// Parameters are not valid
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( CUser::DEFAULT_USER, $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
        }//if( ($sAction=='login') && ($sToken==CSession::GetToken()) )
    }//if( filter_has_var(...

    /** Login
     ********/
	if( !is_null($sUsername) && !is_null($sPassword) )
    {
        // Open database
        require(PBR_PATH.'/includes/db/class/cdb.php');
		if( CDb::GetInstance()->Open(PBR_DB_DSN.PBR_DB_DBN,PBR_DB_USR,PBR_DB_PWD)===FALSE )
		{
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( $sUsername, $sTitle, 'impossible d\'ouvrir la base de données', E_USER_ERROR, FALSE);
        }
        else
        {
			// Generate a new session id
			$sSession = md5(uniqid(rand()));
			// Login
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/sessionset.php');
            $iReturn = SessionSet( $sUsername, $sSession, $sPassword, GetIP().GetUserAgent());
            if( $iReturn>0 )
            {
				// Set cookie
				if( CCookie::GetInstance()->Write($sUsername,$sSession)===FALSE )
                {
		            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
			        ErrorLog( $sUsername, $sTitle, 'impossible d\'écrire le cookie', E_USER_WARNING, TRUE);
                }//if(...
                // Redirect
				include(PBR_PATH.'/includes/init/initclean.php');
                header('Location: '.PBR_URL);
                exit;
            }//if( $iReturn>0 )
            // Trace
			if( ($iReturn==-2)||($iReturn==-3) )
            {
	            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
		        ErrorLog( $sUsername, $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
			}//if( ($iReturn==-2)||($iReturn==-3) )
			// Set Message
			$iMessageCode=1;
		}//if( CDb::GetInstance()->Open(PBR_DB_DSN,PBR_DB_USR,PBR_DB_PWD)===FALSE )
    }//if( !is_null($sUsername) && !is_null($sPassword) )

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    CHeader::GetInstance()->SetTitle('Connexion');
    CHeader::GetInstance()->SetDescription('Connexion');
    CHeader::GetInstance()->SetKeywords('connection,connexion,login');

    /** Build token
     **************/
    $sToken = md5(uniqid(rand(), TRUE));
    CSession::GetInstance()->SetToken($sToken);

    /** Build Default name
     *********************/
    if( is_null($sUsername) && CUser::GetInstance()->IsValid() )
    {
        $sUsername=CUser::GetInstance()->GetUsername();
        if( strcasecmp(CUser::DEFAULT_USER,$sUsername)==0 )
        {
            $sUsername='';
        }//if( strcasecmp(CUser::DEFAULT_USER,$sUsername)==0 )
    }//if( CUser::GetInstance()->IsValid() )

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/displayheader.php');
    require(PBR_PATH.'/includes/display/displaylogin.php');
    require(PBR_PATH.'/includes/display/displayfooter.php');

    /** Delete objects
     *****************/
    CHeader::DeleteInstance();
    include(PBR_PATH.'/includes/init/initclean.php');
?>
