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
  * function: ContactRentsGetCount
  * description: Get rents count for a specific contact.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *            INTEGER|iIdentifier - contact identifier
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >=0 is OK. Number of row.
  *                   -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  */
function ContactRentsGetCount( $sLogin, $sSession, $sInet, $iIdentifier )
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$iIdentifier.')';

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && is_integer($iIdentifier) && ($iIdentifier>0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        if( CUser::GetInstance()->IsAuthenticated() )
        {
			$iReturn=0;
            // try
            try
            {
                // Prepare
                $sSQL='SELECT COUNT(r.`idreservation`) AS "reservation_count" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`idcontact`=:iIdentifier';
                $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':iIdentifier',$iIdentifier,PDO::PARAM_INT);
                // Execute
                $pPDOStatement->execute();
                // Fetch
                $tabResult = $pPDOStatement->fetchAll();
                // Analyse
                if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
                {
                    if( array_key_exists('reservation_count', $tabResult[0])===TRUE )
                    {
                        $iReturn=(integer)$tabResult[0]['reservation_count'];
                    }//if( array_key_exists('reservation_count', $tabResult[0])===TRUE )
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
