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
  * function: ContactsGetCount
  * description: Get contacts count.
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  *           CContact|pSearch  - instance of CContact. Used for search options.
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - >=0 is OK. Number of row.
  *                   -1 when a private error occures.
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBLayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function ContactsGetCount( $sLogin, $sSession, $sInet, CContact $pSearch)
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sSearch = $pSearch->GetLastName();
    $bSearch = !empty( $sSearch );
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$sSearch.')';

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet) )
    {
        // Build query
		$sSQL = 'SELECT COUNT(c.`idcontact`) AS "contact_count" FROM `'.PBR_DB_DBN.'`.`contact` AS c';

        // Sanitize Search
        if( $bSearch )
        {
            $sSearch = str_replace('*', '%', $sSearch);
            $sSQL .= ' WHERE c.`lastname` LIKE :sSearch';
		}//if( $bSearch )

        // try
        try
        {
            // Prepare
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            if( $bSearch )
            {
                $pPDOStatement->bindValue(':sSearch',$sSearch,PDO::PARAM_STR);
            }//if( $bSearch )
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $tabResult = $pPDOStatement->fetchAll();
            // Analyse
            if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
            {
                if( array_key_exists('contact_count', $tabResult[0]) )
                    $iReturn = (integer)$tabResult[0]['contact_count'];
            }//if( is_array($tabResult) && isset($tabResult[0]) && is_array($tabResult[0]) )
        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement=NULL;

    }//if(...

    // Error
    ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);

    return $iReturn;
}

?>
