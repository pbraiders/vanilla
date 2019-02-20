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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_DATE_LOADED') )
    die('-1');

/**
  * function: RentsGet
  * description: Get rent(s) for a specific date.
  *              the first line (reservation_id=0) is the sum of real
  *              planned and canceled rents + max of rents allowed
  *              ( reservation_id=0
  *                reservation_real=<sum real>
  *                reservation_planned=<sum planned>
  *                reservation_planned=<sum canceled>
  *                reservation_arrhes=<max of rent allowed> )
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *              CDate|pDate    - requested date
  *            INTEGER|iOffset  - offset of the first row to return
  *            INTEGER|iLimit   - maximum number of rows to return
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  *         or
  *         ARRAY of none, one or more records (reservation_id,reservation_real
  *               reservation_planned,reservation_canceled,reservation_arrhes,
  *               contact_lastname,contact_firstname,contact_phone)
  * author: Olivier JULLIEN - 2010-02-04
  */
function RentsGet( $sLogin, $sSession, $sInet, &$pDate, $iOffset, $iLimit)
{
    $iReturn=-1;
    $sMessage='';
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && !is_null($pDate)
        && is_integer($iOffset) && ($iOffset>=0)
        && is_integer($iLimit) && ($iLimit>=0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        // try
        try
        {
            // Prepare
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare('CALL sp_RentsGet(:sLogin,:sSession,:sInet,:sDay,:sMonth,:sYear,:iOffset,:iLimit)');
            // Bind
            $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
            $pPDOStatement->bindParam(':sSession',$sSession,PDO::PARAM_STR,200);
            $pPDOStatement->bindParam(':sInet',$sInet,PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':sDay',$pDate->GetRequestDay(),PDO::PARAM_STR,2);
            $pPDOStatement->bindParam(':sMonth',$pDate->GetRequestMonth(),PDO::PARAM_STR,2);
            $pPDOStatement->bindParam(':sYear',$pDate->GetRequestYear(),PDO::PARAM_STR,4);
            $pPDOStatement->bindParam(':iOffset',$iOffset,PDO::PARAM_INT);
            $pPDOStatement->bindParam(':iLimit',$iLimit,PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $iReturn = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
            // Analyse
            if( is_array($iReturn) && isset($iReturn[0]) && is_array($iReturn[0]) )
            {
                if( array_key_exists('ErrorCode', $iReturn[0])===TRUE )
                {
                    $iReturn=$iReturn[0]['ErrorCode'];
                }//if( array_key_exists('ErrorCode', $tabResult[0])===TRUE )
            }//if( is_array($iReturn) && isset($iReturn[0]) && is_array($iReturn[0]) )
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
    if( is_scalar($iReturn) )
    {
        CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}

?>
