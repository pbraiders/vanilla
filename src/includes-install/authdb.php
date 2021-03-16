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
 * description: initialize database connection
 * author: Olivier JULLIEN - 2010-05-24
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') || !defined('PBR_DB_DSN') || !defined('PBR_DB_USR') || !defined('PBR_DB_PWD') )
    die('-1');


    require(PBR_PATH.'/includes/db/class/cdblayer.php');

    // Open database
    if( CDBLayer::GetInstance()->Open( PBR_DB_DSN.PBR_DB_DBN, PBR_DB_USR, PBR_DB_PWD, CAuth::GetInstance()->GetUsername() )===FALSE )
    {
        // Error
        $tMessageCode[] = 1;
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible d\'ouvrir la base de données', E_USER_ERROR, FALSE);
    }
    else
    {
        // Test if already exist
        try
        {
            // Prepare
            $sSQL='SELECT c.`value` AS "version" FROM `'.PBR_DB_DBN.'`.`config` AS c WHERE c.`name` LIKE "schema_version"';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('version', $tabResult[0])===TRUE )
                {
                    $tMessageCode[] = 2;
                }//if( array_key_exists('version', $tabResult[0])===TRUE )
            }//if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
        }
        catch(PDOException $e)
        {
//            $tMessageCode = array();
        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if(...
