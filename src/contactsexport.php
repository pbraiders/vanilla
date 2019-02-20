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
 * description: build and display the contact export page.
 *         GET: act=export, ctl=<search>
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
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
    require(PBR_PATH.'/includes/init/initexport.php');
    $sSearch='';
    $sAction=null;
    $sSended=FALSE;
	$iPagingLimit=0;
    $tCarriage=array("\r\n", "\r", "\n");

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/inituser.php');

    /** Mandatory Parameters
     ***********************/
    if( CUser::GetInstance()->IsAuthenticated() && filter_has_var(INPUT_GET, 'act') )
    {
        // Get action
        $sAction = trim( filter_input( INPUT_GET, 'act', FILTER_SANITIZE_SPECIAL_CHARS));
        // Analyse
        if( $sAction=='export' )
        {
            // Get search name
            if( filter_has_var(INPUT_GET, 'ctl') )
            {
                $sSearch = rawurldecode( trim( filter_input( INPUT_GET, 'ctl', FILTER_UNSAFE_RAW)));
            }//if( filter_has_var(INPUT_GET, 'ctl') )

            /** Get contact count
             ********************/
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactsgetcount.php');
            $iPagingLimit=ContactsGetCount( CUser::GetInstance()->GetUsername()
                                           ,CUser::GetInstance()->GetSession()
                                           ,GetIP().GetUserAgent()
                                           ,$sSearch);
            if( $iPagingLimit<0 )
            {
            	// Failed
            	RedirectError( $iPagingLimit, __FILE__, __LINE__ );
				exit;
            }//if( $iPagingLimit>0 )

            // Get contact list
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/contactsget.php');
            $tRecordset=ContactsGet( CUser::GetInstance()->GetUsername()
                                    ,CUser::GetInstance()->GetSession()
                                    ,GetIP().GetUserAgent()
                                    ,$sSearch
                                    ,0
                                    ,$iPagingLimit);
            if( is_array($tRecordset) )
            {
                /** Refactoring
                 **************/
                $tCSV=array();
                $tCSV[]=array('nom','prénom','téléphone','email','adresse','ville','code postal','commentaire','date de création');
                foreach( $tRecordset as $tRecord )
                {
                    $tCSV[]=array( $tRecord['contact_lastname'],
                                   $tRecord['contact_firstname'],
                                   $tRecord['contact_tel'],
                                   $tRecord['contact_email'],
                                   $tRecord['contact_address'].' '.$tRecord['contact_addressmore'],
                                   $tRecord['contact_addresscity'],
                                   $tRecord['contact_addresszip'],
                                   str_replace($tCarriage, ' ', $tRecord['contact_comment']),
                                   $tRecord['creation_date']);
                }//foreach( $tRecordset as $tRecord )

                /** Open file
                 ************/
                if( CCSV::GetInstance()->Open()===FALSE )
                {
		            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
			        ErrorLog( CUser::GetInstance()->GetUsername(), $sTitle, 'impossible d\'ouvrir le fichier', E_USER_WARNING, TRUE);
                }//if( CCSV::GetInstance()->Open($sExportDir)===FALSE )

                /** Write data
                 *************/
                if( CCSV::GetInstance()->IsOpen()===TRUE )
                {
                    $sSended=CCSV::GetInstance()->Write($tCSV);
                    CCSV::GetInstance()->Close();

                    if( $sSended===FALSE )
                    {
			            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
				        ErrorLog( CUser::GetInstance()->GetUsername(), $sTitle, 'impossible d\'exporter le fichier '.CCSV::GetInstance()->GetFilename(), E_USER_WARNING, TRUE);
                    }
                    else
                    {
                        /** Send data
                         ************/
                        header("Content-disposition: attachment; filename=export.csv");
                        header("Content-Type: application/force-download");
                        header("Content-Transfer-Encoding: text/csv\n"); // ne pas enlever le \n
                        header("Content-Length: ".filesize(CCSV::GetInstance()->GetFilename()));
                        header("Pragma: no-cache");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
                        header("Expires: 0");
                        readfile(CCSV::GetInstance()->GetFilename());
                    }//if( CCSV::GetInstance()->Write($tCSV))===FALSE )
                }//if( CCSV::GetInstance()->IsOpen()===TRUE )

                /** Display
                 **********/
                if( $sSended===FALSE )
                {
                     require(PBR_PATH.'/includes/display/displaycontactsexport.php');
                }//if( $sSended===FALSE )
            }
            else
            {
                echo 'Liste vide.',"\n";
            }//if( is_array($tRecordset) )
        }
        else
        {
            // Parameters are not good
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( CUser::GetInstance()->GetUsername(), $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
        }//if( $sAction=='export' )
    }//if( filter_has_var(...

    /** Delete objects
     *****************/
    CCSV::DeleteInstance();
    include(PBR_PATH.'/includes/init/initclean.php');
?>
