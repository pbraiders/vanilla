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
 * description: build and display the new contact page.
 *        POST: act=new, ctX=<contact info>
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

    /** Read input parameters
     ************************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_POST, 'act')
        										&& filter_has_var(INPUT_POST, 'ctl')
        										&& filter_has_var(INPUT_POST, 'ctf')
        										&& filter_has_var(INPUT_POST, 'ctp') )
    {
        // Get action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Verify action
        if( $sAction=='new' )
        {
            // Get the value
            CContact::GetInstance()->ReadInput();
        }
        else
        {
            // Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }// if( $sToken==CSession::GetToken() && ($sAction=='new') )
    }//if( filter_has_var(...

    /** Build Page
     *************/
    if( CUser::GetInstance()->IsAuthenticated() )
    {
        /** Add a new contact
         ********************/
        if( $sAction=='new' )
        {
            if( CContact::GetInstance()->MandatoriesAreFilled()===TRUE )
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactadd.php');
                $iReturn = ContactAdd( CUser::GetInstance()->GetUsername()
                                     , CUser::GetInstance()->GetSession()
                                     , GetIP().GetUserAgent()
                                     , CContact::GetInstance());
                if( $iReturn>0 )
                {
                    // Succeed
                    $sBuffer='?act=search';
                    $sBuffer.='&ctl='.rawurlencode(CContact::GetInstance()->GetLastName());
                    $sBuffer.='&error=1';
                    include(PBR_PATH.'/includes/init/initclean.php');
                    header('Location: '.PBR_URL.'contacts.php'.$sBuffer);
                    exit;
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
            }//if{ CContact::GetInstance()->MandatoriesAreFilled()===TRUE )
        }// if( $sAction=='new' )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sBuffer='Nouveau contact';
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sBuffer);
        CHeader::GetInstance()->SetDescription($sBuffer);
        CHeader::GetInstance()->SetKeywords($sBuffer);

        /** Display
         **********/
        $sAction='new';
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
