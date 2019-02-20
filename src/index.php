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
 * description: build and display the main page.
 *          POST: act=calendar, cuy=<current year>, cum=<current month>, rem=<requested month>
 *                rey=<requested year>, go=<goto date> ou pre=<previous month> ou nex=<next month>
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
    $sAction=NULL;

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/cdate.php');

    /** Read input parameters
     ************************/

    // Case: POST
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_POST, 'act')
    											&& filter_has_var(INPUT_POST, 'cuy')
										  		&& filter_has_var(INPUT_POST, 'cum')
                                          		&& filter_has_var(INPUT_POST, 'rem')
                                          		&& filter_has_var(INPUT_POST, 'rey')
                                          		&& ( filter_has_var(INPUT_POST,'go')
                                          		||   filter_has_var(INPUT_POST,'pre')
										  		||   filter_has_var(INPUT_POST, 'nex') ) )
    {
        // Get action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Verify action and parameters
        if( $sAction=='calendar' )
        {
            if( filter_has_var(INPUT_POST, 'go') )
            {
                // Goto date
                CDate::GetInstance()->ReadInput(INPUT_POST);
            }
            else
            {
                // Calendar navigation
                // Get month
                $tFilter = array('options' => array('min_range' => 1, 'max_range' => 12));
                CDate::GetInstance()->SetRequestMonth( filter_input( INPUT_POST, 'cum', FILTER_VALIDATE_INT,$tFilter) );
                // Get year
                $tFilter = array('options' => array('min_range' => CDate::MINYEAR, 'max_range' => CDate::MAXYEAR));
                CDate::GetInstance()->SetRequestYear( filter_input( INPUT_POST, 'cuy', FILTER_VALIDATE_INT, $tFilter) );
                // Move
                if( filter_has_var(INPUT_POST, 'pre') )
                {
                    // Previous
                    CDate::GetInstance()->PreviousRequestMonth();
                }
                elseif(  filter_has_var(INPUT_POST, 'nex') )
                {
                    // Next
                    CDate::GetInstance()->NextRequestMonth();
                }//if( filter_has_var(INPUT_POST, 'pre') )
            }//if( filter_has_var(INPUT_POST, 'go') )
        }
        else
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if( $sAction=='calendar' )
    }//filter_has_var( POST ....

    // Case: GET
    if( CUser::GetInstance()->IsAuthenticated() && is_null($sAction)
                                                && filter_has_var(INPUT_GET, 'act')
                                                && filter_has_var(INPUT_GET, 'rem')
                                                && filter_has_var(INPUT_GET, 'rey'))
    {
        // Get action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get date
        CDate::GetInstance()->ReadInput(INPUT_GET);
        // Verify action
        if( $sAction!='calendar' )
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
            TraceWarning('Possible hacking attempt.',__FILE__,__LINE__);
        }//if( $sAction!='calendar' )
    }//filter_has_var( GET

    /** Build Calendar
     *****************/
    if( CUser::GetInstance()->IsAuthenticated() )
    {
        /** Get the rent day infos
         *************************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentsmonthget.php');
        $tRecordset=RentsMonthGet( CUser::GetInstance()->GetUsername()
                             ,CUser::GetInstance()->GetSession()
                             ,GetIP().GetUserAgent()
                             ,CDate::GetInstance());
        if( !is_array($tRecordset) )
        {
            //Error
            RedirectError( $tRecordset, __FILE__, __LINE__ );
            exit;
        }//if( !is_array($tRecordset) )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sBuffer=CDate::GetInstance()->GetMonthName( CDate::GetInstance()->GetRequestMonth() );
        $sBuffer.=' '.CDate::GetInstance()->GetRequestYear();
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sBuffer);
        CHeader::GetInstance()->SetDescription($sBuffer);
        CHeader::GetInstance()->SetKeywords($sBuffer);

		/** Admin case
	     *************/
		if( SessionValid( CUser::GetInstance()->GetUsername()
        				, CUser::GetInstance()->GetSession()
                        , 10
                        , GetIP().GetUserAgent()) >0 )
	    {
	    	$bAdmin=TRUE;
	    }
	    else
	    {
	    	$bAdmin=FALSE;
	    }//admin case

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displaycalendar.php');
        require(PBR_PATH.'/includes/display/displayfooter.php');

        /** Clean
         ********/
        CHeader::DeleteInstance();
    }
    else
    {
        include(PBR_PATH.'/includes/init/initclean.php');
        header('Location: '.PBR_URL.'login.php');
		exit;
    }//if( CUser::GetInstance()->IsAuthenticated() )

    /** Delete objects
     *****************/
    include(PBR_PATH.'/includes/init/initclean.php');
?>
