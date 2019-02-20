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
 *         GET: act=update|show, cti=<contact identifier>, pag=<page>, del=<?>
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.1.0');
    define('PBR_PATH',dirname(__FILE__));

    /** Include config
     *****************/
    require(PBR_PATH.'/config.php');

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/init/init.php');
    $sAction=NULL;
    $iMessageCode=0;

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/ccontact.php');
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/cpaging.php');

    /** Read input parameters
     ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act')
    											&& filter_has_var(INPUT_GET, 'cti') )
    {
        // Get action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
  		// Get the page
		CPaging::GetInstance()->ReadInput();
        // Get identifier
        CContact::GetInstance()->SetIdentifier( filter_input( INPUT_GET,'cti',FILTER_VALIDATE_INT));
        // Verify readed values
        if( (CContact::GetInstance()->GetIdentifier()<1) || (($sAction!='update') && ($sAction!='show')) )
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if( (CContact::GetInstance()->GetIdentifier()<1) || (($sAction!='update') && ($sAction!='show')) )
    }//if( filter_has_var(...

    /** Build the page
    ******************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {
        /** Delete
        **********/
        if( ($sAction=='update') && filter_has_var(INPUT_POST, 'del') )
        {
    		// Create session
		    require(PBR_PATH.'/includes/class/csession.php');
		    CSession::CreateSession();
    		// Build token
		    $sToken = md5(uniqid(rand(), TRUE));
		    CSession::GetInstance()->SetToken($sToken);
            // Send
            $sBuffer=PBR_URL.'contactdelete.php?act=confirm&cti='.CContact::GetInstance()->GetIdentifier().'&tok='.$sToken;
			include(PBR_PATH.'/includes/init/initclean.php');
            header('Location: '.$sBuffer);
            exit;
        }//delete

        /** Update
        **********/
        if( $sAction=='update' )
        {
            // Get the value
            CContact::GetInstance()->ReadInput();
            // Update database
            if( CContact::GetInstance()->MandatoriesAreFilled()===TRUE )
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactupdate.php');
                $iReturn=ContactUpdate( CUser::GetInstance()->GetUsername()
                                      , CUser::GetInstance()->GetSession()
                                      , GetIP().GetUserAgent()
                                      , CContact::GetInstance());
                if( $iReturn>0 )
                {
        			// Succeeded
                    $iMessageCode=2;
                    $sAction='show';
                }
                else
                {
                    // Failed
					RedirectError( $iReturn, __FILE__, __LINE__ );
                    exit;
                }//if( $iReturn>0 )
            }
            else
            {
                // Missing values
                $iMessageCode=1;
                $sAction='show';
            }//if{ CContact::GetInstance()->MandatoriesAreFilled()===TRUE )
        }//update

        /** Show
        ********/
        if( $sAction=='show' )
        {
            // Clean the old data (if update case)
            $iBuffer=CContact::GetInstance()->GetIdentifier();
            CContact::DeleteInstance();

            // Get contact
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactget.php');
            $iReturn=ContactGet( CUser::GetInstance()->GetUsername()
                               , CUser::GetInstance()->GetSession()
                               , GetIP().GetUserAgent()
                               , $iBuffer
                               , CContact::GetInstance());
            if( $iReturn<1 )
            {
				// Failed
				RedirectError( $iReturn, __FILE__, __LINE__ );
                exit;
            }//if( $iReturn<1 )

            // Get the reservations count
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactrentsgetcount.php');
            $iReturn=ContactRentsGetCount( CUser::GetInstance()->GetUsername()
                                         , CUser::GetInstance()->GetSession()
                                         , GetIP().GetUserAgent()
                                         , CContact::GetInstance()->GetIdentifier());
        	if( $iReturn>=0 )
	        {
	        	// Succeeded
				CPaging::GetInstance()->Compute( (integer)PBR_PAGE_RENTS, (integer)$iReturn );
        	}
        	else
	        {
	            // Failed
	            RedirectError( $iReturn, __FILE__, __LINE__ );
				exit;
        	}//if( $iReturn>0 )

            // Get rent
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactrentsget.php');
            $tRecordset=ContactRentsGet( CUser::GetInstance()->GetUsername()
                                       , CUser::GetInstance()->GetSession()
                                       , GetIP().GetUserAgent()
                                       , CContact::GetInstance()->GetIdentifier()
                                	   , CPaging::GetInstance()->GetOffset()
                                	   , CPaging::GetInstance()->GetLimit());
            if( !is_array($tRecordset) )
            {
                // Failed
            	RedirectError( $tRecordset, __FILE__, __LINE__ );
            	exit;
            }//if( !is_array($tRecordset) )

        }//if( $sAction=='show' )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sBuffer=CContact::GetInstance()->GetLastName().' '.CContact::GetInstance()->GetFirstName();
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sBuffer);
        CHeader::GetInstance()->SetDescription($sBuffer);
        CHeader::GetInstance()->SetKeywords($sBuffer);

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displaycontact.php');
        require(PBR_PATH.'/includes/display/displayfooter.php');

        /** Clean
         ********/
        CHeader::DeleteInstance();

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
