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
  * function: DBStatus
  * description: return the record count and database size
  * parameters: STRING|sLogin   - login identifier
  *             STRING|sSession - session identifier
  *             STRING|sInet    - concatenation of IP and USER_AGENT
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  *         or
  *         ARRAY of 2 records (records,<value>)(size,<value>)
  * author: Olivier JULLIEN - 2010-06-15
  */
function DBStatus( $sLogin, $sSession, $sInet )
{
    /** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated])';

    /** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet) )
    {

        $iReturn = array('records'=>0, 'size'=>0);

        // Size
        try
        {
            // Prepare
            $sSQL = 'SHOW TABLE STATUS FROM `'.PBR_DB_DBN.'`';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            while( $tRow = $pPDOStatement->fetch(PDO::FETCH_ASSOC) )
            {
                $iReturn['size'] = $iReturn['size'] + $tRow['Data_length'] + $tRow['Index_length'];
            }//while

            // Round
            $iReturn['size'] = $iReturn['size'] / 1024;
            if( $iReturn['size']>1024 )
            {
                $iReturn['size'] = round( $iReturn['size'] / 1024, 2).' MB';
            }
            else
            {
                $iReturn['size'] = round( $iReturn['size'] , 2).' KB';
            }// Round
        }
        catch(PDOException $e)
        {
//            $iReturn = FALSE;
//            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement = NULL;

        // records
        try
        {
            // Prepare
            $sSQL = 'SELECT "config" AS "name", COUNT(p.`name`) AS "count" FROM `'.PBR_DB_DBN.'`.`config` AS p UNION SELECT "contact" AS "name", COUNT(c.`idcontact`) AS "count" FROM `'.PBR_DB_DBN.'`.`contact` AS c UNION SELECT "log" AS "name", COUNT(l.`idlog`) AS "count" FROM `log` AS l UNION SELECT "reservation" AS "name", COUNT(r.`idreservation`) AS "count" FROM `'.PBR_DB_DBN.'`.`reservation` AS r UNION SELECT "session" AS "name", COUNT(s.`login`) AS "count" FROM `'.PBR_DB_DBN.'`.`session` AS s UNION SELECT "user" AS "name", COUNT(u.`iduser`) AS "count" FROM `'.PBR_DB_DBN.'`.`user` AS u';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            while( $tRow = $pPDOStatement->fetch(PDO::FETCH_ASSOC) )
            {
                $iReturn[$tRow['name']] = $tRow['count'];
                $iReturn['records'] += $tRow['count'];
            }//while

        }
        catch(PDOException $e)
        {
            $iReturn = FALSE;
            $sMessage = $e->getMessage();
        }//try

        // Free resource
        $pPDOStatement = NULL;

    }//if(...

    // Error
    if( is_scalar($iReturn) )
    {
        ErrorDBLog( $sLogin, $sErrorTitle, $sMessage, $iReturn, TRUE);
    }//if( is_scalar($iReturn) )

    return $iReturn;
}
