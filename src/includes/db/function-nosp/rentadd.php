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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_RENT_LOADED') || !defined('PBR_DATE_LOADED') )
    die('-1');

require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentmax.php');

/**
  * function: RentAdd
  * description: Create a rent.
  * parameters: STRING|sLogin      - login identifier
  *             STRING|sSession    - session identifier
  *             STRING|sInet       - concatenation of IP and USER_AGENT
  *            INTEGER|iIdentifier - contact identifier
  *              CDate|pDate       - date
  *              CRent|pRent       - rent
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. Number of row inserted.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  */
function RentAdd( $sLogin, $sSession, $sInet, $iIdentifier, &$pDate, &$pRent)
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$iIdentifier.',...)';
    $iUserId=CUser::GetInstance()->GetUserBDIdentifier();
	$iMaxRent=RentMax($sLogin, $sSession, $sInet, $pDate);

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && is_integer($iIdentifier) && ($iIdentifier>0)
        && !is_null($pDate)
        && !is_null($pRent)
        && ($iMaxRent>=0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
    	// Set error title
    	$sErrorTitle=__FUNCTION__;
    	$sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$iIdentifier.','.$pDate->GetRequestDay().','.$pDate->GetRequestMonth().','.$pDate->GetRequestYear().','.$pRent->GetCountReal().','.$pRent->GetCountPlanned().','.$pRent->GetCountCanceled().')';
    	// Request
        if( ($iUserId>0) && CUser::GetInstance()->IsAuthenticated() )
        {
            //try
            try
            {
                // Prepare
                $sSQL='INSERT INTO `'.PBR_DB_DBN.'`.`reservation` (`idcontact`, `year`, `month`, `day`, `rent_real`, `rent_planned`, `rent_canceled`, `rent_max`, `age`, `arrhe`, `create_date`, `create_iduser`, `update_date`, `update_iduser`) VALUES (:iIdentifier, :iYear, :iMonth, :iDay, :iReal, :iPlanned, :iCanceled, :iMaxRent, :iAge, :iArrhes, SYSDATE(), :iUserId, NULL, NULL)';
                $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':iIdentifier',$iIdentifier,PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iYear',$pDate->GetRequestYear(),PDO::PARAM_INT);;
                $pPDOStatement->bindParam(':iMonth',$pDate->GetRequestMonth(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iDay',$pDate->GetRequestDay(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iReal',$pRent->GetCountReal(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iPlanned',$pRent->GetCountPlanned(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iCanceled',$pRent->GetCountCanceled(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iMaxRent',$iMaxRent,PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iAge',$pRent->GetAge(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iArrhes',$pRent->GetArrhes(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iUserId',$iUserId,PDO::PARAM_INT);
                // Execute
                $pPDOStatement->execute();
                // Last insert id
                $iReturn=CDb::GetInstance()->PDO()->lastInsertId();
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
        	//Authentication failed
        	$iReturn=-2;
        }//if( ($iUserId>0) && CUser::GetInstance()->IsAuthenticated() )
    }//if( IsParameterScalarNotEmpty(

    // Error
    CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);

    return $iReturn;
}

?>
