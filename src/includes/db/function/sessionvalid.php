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
 * file encoding: UTF-8                                                  *
 *                                                                       *
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') )
    die('-1');

/**
  * function: SessionValid
  * description: Check if the session is valid.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *            INTEGER|iRole    - credential required for the action
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  * return: BOOLEAN - FALSE if an exception occures.
  *         or
  *         INTEGER - >0 if the session is valid. User DB identifier.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function SessionValid( $sLogin, $sSession, $iRole, $sInet)
{
    /** Initialize
     *************/
    $iReturn = -1;
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.','.$iRole.',[obfuscated])';
    $sMessage = '';
    $iUnixTimestamp = time();
    $iCRC32 = 0;

    /** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && is_integer($iRole) && ($iRole>=0)
     && IsStringNotEmpty($sInet) )
    {
        try
        {
            /** CRC32
             ********/
            // Prepare
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare('SELECT CRC32(:sInet) AS "CRC32"');
            // Bind
            $pPDOStatement->bindValue(':sInet',$sInet,PDO::PARAM_STR);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
            if( !empty($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('CRC32', $tabResult[0]) )
                    $iCRC32 = $tabResult[0]['CRC32'];
            }//if( !empty($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            // Free resource
            $pPDOStatement = NULL;

            /** SessionValid
             ***************/
            $iReturn = -2;
            // Prepare
            $sSQL = 'SELECT IFNULL(u.`iduser`,0) AS "SessionValid" FROM `'.PBR_DB_DBN.'`.`user` AS u INNER JOIN '.PBR_DB_DBN.'.`session` AS s ON u.`login`=s.`login` AND s.`logoff`=0 AND s.`session`=:sSession AND s.`inet`=:iCRC32 WHERE u.`login`=:sLogin AND s.`expire_date`>=:iUnixTimestamp AND u.`role`>=:iRole AND u.`state`=1';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':sLogin',$sLogin,PDO::PARAM_STR);
            $pPDOStatement->bindValue(':sSession',$sSession,PDO::PARAM_STR);
            $pPDOStatement->bindValue(':iRole',$iRole,PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iCRC32',$iCRC32,PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iUnixTimestamp',$iUnixTimestamp,PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
            if( !empty($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('SessionValid', $tabResult[0]) )
                    $iReturn = (integer)$tabResult[0]['SessionValid'];
            }//if( !empty($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if( IsParameterScalarNotEmpty(

    // Error trace only for simple user
    if( $iReturn==-2 )
    {
        if($iRole<10)
        {
            ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, FALSE);
        }//if($iRole<10)
    }
    else
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }//if( $iReturn==-2 )

    return $iReturn;
}
