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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_AUTH_LOADED') )
    die('-1');

require(PBR_PATH.'/includes/db/function/rentmax.php');

/**
  * function: RentAdd
  * description: Create a rent.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *           CContact|pContact - instance of CContact. Identifier must be filled.
  *              CDate|pDate    - instance of CDate.
  *              CRent|pRent    - instance of CRent with valid datas.
  *                               OUT: contains the new identifier.
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. New rent identifier.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function RentAdd( $sLogin, $sSession, $sInet, CContact $pContact, CDate $pDate, CRent $pRent)
{
    /** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$pContact->GetIdentifier();
    $sErrorTitle .= ','.$pDate->GetRequestDay().','.$pDate->GetRequestMonth().','.$pDate->GetRequestYear();
    $sErrorTitle .= ','.$pRent->GetCountReal().','.$pRent->GetCountPlanned().','.$pRent->GetCountCanceled();
    $sErrorTitle .= ','.$pRent->GetAge().','.$pRent->GetArrhes();
    $sErrorTitle .= ',...)';
    $pRent->SetIdentifier(0);

    /** Get max rent for the requested month
     ***************************************/
    $iMaxRent = RentMax( $sLogin, $sSession, $sInet, $pDate);

    /** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet)
     && ($pContact->GetIdentifier()>0)
     && ($iMaxRent>=0) )
    {
        try
        {
            // Prepare
            $sSQL = 'INSERT INTO `'.PBR_DB_DBN.'`.`reservation` (`idcontact`, `year`, `month`, `day`, `rent_real`, `rent_planned`, `rent_canceled`, `rent_max`, `age`, `arrhe`, `create_date`, `create_iduser`, `update_date`, `update_iduser`) VALUES (:iIdentifier, :iYear, :iMonth, :iDay, :iReal, :iPlanned, :iCanceled, :iMaxRent, :iAge, :iArrhes, SYSDATE(), :iUserId, NULL, NULL)';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iIdentifier',$pContact->GetIdentifier(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iYear',$pDate->GetRequestYear(),PDO::PARAM_INT);;
            $pPDOStatement->bindValue(':iMonth',$pDate->GetRequestMonth(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iDay',$pDate->GetRequestDay(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iReal',$pRent->GetCountReal(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iPlanned',$pRent->GetCountPlanned(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iCanceled',$pRent->GetCountCanceled(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iMaxRent',$iMaxRent,PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iAge',$pRent->GetAge(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iArrhes',$pRent->GetArrhes(),PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iUserId',CAuth::GetInstance()->GetUserBDIdentifier(),PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Last insert id
            $iReturn = CDBLayer::GetInstance()->GetLastInsertId();
            $pRent->SetIdentifier($iReturn);

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
    ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);

    return $iReturn;
}
