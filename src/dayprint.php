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
 * description: build and display the reservation list for a day.
 *         GET: act=print, rey=<year>, rem=<month>, red=<day>
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
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
    $iPagingLimit=0;

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/cdate.php');

    /** Read input parameters
     ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act') )
    {
        // Get action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Verify action and parameters
        if( $sAction=='print' && filter_has_var(INPUT_GET, 'red')
                              && filter_has_var(INPUT_GET, 'rem')
                              && filter_has_var(INPUT_GET, 'rey') )
        {
            // Get the date
            CDate::GetInstance()->ReadInput(INPUT_GET);
        }
        else
        {
            // Parameters are not valid
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( CUser::GetInstance()->GetUsername(), $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
            CUser::GetInstance()->Invalidate();
        }// if( ($sAction=='update') || ($sAction=='show') )
    }//action = show

    /** Build the page
    ******************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {

        /** Get the current reservations count
         *************************************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentsgetcount.php');
        $iPagingLimit=RentsGetCount( CUser::GetInstance()->GetUsername()
                                    ,CUser::GetInstance()->GetSession()
                                    ,GetIP().GetUserAgent()
                                    ,CDate::GetInstance());
        if( $iPagingLimit<0 )
        {
            // Failed
            RedirectError( $iReturn, __FILE__, __LINE__ );
			exit;
        }//if( $iReturn>0 )

        /** Get the current reservations
         *******************************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentsget.php');
        $tRecordset=RentsGet( CUser::GetInstance()->GetUsername()
                             ,CUser::GetInstance()->GetSession()
                             ,GetIP().GetUserAgent()
                             ,CDate::GetInstance()
                             ,0
                             ,$iPagingLimit);
        if( !is_array($tRecordset) )
        {
            // Failed
        	include(PBR_PATH.'/includes/init/initclean.php');
			exit;
        }//if( !is_array($tRecordset) )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sFormTitle=CDate::GetInstance()->GetRequestDay().' ';
        $sFormTitle.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth()).' ';
        $sFormTitle.=CDate::GetInstance()->GetRequestYear();
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->ToPrint();
        CHeader::GetInstance()->SetTitle($sFormTitle);
        CHeader::GetInstance()->SetDescription($sFormTitle);
        CHeader::GetInstance()->SetKeywords($sFormTitle);
        CHeader::GetInstance()->SetTitle('Imprimer');
        CHeader::GetInstance()->SetDescription('Imprimer');
        CHeader::GetInstance()->SetKeywords('imprimer,print');

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displaydayprint.php');

        /** Clean
         ********/
        CHeader::DeleteInstance();

    }
    else
    {
        //Error
        include(PBR_PATH.'/includes/init/initclean.php');
        exit;
    }//if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
