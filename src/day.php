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
 * description: build and display the day page.
 *         GET: act=show, rey=<year>, rem=<month>, red=<day>, pag=<page>
 *         GET: act=select, rey=<year>, rem=<month>, red=<day>, cti=<contact identifier>, pag=<page>
 *        POST: act=new, rey=<year>, rem=<month>, red=<day>, ctX=<contact info>
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
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/ccontact.php');
    require(PBR_PATH.'/includes/class/crent.php');
    require(PBR_PATH.'/includes/class/cpaging.php');

    /** Read input parameters
     ************************/

    /// Case action = show|select
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act')
                                         		&& filter_has_var(INPUT_GET, 'red')
                                         		&& filter_has_var(INPUT_GET, 'rem')
                                         		&& filter_has_var(INPUT_GET, 'rey') )
    {
        // Get the action
        $sAction = trim(filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));

        // Get the Date
        CDate::GetInstance()->ReadInput(INPUT_GET);

        // Get the message code
		$iMessageCode=GetMessageCode();

        // Get the page
		CPaging::GetInstance()->ReadInput();

        // Get the contact identifier
        if( $sAction=='select' )
        {
           CContact::GetInstance()->SetIdentifier( filter_input(INPUT_GET,'cti',FILTER_VALIDATE_INT) );
        }//if( $sAction=='select' )

        // Verify action and data
        if( (($sAction!='show') && ($sAction!='select'))
         || (($sAction=='select') && CContact::GetInstance()->GetIdentifier()<1) )
        {
        	// Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if...

    }//action = show|select

    // Case action = new|newselected
    if( CUser::GetInstance()->IsAuthenticated() && is_null($sAction)
                                          		&& filter_has_var(INPUT_POST, 'act')
                                          		&& filter_has_var(INPUT_POST, 'red')
                                          		&& filter_has_var(INPUT_POST, 'rem')
                                          		&& filter_has_var(INPUT_POST, 'rey'))
    {

        // Get the action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));

        // Get the date
        CDate::GetInstance()->ReadInput(INPUT_POST);

        // Get the contact
        CContact::GetInstance()->ReadInput();

        // Get contact identifier
        if( filter_has_var(INPUT_POST, 'cti') )
        {
            CContact::GetInstance()->SetIdentifier( filter_input( INPUT_POST,'cti',FILTER_VALIDATE_INT));
        }//if( filter_has_var(INPUT_POST, 'cti') )

        // Get the rent
        CRent::GetInstance()->ReadInput(INPUT_POST);

        // Verify action and data
        if( $sAction=='new' )
        {
            if( !CContact::GetInstance()->MandatoriesAreFilled() )
            {
                // Missing values
                $iMessageCode=1;
                $sAction='show';
            }//if( !CContact::GetInstance()->MandatoriesAreFilled() )
        }
        elseif( ($sAction=='newselected') && (CContact::GetInstance()->GetIdentifier()>0) )
        {
            // OK
        }
        else
        {
        	// Parameters are not valid
            CUser::GetInstance()->Invalidate();
        }//if...

    }//action = new|newselected

    /** Build the page
    ******************/
    if( CUser::GetInstance()->IsAuthenticated() && !is_null($sAction) )
    {

        /** Action=select, display contact info
         **************************************/
        if( $sAction=='select' )
        {
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactget.php');
            $iReturn=ContactGet( CUser::GetInstance()->GetUsername()
                               , CUser::GetInstance()->GetSession()
                               , GetIP().GetUserAgent()
                               , CContact::GetInstance()->GetIdentifier()
                               , CContact::GetInstance());
            if( $iReturn<1 )
            {
            	//Error
            	RedirectError( $iReturn, __FILE__, __LINE__ );
            	exit;
            }//if( $iReturn<1 )
        }//if( $sAction=='select' )

        /** Action=new (add new contact and rent)
         ** or
         ** Action=new selected (add new rent to a contact )
         ***************************************************/
        if( ($sAction=='new') || ($sAction=='newselected') )
        {
            if( $sAction=='new' )
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentcontactadd.php');
                $iReturn = RentContactAdd( CUser::GetInstance()->GetUsername()
                                         , CUser::GetInstance()->GetSession()
                                         , GetIP().GetUserAgent()
                                         , CContact::GetInstance()
                                         , CDate::GetInstance()
                                         , CRent::GetInstance());
            }//if( $sAction=='new' )

            if( $sAction=='newselected' )
            {
                require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentadd.php');
                $iReturn = RentAdd( CUser::GetInstance()->GetUsername()
                                  , CUser::GetInstance()->GetSession()
                                  , GetIP().GetUserAgent()
                                  , CContact::GetInstance()->GetIdentifier()
                                  , CDate::GetInstance()
                                  , CRent::GetInstance());
            }//if( $sAction=='newselected' )

            // Check error
            if( $iReturn>0 )
            {
                // erase info
                CContact::DeleteInstance();
                CRent::DeleteInstance();
                $iMessageCode=2;
                $sAction='show';
            }
            else
            {
            	// Failed
            	RedirectError( $iReturn, __FILE__, __LINE__ );
				exit;
            }//if( $iReturn>0 )
        }// if( ($sAction=='new') || ($sAction=='newselected') )

        /** Get the current reservations count
         *************************************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentsgetcount.php');
        $iReturn=RentsGetCount( CUser::GetInstance()->GetUsername()
                               ,CUser::GetInstance()->GetSession()
                               ,GetIP().GetUserAgent()
                               ,CDate::GetInstance());
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

        /** Get the current reservations
         *******************************/
        require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentsget.php');
        $tRecordset=RentsGet( CUser::GetInstance()->GetUsername()
                             ,CUser::GetInstance()->GetSession()
                             ,GetIP().GetUserAgent()
                             ,CDate::GetInstance()
                             ,CPaging::GetInstance()->GetOffset()
                             ,CPaging::GetInstance()->GetLimit());
        if( !is_array($tRecordset) )
        {
            //Error
            RedirectError( $tRecordset, __FILE__, __LINE__ );
            exit;
        }//if( !is_array($tRecordset) )

        /** Build header
         ***************/
        require(PBR_PATH.'/includes/class/cheader.php');
        $sFormTitle=CDate::GetInstance()->GetRequestDay().' ';
        $sFormTitle.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth()).' ';
        $sFormTitle.=CDate::GetInstance()->GetRequestYear();
        CHeader::GetInstance()->SetNoCache();
        CHeader::GetInstance()->SetTitle($sFormTitle);
        CHeader::GetInstance()->SetDescription($sFormTitle);
        CHeader::GetInstance()->SetKeywords($sFormTitle);

        /** Display
         **********/
        require(PBR_PATH.'/includes/display/displayheader.php');
        require(PBR_PATH.'/includes/display/displayday.php');
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
