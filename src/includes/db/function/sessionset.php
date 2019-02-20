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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_LIFETIME_SESSION') )
    die('-1');

/**
  * function: SessionSet
  * description: Create a session
  * parameters: STRING|sLogin    - login identifier
  *             STRING|sSession  - session identifier
  *             STRING|sPassword - password
  *             STRING|sInet     - concatenation of IP and USER_AGENT
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. Number of row inserted.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function SessionSet( $sLogin, $sSession, $sPassword, $sInet)
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],[obfuscated])';
    $sMessage = '';
    $iUserId = -2;
	$iUnixTimestamp = time();
    $iSessionTimeExpire = $iUnixTimestamp + (integer)PBR_LIFETIME_SESSION + 0;

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sPassword)
     && IsStringNotEmpty($sInet) )
    {
        try
        {
			/** Check user
             *************/
			// Prepare
			$sSQL='SELECT IFNULL(u.`iduser`,0) AS "user_id" FROM `'.PBR_DB_DBN.'`.`user`AS u WHERE u.`login`=:sLogin AND u.`state`=1 AND u.`password`=:sPassword';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':sLogin',$sLogin,PDO::PARAM_STR);
            $pPDOStatement->bindValue(':sPassword',$sPassword,PDO::PARAM_STR);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
    		$iUserId = $iReturn = -2;
            if( !empty($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('user_id', $tabResult[0]) )
                    $iUserId = $tabResult[0]['user_id'];
            }//if( !empty($tabResult) && is_array($tabResult[0]) )
        	// Free resource
        	$pPDOStatement = NULL;

            if( $iUserId>0 )
			{
				/** Insert session
            	 *****************/
	            // Prepare
				$sSQL='REPLACE INTO `'.PBR_DB_DBN.'`.`session`( `login`, `session`, `create_date`, `expire_date`, `logoff`, `inet`) VALUES ( :sLogin, :sSession, :iUnixTimestamp, :iSessionTimeExpire, 0, CRC32(:sInet))';
				$pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            	// Bind
	            $pPDOStatement->bindValue(':sLogin',$sLogin,PDO::PARAM_STR);
	            $pPDOStatement->bindValue(':sSession',$sSession,PDO::PARAM_STR);
	            $pPDOStatement->bindValue(':iUnixTimestamp',$iUnixTimestamp,PDO::PARAM_INT);
				$pPDOStatement->bindValue(':iSessionTimeExpire',$iSessionTimeExpire,PDO::PARAM_INT);
	            $pPDOStatement->bindValue(':sInet',$sInet,PDO::PARAM_STR);
            	// Execute
            	$pPDOStatement->execute();
            	// Count
                $iReturn = $pPDOStatement->rowCount();
        		// Free resource
        		$pPDOStatement = NULL;

				/** Update user
            	 **************/
                if( $iReturn>0 )
                {
	            	// Prepare
					$sSQL='UPDATE `'.PBR_DB_DBN.'`.`user` SET `last_visit`=SYSDATE() WHERE `iduser`=:iUserId';
					$pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            		// Bind
		            $pPDOStatement->bindValue(':iUserId',$iUserId,PDO::PARAM_INT);
            		// Execute
            		$pPDOStatement->execute();
            		// Count
                	$iReturn = $pPDOStatement->rowCount();
                }//if( $iReturn>0 )
            }//if( $iUserId>0 )
        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if( IsParameterScalarNotEmpty(...

    // Error
    ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);

    return $iReturn;
}

?>
