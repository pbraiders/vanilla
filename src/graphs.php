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
 * description: build and display the graphs page.
 *        POST: opX=<option value>, exp=<export case>
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/

    /** Defines
     **********/
    define('PBR_VERSION','1.2.0');
    define('PBR_PATH',dirname(__FILE__));

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

    /** Build graph list
     *******************/
    $tGraphs = array();
    $tGraphs[1] = array('Nombre de réservations par','rentsmonthcount','Réservations','mois');
    $tGraphs[2] = array('Nombre de réservations par','rentsyearcount','Réservations','an');
    $tGraphs[3] = array('Détail des réservations par','rentsmonthsum','Réservations','mois');
    $tGraphs[4] = array('Détail des réservations par','rentsyearsum','Réservations','an');
    $tGraphs[5] = array('Création de contacts par','contactsmonthcount','Contacts','mois');
    $tGraphs[6] = array('Création de contacts par','contactsyearcount','Contacts','an');
    $tGraphs[7] = array('Répartition des arrhes','arrhesyearcount','Arrhes','');
    $tGraphs[8] = array('Répartition des ages','agesyearcount','Ages','');

    /** Initialize
     *************/
    require(PBR_PATH.'/includes/class/cdate.php');
    require(PBR_PATH.'/includes/class/coption.php');
    $pDate = new CDate();
    $pChoice = new COption('1', 1, count($tGraphs) );
    $pInterval = new COption('2', 0, 60);

    /** Read input parameters
     ************************/
    $pChoice->ReadInput(INPUT_POST);
    $pInterval->ReadInput(INPUT_POST);

    /** Build header
     ***************/
    require(PBR_PATH.'/includes/class/cheader.php');
    $pHeader = new Cheader();
    $sBuffer = 'Graphes';
    $sTitle = $sKeywords = $tGraphs[$pChoice->GetValue()][2];
    if( $pInterval->GetValue()>0 )
    {
        $sTitle .= ' de '.($pDate->GetCurrentYear()-$pInterval->GetValue()).' à '.$pDate->GetCurrentYear();
    }
    else
    {
        if( !empty($tGraphs[$pChoice->GetValue()][3]) )
            $sTitle .= ' par '.$tGraphs[$pChoice->GetValue()][3];
    }
    $sBuffer .= ' - '.$sTitle;
    $pHeader->SetNoCache();
    $pHeader->SetTitle($sBuffer);
    $pHeader->SetDescription($sBuffer);
    $pHeader->SetKeywords('graphs');
    $pHeader->SetKeywords($sKeywords);

    /** Display
     **********/
    require(PBR_PATH.'/includes/display/header.php');
    require(PBR_PATH.'/includes/display/graphs.php');
    require(PBR_PATH.'/includes/display/footer.php');

    /** Delete objects
     *****************/
    unset($pDate, $pChoice, $pInteval, $pHeader);
    include(PBR_PATH.'/includes/init/clean.php');
?>
