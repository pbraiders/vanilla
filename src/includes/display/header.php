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
 * description: Display the page header.
 *              The following object(s) should exist:
 *                  - CHeader
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-21 - Case: short_open_tag is on
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !isset($pHeader) )
    die('-1');

    /** Time calculation
     *******************/
    $sCurrent_GMT = gmdate('D, d M Y H:i:s').' GMT';
    $iTimestamp = time()-3600;
    $sExpire_RFC822 = date( DATE_RFC822, $iTimestamp);
    $sExpire_GMT = gmdate( 'D, d M Y H:i:s', $iTimestamp).' GMT';

    /** Send no-cache headers
     ************************/
    header('Last-Modified: '.$sCurrent_GMT);
    header('Expires: '.$sExpire_GMT);
    if( $pHeader->IsNoCache())
    {
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
    }//if( CHeader::GetInstance()->IsNoCache())
    header('Cache-Control: post-check=0, pre-check=0', FALSE);
    header('Pragma: no-cache');     // For HTTP/1.0 compability

    /** Build header
     ***************/
    $sContentType = '<meta http-equiv="Content-Type" content="';
    if( $pHeader->AcceptXML()===TRUE )
    {

        /** XHTML 1.0 strict
         *******************/

        // Send content-type header
        header('Content-type: application/xhtml+xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="utf-8"?>',"\n";
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',"\n";
        echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">',"\n";
        $sContentType .= 'application/xhtml+xml;charset=utf-8" />';
    }
    else
    {

        /** HTML 4.01 strict
         *******************/

        // Send content-type header
        header('Content-type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',"\n";
        echo '<html lang="fr">',"\n";
        $sContentType .= 'text/html;charset=utf-8">';
    }//if( $pHeader->AcceptXML()===TRUE )

    /** COMMON
     *********/
    echo '<head>',"\n";
    echo '<title>'.$pHeader->GetTitle().'</title>',"\n";
    echo $sContentType,"\n";
    echo '<link rel="shortcut icon" href="'.PBR_URL.'favicon.ico" type="image/x-icon"'.$pHeader->GetCloseTag(),"\n";
    echo '<link rel="apple-touch-icon" href="'.PBR_URL.'apple-touch-icon.png"'.$pHeader->GetCloseTag(),"\n";
    echo '<meta http-equiv="expires" content="'.$sExpire_RFC822.'"'.$pHeader->GetCloseTag(),"\n";
    if( $pHeader->ForPrinting() )
    {
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/prints.css" media="screen"'.$pHeader->GetCloseTag(),"\n";
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/printp.css" media="print"'.$pHeader->GetCloseTag(),"\n";
    }
    elseif( $pHeader->IsMobile() )
    {
        if( CAuth::GetInstance()->GetForceDesktop() )
        {
            echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/style.css" media="all"'.$pHeader->GetCloseTag(),"\n";
        }
        else
        {
            echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/mobile.css"'.$pHeader->GetCloseTag(),"\n";
        }
    }
    else
    {
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/style.css" media="all"'.$pHeader->GetCloseTag(),"\n";
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/mobilize.css" media="only screen and (max-device-width: 480px)"'.$pHeader->GetCloseTag(),"\n";
    }//if( $pHeader->ForPrinting() )
    echo '<meta http-equiv="Content-Language" content="fr"'.$pHeader->GetCloseTag(),"\n";
    echo '<meta name="description" content="'.$pHeader->GetDescription().'"'.$pHeader->GetCloseTag(),"\n";
    echo '<meta name="keywords"  content="'.$pHeader->GetKeywords().'"'.$pHeader->GetCloseTag(),"\n";
    echo '<meta name="robots" content="'.$pHeader->GetRobot().'"'.$pHeader->GetCloseTag(),"\n";
    echo '<meta name="copyright" content="Olivier JULLIEN"'.$pHeader->GetCloseTag(),"\n";
?>
</head>
<body>
