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
 * W3C: This document was successfully checked as XHTML 1.0 Strict!
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

    /** Send content-type header
     ***************************/
    $sContentType = '<meta http-equiv="Content-Type" content="';
    if( $pHeader->AcceptXML()===TRUE )
    {
        header('Content-type: application/xhtml+xml; charset=UTF-8');
        $sContentType .= 'application/xhtml+xml';
    }
    else
    {
        header('Content-type: text/html; charset=UTF-8');
        $sContentType .= 'text/html';
    }//if( $pHeader->AcceptXML()===TRUE )
    $sContentType .= ';charset=utf-8" />';

    // In case of short_open_tag is on
    echo '<?xml version="1.0" encoding="utf-8"?>',"\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title><?php echo $pHeader->GetTitle(); ?></title>
<?php echo $sContentType,"\n"; ?>
<link rel="shortcut icon" href="<?php echo PBR_URL; ?>favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="<?php echo PBR_URL; ?>apple-touch-icon.png" />
<meta http-equiv="expires" content="<?php echo $sExpire_RFC822; ?>" />
<?php
    if( $pHeader->ForPrinting() )
    {
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/prints.css" media="screen" />',"\n";
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/printp.css" media="print" />',"\n";
    }
    elseif( $pHeader->IsMobile() )
    {
        if( CAuth::GetInstance()->GetForceDesktop() )
        {
            echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/style.css" media="all" />',"\n";
        }
        else
        {
            echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/mobile.css" />',"\n";
        }
    }
    else
    {
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/style.css" media="all" />',"\n";
        echo '<link rel="stylesheet" type="text/css" href="'.PBR_URL.'css/mobilize.css" media="only screen and (max-device-width: 480px)" />',"\n";
    }//if( $pHeader->ForPrinting() )
?>
<meta http-equiv="Content-Language" content="fr" />
<meta name="description" content="<?php echo $pHeader->GetDescription(); ?>" />
<meta name="keywords"  content="<?php echo $pHeader->GetKeywords(); ?>" />
<meta name="robots" content="<?php echo $pHeader->GetRobot(); ?>" />
<meta name="copyright" content="Olivier JULLIEN" />
</head>
<body>
