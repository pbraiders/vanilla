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
  */
function SessionSet( $sLogin, $sSession, $sPassword, $sInet)
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],[obfuscated])';
    $sMessage='';
    $iUserId=-2;
	$iUnixTimestamp=time();
    $iSessionTimeExpire=0;

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sPassword)
        && IsParameterScalarNotEmpty($sInet)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        try
        {
			/** Get config value
             *******************/
			// Prepare
            $sSQL='SELECT CONVERT(c.`value`, UNSIGNED INTEGER) AS  "session_time_expire" FROM `'.PBR_DB_DBN.'`.`config` AS c WHERE c.`name` LIKE "session_time_expire"';
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('session_time_expire', $tabResult[0])===TRUE )
                {
                    $iSessionTimeExpire=$tabResult[0]['session_time_expire'];
                }//if( array_key_exists('session_time_expire', $tabResult[0])===TRUE )
            }//if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
        	// Free resource
        	$pPDOStatement=NULL;
            $iSessionTimeExpire=$iSessionTimeExpire+$iUnixTimestamp;

			/** Check user
             *************/
			// Prepare
			$sSQL='SELECT IFNULL(u.`iduser`,0) AS "user_id" FROM `'.PBR_DB_DBN.'`.`user`AS u WHERE u.`login`=:sLogin AND u.`state`=1 AND u.`password`=:sPassword';
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
            $pPDOStatement->bindParam(':sPassword',$sPassword,PDO::PARAM_STR,40);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
    		$iUserId=$iReturn=-2;
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('user_id', $tabResult[0])===TRUE )
                {
                    $iUserId=$tabResult[0]['user_id'];
                }//if( array_key_exists('user_id', $tabResult[0])===TRUE )
            }//if( is_array($tabResult) && is_array($tabResult[0]) )
        	// Free resource
        	$pPDOStatement=NULL;

            if( $iUserId>0 )
			{
				/** Insert session
            	 *****************/
	            // Prepare
				$sSQL='REPLACE INTO `'.PBR_DB_DBN.'`.`session`( `login`, `session`, `create_date`, `expire_date`, `logoff`, `inet`) VALUES ( :sLogin, :sSession, :iUnixTimestamp, :iSessionTimeExpire, 0, CRC32(:sInet))';
				$pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
            	// Bind
	            $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
	            $pPDOStatement->bindParam(':sSession',$sSession,PDO::PARAM_STR,200);
	            $pPDOStatement->bindParam(':iUnixTimestamp',$iUnixTimestamp,PDO::PARAM_INT);
				$pPDOStatement->bindParam(':iSessionTimeExpire',$iSessionTimeExpire,PDO::PARAM_INT);
	            $pPDOStatement->bindParam(':sInet',$sInet,PDO::PARAM_STR,255);
            	// Execute
            	$pPDOStatement->execute();
            	// Count
                $iReturn=$pPDOStatement->rowCount();
        		// Free resource
        		$pPDOStatement=NULL;

				/** Update user
            	 **************/
                if( $iReturn>0 )
                {
	            	// Prepare
					$sSQL='UPDATE `'.PBR_DB_DBN.'`.`user` SET `last_visit`=SYSDATE() WHERE `iduser`=:iUserId';
					$pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
            		// Bind
		            $pPDOStatement->bindParam(':iUserId',$iUserId,PDO::PARAM_INT);
            		// Execute
            		$pPDOStatement->execute();
            		// Count
                	$iReturn=$pPDOStatement->rowCount();
                }//if( $iReturn>0 )
            }//if( $iUserId>0 )
        }
        catch(PDOException $e)
        {
            $iReturn=FALSE;
            $sMessage=$e->getMessage();
            CDb::GetInstance()->LogError( PBR_DB_DBN, $sLogin, $sErrorTitle, $sMessage);
        }//try

        // Free resource
        $pPDOStatement=NULL;

    }//if( IsParameterScalarNotEmpty(...

    // Error
    CErrorList::GetInstance()->AddDB($iReturn,__FILE__,__LINE__,$sMessage);

    return $iReturn;
}

?>
