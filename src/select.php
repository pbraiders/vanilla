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
 * description: build and display the select page.
 *         GET: act=select, rey=<year>, rem=<month>, red=<day>, pag=<page>
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

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/init/init.php');
    $sSearch='';
    $sAction=NULL;

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/cpaging.php');

    /** Read input parameters
     ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act')
    											&& filter_has_var(INPUT_GET, 'rey')
                                         		&& filter_has_var(INPUT_GET, 'rem')
                                         		&& filter_has_var(INPUT_GET, 'red') )
    {
        // Get action
        $sAction = trim( filter_input(INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS) );

      	// Get date
       	CDate::GetInstance()->ReadInput(INPUT_GET);

		// Get the page
		CPaging::GetInstance()->ReadInput();

        // Get search key
        if( filter_has_var(INPUT_GET, 'ctl') )
        {
            $sSearch = rawurldecode( trim( filter_input(INPUT_GET,'ctl',FILTER_UNSAFE_RAW) ) );
        }//if( filter_has_var(INPUT_GET, 'ctl') )

        // Verify readed values
        if( $sAction!='search' )
	    {
	        // Parameters are not valid
	        CUser::GetInstance()->Invalidate();
	    }//if( $sAction!='search' )
    }//if( filter_has_var( ....

    /** Build Page
     *************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {

        /** Get contact count
         ********************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactsgetcount.php');
        $iReturn=ContactsGetCount( CUser::GetInstance()->GetUsername()
                                  ,CUser::GetInstance()->GetSession()
                                  ,GetIP().GetUserAgent()
                                  ,$sSearch);
        if( $iReturn>=0 )
        {
        	// Succeeded
			CPaging::GetInstance()->Compute( (integer)PBR_PAGE_CONTACTS, (integer)$iReturn );
        }
        else
        {
            // Failed
            RedirectError( $iReturn, __FILE__, __LINE__ );
			exit;
        }//if( $iReturn>0 )

        /** Get contact list
         *******************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactsget.php');
        $tRecordset=ContactsGet( CUser::GetInstance()->GetUsername()
                                ,CUser::GetInstance()->GetSession()
                                ,GetIP().GetUserAgent()
                                ,$sSearch
                                ,CPaging::GetInstance()->GetOffset()
                                ,CPaging::GetInstance()->GetLimit());
        if( is_array($tRecordset) )
        {

            /** Build header
             ***************/
            require(PBR_PATH.'/includes/class/cheader.php');
            $sBuffer='Contacts';
            CHeader::GetInstance()->SetNoCache();
            CHeader::GetInstance()->SetTitle($sBuffer);
            CHeader::GetInstance()->SetDescription($sBuffer);
            CHeader::GetInstance()->SetKeywords($sBuffer);
            if( strlen($sSearch)>0 )
            {
                CHeader::GetInstance()->SetTitle($sSearch);
                CHeader::GetInstance()->SetDescription($sSearch);
                CHeader::GetInstance()->SetKeywords($sSearch);
            }//if( strlen($sSearch)>0 )

            /** Display
             **********/
            require(PBR_PATH.'/includes/display/displayheader.php');
            require(PBR_PATH.'/includes/display/displayselect.php');
            require(PBR_PATH.'/includes/display/displayfooter.php');

            /** Clean
             ********/
            CHeader::DeleteInstance();

        }
        else
        {
            //Error
            RedirectError( $tRecordset, __FILE__, __LINE__ );
            exit;
        }//if( is_array($tRecordset) )
    }
    else
    {
		//Error
		RedirectError( -2, __FILE__, __LINE__ );
		exit;
    }//if( CUser::GetInstance()->IsAuthenticated() && ($sAction=='search') )

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
