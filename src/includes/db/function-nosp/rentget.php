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
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],...)';

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && !is_null($pRent) && ($pRent->GetIdentifier()>0)
        && !is_null($pDate)
        && !is_null($pContact)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
    	// Set error title
		$sErrorTitle=__FUNCTION__;
	    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$pRent->GetIdentifier().')';
        // Request
        if( CUser::GetInstance()->IsAuthenticated() )
        {
            $iReturn=0;
            // try
            try
            {
                // Prepare
    			$sSQL='SELECT r.`idreservation` AS "reservation_id", r.`year` AS "reservation_year", r.`month` AS "reservation_month", r.`day` AS "reservation_day", r.`rent_real` AS "reservation_real", r.`rent_planned` AS "reservation_planned", r.`rent_canceled` AS "reservation_canceled", r.`rent_max` AS "reservation_max", r.`age` AS "reservation_age", r.`arrhe` AS "reservation_arrhes", r.`comment` AS "reservation_comment", r.`create_date` AS "creation_date", u.`login` AS "creation_username", r.`update_date` AS "update_date", v.`login` AS "update_username", c.`lastname` AS "contact_lastname", c.`firstname` AS "contact_firstname", c.`tel` AS "contact_tel", c.`email` AS "contact_email", c.`address` AS "contact_address", c.`address_more` AS "contact_addressmore", c.`city` AS "contact_addresscity", c.`zip` AS "contact_addresszip", c.`idcontact` AS "contact_id" FROM `'.PBR_DB_DBN.'`.`reservation` AS r INNER JOIN `'.PBR_DB_DBN.'`.`contact` AS c ON r.`idcontact`=c.`idcontact` INNER JOIN `'.PBR_DB_DBN.'`.`user` AS u ON r.`create_iduser`=u.`iduser` LEFT JOIN `'.PBR_DB_DBN.'`.`user` AS v ON r.`update_iduser`=v.`iduser` WHERE r.`idreservation`=:iIdentifier';
                $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':iIdentifier',$pRent->GetIdentifier(),PDO::PARAM_INT);
                // Execute
                $pPDOStatement->execute();
                // Fetch
                $tabResult = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
                // Analyse
                if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
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
                }//if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
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
    CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);

    return $iReturn;
}

?>
