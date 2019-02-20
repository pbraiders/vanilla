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
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_PATH') )
    die('-1');

/**
  * function: TruncMe
  * description: Trunc a string
  * parameters: STRING|sValue   - string to trunc
  *            INTEGER|iLenght  - max length of the string
  * return: STRING - truncated string
  * author: Olivier JULLIEN - 2010-02-04
  */
function TruncMe($sValue,$iSize)
{
    $sReturn=$sValue;
    if( is_scalar($sValue) && is_integer($iSize) )
    {
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
            $sReturn.='...';
        }//if( strlen($sValue)>$iSize )
    }//if( is_scalar($sValue) && is_integer($iSize) )
    return $sReturn;
}

/**
  * function: IsParameterScalar
  * description: Tests if the parameter is scalar
  *              (string, boolean, numeric and float)
  * parameters: mixed|var - parameter to test
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-02-04
  */
function IsParameterScalar($var)
{
    return is_scalar($var);
}

/**
  * function: IsParameterScalarNotEmpty
  * description: Tests if the parameter is scalar and not empty
  *              (string, boolean, numeric and float)
  * parameters: mixed|var - parameter to test
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-02-04
  */
function IsParameterScalarNotEmpty($var)
{
    $bReturn=is_scalar($var);
    if( $bReturn && is_string($var) && strlen(trim($var))<=0 )
    {
        $bReturn=FALSE;
    }//if...
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
  */
function GetUserAgent( $iFormat=1 )
{
    // Default
    $sReturn = 'none';
    // Format
    if( isset($_SERVER['HTTP_USER_AGENT']) )
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
    }//if( isset($_SERVER['HTTP_USER_AGENT'] )
    return $sReturn;
}

/**
  * function: GetIP
  * description: return the ip
  * parameters: INTEGER|iFormat - 1 = normal
  *                               2 = sanitized
  * return: STRING - ip address in asked format
  * author: Olivier JULLIEN - 2010-02-04
  */
function GetIP($iFormat=1)
{
    $sReturn='0.0.0.0';
    if( isset($_SERVER['REMOTE_ADDR']) )
    {
        $sReturn=$_SERVER['REMOTE_ADDR'];
        if( $iFormat==2 )
        {
            $sReturn=htmlspecialchars($sReturn);
        }//if( $iFormat==2 )
    }//if( isset($_SERVER['REMOTE_ADDR']) )
    return $sReturn;
}

/**
  * function: TraceWarning
  * description: Display an user warning
  * parameters: STRING|sValue - error description
  *             STRING|sFile  - file name
  *            INTEGER|iLine  - line
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function TraceWarning($sValue, $sFile, $iLine)
{
    CErrorList::GetInstance()->Add($sValue, $sFile, $iLine);
}

/**
  * function: WriteTrace
  * description: Display the errorl list
  * parameters: none
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function WriteTrace()
{
    if( defined('PBR_DEBUG') )
    {
        echo '<div>',"\n";
        if( CErrorList::GetInstance()->GetCount()>0 )
        {
            echo '<p>Error list</p>',"\n";
            foreach( CErrorList::GetInstance() as $key=>$value )
            {
                trigger_error(htmlspecialchars($value),E_USER_WARNING);
            }//foreach( CErrorList::GetInstance() as $key=>$value )
        }
        else
        {
            echo '<p>No error.</p>',"\n";
        }//if( CErrorList::GetInstance()->GetCount()>0)
        echo '</div>',"\n";
    }//if( define('PBR_DEBUG') )
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
		$tFilter = array('options' => array('min_range' => 1, 'max_range' => 3));
		$iReturn=(integer)filter_input( INPUT_GET, 'error', FILTER_VALIDATE_INT, $tFilter);
	}//if( filter_has_var(INPUT_GET, 'error') )
    return $iReturn;
}

/**
  * function: RedirectError
  * description: Trace error and display logout page
  * parameters: INTEGER|iError - error code
  *              STRING|sFile  - file name
  *             INTEGER|iLine  - line
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function RedirectError( $iError, $sFile, $iLine)
{
	$sUrl=PBR_URL.'logout.php?error=';
    $iOption=1;
    if( ($iError==-2) || ($iError==-3) )
	{
		TraceWarning('Possible hacking attempt', $sFile, $iLine);
		$iOption=2;
	}//if( ($iError==-2) || ($iError==-3) )
	include(PBR_PATH.'/includes/init/initclean.php');
	header('Location: '.$sUrl.$iOption);
}

?>
