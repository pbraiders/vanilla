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
 *         GET: act=confirm|delete, tok=<token>, cti<contact identifier>
 *              cancel=<cancel case>
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.0');
    define('PBR_PATH',dirname(__FILE__));

    /** Include stat
     ***************/
//    require(PBR_PATH.'/includes/stat.php');

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
    $sAction=NULL;
    $sToken=NULL;

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/ccontact.php');

    /** Read input parameters
     ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act')
                                                && filter_has_var(INPUT_GET, 'tok')
    											&& filter_has_var(INPUT_GET, 'cti') )
    {
        // Get action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get token
        $sToken = trim(filter_input( INPUT_GET, 'tok', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get identifier
        CContact::GetInstance()->SetIdentifier( (integer)filter_input( INPUT_GET,'cti',FILTER_VALIDATE_INT));
        // Verify readed values
        if( $sToken!=CSession::GetToken() || (($sAction!='delete') && ($sAction!='confirm'))
                                          || (CContact::GetInstance()->GetIdentifier()<1) )
        {
 			// Parameters are not valid
			CUser::GetInstance()->Invalidate();
        }// if(....
    }//if( filter_has_var(...

    // Unset token
    CSession::CleanToken();

    /** Build the page
    ******************/
    if( (CUser::GetInstance()->IsAuthenticated()===TRUE) && !is_null($sAction) )
    {
        // Get contact
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactget.php');
        $iReturn=ContactGet( CUser::GetInstance()->GetUsername()
                           , CUser::GetInstance()->GetSession()
                           , GetIP().GetUserAgent()
                           , CContact::GetInstance()->GetIdentifier()
                           , CContact::GetInstance());
        if( $iReturn<1 )
        {
            RedirectError( $iReturn, __FILE__, __LINE__ );
			exit;
        }//if( $iReturn<1 )

        /** Cancel
         *********/
        if( $sAction=='delete' && filter_has_var(INPUT_GET, 'cancel') )
        {
            $sBuffer=PBR_URL.'contact.php?act=show&cti='.CContact::GetInstance()->GetIdentifier();
            include(PBR_PATH.'/includes/init/initclean.php');
            header('Location: '.$sBuffer);
            exit;
        }//if( $sAction='delete' && filter_has_var(INPUT_GET, 'cancel') )

        /** Delete
         *********/
        if( $sAction=='delete' )
        {
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactdel.php');
            $iReturn=ContactDel( CUser::GetInstance()->GetUsername()
                               , CUser::GetInstance()->GetSession()
                               , GetIP().GetUserAgent()
                               , CContact::GetInstance()->GetIdentifier());
            // Error
            if( $iReturn<1 )
            {
				// Failed
            	RedirectError( $iReturn, __FILE__, __LINE__ );
				exit;
            }//if( $iReturn<1 )

            // No error
            $sBuffer='?error=3';
            include(PBR_PATH.'/includes/init/initclean.php');
            header('Location: '.PBR_URL.'contacts.php'.$sBuffer);
            exit;
        }//if( $sAction='confirm' && filter_has_var(INPUT_GET, 'cancel') )

        /** Confirm
         **********/
        if( $sAction=='confirm' )
        {

            /** Build token
             **************/
            $sToken = md5(uniqid(rand(), TRUE));
            CSession::GetInstance()->SetToken($sToken);

            /** Build header
             ***************/
            require(PBR_PATH.'/includes/class/cheader.php');
            $sBuffer='Supprimer '.CContact::GetInstance()->GetLastName().' '.CContact::GetInstance()->GetFirstName();
            CHeader::GetInstance()->SetNoCache();
            CHeader::GetInstance()->SetTitle($sBuffer);
            CHeader::GetInstance()->SetDescription($sBuffer);
            CHeader::GetInstance()->SetKeywords($sBuffer);

            /** Display
             **********/
            require(PBR_PATH.'/includes/display/displayheader.php');
            require(PBR_PATH.'/includes/display/displaycontactdelete.php');
            require(PBR_PATH.'/includes/display/displayfooter.php');

            /** Clean
             ********/
            CHeader::DeleteInstance();

        }//if( $sAction='confirm' )

    }
    else
    {
		//Error
		RedirectError( -2, __FILE__, __LINE__ );
		exit;
    }//if( CUser::GetInstance()->IsAuthenticated() )

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
