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
 * description: contain usefull functions
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - remove WriteTrace()
 *                                        remove TraceWarning()
 * update: Olivier JULLIEN - 2010-06-11 - add stripslashes_deep()
 *                                        add GetRegExPatternName()
 * update: Olivier JULLIEN - 2010-06-15 - add GetRegExPatternSession()
 *                                        add IsParameterStringNotEmpty()
 *                                        update IsParameterScalarNotEmpty()
 *                                        remove IsParameterScalar()
 *                                        add GetRegExPatternDirectory()
 *                                        add GetHttpAccept()
 *                                        add GetTime()
 *                                        add GetMemoryUsage()
 *                                        add DisplayUsage()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/**
  * function: TruncMe
  * description: Trunc a string
  * parameters: STRING|sValue   - string to trunc
  *            INTEGER|iLenght  - max length of the string
  * return: STRING - truncated string
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-06-15 - fixed minor bug
  */
function TruncMe($sValue,$iSize)
{
    $sReturn='';
    if( is_scalar($sValue) && is_integer($iSize) )
    {
        $sReturn=$sValue;
        if( mb_strlen($sValue,'UTF-8')>$iSize )
        {
            // Trunc
            $sReturn=mb_substr($sValue,0,$iSize,'UTF-8');
            // Look for last word
            $iIndex=strrpos($sReturn,' ');
            // Remove last word
            if( $iIndex>0 )
            {
                $sReturn=mb_substr($sReturn,0,$iIndex,'UTF-8');
            }//if( $iIndex>0 )
            // Add ...
        }//if( strlen($sValue)>$iSize )
    }//if( is_scalar($sValue) && is_integer($iSize) )
    return $sReturn;
}

/**
  * function: IsScalarNotEmpty
  * description: Tests if the parameter is scalar and not empty
  *              (string, boolean, numeric and float)
  * parameters: mixed|var - parameter to test
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function IsScalarNotEmpty($var)
{
    $bReturn = is_scalar($var);
    if( $bReturn && is_string($var) )
    {
        $var = trim($var);
        if( strlen($var)<=0 )
        {
            $bReturn = FALSE;
        }//if( strlen($var)<=0 )
    }//if( $bReturn && is_string($var) )
    return $bReturn;
}

/**
  * function: GetUserAgent
  * description: return user agent
  * parameters: INTEGER|iFormat - 1 = normal
  *                               2 = binary md5 checksum
  *                               3 = md5 checksum
  *                               4 = sanitized
  * return: STRING - user agent in asked format
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-06-11 - test input parameter
  */
function GetUserAgent( $iFormat=1 )
{
    // Default
    $sReturn = 'none';
    // Format
    if( is_int($iFormat) && isset($_SERVER['HTTP_USER_AGENT']) )
    {
        // Get user agent
        $sReturn = $_SERVER['HTTP_USER_AGENT'];
        switch( $iFormat )
        {
        case 2: // Binary md5 checksum
            $sReturn = md5($sReturn,TRUE);
            break;
        case 3: // md5 checksum
            $sReturn = md5($sReturn);
            break;
        case 4: // sanitized
            $sReturn = htmlspecialchars($sReturn);
            break;
        default:
            break;
        }// switch( $iFormat )
    }//if( is_int($iFormat) && isset($_SERVER['HTTP_USER_AGENT']) )
    return $sReturn;
}

/**
  * function: GetIP
  * description: return the ip
  * parameters: INTEGER|iFormat - 1 = normal
  *                               2 = sanitized
  * return: STRING - ip address in asked format
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-06-11 - test input parameter
  */
function GetIP($iFormat=1)
{
    $sReturn='0.0.0.0';
    if( is_int($iFormat) && isset($_SERVER['REMOTE_ADDR']) )
    {
        $sReturn=$_SERVER['REMOTE_ADDR'];
        if( $iFormat==2 )
        {
            $sReturn=htmlspecialchars($sReturn);
        }//if( $iFormat==2 )
    }//if( is_int($iFormat) && isset($_SERVER['REMOTE_ADDR']) )
    return $sReturn;
}

/**
  * function: GetMessageCode
  * description: Read input message code parameter
  * parameters: none
  * return: INTEGER - message code
  * author: Olivier JULLIEN - 2010-02-04
  */
function GetMessageCode()
{
    $iReturn = 0;
    if( filter_has_var(INPUT_GET, 'error') )
    {
        $tFilter = array('options' => array('min_range' => 1, 'max_range' => 10));
        $iReturn=(integer)filter_input( INPUT_GET, 'error', FILTER_VALIDATE_INT, $tFilter);
    }//if( filter_has_var(INPUT_GET, 'error') )
    return $iReturn;
}

