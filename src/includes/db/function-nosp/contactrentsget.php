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
  * function: ContactRentsGet
  * description: Get rent(s) for a specific contact.
  * parameters: STRING|sLogin      - login identifier
  *             STRING|sSession    - session identifier
  *             STRING|sInet       - concatenation of IP and USER_AGENT
  *            INTEGER|iIdentifier - contact identifier
  *            INTEGER|iOffset     - offset of the first row to return
  *            INTEGER|iLimit      - maximum number of rows to return
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  *         or
  *         ARRAY of none, one or more records (reservation_id, reservation_year,
  *         reservation_month, reservation_day, reservation_real, reservation_planned,
  *         reservation_canceled, reservation_arrhes, reservation_age, reservation_comment)
  * author: Olivier JULLIEN - 2010-02-04
  */
function ContactRentsGet( $sLogin, $sSession, $sInet, $iIdentifier, $iOffset, $iLimit )
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$iIdentifier.','.$iOffset.','.$iLimit.')';

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && is_integer($iIdentifier) && ($iIdentifier>0)
        && is_integer($iOffset) && ($iOffset>=0)
        && is_integer($iLimit) && ($iLimit>0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        if( CUser::GetInstance()->IsAuthenticated() )
        {
            // try
            try
            {
                // Prepare
				$sSQL='SELECT r.`idreservation` AS "reservation_id", r.`year` AS "reservation_year", r.`month` AS "reservation_month", r.`day` AS "reservation_day", r.`rent_real` AS "reservation_real", r.`rent_planned` AS "reservation_planned", r.`rent_canceled` AS "reservation_canceled", r.`age` AS "reservation_age", r.`arrhe` AS "reservation_arrhes", r.`comment` AS "reservation_comment", r.`rent_max` AS "reservation_max" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`idcontact`=:iIdentifier ORDER BY r.`year` DESC, r.`month` DESC, r.`day` DESC LIMIT :iLimit OFFSET :iOffset';
                $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':iIdentifier',$iIdentifier,PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iOffset',$iOffset,PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iLimit',$iLimit,PDO::PARAM_INT);
                // Execute
                $pPDOStatement->execute();
                // Fetch
                $iReturn = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
                // Analyse
                if( !is_array($iReturn) || (isset($iReturn[0]) && !is_array($iReturn[0])) )
                {
                    $iReturn=0;
                }//if( !is_array($iReturn) || (isset($iReturn[0]) && !is_array($iReturn[0]) )
            }
            catch(PDOException $e)
            {
                $iReturn=FALSE;
                $sMessage=$e->getMessage();
                CDb::GetInstance()->LogError( PBR_DB_DBN, $sLogin, $sErrorTitle, $sMessage);
            }//try

            // Free resource
            $pPDOStatement=NULL;
		}
        else
        {
        	$iReturn=-2;
        }//if( CUser::GetInstance()->IsAuthenticated() )
    }//if( IsParameterScalarNotEmpty(

    // Error
    if( is_scalar($iReturn) )
    {
        CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}

?>
