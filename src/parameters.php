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
 * description: build and display the parameters page.
 *         GET: act=show
 *         GET: act=delete, rey=<year>
 *        POST: act=update, paX=<parameter value>
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
    $iYear=NULL;
    $tMonthMax=NULL;

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/initadmin.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/cdate.php');

    /** Read input parameters
     ************************/

    // Get the message code
    $iMessageCode=GetMessageCode();

    // Case action = GET show|delete
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act') )
    {
        // Get the action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get the Date
        if( ($sAction=='delete') && filter_has_var(INPUT_GET, 'rey') )
        {
            $iYear=(integer)filter_input(INPUT_GET,'rey',FILTER_VALIDATE_INT);
        }//if( ($sAction=='delete') && filter_has_var(INPUT_GET, 'rey') )
        // Verify action and data
        if( ($sAction!='show') && ($sAction!='delete') )
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if( ($sAction!='show') && ($sAction!='delete') )
    }//GET show|delete

    // Case action = POST update
    if( CUser::GetInstance()->IsAuthenticated() && is_null($sAction)
                                                && filter_has_var(INPUT_POST, 'act') )
    {
        // Get the action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get the values
        $tMonthMax=array();
        for($iIndex=1;$iIndex<13;$iIndex++)
        {
            $sBuffer='pa'.$iIndex;
            $iValue=0;
            if( filter_has_var(INPUT_POST,$sBuffer) )
            {
                $iValue=filter_input(INPUT_POST,$sBuffer,FILTER_VALIDATE_INT);
            }//if( filter_has_var(INPUT_POST,$sBuffer) )
            if( !is_null($iValue) && ($iValue!==FALSE) && ($iValue>=0) )
            {
                $tMonthMax['max_rent_'.$iIndex]=$iValue;
            }
            else
            {
                $tMonthMax=NULL;
                break;
            }//if( !is_null($iValue) && ($iValue!==FALSE) && ($iValue>=0) )
        }//for($iIndex=1;$iIndex<13;$iIndex++)
        // Verify action and data
        if( $sAction!='update' )
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if( $sAction!='update')
    }//POST update

    /** Build the page
    ******************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {
        /** Action=update
         ****************/
        if( $sAction=='update' )
        {
            if( is_null($tMonthMax) )
            {
                $iMessageCode=1;
                $sAction='show';
            }
            else
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/maxupdate.php');
                $iReturn = MaxUpdate( CUser::GetInstance()->GetUsername()
                                    , CUser::GetInstance()->GetSession()
                                    , GetIP().GetUserAgent()
                                    , $tMonthMax);
                // Check error
                if( $iReturn>=0 )
                {
                    $iMessageCode=4;
                    $sAction='show';
                }
                else
		    	{
			    	// Failed
				    RedirectError( $iReturn, __FILE__, __LINE__ );
    				exit;
	    		}//if( $iReturn>=0 )
            }//if( is_null($tMonthMax) )
        }//if( $sAction=='update' )

        /** Action=delete
         ****************/
        if( $sAction=='delete' )
        {
            if( ($iYear>CDate::GetInstance()->GetCurrentYear()) || ($iYear<=0) )
            {
                $iMessageCode=3;
                $sAction=='show';
            }
            else
            {
	            // Create session
	            require(PBR_PATH.'/includes/class/csession.php');
	            CSession::CreateSession();
	            // Build token
	            $sToken = md5(uniqid(rand(), TRUE));
	            CSession::GetInstance()->SetToken($sToken);
	            // Send
	            $sBuffer=PBR_URL.'rentsdelete.php?act=confirm&tok='.$sToken.'&rey='.$iYear;
	            include(PBR_PATH.'/includes/init/initclean.php');
	            header('Location: '.$sBuffer);
	            exit;
            }//if( ($iYear>=CDate::GetInstance()->GetCurrentYear()) || ($iYear<=0) )
        }//if( $sAction=='delete' )

        /** Get the max
         **************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/maxget.php');
        $tRecordset=MaxGet( CUser::GetInstance()->GetUsername()
                           ,CUser::GetInstance()->GetSession()
                           ,GetIP().GetUserAgent());
        if( !is_array($tRecordset) )
        {
            //Error
            RedirectError( $tRecordset, __FILE__, __LINE__ );
            exit;
        }//if( !is_array($tRecordset) )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sBuffer='Paramètres';
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sBuffer);
        CHeader::GetInstance()->SetDescription($sBuffer);
        CHeader::GetInstance()->SetKeywords($sBuffer);

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displayparameters.php');
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