/**
  * function: stripslashes_deep
  * description: Navigates through an array and removes slashes from the values.
  *              If an array is passed, the array_map() function causes a callback to pass the
  *              value back to the function. The slashes from this value will removed.
  * parameters: ARRAY|STRING $value - The array or string to be striped.
  * return: ARRAY|STRING - Stripped array (or string in the callback).
  * author: WORDPRESS
  */
function stripslashes_deep($value)
{
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
}

/**
  * function: GetRegExPatternName
  * description: Return regex filter for input name
  * parameters: none
  * return: STRING
  * author: Olivier JULLIEN - 2010-06-11
  */
function GetRegExPatternName()
{
    return '/^[^\s'.preg_quote('!"#$%&()*+,/:;<=>?[\]^`{|}~','/').']+$/';
}

/**
  * function: GetRegExPatternSession
  * description: Return regex filter for input session
  * parameters: none
  * return: STRING
  * author: Olivier JULLIEN - 2010-06-15
  */
function GetRegExPatternSession()
{
    return '/^[[:alnum:]]+$/';
}

/**
  * function: IsStringNotEmpty
  * description: Tests if the parameter is a scalar and not empty. Return the trimmed string.
  * parameters:  mixed|var     - parameter to test
  *             STRING|sFilter - regex filter
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-06-15
  */
function IsStringNotEmpty( &$var, $sFilter=null)
{
    $bReturn = FALSE;
    if( is_scalar($var) )
    {
        $var = trim($var);
        if( strlen($var)>0 )
        {
            if( !is_null($sFilter) )
            {
                if( (preg_match( $sFilter, $var)>0) )
                {
                    $bReturn=TRUE;
                }//if( (preg_match( $sFilter, $var)>0) )
            }
            else
            {
                $bReturn=TRUE;
            }//if( !is_null($sFilter) )
        }//if( strlen($var)>0 )
    }//if( is_scalar($var) )
    return $bReturn;
}

/**
  * function: GetRegExPatternDirectory
  * description: Return regex filter for directory name
  * parameters: none
  * return: STRING
  * author: Olivier JULLIEN - 2010-06-15
  */
function GetRegExPatternDirectory()
{
    return '/^[^\s'.preg_quote('!"#$%&()*+,:;<=>?[]^`{|}~','/').']+$/';
}

/**
  * function: GetHttpAccept
  * description: return the browser MIME types
  * parameters: INTEGER|iFilter - 0 = normal
  *                               1 = sanitized
  * return: STRING - browser MIME types
  * author: Olivier JULLIEN - 2010-06-15
  */
function GetHttpAccept( $iFilter=0 )
{
    // Default
    $sReturn = 'none';
    // Format
    if( is_int($iFilter) && isset($_SERVER['HTTP_ACCEPT']) )
    {
        // Get value
        $sReturn = $_SERVER['HTTP_ACCEPT'];
        if( $iFilter===4 )
        {
            $sReturn = htmlspecialchars($sReturn);
        }//if( $iFilter===4 )
    }//if( is_int($iFilter) && isset($_SERVER['HTTP_ACCEPT']) )
    return $sReturn;
}

/**
  * function: GetTime
  * description: return the current time in seconds and microseconds
  * parameters: none
  * return: FLOAT: time in second and microseconds
  * author: Olivier JULLIEN - 2010-06-15
  */
function GetTime()
{
    list($tps_usec, $tps_sec) = explode(" ",microtime());
    return (float)$tps_usec + (float)$tps_sec;
}

/**
  * function: GetMemoryUsage()
  * description: return the memory usage
  * parameters: none
  * return: STRING - memory usage
  * author: Olivier JULLIEN - 2010-06-15
  */
function GetMemoryUsage()
{
    $iBytes = memory_get_usage();
    $iMByte = round( ($iBytes/1024/1024), 2);
    $sBuffer = $iMByte.' MByte(s)';
    $sBuffer = $sBuffer.' - '.$iBytes.' Byte(s)';
    return $sBuffer;
}

/**
  * function: Display usage
  * description: return the script execution time and the memory usage
  * parameters: FLOAT:fBegin begining time ( GetTime() )
  * return: STRING
  * author: Olivier JULLIEN - 2010-06-15
  */
function DisplayUsage( $fBegin )
{
    $sReturn = 'Script';
    if( isset($fBegin) && !empty($fBegin) )
    {
        $fEnd = GetTime();
        $fDiff = $fEnd - $fBegin;
        $sReturn .= ' duration: '.$fDiff.', ';
    }
    $sReturn .= 'memory usage: '.GetMemoryUsage();
    return $sReturn;
}
