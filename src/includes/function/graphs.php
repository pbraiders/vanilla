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
 * description: contains usefull functions for graphs
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/**
  * function: DrawGraph
  * description: Initilize, draw and Output the graph
  * parameters: CGraph|pGraph     - instance of CGraph
  *              ARRAY|tRecordset - data
  * return: BOOLEAN - FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function DrawGraph( CGraph $pGraph, $tRecordset )
{
    $iCount = 0;
    // Count
    if( is_array($tRecordset) )
    {
        if( array_key_exists( 'values', $tRecordset) )
        {
            $iCount = count( $tRecordset['values'] );
        }
        else
        {
            $iCount = count( $tRecordset );
        }//if( array_key_exists(...
    }//if( is_array(...
    // Initialize
    $bReturn = $pGraph->Initialize($iCount);
    // Draw
    if( $iCount>0 )
    {
        $bReturn = $bReturn && $pGraph->Draw($tRecordset);
    }
    // Output
    $bReturn = $bReturn && $pGraph->Output();
    // Destroy
    $pGraph->Destroy();
    return $bReturn;
}

/**
  * function: ExportMonthCount
  * description: Export month count data.
  * parameters: CCSV|pCCSV      - instance of CGraph
  *            ARRAY|tRecordset - data
  * return: BOOLEAN - FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportMonthCount( CCSV $pCCSV, $tRecordset )
{
    $bReturn = FALSE;
    if( is_array($tRecordset) && count($tRecordset>0) )
    {
        // Open file
        $bReturn = ExportInit( $pCCSV, array('année','mois','total') );
        // Write data
        if( ($bReturn!==FALSE) && $pCCSV->IsOpen() )
        {
            foreach( $tRecordset as $iYear=>$tMonths )
            {
                foreach( $tMonths as $iMonth=>$iCount )
                {
                    $t =  array($iYear,$iMonth,$iCount);
                    ExportWrite( $pCCSV, $t);
                }//foreach( month
            }//foreach( year
        }//Write data

        // Close the file
        if( $pCCSV->IsOpen() )
            $pCCSV->Close();

        // Send
        $bReturn = $bReturn && ExportSend( $pCCSV );
    }// if( is_array($tRecordset) && count($tRecordset>0) )

    return $bReturn;
}

/**
  * function: ExportMonthSum
  * description: Export month sum data.
  * parameters: CCSV|pCCSV      - instance of CGraph
  *            ARRAY|tRecordset - data
  * return: BOOLEAN - FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportMonthSum( CCSV $pCCSV, $tRecordset )
{
    $bReturn = FALSE;
    if( is_array($tRecordset) && count($tRecordset>0) )
    {
        // Open file
        $bReturn = ExportInit( $pCCSV, array('année','mois','réel','planifié','annulé') );
        // Write data
        if( ($bReturn!==FALSE) && $pCCSV->IsOpen() )
        {
            foreach( $tRecordset as $iYear=>$tMonths )
            {
                foreach( $tMonths as $iMonth=>$iCount )
                {
                    $t =  array($iYear,$iMonth,$iCount['real'],$iCount['planned'],$iCount['canceled']);
                    ExportWrite( $pCCSV, $t);
                }//foreach( month
            }//foreach( year
        }//Write data

        // Close the file
        if( $pCCSV->IsOpen() )
            $pCCSV->Close();

        // Send
        $bReturn = $bReturn && ExportSend( $pCCSV );
    }// if( is_array($tRecordset) && count($tRecordset>0) )

    return $bReturn;
}

/**
  * function: ExportYearCount
  * description: Export year count data.
  * parameters: CCSV|pCCSV      - instance of CGraph
  *            ARRAY|tRecordset - data
  * return: BOOLEAN - FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportYearCount( CCSV $pCCSV, $tRecordset )
{
    $bReturn = FALSE;
    if( is_array($tRecordset) && count($tRecordset>0) )
    {
        // Open file
        $bReturn = ExportInit( $pCCSV, array('année','total') );
        // Write data
        if( ($bReturn!==FALSE) && $pCCSV->IsOpen() )
        {
            foreach( $tRecordset as $iYear=>$iCount )
            {
                $t =  array($iYear,$iCount);
                ExportWrite( $pCCSV, $t);
            }//foreach( year
        }//Write data

        // Close the file
        if( $pCCSV->IsOpen() )
            $pCCSV->Close();

        // Send
        $bReturn = $bReturn && ExportSend( $pCCSV );
    }// if( is_array($tRecordset) && count($tRecordset>0) )

    return $bReturn;
}

/**
  * function: ExportYearSum
  * description: Export year sum data.
  * parameters: CCSV|pCCSV      - instance of CGraph
  *            ARRAY|tRecordset - data
  * return: BOOLEAN - FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportYearSum( CCSV $pCCSV, $tRecordset )
{
    $bReturn = FALSE;
    if( is_array($tRecordset) && count($tRecordset>0) )
    {
        // Open file
        $bReturn = ExportInit( $pCCSV, array('année','réel','planifié','annulé') );
        // Write data
        if( ($bReturn!==FALSE) && $pCCSV->IsOpen() )
        {
            foreach( $tRecordset as $iYear=>$iCount )
            {
                $t =  array($iYear,$iCount['real'],$iCount['planned'],$iCount['canceled']);
                ExportWrite( $pCCSV, $t);
            }//foreach( year
        }//Write data

        // Close the file
        if( $pCCSV->IsOpen() )
            $pCCSV->Close();

        // Send
        $bReturn = $bReturn && ExportSend( $pCCSV );
    }// if( is_array($tRecordset) && count($tRecordset>0) )

    return $bReturn;
}

/**
  * function: RefactoreMonthCountOrSum
  * description: Refactor month count and sum
  * parameters: ARRAY|tIn  - data
  *           BOOLEAN|bSum - TRUE if refactoring sum
  * return: BOOLEAN - FALSE if an error occure
  * author: Olivier JULLIEN - 2010-06-15
  */
