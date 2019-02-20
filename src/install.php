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
    require(PBR_PATH.'/includes-install/init.php');
    $sAction=NULL;
    $iMessageCode=0;

    /** Include main object(s)
     *************************/
    require(PBR_PATH.'/includes-install/cnewuser.php');

    /** Initialize database
     **********************/
    require(PBR_PATH.'/includes-install/initdb.php');

    /** Prerequiste test
     ******************/
    $sPHPVersion=phpversion();
    $sMYSQLVersion=mysql_get_client_info();
    $sPHPVersionRequired='5.2';
    $sMYSQLVersionRequired='5.0';
    if( !version_compare( $sPHPVersion, $sPHPVersionRequired, '>=')
        && !version_compare( $sMYSQLVersion, $sMYSQLVersionRequired, '>=') )
    {
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
		ErrorLog( 'install', $sTitle, 'les versions ne sont pas valides', E_USER_ERROR, FALSE);
        $iMessageCode=3;
    }//if( !version_compare( $sPHPVersion, $sPHPVersionRequired, '>=') )

    /** Read input parameters
     ************************/
    if( filter_has_var(INPUT_POST, 'act')
        && filter_has_var(INPUT_POST, 'usr')
        && filter_has_var(INPUT_POST, 'pwd1')
        && filter_has_var(INPUT_POST, 'pwd2') )
    {
        // Get action
        $sAction = trim(filter_input( INPUT_POST, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Get user
        CNewUser::GetInstance()->ReadInput(INPUT_POST);
        // Verify action and data
        if( ($sAction!='install') || (CNewUser::GetInstance()->IsValidNew()==FALSE) )
        {
            // Parameters are not valid
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( 'install', $sTitle, 'les paramètres ne sont pas valides', E_USER_ERROR, FALSE);
            $iMessageCode=4;
            $sAction=NULL;
        }//if( ($sAction=='login') && ($sToken==CSession::GetToken()) )
    }//if( filter_has_var(...

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    CHeader::GetInstance()->SetNoCache();
    CHeader::GetInstance()->SetTitle('Installation de PBRaiders '.PBR_VERSION);
    CHeader::GetInstance()->SetDescription('Installation de PBRaiders '.PBR_VERSION);
    CHeader::GetInstance()->SetKeywords('install,installation,installer');

    /** Display or install
     *********************/
    require(PBR_PATH.'/includes/display/displayheader.php');
    if( $sAction==NULL )
    {
        // Display
        require(PBR_PATH.'/includes-install/displayinstall.php');
    }
    else
    {
        // Install
        require(PBR_PATH.'/includes-install/installdb.php');
    }//
    require(PBR_PATH.'/includes/display/displayfooter.php');

    /** Clean
     ********/
    CHeader::DeleteInstance();
    if(defined('PBR_DB_LOADED')) CDb::DeleteInstance();
    if(defined('PBR_NEWUSER_LOADED') ) CNewUser::DeleteInstance();
    if(defined('PBR_LOG_LOADED')) CLog::DeleteInstance();
    CErrorList::DeleteInstance();
?>
