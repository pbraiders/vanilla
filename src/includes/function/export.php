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
 * description: contain usefull functions for export
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') || !defined('PBR_EXPORT_DIR') || !defined('PBR_AUTH_LOADED') )
    die('-1');

/**
  * function: ExportSend
  * description: send the file
  * parameters: CCSV|pCCSV - instance of CCSV
  * return: INTEGER - number of bytes read from the file or FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportSend( CCSV $pCCSV )
{
    // Initialize
    $iReturn = FALSE;
    // Send
    header("Content-disposition: attachment; filename=export.csv");
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: text/csv\n"); // ne pas enlever le \n
    header("Content-Length: ".filesize( $pCCSV->GetFilename() ) );
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
    header("Expires: 0");
    $iReturn = readfile( $pCCSV->GetFilename() );
    // Delete
    $pCCSV->DeleteFile();
    return $iReturn;
}

/**
  * function: ExportWrite
  * description: write the file
  * parameters: CCSV|pCCSV - instance of CCSV
  *            ARRAY|tCSV  - data
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportWrite( CCSV $pCCSV, &$tCSV )
{
    $bReturn = FALSE;
    if( $pCCSV->IsOpen() && !empty($tCSV) )
    {
        $bReturn = $pCCSV->WriteLine($tCSV);
        if( !$bReturn )
        {
            // Error
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible d\'écrire dans le fichier '.$pCCSV->GetFilename(), E_USER_ERROR, TRUE);
        }//if( !$bReturn )
    }//if( $pCCSV->IsOpen() && !empty($tCSV) )
    return $bReturn;
}

/**
  * function: ExportOpen
  * description: create and open the file
  * parameters: CCSV|pCCSV - instance of CCSV
  *           STRING|sDir  - working directory
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportOpen( CCSV $pCCSV, $sDir='' )
{
    if( $pCCSV->Open( $sDir, CCSV::EXPORTFILEPREFIX )===FALSE )
    {
        // Error
        $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
        ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible d\'ouvrir le fichier export', E_USER_ERROR, TRUE);
    }
    return $pCCSV->IsOpen();
}

/**
  * function: ExportRebuild
  * description: Rebuild the data
  * parameters: ARRAY|tIn  - data
  *             ARRAY|tOut - data rebuilded
  * return: BOOLEAN - TRUE or FALSE if an error occurs
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportRebuild( &$tIn, &$tOut )
{
    // Initialize
    $bReturn = FALSE;
    $tCarriage = array("\r\n", "\r", "\n");
    // Build
    if( is_array($tIn) && !empty($tIn) && is_array($tOut)
        && array_key_exists('contact_lastname', $tIn)
        && array_key_exists('contact_firstname', $tIn)
        && array_key_exists('contact_tel', $tIn)
        && array_key_exists('contact_email', $tIn)
        && array_key_exists('contact_address', $tIn)
        && array_key_exists('contact_addressmore', $tIn)
        && array_key_exists('contact_addresscity', $tIn)
        && array_key_exists('contact_addresszip', $tIn)
        && array_key_exists('contact_comment', $tIn)
        && array_key_exists('creation_date', $tIn) )
    {
        $tOut[] = $tIn['contact_lastname'];
        $tOut[] = $tIn['contact_firstname'];
        $tOut[] = $tIn['contact_tel'];
        $tOut[] = $tIn['contact_email'];
        $tOut[] = $tIn['contact_address'].' '.$tIn['contact_addressmore'];
        $tOut[] = $tIn['contact_addresscity'];
        $tOut[] = $tIn['contact_addresszip'];
        $tOut[] = str_replace($tCarriage, ' ', $tIn['contact_comment']);
        $tOut[] = $tIn['creation_date'];
    }//if( ...
    return !empty($tOut);
}

/**
  * function: ExportInitialize
  * description: Initialize export process
  * parameters: CCSV|pCCSV   - instance of CCSV
  *            ARRAY|tHeader - header of the file
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportInit( CCSV $pCCSV, $tCSV )
{
    // Initialize
    $sDir = PBR_PATH.'/'.PBR_EXPORT_DIR;
    // Open the file
    $bReturn = ExportOpen( $pCCSV, $sDir );
    // Write header
    if( $pCCSV->IsOpen() )
        $bReturn = ExportWrite( $pCCSV, $tCSV );
    return $bReturn;
}

?>