function RefactoreMonthCountOrSum( &$tRecordset, $bSum = FALSE )
{
    // Initialize
    $bReturn = FALSE;
    $tMonthName = array('Jan','Fév','Mar','Avr','Mai','Jun','Jui','Aoû','Sept','Oct','Nov','Déc');
    $tOut = array();

    if( is_array($tRecordset)
     && array_key_exists('info',$tRecordset) && is_array($tRecordset['info'])
     && array_key_exists('min',$tRecordset['info']) && array_key_exists('max',$tRecordset['info'])
     && array_key_exists('values',$tRecordset) )
    {
        $bReturn = TRUE;
        // For each years
        foreach($tRecordset['values'] as $tYear=>$tMonths )
        {
            // Last year test
            $bLastYear = FALSE;
            if( !isset($tRecordset['values'][$tYear+1]) )
            {
                $bLastYear = TRUE;
            }// Last year test

            // For each month
            $iMonthCount = count($tMonths);
            for( $iIndex=1;$iIndex<13;$iIndex++ )
            {
                // Label
                $sKey = $tMonthName[ $iIndex-1 ].' '.$tYear;
                // Value
                if( isset($tMonths[$iIndex]) )
                {
                    $iValue = $tMonths[$iIndex];
                    $iMonthCount-=1;
                }
                else
                {
                    if( ($bLastYear===TRUE) && ($iMonthCount<=0) )
                    {
                        // Do not display last 0 count
                        break;
                    }
                    else
                    {
                        if( !$bSum )
                            $iValue = 0;
                        else
                            $iValue = array( 'real'=>0, 'planned'=>0, 'canceled'=>0 );
                        $tRecordset['info']['min']=0;
                    }//if(...
                }//if( array_key_exists($iIndex,$tMonths) )

                // Add to the list
                $tOut[$sKey] = $iValue;

            }//For each month
        }//For each years

        $tRecordset['values'] = $tOut;

    }// if( ...

    return $bReturn;
}

/**
  * function: ExportDistinct
  * description: Export distinct data.
  * parameters: CCSV|pCCSV      - instance of CGraph
  *            ARRAY|tRecordset - data
  *            ARRAY|tLabels    - label
  * return: BOOLEAN - FALSE if an error occures
  * author: Olivier JULLIEN - 2010-06-15
  */
function ExportDistinct( CCSV $pCCSV, $tRecordset, $tLabels )
{
    $bReturn = FALSE;
    if( is_array($tRecordset) && count($tRecordset>0) && is_array($tLabels ) )
    {
        // Open file
        $bReturn = ExportInit( $pCCSV, array('label','valeur') );
        // Write data
        if( ($bReturn!==FALSE) && $pCCSV->IsOpen() )
        {
            foreach( $tRecordset as $iLabel=>$iValue )
            {
                if( array_key_exists($iLabel,$tLabels) )
                {
                    $iLabel = $tLabels[$iLabel];
                }//if( ...
                $t =  array( $iLabel, $iValue );
                ExportWrite( $pCCSV, $t );
            }//foreach( year
        }//Write data

        // Close the file
        if( $pCCSV->IsOpen() )
            $pCCSV->Close();

        // Send
        $bReturn = $bReturn && ExportSend( $pCCSV );
    }// if( is_array($tRecordset) && count($tRecordset>0) )

    return $bReturn;
}
