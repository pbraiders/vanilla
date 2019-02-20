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
 * description: build and display the database log page.
 *         GET: act=show, pag=<page>
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
    require(PBR_PATH.'/includes/init/initadmin.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/cpaging.php');

	/** Read input parameters
    ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act') )
	{
		// Get action
		$sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));

		// Get the message code
		$iMessageCode=GetMessageCode();

		// Get the page
		CPaging::GetInstance()->ReadInput();

  	    // Verify action
   	    if( ($sAction!='show') && ($sAction!='delete') )
   	    {
   	        // Parameters are not valid
   	        CUser::GetInstance()->Invalidate();
   	    }//if( ($sAction!='show') && ($sAction!='delete') )

    }//if( filter_has_var(...

    /** Build Page
     *************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {

        /** Delete
        **********/
        if( $sAction=='delete' )
        {
            // Create session
            require(PBR_PATH.'/includes/class/csession.php');
            CSession::CreateSession();
            // Build token
            $sToken = md5(uniqid(rand(), TRUE));
            CSession::GetInstance()->SetToken($sToken);
            // Send
            $sBuffer=PBR_URL.'logsdelete.php?act=confirm&tok='.$sToken;
            include(PBR_PATH.'/includes/init/initclean.php');
            header('Location: '.$sBuffer);
            exit;
        }//delete

        // Get log count
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/logsgetcount.php');
        $iReturn=LogsGetCount( CUser::GetInstance()->GetUsername()
                              ,CUser::GetInstance()->GetSession()
                              ,GetIP().GetUserAgent());
        if( $iReturn>=0 )
        {
        	// Succeeded
			CPaging::GetInstance()->Compute( (integer)PBR_PAGE_LOGS, (integer)$iReturn );
        }
        else
        {
            // Failed
            RedirectError( $iReturn, __FILE__, __LINE__ );
			exit;
        }//if( $iReturn>0 )

        // Get log list
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/logsget.php');
        $tRecordset=LogsGet( CUser::GetInstance()->GetUsername()
                            ,CUser::GetInstance()->GetSession()
                            ,GetIP().GetUserAgent()
                            ,CPaging::GetInstance()->GetOffset()
                            ,CPaging::GetInstance()->GetLimit());
        if( is_array($tRecordset) )
        {
            /** Build header
             ***************/
            require(PBR_PATH.'/includes/class/cheader.php');
            $sBuffer='Logs';
            CHeader::GetInstance()->SetNoCache();
            CHeader::GetInstance()->SetTitle($sBuffer);
            CHeader::GetInstance()->SetDescription($sBuffer);
            CHeader::GetInstance()->SetKeywords($sBuffer);

            /** Display
             **********/
            require(PBR_PATH.'/includes/display/displayheader.php');
            require(PBR_PATH.'/includes/display/displaylogs.php');
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
    }//if( CUser::GetInstance()->IsAuthenticated() )

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
