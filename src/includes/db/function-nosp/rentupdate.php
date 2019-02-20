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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_RENT_LOADED') )
    die('-1');

/**
  * function: RentUpdate
  * description: Update a rent.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *              CRent|pRent    - rent object instance
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. Number of row updated.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  */
function RentUpdate( $sLogin, $sSession, $sInet, &$pRent)
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],...)';
    $iUserId=CUser::GetInstance()->GetUserBDIdentifier();
    $bInTransaction = FALSE;

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && !is_null($pRent) && ($pRent->GetIdentifier()>0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
    	// Set error title
	    $sErrorTitle=__FUNCTION__;
	    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$pRent->GetIdentifier().')';
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

			// Try
            try
            {
     			/** Update max
    		 	 *************/
                // Prepare
	            $sSQL='UPDATE `'.PBR_DB_DBN.'`.`reservation` AS r INNER JOIN `'.PBR_DB_DBN.'`.`reservation` AS s USING(`year`,`month`,`day`) SET r.`rent_max`=:iMax WHERE s.`idreservation`=:iIdentifier';
				$pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
				// Bind
                $pPDOStatement->bindParam(':iMax',$pRent->GetMax(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iIdentifier',$pRent->GetIdentifier(),PDO::PARAM_INT);
    			// Execute
    			$pPDOStatement->execute();
    			// Count
    			$iReturn=$pPDOStatement->rowCount();
    			// Free resource
    			$pPDOStatement=NULL;

     			/** Update rent
    		 	 *************/
                // Prepare
	            $sSQL='UPDATE `'.PBR_DB_DBN.'`.`reservation` SET `rent_real`=:iReal, `rent_planned`=:iPlanned, `rent_canceled`=:iCanceled, `age`=:iAge, `arrhe`=:iArrhes, `comment`=:sComment, `update_date`=SYSDATE(), `update_iduser`=:iUserId WHERE `idreservation`=:iIdentifier';
                $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':iIdentifier',$pRent->GetIdentifier(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iReal',$pRent->GetCountReal(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iPlanned',$pRent->GetCountPlanned(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iCanceled',$pRent->GetCountCanceled(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iAge',$pRent->GetAge(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iArrhes',$pRent->GetArrhes(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':sComment',$pRent->GetComment(),PDO::PARAM_STR);
				$pPDOStatement->bindParam(':iUserId',$iUserId,PDO::PARAM_INT);
    			// Execute
    			$pPDOStatement->execute();
    			// Count
    			$iReturn=$iReturn+$pPDOStatement->rowCount();

    			/** Commit transaction
    			 *********************/
    			if( $bInTransaction===TRUE )
    			{
    				CDb::GetInstance()->PDO()->commit();
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
