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
if( !defined('PBR_VERSION') || !defined('PBR_DB_LOADED') || !defined('PBR_NEWUSER_LOADED') )
    die('-1');

/**
  * function: UserUpdate
  * description: Create an user.
  * parameters: STRING|sLogin      - login identifier
  *             STRING|sSession    - session identifier
  *             STRING|sInet       - concatenation of IP and USER_AGENT
  *           CNewUser|pNewUser - new user
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. Number of row inserted.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDb::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-11 - fixed major bug
  */
function UserUpdate( $sLogin, $sSession, $sInet, &$pNewUser)
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
        && !is_null($pNewUser) && ($pNewUser->IsValidUpdate()===TRUE)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        if( CUser::GetInstance()->IsAuthenticated() )
        {
        	// Build Request
        	$sSQL='UPDATE `'.PBR_DB_DBN.'`.`user` SET `state`=:iState';
            if( !is_null($pNewUser->GetPassword()) )
            {
            	$sSQL.=',`password`=:sPassword';
            }//if( !is_null($pNewUser->GetPassword()) )
            $sSQL.=' WHERE `iduser`=:iIdentifier';

            //try
            try
            {
              	// Prepare
               	$pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':iIdentifier',$pNewUser->GetIdentifier(),PDO::PARAM_INT);
                $pPDOStatement->bindParam(':iState',$pNewUser->GetState(),PDO::PARAM_INT);
            	if( !is_null($pNewUser->GetPassword()) )
            	{
                	$pPDOStatement->bindParam(':sPassword',$pNewUser->GetPassword(),PDO::PARAM_STR,40);
            	}//if( !is_null($pNewUser->GetPassword()) )
    			// Execute
    			$pPDOStatement->execute();
    			// Count
    			$iReturn=$pPDOStatement->rowCount();
    			// Free resource
    			$pPDOStatement=NULL;
            }
            catch(PDOException $e)
            {
                $iReturn=FALSE;
                $sMessage=$e->getMessage();
            }//try

            // Free resource
            $pPDOStatement=NULL;
		}
        else
        {
        	//Authentication failed
        	$iReturn=-2;
        }//if( CUser::GetInstance()->IsAuthenticated() )
    }//if( IsParameterScalarNotEmpty(

    // Error
    ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);

    return $iReturn;
}

?>
