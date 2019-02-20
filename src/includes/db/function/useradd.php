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
  * function: RentAdd
  * description: Create an user.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *              CUser|pUser    - instance of CUser with valid datas
  *                               OUT: contains the new identifier.
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >0 is OK. New user identifier.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function UserAdd( $sLogin, $sSession, $sInet, CUser $pUser)
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],...)';
    $pUser->SetIdentifier(0);

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet) )
    {
        try
        {
            // Prepare
            $sSQL = 'INSERT INTO `'.PBR_DB_DBN.'`.`user` (`login`, `password`, `registered`, `role`, `last_visit`, `state`) VALUES ( :sName, :sPassword, SYSDATE(), 1, NULL, 1)';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':sName',$pUser->GetUsername(),PDO::PARAM_STR);
            $pPDOStatement->bindValue(':sPassword',$pUser->GetPassword(),PDO::PARAM_STR);
            // Execute
            $pPDOStatement->execute();
            // Last insert id
            $iReturn = CDBLayer::GetInstance()->GetLastInsertId();
            $pUser->SetIdentifier($iReturn);
        }
        catch(PDOException $e)
        {
            // Duplicate error
            if( $e->getCode()==23000 )
            {
                $iReturn = -4;
            }
            else
            {
                $iReturn  = FALSE;
                $sMessage = $e->getMessage();
            }//if( $e->getCode()==23000 )

        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if( IsParameterScalarNotEmpty(

    // Error
    if( $iReturn == -4 )
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, FALSE);
    }
    else
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }

    return $iReturn;
}

?>
