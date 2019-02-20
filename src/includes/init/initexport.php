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
 * description: manage export directory
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') )
    die('-1');

    /** Define
     *********/
    define('PBR_EXPORT_DIR','export');

    /** Initialize
     *************/
    $sExportDir=PBR_PATH.'/'.PBR_EXPORT_DIR;

    /** Create directory
     *******************/
    if( is_dir($sExportDir)===FALSE )
    {
        /** Create directory
         *******************/
        if( mkdir($sExportDir,0770)===FALSE )
        {
        	$sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( $sUser, $sTitle, 'impossible de créer le répertoire '.$sExportDir, E_USER_WARNING, TRUE);
        }//if( mkdir($sExportDir,0770)===FALSE )
    }
    else
    {
        /** Clean directory
         ******************/
        //Get files list
        $sExportPathnames=$sExportDir.'/{csv*}';
        $pFiles=glob( $sExportPathnames, GLOB_BRACE );
        if( $pFiles===FALSE )
        {
        	$sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
	        ErrorLog( $sUser, $sTitle, 'impossible de trouver les fichiers '.$sExportPathnames, E_USER_WARNING, TRUE);
        }
        else
        {
            // Delete files
            $iTime = time() - 60;
            foreach( $pFiles as $sFile )
            {
                if( (filemtime($sFile)<=$iTime) || (filectime($sFile)<=$iTime) )
                {
                    if( unlink($sFile)===FALSE )
                    {
			        	$sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
			            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
				        ErrorLog( $sUser, $sTitle, 'impossible de d\'effacer le fichier '.$sFile, E_USER_WARNING, TRUE);
                    }//if( unlink($pFile) )
                }//if( (filemtime($sFile)<=$iTime) || (filectime($sFile)<=$iTime) )
            }//foreach( $pFiles as $sFile )
        }//if( $pFiles===FALSE )
    }//if( !is_dir($sExportDir) )

    /** Include file object
     **********************/
    require(PBR_PATH.'/includes/class/ccsv.php');
?>
