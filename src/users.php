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
 * description: build and display the user page.
 *         GET: act=show
 *         GET: act=select, usi<user identifier>
 *        POST: act=new, usr=<username>, pwd=<password>
 *        POST: act=update, usi<user identifier>, pwd=<password>, pwdc=<password>, sta=<state>
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-11 - add password check
 *                                      - fixed minor bug
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
    require(PBR_PATH.'/includes/init/initadmin.php');

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes/class/cnewuser.php');

    /** Read input parameters
     ************************/

    // Get the message code
    $iMessageCode=GetMessageCode();

    // Case action = GET show|select
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act') )
    {
        // Get the action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));

        // Get user identifier
        if($sAction=='select')
        {
			CNewUser::GetInstance()->ReadInput(INPUT_GET);
        }//if($sAction=='select')

        // Verify action and data
        if( (($sAction!='show') && ($sAction!='select'))
         || (($sAction=='select')&&(CNewUser::GetInstance()->GetIdentifier()<1)) )
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if...

    }//GET show|select

    // Case action = POST new|update
    if( CUser::GetInstance()->IsAuthenticated() && is_null($sAction)
                                                && filter_has_var(INPUT_POST, 'act') )
    {
        // Get the action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));

        // Get user data
        CNewUser::GetInstance()->ReadInput(INPUT_POST);

        // Verify action and data
        if( ($sAction!='new') && ($sAction!='update') )
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if( ($sAction!='new') && ($sAction!='update') )
    }//POST new|update

    /** Build the page
    ******************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {
        /** Action=update
         ****************/
        if( $sAction=='update' )
        {
            if( CNewUser::GetInstance()->IsValidUpdate()==FALSE )
            {
                $iMessageCode=1;
                $sAction='select';
                CNewUser::GetInstance()->SetPassword(null);
                CNewUser::GetInstance()->SetPasswordCheck(null);
            }
            else
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/userupdate.php');
                $iReturn = UserUpdate( CUser::GetInstance()->GetUsername()
                                     , CUser::GetInstance()->GetSession()
                                     , GetIP().GetUserAgent()
                                     , CNewUser::GetInstance());
                // Check error
                if( $iReturn>=0 )
                {
                    $iMessageCode=2;
                    $sAction='show';
                    CNewUser::DeleteInstance();
                }
                else
                {
                    // Failed
                    RedirectError( $iReturn, __FILE__, __LINE__ );
                    exit;
                }//if( $iReturn>=0 )
			}//if( (CNewUser::GetInstance()->IsValidUpdate()==FALSE) )
        }//if( $sAction=='update' )

        /** Action=new
         *************/
        if( $sAction=='new' )
        {
            if( CNewUser::GetInstance()->IsValidNew()==FALSE )
            {
                $iMessageCode=1;
                $sAction='show';
                CNewUser::GetInstance()->SetPassword(null);
                CNewUser::GetInstance()->SetPasswordCheck(null);
            }
            else
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/useradd.php');
                $iReturn = UserAdd( CUser::GetInstance()->GetUsername()
                                  , CUser::GetInstance()->GetSession()
                                  , GetIP().GetUserAgent()
                                  , CNewUser::GetInstance());
                // Check error
                if( $iReturn>0 )
                {
                    $iMessageCode=2;
                    $sAction='show';
                    CNewUser::DeleteInstance();
                }
                elseif( $iReturn==-4 )
                {
                    $iMessageCode=3;
                    $sAction='show';
                    CNewUser::DeleteInstance();
                }
                else
                {
                    // Failed
                    RedirectError( $iReturn, __FILE__, __LINE__ );
                    exit;
                }//if( $iReturn>0 )
            }//if( CNewUser::GetInstance()->IsValidNew()==FALSE )
        }//if( $sAction=='new' )

        /** Action=select
         ****************/
        if( $sAction=='select' )
        {
        	$iReturn=-2;
            if( CNewUser::GetInstance()->GetIdentifier()>0 )
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/userget.php');
                $iReturn = UserGet( CUser::GetInstance()->GetUsername()
                                  , CUser::GetInstance()->GetSession()
                                  , GetIP().GetUserAgent()
                                  , CNewUser::GetInstance());
			}//if( CNewUser::GetInstance()->GetIdentifier()>0 )

            //Error
            if( ($iReturn<1) || (CNewUser::GetInstance()->IsValidUpdate()==FALSE) )
			{
				// Failed
				RedirectError( $iReturn, __FILE__, __LINE__ );
				exit;
			}//if( ($iReturn<1) || (CNewUser::GetInstance()->IsValidUpdate()==FALSE) )
        }//if( $sAction=='select' )

        /** Get the users
         ****************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/usersget.php');
        $tRecordset=UsersGet( CUser::GetInstance()->GetUsername()
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
        $sBuffer='Utilisateurs';
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sBuffer);
        CHeader::GetInstance()->SetDescription($sBuffer);
        CHeader::GetInstance()->SetKeywords($sBuffer);

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displayusers.php');
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
