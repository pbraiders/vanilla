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
 * description: Display the contacts export page.
 *              The following object(s) should exist:
 *                  - $tRecordset
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') )
    die('-1');

    /** Send no-cache headers
     ************************/
    header('Expires: Wed, 21 Jan 1984 05:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', FALSE);
    header('Pragma: no-cache');     // For HTTP/1.0 compability
    header('Content-type: text/html; charset=UTF-8');

    /** build lines
     **************/
    echo 'nom;prénom;téléphone;email;adresse;ville;code postal;commentaire;date de création',"\n";
    if( is_array($tRecordset) )
    {
        foreach( $tRecordset as $tRecord )
        {
            $sBuffer=$tRecord['contact_lastname'].';';
            $sBuffer.=$tRecord['contact_firstname'].';';
            $sBuffer.=$tRecord['contact_tel'].';';
            $sBuffer.=$tRecord['contact_email'].';';
            $sBuffer.=$tRecord['contact_address'].' ';
            $sBuffer.=$tRecord['contact_addressmore'].';';
            $sBuffer.=$tRecord['contact_addresscity'].';';
            $sBuffer.=$tRecord['contact_addresszip'].';';
            $sBuffer.=$tRecord['contact_comment'].';';
            $sBuffer.=$tRecord['creation_date'];
            echo $sBuffer,"\n";
        }//foreach( $tRecordset as $tRecord )
    }//if( is_array($tRecordset) )
?>