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
  *           CContact|pSearch  - instance of CContact. Used for search options.
  *            CPaging|pPaging  - instance of CPaging. Offset of the first row to return
  *                               and maximum number of rows to return.
  *            COption|pOrder   - "order by" option
  *            COption|pSort    - sort option (desc or asc)
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
  * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::AddDB(...) and CDBlayer::GetInstance()->LogError(...)
  * update: Olivier JULLIEN - 2010-06-15 - improvement
  */
function ContactsGet( $sLogin, $sSession, $sInet, CContact $pSearch, CPaging $pPaging, COption $pOrder, COption $pSort )
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sSearch = $pSearch->GetLastName();
    $bSearch = !empty( $sSearch );
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.','.$sSearch.','.$pPaging->GetOffset().','.$pPaging->GetLimit().','.$pOrder->GetValue().','.$pSort->GetValue().')';

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet) )
    {

        // Build query

        // Order by
        if( $pOrder->GetValue()==2 )
        {
            $sSQL_OrderBy1 = ' ORDER BY cc.`create_date`';
            $sSQL_OrderBy2 = ' ORDER BY c.`create_date`';
        }
        else
        {
            $sSQL_OrderBy1 = ' ORDER BY cc.`lastname`';
            $sSQL_OrderBy2 = ' ORDER BY c.`lastname`';
        }//Option: order by

        // Sort
        if( $pSort->GetValue()==2 )
        {
            $sSQL_OrderBy1 .= ' DESC';
            $sSQL_OrderBy2 .= ' DESC';
        }
        else
        {
            $sSQL_OrderBy1 .= ' ASC';
            $sSQL_OrderBy2 .= ' ASC';
        }//Option: sort

        // Limit
        $sSQL_Limit = ' LIMIT :iLimit OFFSET :iOffset';

        // Join
        $sSQL_Join = ' INNER JOIN `'.PBR_DB_DBN.'`.`user` AS u ON c.`create_iduser`=u.`iduser` LEFT JOIN `'.PBR_DB_DBN.'`.`user` AS v ON c.`update_iduser`=v.`iduser`';

        // Select
        $sSQL_Select = 'SELECT c.`idcontact` AS "contact_id",c.`lastname` AS "contact_lastname",c.`firstname` AS "contact_firstname",c.`tel` AS "contact_tel",c.`email` AS "contact_email",c.`address` AS "contact_address",c.`address_more` AS "contact_addressmore",c.`city` AS "contact_addresscity",c.`zip` AS "contact_addresszip",c.`comment` AS "contact_comment",c.`create_date` AS "creation_date",u.`login` AS "creation_username",c.`update_date` AS "update_date",v.`login` AS "update_username" FROM';

        // Search
        if( $bSearch===FALSE )
        {
            // From
            $sSQL_Select .= ' ( SELECT cc.`idcontact` FROM `'.PBR_DB_DBN.'`.`contact` AS cc'.$sSQL_OrderBy1.$sSQL_Limit.') AS t';
            // Join
            $sSQL_Select .= ' INNER JOIN `'.PBR_DB_DBN.'`.`contact` AS c ON t.`idcontact`=c.`idcontact`'.$sSQL_Join;
            $sSQL_Select .= $sSQL_OrderBy2;
        }
        else
        {
            // From
            $sSQL_Select .= '`'.PBR_DB_DBN.'`.`contact` AS c';
            // Join
            $sSQL_Select .= $sSQL_Join;
            // Where
           	$sSearch = str_replace('*', '%', $sSearch);
            $sSQL_Select .= ' WHERE c.`lastname` LIKE :sSearch'.$sSQL_OrderBy2.$sSQL_Limit;
        }//if( $bSearch )

        // try
        try
        {
            // Prepare
            $pPDOStatement = CDBlayer::GetInstance()->GetDriver()->prepare($sSQL_Select);
            // Bind
            if( $bSearch )
            {
                $pPDOStatement->bindValue(':sSearch',$sSearch,PDO::PARAM_STR);
            }//if( $bSearch )
            $pPDOStatement->bindValue(':iOffset',$pPaging->GetOffset(),PDO::PARAM_INT);
  			$pPDOStatement->bindValue(':iLimit',$pPaging->GetLimit(),PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $iReturn = $pPDOStatement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if( ....

    // Error
    if( is_scalar($iReturn) )
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}

?>
