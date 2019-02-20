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
 * description: validate admin user with database
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') || !defined('PBR_URL') || !defined('PBR_DB_DSN') || !defined('PBR_DB_USR') || !defined('PBR_DB_PWD') )
    die('-1');

    /** Check user validity
     **********************/
    CUser::GetInstance()->UnsetAuthentication();
    if( CUser::GetInstance()->IsValid()===TRUE )
    {
        // Open database
        require(PBR_PATH.'/includes/db/class/cdb.php');
        if( CDb::GetInstance()->Open(PBR_DB_DSN.PBR_DB_DBN,PBR_DB_USR,PBR_DB_PWD)===FALSE )
        {
            // Trace
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( CUser::DEFAULT_USER, $sTitle, 'impossible d\'ouvrir la base de données', E_USER_ERROR, FALSE);
        }
        else
        {
            // Verify
            require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/sessionvalid.php');
            $iReturn = SessionValid( CUser::GetInstance()->GetUsername()
                                   , CUser::GetInstance()->GetSession()
                                   , 10, GetIP().GetUserAgent());
            if( $iReturn>0 )
            {
                // The session is valid
                CUser::GetInstance()->SetAuthentication((integer)$iReturn);
            }
            else
            {
        		$sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
            	$sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        	ErrorLog( $sUser, $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
				CUser::GetInstance()->Invalidate();
                include(PBR_PATH.'/includes/init/initclean.php');
                header('Location: '.PBR_URL.'login.php');
                exit;
            }//if( $iReturn>0 )
        }//if( CDb::GetInstance()->Open(PBR_DB_DSN,PBR_DB_USR,PBR_DB_PWD)===FALSE )
    }
    else
    {
        include(PBR_PATH.'/includes/init/initclean.php');
        header('Location: '.PBR_URL.'login.php');
        exit;
    }//if( CUser::GetInstance()->IsValid()===TRUE )
?>
