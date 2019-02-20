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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_RENT_LOADED') || !defined('PBR_DATE_LOADED') || !defined('PBR_CONTACT_LOADED') )
    die('-1');

/**
  * function: RentContactAdd
  * description: Create a contact and a rent.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *           CContact|pContact - contact
  *              CDate|pDate    - date
  *              CRent|pRent    - rent
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. Number of row inserted.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  */
function RentContactAdd( $sLogin, $sSession, $sInet, &$pContact, &$pDate, &$pRent)
{
    $iReturn=-1;
    $sMessage='';
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && !is_null($pContact)
        && !is_null($pDate)
        && !is_null($pRent)
        && $pContact->MandatoriesAreFilled()
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        //try
        try
        {
            // Prepare
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare('CALL sp_RentContactSet(:sLogin,:sSession,:sInet,:sLastName,:sFirstName,:sTel,:sEmail,:sAddress,:sAddressMore,:sCity,:sZip,:iReal,:iPlanned,:iCanceled,:iAge,:iArrhes,:sDay,:sMonth,:sYear)');
            // Bind
            $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
            $pPDOStatement->bindParam(':sSession',$sSession,PDO::PARAM_STR,200);
            $pPDOStatement->bindParam(':sInet',$sInet,PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':sLastName',$pContact->GetLastName(),PDO::PARAM_STR,40);
            $pPDOStatement->bindParam(':sFirstName',$pContact->GetFirstName(),PDO::PARAM_STR,40);
            $pPDOStatement->bindParam(':sTel',$pContact->GetTel(),PDO::PARAM_STR,40);
            $pPDOStatement->bindParam(':sEmail',$pContact->GetEmail(),PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':sAddress',$pContact->GetAddress(),PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':sAddressMore',$pContact->GetAddressMore(),PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':sCity',$pContact->GetCity(),PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':sZip',$pContact->GetZip(),PDO::PARAM_STR,8);
            $pPDOStatement->bindParam(':iReal',$pRent->GetCountReal(),PDO::PARAM_INT);
            $pPDOStatement->bindParam(':iPlanned',$pRent->GetCountPlanned(),PDO::PARAM_INT);
            $pPDOStatement->bindParam(':iCanceled',$pRent->GetCountCanceled(),PDO::PARAM_INT);
            $pPDOStatement->bindParam(':iAge',$pRent->GetAge(),PDO::PARAM_INT);
            $pPDOStatement->bindParam(':iArrhes',$pRent->GetArrhes(),PDO::PARAM_INT);
            $pPDOStatement->bindParam(':sDay',$pDate->GetRequestDay(),PDO::PARAM_STR,2);
            $pPDOStatement->bindParam(':sMonth',$pDate->GetRequestMonth(),PDO::PARAM_STR,2);
            $pPDOStatement->bindParam(':sYear',$pDate->GetRequestYear(),PDO::PARAM_STR,4);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('ErrorCode', $tabResult[0])===TRUE )
                {
                    $iReturn=$tabResult[0]['ErrorCode'];
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
    CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);

    return $iReturn;
}

?>
