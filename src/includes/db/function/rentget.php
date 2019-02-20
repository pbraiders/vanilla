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
  * function: RentGet
  * description: Get rent and contact datas.
  * parameters: STRING|sLogin      - login identifier
  *             STRING|sSession    - session identifier
  *             STRING|sInet       - concatenation of IP and USER_AGENT
  *              CRent|pRent       - instance of CRent. Identifier should be correctly filled.
  *              CDate|pDate       - OUT: instance of CDate.
  *           CContact|pContact    - OUT: instance of CContact
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 when rent is found. Rent identifier.
  *                    0 when no row found.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function RentGet( $sLogin, $sSession, $sInet, CRent $pRent, CDate $pDate, CContact $pContact)
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$pRent->GetIdentifier().')';
    $pContact->ResetMe();

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet)
     && ($pRent->GetIdentifier()>0) )
    {
        $iReturn = 0;

        try
        {
            // Prepare
    		$sSQL = 'SELECT r.`idreservation` AS "reservation_id", r.`year` AS "reservation_year", r.`month` AS "reservation_month", r.`day` AS "reservation_day", r.`rent_real` AS "reservation_real", r.`rent_planned` AS "reservation_planned", r.`rent_canceled` AS "reservation_canceled", r.`rent_max` AS "reservation_max", r.`age` AS "reservation_age", r.`arrhe` AS "reservation_arrhes", r.`comment` AS "reservation_comment", r.`create_date` AS "creation_date", u.`login` AS "creation_username", r.`update_date` AS "update_date", v.`login` AS "update_username", c.`lastname` AS "contact_lastname", c.`firstname` AS "contact_firstname", c.`tel` AS "contact_tel", c.`email` AS "contact_email", c.`address` AS "contact_address", c.`address_more` AS "contact_addressmore", c.`city` AS "contact_addresscity", c.`zip` AS "contact_addresszip", c.`idcontact` AS "contact_id" FROM `'.PBR_DB_DBN.'`.`reservation` AS r INNER JOIN `'.PBR_DB_DBN.'`.`contact` AS c ON r.`idcontact`=c.`idcontact` INNER JOIN `'.PBR_DB_DBN.'`.`user` AS u ON r.`create_iduser`=u.`iduser` LEFT JOIN `'.PBR_DB_DBN.'`.`user` AS v ON r.`update_iduser`=v.`iduser` WHERE r.`idreservation`=:iIdentifier';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iIdentifier', $pRent->GetIdentifier(), PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                $pRent->ResetMe();
                $pContact->ResetMe();
                if( array_key_exists( 'reservation_year', $tabResult[0]) )
                    $pDate->SetRequestYear((integer)$tabResult[0]['reservation_year']);
                if( array_key_exists( 'reservation_month', $tabResult[0]) )
                    $pDate->SetRequestMonth((integer)$tabResult[0]['reservation_month']);
                if( array_key_exists( 'reservation_day', $tabResult[0]) )
                    $pDate->SetRequestDay((integer)$tabResult[0]['reservation_day']);
                if( array_key_exists( 'reservation_id', $tabResult[0]) )
                    $pRent->SetIdentifier((integer)$tabResult[0]['reservation_id']);
                if( array_key_exists( 'reservation_real', $tabResult[0]) )
                    $pRent->SetCountReal((integer)$tabResult[0]['reservation_real']);
                if( array_key_exists( 'reservation_planned', $tabResult[0]) )
                    $pRent->SetCountPlanned((integer)$tabResult[0]['reservation_planned']);
                if( array_key_exists( 'reservation_canceled', $tabResult[0]) )
                    $pRent->SetCountCanceled((integer)$tabResult[0]['reservation_canceled']);
                if( array_key_exists( 'reservation_max', $tabResult[0]) )
                    $pRent->SetMax((integer)$tabResult[0]['reservation_max']);
                if( array_key_exists( 'reservation_age', $tabResult[0]) )
                    $pRent->SetAge((integer)$tabResult[0]['reservation_age']);
                if( array_key_exists( 'reservation_arrhes', $tabResult[0]) )
                    $pRent->SetArrhes((integer)$tabResult[0]['reservation_arrhes']);
                if( array_key_exists( 'reservation_comment', $tabResult[0]) )
                    $pRent->SetComment($tabResult[0]['reservation_comment']);
                if( array_key_exists( 'creation_date', $tabResult[0]) )
                    $pRent->SetCreationDate($tabResult[0]['creation_date']);
                if( array_key_exists( 'creation_username', $tabResult[0]) )
                    $pRent->SetCreationUser($tabResult[0]['creation_username']);
                if( array_key_exists( 'update_date', $tabResult[0]) )
                    $pRent->SetUpdateDate($tabResult[0]['update_date']);
                if( array_key_exists( 'update_username', $tabResult[0]) )
                    $pRent->SetUpdateUser($tabResult[0]['update_username']);
                if( array_key_exists( 'contact_id', $tabResult[0]) )
                    $pContact->SetIdentifier((integer)$tabResult[0]['contact_id']);
                if( array_key_exists( 'contact_lastname', $tabResult[0]) )
                    $pContact->SetLastName($tabResult[0]['contact_lastname']);
                if( array_key_exists( 'contact_firstname', $tabResult[0]) )
                    $pContact->SetFirstName($tabResult[0]['contact_firstname']);
                if( array_key_exists( 'contact_tel', $tabResult[0]) )
                    $pContact->SetTel($tabResult[0]['contact_tel']);
                if( array_key_exists( 'contact_email', $tabResult[0]) )
                    $pContact->SetEmail($tabResult[0]['contact_email']);
                if( array_key_exists( 'contact_address', $tabResult[0]) )
                    $pContact->SetAddress($tabResult[0]['contact_address']);
                if( array_key_exists( 'contact_addressmore', $tabResult[0]) )
                    $pContact->SetAddressMore($tabResult[0]['contact_addressmore']);
                if( array_key_exists( 'contact_addresscity', $tabResult[0]) )
                    $pContact->SetCity($tabResult[0]['contact_addresscity']);
                if( array_key_exists( 'contact_addresszip', $tabResult[0]) )
                    $pContact->SetZip($tabResult[0]['contact_addresszip']);
                $iReturn = $pRent->GetIdentifier();
            }//if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
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

?>
