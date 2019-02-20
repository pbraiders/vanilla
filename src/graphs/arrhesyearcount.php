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
 * description: draw the arrhes' details graph
 *         GET: op2=interval
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.2.0');
    define('PBR_PATH',dirname(__FILE__).'/..');
    define('PBR_FONT_PATH',dirname(__FILE__));

    /** Include config
     *****************/
    require(PBR_PATH.'/config.php');

    /** Include functions
     ********************/
    require(PBR_PATH.'/includes/function/functions.php');

    /** Initialize context
     *********************/
    require(PBR_PATH.'/includes/init/context.php');

    /** Authenticate
     ***************/
    require(PBR_PATH.'/includes/init/authadmin.php');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/class/coption.php');
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/cgraph.php');
    require(PBR_PATH.'/includes/class/cgpie.php');
    require(PBR_PATH.'/includes/class/ccsv.php');
    require(PBR_PATH.'/includes/function/graphs.php');
    require(PBR_PATH.'/includes/function/export.php');
    $pInterval = new COption('2', 0, 60);
    $pDate = new CDate();
    $pCCSV = null;

    /** Read input parameters
     ************************/
    $pInterval->ReadInput(INPUT_GET);
    if( filter_has_var( INPUT_POST, 'exp') )
    {
        // Export
        $pCCSV = new CCSV();
    }

    /** Read the data
     ****************/
    require(PBR_PATH.'/includes/db/function/arrhesdistinctcount.php');
    $tRecordset = ArrhesDistinctCount( CAuth::GetInstance()->GetUsername()
                                     , CAuth::GetInstance()->GetSession()
                                     , GetIP().GetUserAgent()
                                     , $pDate
                                     , $pInterval );

    unset( $pInterval, $pDate );

    if( !is_array($tRecordset) )
    {
        // Error
        unset($pCCSV);
        RedirectError( $tRecordset, __FILE__, __LINE__ );
        exit;
    }//if( !is_array($tRecordset) )

    /** Build page
     *************/

   // Change label
    $tLabels = array('Aucun','Espece','Cheque','CB');

    if( isset($pCCSV) )
    {
        /** Export case
         **************/
        if( ExportDistinct( $pCCSV, $tRecordset, $tLabels )===FALSE )
        {
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible de générer le fichier export', E_USER_ERROR, TRUE);
            unset($pCCSV);
            RedirectError( FALSE, __FILE__, __LINE__ );
            exit;
        }//if(...
    }
    else
    {
        /** Image case
         *************/

        // Draw chart
        $pGraph = new CGPie();
        $pGraph->SetLabel($tLabels);
        if( DrawGraph( $pGraph, $tRecordset)===FALSE )
        {
            $sTitle='fichier: '.basename(__FILE__).', ligne:'.__LINE__;
            ErrorLog( CAuth::GetInstance()->GetUsername(), $sTitle, 'impossible de dessiner le graphique', E_USER_ERROR, TRUE);
        }//if( DrawGraph($pGraph,$tRecordset)===FALSE )

    }//Case

    /** Delete objects
     *****************/
    unset( $pGraph, $pCCSV);
    include(PBR_PATH.'/includes/init/clean.php');
?>
