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
 * description: describes current action
 * author: Olivier JULLIEN - 2010-0--15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CAction
{

    /** Constant
     ***********/
    const ACTIONTAG = 'act';
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
     * description: Read input action
     * parameters: INTEGER|iFilter - filter: INPUT_GET or INPUT_POST
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
    */
    public static function Read($iFilter)
    {
        $sReturn = '';
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            if( filter_has_var( $iFilter, CAction::ACTIONTAG) )
            {
                $sReturn = CAction::Sanitize( filter_input( $iFilter, CAction::ACTIONTAG, FILTER_SANITIZE_SPECIAL_CHARS), GetRegExPatternName() );
            }//if( filter_has_var( $iFilter, CAction::ACTIONTAG) )
        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        return $sReturn;
    }

    /**
     * function: IsValid
     * description: Read input action and compare with expected
     * parameters: INTEGER|iFilter - INPUT_GET or INPUT_POST
     *              STRING|sIn     - expected action
     * return: UNKNOWN, NOTVALID or VALID
     * author: Olivier JULLIEN - 2010-06-15
    */
    public static function IsValid( $iFilter, $sIn)
    {
        $iReturn = CAction::UNKNOWN;
        $sAction = CAction::Read( $iFilter );
        if( (strlen($sAction)>0) && is_string($sIn) && !empty($sIn) )
        {
            if( $sIn===$sAction )
            {
                $iReturn = CAction::VALID;
            }
            else
            {
                $iReturn = CAction::NOTVALID;
            }//if( $sIn===$sAction )
        }//if( (strlen($sAction)>0) && istring($sIn) && !empty($sIn) )
        return $iReturn;
    }

}

?>
