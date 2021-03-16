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
 * description: describes current source
 * author: Olivier JULLIEN - 2010-0--15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CSource
{

    /** Constant
     ***********/
    const SOURCETAG = 'src';
    const UNKNOWN = -1;
    const NOTVALID = FALSE;
    const VALID = TRUE;

    /**
     * function: Sanitize
     * description: return sanitized value
     * parameter: STRING|sValue  - value to sanitize
     *            STRING|sFilter - regex filter
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
     */
    public static function Sanitize($sValue, $sFilter='')
    {
        $sReturn = '';
        if( is_scalar($sValue) && is_scalar($sFilter) )
        {
            // Trim
            $sReturn = trim($sValue);
            // Authorized caracteres
            if( !empty($sFilter) )
            {
                if( 0==preg_match( $sFilter, $sReturn) )
                {
                    $sReturn = '';
                }//if( 0==preg_match($sFilter, $sReturn) )
            }//if( !empty($sFilter) )
        }//if(...
        return $sReturn;
    }

    /**
     * function: Read
     * description: Read input source
     * parameters: INTEGER|iFilter - filter: INPUT_GET or INPUT_POST
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
    */
    public static function ReadInput($iFilter)
    {
        $sReturn = '';
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            if( filter_has_var( $iFilter, CSource::SOURCETAG) )
            {
                $sReturn = CSource::Sanitize( filter_input( $iFilter, CSource::SOURCETAG, FILTER_SANITIZE_SPECIAL_CHARS), GetRegExPatternName() );
            }//if( filter_has_var( $iFilter, CSource::ACTIONTAG) )
        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        return $sReturn;
    }

    /**
     * function: IsValidInput
     * description: Read input action and compare with expected
     * parameters: INTEGER|iFilter - INPUT_GET or INPUT_POST
     *              STRING|sIn     - expected action
     * return: UNKNOWN, NOTVALID or VALID
     * author: Olivier JULLIEN - 2010-06-15
    */
    public static function IsValidInput( $iFilter, $sIn)
    {
        $iReturn = CSource::UNKNOWN;
        $sSource = CSource::ReadInput( $iFilter );
        if( (strlen($sSource)>0) && is_string($sIn) && !empty($sIn) )
        {
            if( $sIn===$sSource )
            {
                $iReturn = CSource::VALID;
            }
            else
            {
                $iReturn = CSource::NOTVALID;
            }//if( $sIn===$sSource )
        }//if( (strlen($sSource)>0) && istring($sIn) && !empty($sIn) )
        return $iReturn;
    }

    /**
     * function: ReadServer
     * description: Read SERVER HTTP_REFERER
     * parameters: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
    */
    public static function ReadServer()
    {
        $sReturn = '';
        if( isset($_SERVER) && isset($_SERVER['HTTP_REFERER']) )
        {
            $sReturn = CSource::Sanitize( $_SERVER['HTTP_REFERER'] );
        }//if( isset($_SERVER) && isset($_SERVER['HTTP_REFERER']) )
        return $sReturn;
    }

    /**
     * function: IsValidServer
     * description: Read SERVER HTTP_REFERER and compare with expected script
     * parameters: STRING|sExpectedReferer - expected referer
     *             STRING|sScript  - expected script
     * return: UNKNOWN, NOTVALID or VALID
     * author: Olivier JULLIEN - 2010-06-15
    */
    public static function IsValidServer( $sExpectedReferer, $sScript )
    {
        // Initialize
        $iReturn = CSource::UNKNOWN;
        // Get referer
        $sReferer = str_replace('www.', '', CSource::ReadServer() );
        // Analyse
        if( !empty($sReferer) && is_string($sExpectedReferer) && !empty($sExpectedReferer) )
        {
            // Format expected referer
            $sExpectedReferer = str_replace('www.', '', $sExpectedReferer);
            // Add script
            if( is_string($sScript) && !empty($sScript) )
            {
                $sExpectedReferer .= $sScript;
            }//if( is_string($sScript) && !empty($sScript) )
            // Compare
            $iExpectedRefererLength = strlen($sExpectedReferer);
            $iRefererLength = strlen($sReferer);
            if( ($iRefererLength>=$iExpectedRefererLength)
             && (substr_compare( $sReferer, $sExpectedReferer, 0, $iExpectedRefererLength, TRUE)===0) )
            {
                $iReturn = CSource::VALID;
            }
            else
            {
                $iReturn = CSource::NOTVALID;
            }//if( substr_compare( $sReferer, $sExpectedReferer, 0, $iLength, TRUE)===0 )
        }//if( !empty($sReferer) && is_string($sExpectedReferer) && !empty($sExpectedReferer) )
        return $iReturn;
    }

}
