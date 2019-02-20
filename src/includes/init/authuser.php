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
 * description: authenticate common user
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') || !defined('PBR_URL') || !defined('PBR_DB_DSN') || !defined('PBR_DB_USR') || !defined('PBR_DB_PWD') )
    die('-1');

    require(PBR_PATH.'/includes/db/class/cdblayer.php');

    // Reset DB authentication
    CAuth::GetInstance()->UnsetAuthentication();

    // Check SESSION validity
    if( CAuth::GetInstance()->IsValid()===TRUE )
    {
        // Open database
        if( CDBLayer::GetInstance()->Open( PBR_DB_DSN.PBR_DB_DBN, PBR_DB_USR, PBR_DB_PWD, CAuth::GetInstance()->GetUsername() )===FALSE )
        {
            // Trace
            $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible d\'ouvrir la base de données', E_USER_ERROR, FALSE);
        }
        else
        {
            // Verify
            require(PBR_PATH.'/includes/db/function/sessionvalid.php');
            $iReturn = SessionValid( CAuth::GetInstance()->GetUsername()
                                   , CAuth::GetInstance()->GetSession()
                                   , 1, GetIP().GetUserAgent());
            if( $iReturn>0 )
            {
                // The session is valid
                CAuth::GetInstance()->SetAuthentication( $iReturn );
            }
            else
            {
                if( ($iReturn==-2) || ($iReturn==-3) )
                {
    	            $sTitle = 'fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	            	ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
                }//if( ($iReturn==-2) || ($iReturn==-3) )
                CAuth::GetInstance()->Invalidate();
            }//if( $iReturn>0 )
        }//if( CDBLayer::GetInstance()->Open(....
    }//if( CAuth::GetInstance()->IsValid(...

    // No authenticated
    if( CAuth::GetInstance()->IsAuthenticated()===FALSE )
    {
        include(PBR_PATH.'/includes/init/clean.php');
        header('Location: '.PBR_URL.'login.php');
        exit;
    }//if( !CAuth::GetInstance()->IsAuthenticated(...

?>
