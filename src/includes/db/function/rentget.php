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
  * function: RentGet
  * description: Get a rent.
  * parameters: STRING|sLogin      - login identifier
  *             STRING|sSession    - session identifier
  *             STRING|sInet       - concatenation of IP and USER_AGENT
  *              CRent|pRent       - rent object instance
  *              CDate|pDate       - date object instance
  *           CContact|pContact    - contact object instance
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER -  0 when no record found
  *                   -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  */
function RentGet( $sLogin, $sSession, $sInet, &$pRent, &$pDate, &$pContact)
{
    $iReturn=-1;
    $sMessage='';
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && !is_null($pRent) && ($pRent->GetIdentifier()>0)
        && !is_null($pDate)
        && !is_null($pContact)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        $iReturn=0;
        // try
        try
        {
            // Prepare
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare('CALL sp_RentGet(:sLogin,:sSession,:sInet,:iRent)');
            // Bind
            $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
            $pPDOStatement->bindParam(':sSession',$sSession,PDO::PARAM_STR,200);
            $pPDOStatement->bindParam(':sInet',$sInet,PDO::PARAM_STR,255);
            $pPDOStatement->bindParam(':iRent',$pRent->GetIdentifier(),PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('ErrorCode', $tabResult[0])===TRUE )
                {
                    $iReturn=$tabResult[0]['ErrorCode'];
                }
                else
                {
                    $pDate->SetRequestYear((integer)$tabResult[0]['reservation_year']);
                    $pDate->SetRequestMonth((integer)$tabResult[0]['reservation_month']);
                    $pDate->SetRequestDay((integer)$tabResult[0]['reservation_day']);
                    $pRent->SetIdentifier((integer)$tabResult[0]['reservation_id']);
                    $pRent->SetCountReal((integer)$tabResult[0]['reservation_real']);
                    $pRent->SetCountPlanned((integer)$tabResult[0]['reservation_planned']);
                    $pRent->SetCountCanceled((integer)$tabResult[0]['reservation_canceled']);
                    $pRent->SetMax((integer)$tabResult[0]['reservation_max']);
                    $pRent->SetAge((integer)$tabResult[0]['reservation_age']);
                    $pRent->SetArrhes((integer)$tabResult[0]['reservation_arrhes']);
                    $pRent->SetComment($tabResult[0]['reservation_comment']);
                    $pRent->SetCreationDate($tabResult[0]['creation_date']);
                    $pRent->SetCreationUser($tabResult[0]['creation_username']);
                    $pRent->SetUpdateDate($tabResult[0]['update_date']);
                    $pRent->SetUpdateUser($tabResult[0]['update_username']);
                    $pContact->SetIdentifier((integer)$tabResult[0]['contact_id']);
                    $pContact->SetLastName($tabResult[0]['contact_lastname']);
                    $pContact->SetFirstName($tabResult[0]['contact_firstname']);
                    $pContact->SetTel($tabResult[0]['contact_tel']);
                    $pContact->SetEmail($tabResult[0]['contact_email']);
                    $pContact->SetAddress($tabResult[0]['contact_address']);
                    $pContact->SetAddressMore($tabResult[0]['contact_addressmore']);
                    $pContact->SetCity($tabResult[0]['contact_addresscity']);
                    $pContact->SetZip($tabResult[0]['contact_addresszip']);
                    $iReturn=1;
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
