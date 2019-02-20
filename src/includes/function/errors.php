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
 * description: contain usefull error functions
 * author: Olivier JULLIEN - 2010-05-24
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_PATH')  || !defined('PBR_LOG_LOADED') || !defined('PBR_DB_DBN') )
    die('-1');

/**
  * function: ErrorLog
  * description: Log an error
  * parameters: STRING|sLogin       - logged user
  *             STRING|sTitle       - error title
  *             STRING|sDescription - error description
  *            INTEGER|iType        - error type (E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE)
  *            BOOLEAN|bLogToDB     - if TRUE, insert log into database
  * return: none
  * author: Olivier JULLIEN - 2010-05-24
  */
function ErrorLog( $sLogin, $sTitle, $sDescription, $iType, $bLogToDB)
{
    // Build type
    switch($iType)
    {
        case E_USER_ERROR:
            $sBuffer = 'Une erreur est survenue dans '.$sTitle.': '.$sDescription;
            $sType = 'error';
            break;
        case E_USER_WARNING:
            $$sDescription = 'Attention: '.$sDescription;
            $sBuffer = $sDescription.' ('.$sTitle.')';
            $sType = 'warning';
            break;
        default:
        	$sDescription = 'Information: '.$sDescription;
            $sBuffer = $sDescription.' ('.$sTitle.')';
            $sType = 'notice';
    }//switch($iType)

	// Add to list
    CErrorList::GetInstance()->Add($sBuffer);

    // Add to log
    CLog::GetInstance()->Write( $sLogin, $sType, $sBuffer);

    // Add to database
    if( defined('PBR_DB_LOADED') && ($bLogToDB===TRUE) )
    {
		CDb::GetInstance()->ErrorInsert( PBR_DB_DBN, $sLogin, $sType, $sTitle, $sDescription);
	}//if( define('PBR_DB_LOADED') && ($bLogToDB===TRUE) )
}

/**
  * function: ErrorDBLog
  * description: Build and log a database error
  * parameters: STRING|sLogin       - logged user
  *             STRING|sTitle       - error title
  *             STRING|sDescription - error description
  *            INTEGER|iCode        - error code (FALSE,-1,...)
  *            BOOLEAN|bLogToDB     - if TRUE, insert log into database
  * return: none
  * author: Olivier JULLIEN - 2010-05-24
  */
function ErrorDBLog( $sLogin, $sTitle, $sDescription, $iCode, $bLogToDB)
{
    if( ($iCode===FALSE) || ($iCode<0) )
    {
        // Build message
        if( ($iCode===FALSE) )
        {
            $sBuffer = 'Une exception PDO est survenue dans '.$sTitle.': '.$sDescription;
        }
        elseif( $iCode==-1 )
        {
        	$sDescription = 'mauvais paramètre(s)';
            $sBuffer = 'Une erreur est survenue dans '.$sTitle.': '.$sDescription;
        }
        elseif( ($iCode==-2) || ($iCode==-3) )
        {
			$sDescription = 'authentification erronée';
            $sBuffer = 'Une erreur est survenue dans '.$sTitle.': '.$sDescription;
            $bLogToDB=FALSE;
        }
        elseif( $iCode==-4 )
        {
        	$sDescription = 'données dupliquées';
            $sBuffer = 'Une erreur est survenue dans '.$sTitle.': '.$sDescription;
        }
        else
        {
        	$sDescription = 'erreur inattendue';
            $sBuffer = 'Une erreur inattendue est survenue dans '.$sTitle;
        }//if( ...

        // Add to list
        CErrorList::GetInstance()->Add($sBuffer);

        // Add to log
        CLog::GetInstance()->Write( $sLogin, 'error', $sBuffer);

        // Add to database
        if( defined('PBR_DB_LOADED') && ($bLogToDB===TRUE) )
        {
			CDb::GetInstance()->ErrorInsert( PBR_DB_DBN, $sLogin, 'error', $sTitle, $sDescription);
        }//if( define('PBR_DB_LOADED') && ($bLogToDB===TRUE) )

    }//if( ($iCode===FALSE) || ($iCode<0) )
}

/**
  * function: RedirectError
  * description: Trace error and display logout page
  * parameters: INTEGER|iError - error code
  *              STRING|sFile  - file name
  *             INTEGER|iLine  - line
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorLog instead of TraceWarning
  */
function RedirectError( $iError, $sFile, $iLine)
{
	$sUrl=PBR_URL.'logout.php?error=';
    $iOption=1;
    if( ($iError==-2) || ($iError==-3) )
	{
        $sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
        $sTitle='fichier: '.basename($sFile).', ligne:'.$iLine;
        ErrorLog( $sUser, $sTitle, 'possible tentative de piratage', E_USER_WARNING, FALSE);
		$iOption=2;
	}//if( ($iError==-2) || ($iError==-3) )
	include(PBR_PATH.'/includes/init/initclean.php');
	header('Location: '.$sUrl.$iOption);
}

?>
