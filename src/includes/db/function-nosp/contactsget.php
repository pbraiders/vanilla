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
  * function: ContactsGet
  * description: Get contact(s).
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *             STRING|sSearch  - search contact last name (optionnal)
  *            INTEGER|iOffset  - offset of the first row to return
  *            INTEGER|iLimit   - maximum number of rows to return
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  *         or
  *         ARRAY of none, one or more records (contact_id,contact_lastname,
  *         contact_firstname,contact_tel,contact_email,contact_address
  *         contact_addressmore,contact_addresscity,contact_addresszip
  *         contact_comment,creation_date,creation_username,update_date
  *         update_username)
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDb::GetInstance()->LogError(...)
  */
function ContactsGet( $sLogin, $sSession, $sInet, $sSearch, $iOffset, $iLimit)
{
	/** Initialize
     *************/
    $iReturn=-1;
    $sMessage='';
    $sErrorTitle=__FUNCTION__;
    $sErrorTitle.='('.$sLogin.','.$sSession.',[obfuscated],'.$sSearch.','.$iOffset.','.$iLimit.')';

	/** Request
     **********/
    if( IsParameterScalarNotEmpty($sLogin)
        && IsParameterScalarNotEmpty($sSession)
        && IsParameterScalarNotEmpty($sInet)
        && IsParameterScalar($sSearch)
        && is_integer($iOffset) && ($iOffset>=0)
        && is_integer($iLimit) && ($iLimit>0)
        && CDb::GetInstance()->IsOpen()===TRUE )
    {
        if( CUser::GetInstance()->IsAuthenticated() )
        {
            // Build query
			$sSQL='SELECT c.`idcontact` AS "contact_id",c.`lastname` AS "contact_lastname",c.`firstname` AS "contact_firstname",c.`tel` AS "contact_tel",c.`email` AS "contact_email",c.`address` AS "contact_address",c.`address_more` AS "contact_addressmore",c.`city` AS "contact_addresscity",c.`zip` AS "contact_addresszip",c.`comment` AS "contact_comment",c.`create_date` AS "creation_date",u.`login` AS "creation_username",c.`update_date` AS "update_date",v.`login` AS "update_username" FROM `'.PBR_DB_DBN.'`.`contact` AS c INNER JOIN `'.PBR_DB_DBN.'`.`user` AS u ON c.`create_iduser`=u.`iduser` LEFT JOIN `'.PBR_DB_DBN.'`.`user` AS v ON c.`update_iduser`=v.`iduser`';
            if( strlen($sSearch)>0 )
            {
            	$sSearch = str_replace('*', '%', $sSearch);
                $sSQL.=' WHERE c.`lastname` LIKE :sSearch';
            }//if( strlen($sSearch)>0 )
			$sSQL.=' ORDER BY c.`lastname` LIMIT :iLimit OFFSET :iOffset';

            // try
            try
            {
                // Prepare
                $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
                // Bind
                if( strlen($sSearch)>0 )
                {
                	$pPDOStatement->bindParam(':sSearch',$sSearch,PDO::PARAM_STR,40);
                }//if( strlen($sSearch)>0 )
                $pPDOStatement->bindParam(':iOffset',$iOffset,PDO::PARAM_INT);
    			$pPDOStatement->bindParam(':iLimit',$iLimit,PDO::PARAM_INT);
                // Execute
                $pPDOStatement->execute();
                // Fetch
                $iReturn = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
                // Analyse
                if( !is_array($iReturn) || (isset($iReturn[0]) && !is_array($iReturn[0])) )
                {
					$iReturn=0;
                }//if( !is_array($iReturn) || (isset($iReturn[0]) && !is_array($iReturn[0])) )
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
        	$iReturn=-2;
        }//if( CUser::GetInstance()->IsAuthenticated() )
    }//if( IsParameterScalarNotEmpty(

    // Error
    if( is_scalar($iReturn) )
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}

?>
