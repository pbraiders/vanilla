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
  * parameters: STRING|sLogin   - login identifier.
  *             STRING|sSession - session identifier.
  *             STRING|sInet    - concatenation of IP and USER_AGENT.
  *           CContact|pContact - instance of CContact. Identifier must be filled.
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
  *         ARRAY of none, one or more records (reservation_id, reservation_year,
  *         reservation_month, reservation_day, reservation_real, reservation_planned,
  *         reservation_canceled, reservation_arrhes, reservation_age, reservation_comment)
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function ContactRentsGet( $sLogin, $sSession, $sInet, CContact $pContact, CPaging $pPaging )
{
    /** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$pContact->GetIdentifier().','.$pPaging->GetOffset().','.$pPaging->GetLimit().')';

    /** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet)
     && ($pContact->GetIdentifier()>0) )
    {
        try
        {
            // Prepare
            $sSQL = 'SELECT r.`idreservation` AS "reservation_id", r.`year` AS "reservation_year", r.`month` AS "reservation_month", r.`day` AS "reservation_day", r.`rent_real` AS "reservation_real", r.`rent_planned` AS "reservation_planned", r.`rent_canceled` AS "reservation_canceled", r.`age` AS "reservation_age", r.`arrhe` AS "reservation_arrhes", r.`comment` AS "reservation_comment", r.`rent_max` AS "reservation_max" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`idcontact`=:iIdentifier ORDER BY r.`year` DESC, r.`month` DESC, r.`day` DESC LIMIT :iLimit OFFSET :iOffset';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iIdentifier',$pContact->GetIdentifier(),PDO::PARAM_INT);
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

    }//if(...

    // Error
    if( is_scalar($iReturn) )
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}
