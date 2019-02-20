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

require(PBR_PATH.'/includes/db/'.PBR_DB_DIR.'/rentmax.php');

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
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDb::GetInstance()->LogError(...)
  */
function RentContactAdd( $sLogin, $sSession, $sInet, &$pContact, &$pDate, &$pRent)
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],...)';
    $bInTransaction = FALSE;
    $iUserId=CUser::GetInstance()->GetUserBDIdentifier();
	$iMaxRent=RentMax($sLogin, $sSession, $sInet, $pDate);
    $iContact=0;

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && !is_null($pContact)
        && !is_null($pDate)
        && !is_null($pRent)
        && $pContact->MandatoriesAreFilled()
        && ($iMaxRent>=0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
		// Set error title
	    $sErrorTitle=__FUNCTION__;
	    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$pContact->GetLastName().','.$pContact->GetFirstName().','.$pContact->GetTel().','.$pDate->GetRequestDay().','.$pDate->GetRequestMonth().','.$pDate->GetRequestYear().'...)';
        // Request
        if( ($iUserId>0) && CUser::GetInstance()->IsAuthenticated() )
        {
    		/** Start transaction
    		 ********************/
    		try
    		{
    			CDb::GetInstance()->PDO()->beginTransaction();
    			$bInTransaction = TRUE;
    		}
    		catch(PDOException $e)
    		{
    			$bInTransaction = FALSE;
    		}//try

            //try
            try
            {
    			/** Insert contact
    		 	 *****************/
	            // Prepare
	            $sSQL='INSERT INTO `'.PBR_DB_DBN.'`.`contact`(`lastname`, `firstname`, `tel`, `email`, `address`, `address_more`, `city`, `zip`, `comment`, `create_date`, `create_iduser`, `update_date`, `update_iduser`) VALUES (:sLastName, :sFirstName, :sTel, :sEmail, :sAddress, :sAddressMore, :sCity, :sZip, :sComment, SYSDATE(), :iUserId, NULL, NULL)';
				$pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
				// Bind
                $pPDOStatement->bindParam(':sLastName',$pContact->GetLastName(),PDO::PARAM_STR,40);
                $pPDOStatement->bindParam(':sFirstName',$pContact->GetFirstName(),PDO::PARAM_STR,40);
                $pPDOStatement->bindParam(':sTel',$pContact->GetTel(),PDO::PARAM_STR,40);
                $pPDOStatement->bindParam(':sEmail',$pContact->GetEmail(),PDO::PARAM_STR,255);
                $pPDOStatement->bindParam(':sAddress',$pContact->GetAddress(),PDO::PARAM_STR,255);
                $pPDOStatement->bindParam(':sAddressMore',$pContact->GetAddressMore(),PDO::PARAM_STR,255);
                $pPDOStatement->bindParam(':sCity',$pContact->GetCity(),PDO::PARAM_STR,255);
                $pPDOStatement->bindParam(':sZip',$pContact->GetZip(),PDO::PARAM_STR,8);
                $pPDOStatement->bindParam(':sComment',$pContact->GetComment(),PDO::PARAM_STR);
				$pPDOStatement->bindParam(':iUserId',$iUserId,PDO::PARAM_INT);
	            // Execute
	            $pPDOStatement->execute();
				// Last insert id
            	$iContact=CDb::GetInstance()->PDO()->lastInsertId();
				// Free resource
				$pPDOStatement=NULL;

				if( $iContact>0 )
				{
    				/** Insert rent
    		 		 **************/
                	// Prepare
	            	$sSQL='INSERT INTO `'.PBR_DB_DBN.'`.`reservation` (`idcontact`, `year`, `month`, `day`, `rent_real`, `rent_planned`, `rent_canceled`, `rent_max`, `age`, `arrhe`, `create_date`, `create_iduser`, `update_date`, `update_iduser`) VALUES (:iContact, :iYear, :iMonth, :iDay, :iReal, :iPlanned, :iCanceled, :iMaxRent, :iAge, :iArrhes, SYSDATE(), :iUserId, NULL, NULL)';
                	$pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
	                // Bind
					$pPDOStatement->bindParam(':iContact',$iContact,PDO::PARAM_INT);
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
				}//if( $iContact>0 )

				/** Commit transaction
    			 *********************/
    			if( $bInTransaction===TRUE )
                {
                	if( $iReturn>0 )
    				{
    					CDb::GetInstance()->PDO()->commit();
                	}
	                else
                	{
    					CDb::GetInstance()->PDO()->rollback();
	    			}//if( $iReturn>0 )
					$bInTransaction = FALSE;
				}//if( $bInTransaction===TRUE )

            }
            catch(PDOException $e)
            {
                $iReturn=FALSE;
                $sMessage=$e->getMessage();
    			if( $bInTransaction===TRUE )
    			{
    				CDb::GetInstance()->PDO()->rollBack();
    				$bInTransaction = FALSE;
    			}//if( $bInTransaction===TRUE )
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
    ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);

    return $iReturn;
}

?>
