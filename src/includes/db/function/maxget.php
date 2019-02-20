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
  * function: MaxGet
  * description: Get rent max per month.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  *         or
  *         ARRAY of none, one or more records (month, max)
  * author: Olivier JULLIEN - 2010-02-04
  */
function MaxGet( $sLogin, $sSession, $sInet)
{
    $iReturn=-1;
    $sMessage='';
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        // try
        try
        {
            // Prepare
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare('CALL sp_MaxGet(:sLogin,:sSession,:sInet)');
            // Bind
            $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
            $pPDOStatement->bindParam(':sSession',$sSession,PDO::PARAM_STR,200);
            $pPDOStatement->bindParam(':sInet',$sInet,PDO::PARAM_STR,255);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('ErrorCode', $tabResult[0])===TRUE )
                {
                    $tabResult=$tabResult[0]['ErrorCode'];
                }//if( array_key_exists('ErrorCode', $tabResult[0])===TRUE )
            }//if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
        }
        catch(PDOException $e)
        {
            $iReturn=FALSE;
            $sMessage=$e->getMessage();
        }//try

        // Free resource
        $pPDOStatement=NULL;

    }//if( IsParameterScalarNotEmpty(

    // Error
    if( is_scalar($tabResult) )
    {
        $iReturn=$tabResult;
        CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);
    }
    elseif( is_array($tabResult) )
    {
        $iReturn=array();
        foreach( $tabResult as $tRecord )
        {
            $iReturn[$tRecord['month']]=$tRecord['max'];
        }//foreach( $tRecordset as $tRecord )
    }//if( is_scalar($iReturn) )

    return $iReturn;
}

?>
