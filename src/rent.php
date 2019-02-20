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
 *         GET: act=show, rei=<rent identifier>
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.0.1');
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
    require(PBR_PATH.'/includes/class/crent.php');

    /** Read input parameters
     ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act')
    											&& filter_has_var(INPUT_GET, 'rei') )
    {
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        if( $sAction=='show' )
        {
            CRent::GetInstance()->ReadInput(INPUT_GET);
        }//if( $sAction=='show' )
    }//GET
    if( CUser::GetInstance()->IsAuthenticated() && is_null($sAction)
    											&& filter_has_var(INPUT_POST, 'act')
    											&& filter_has_var(INPUT_POST, 'rei') )
    {
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        if( $sAction=='update' )
        {
            CRent::GetInstance()->ReadInput(INPUT_POST);
        }//if( $sAction=='update' )
    }//POST
    // Parameters are not valid
	if( (CRent::GetInstance()->GetIdentifier()<1) || (($sAction!='update') && ($sAction!='show')) )
    {
		CUser::GetInstance()->Invalidate();
    }//if( $sAction!='show' || CRent::GetInstance()->GetIdentifier()<1 )

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
            $sBuffer=PBR_URL.'rentdelete.php?act=confirm&rei='.CRent::GetInstance()->GetIdentifier().'&tok='.$sToken;
			include(PBR_PATH.'/includes/init/initclean.php');
            header('Location: '.$sBuffer);
            exit;
        }//delete

        /** Update
        **********/
        if( $sAction=='update' )
        {
            $sAction='show';
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentupdate.php');
            $iReturn=RentUpdate( CUser::GetInstance()->GetUsername()
                               , CUser::GetInstance()->GetSession()
                               , GetIP().GetUserAgent()
                               , CRent::GetInstance());
            if( $iReturn>0 )
            {
                $iMessageCode=2;
            }
            else
            {
            	// Failed
            	RedirectError( $iReturn, __FILE__, __LINE__ );
				exit;
            }//if( $iReturn>0 )
        }//if( $sAction=='update' )

        /** Show
        ********/
        if( $sAction=='show' )
        {
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentget.php');
            $iReturn=RentGet( CUser::GetInstance()->GetUsername()
                            , CUser::GetInstance()->GetSession()
                            , GetIP().GetUserAgent()
                            , CRent::GetInstance()
                            , CDate::GetInstance()
                            , CContact::GetInstance());
            if( $iReturn<1 )
            {
            	// Failed
            	RedirectError( $iReturn, __FILE__, __LINE__ );
				exit;
            }//if( $iReturn<1 )
        }//if( $sAction=='show' )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sBuffer=CDate::GetInstance()->GetRequestDay().' ';
        $sBuffer.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth()).' ';
        $sBuffer.=CDate::GetInstance()->GetRequestYear().' - ';
        $sBuffer.=CContact::GetInstance()->GetLastName().' '.CContact::GetInstance()->GetFirstName();
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sBuffer);
        CHeader::GetInstance()->SetDescription($sBuffer);
        CHeader::GetInstance()->SetKeywords($sBuffer);

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displayrent.php');
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
    }//if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
