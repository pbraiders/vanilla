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
  * function: RentsGet
  * description: Get rent(s) for a specific date.
  *              the first line (reservation_id=0) is the sum of real
  *              planned and canceled rents + max of rents allowed
  *              ( reservation_id=0
  *                reservation_real=<sum real>
  *                reservation_planned=<sum planned>
  *                reservation_planned=<sum canceled>
  *                reservation_arrhes=<max of rent allowed> )
  * Parameters: STRING|sLogin   - login identifier.
  *             STRING|sSession - session identifier.
  *             STRING|sInet    - concatenation of IP and USER_AGENT.
  *              CDate|pDate    - instance of CDate. Requested date.
  *            CPaging|pPaging  - instance of CPaging.
  *                                + offset of the first row to return.
  *                                + maximum number of rows to return.
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
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDb::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function RentsGet( $sLogin, $sSession, $sInet, CDate $pDate, CPaging $pPaging)
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$pDate->GetRequestDay().','.$pDate->GetRequestMonth().','.$pDate->GetRequestYear().','.$pPaging->GetOffset().','.$pPaging->GetLimit().')';

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet) )
    {
        try
        {
            // Prepare
			$sSQL = 'SELECT 0 AS "reservation_id", IFNULL(SUM(r.`rent_real`),0) AS "reservation_real", IFNULL(SUM(r.`rent_planned`),0) AS "reservation_planned", IFNULL(SUM(r.`rent_canceled`),0) AS "reservation_canceled", IFNULL(MAX(r.`rent_max`),0) AS "reservation_arrhes", NULL AS "contact_lastname", NULL AS "contact_firstname", NULL AS "contact_phone", NULL AS "reservation_comment" FROM `'.PBR_DB_DBN.'`.`reservation` AS r INNER JOIN `'.PBR_DB_DBN.'`.`contact` AS c ON r.`idcontact`=c.`idcontact` WHERE r.`year`=:iYear AND r.`month`=:iMonth AND r.`day`=:iDay UNION (SELECT r.`idreservation` AS "reservation_id", r.`rent_real` AS "reservation_real", r.`rent_planned` AS "reservation_planned", r.`rent_canceled` AS "reservation_canceled", r.`arrhe` AS "reservation_arrhes", c.`lastname` AS "contact_lastname", c.`firstname` AS "contact_firstname", c.`tel` AS "contact_phone", r.`comment` AS "reservation_comment" FROM `'.PBR_DB_DBN.'`.`reservation` AS r INNER JOIN `'.PBR_DB_DBN.'`.`contact` AS c ON r.`idcontact`=c.`idcontact` WHERE r.`year`=:iYear AND r.`month`=:iMonth AND r.`day`=:iDay LIMIT :iLimit OFFSET :iOffset)';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iDay',$pDate->GetRequestDay(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iMonth',$pDate->GetRequestMonth(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iYear',$pDate->GetRequestYear(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iOffset',$pPaging->GetOffset(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iLimit',$pPaging->GetLimit(),PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $iReturn = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if...

    // Error
    if( is_scalar($iReturn) )
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}

?>
