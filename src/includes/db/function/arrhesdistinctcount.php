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
  * function: ArrhesDistinctCount
  * description: Get the distinct count of arrhes
  * parameters: STRING|sLogin    - login identifier
  *             STRING|sSession  - session identifier
  *             STRING|sInet     - concatenation of IP and USER_AGENT
  *              CDate|pDate     - current date
  *            COption|pInterval - interval
  * return: BOOLEAN - FALSE if an exception occures
  *         or
  *         INTEGER - -1 when a private error occures
  *                   -2 when an authentication error occures.
  *                   -3 when an access denied error occures.
  *                   -4 when a duplicate error occures.
  *         or
  *         ARRAY of :  label => count
  * author: Olivier JULLIEN - 2010-06-15
  */
function ArrhesDistinctCount( $sLogin, $sSession, $sInet, CDate $pDate, COption $pInterval)
{
	/** Initialize
     *************/
    $iReturn = -1;
    $sMessage = '';
    $sErrorTitle = __FUNCTION__ .'('.$sLogin.','.$sSession.',[obfuscated],'.$pInterval->GetValue().')';

	/** Request
     **********/
    if( (CDBLayer::GetInstance()->IsOpen()===TRUE)
     && IsScalarNotEmpty(PBR_DB_DBN)
     && IsStringNotEmpty($sLogin)
     && IsStringNotEmpty($sSession)
     && IsStringNotEmpty($sInet) )
    {

        $iYear = $pDate->GetCurrentYear() - $pInterval->GetValue();

        try
        {
            // Prepare
            $sSQL = 'SELECT 0 AS "label", count(r.`idreservation`) AS "count" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`arrhe`=0 AND r.`year`>=:iYear1 UNION SELECT 1 AS "label", count(r.`idreservation`) AS "count" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`arrhe`=1 AND r.`year`>=:iYear2 UNION SELECT 2 AS "label", count(r.`idreservation`) AS "count" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`arrhe`=2 AND r.`year`>=:iYear3 UNION SELECT 3 AS "label", count(r.`idreservation`) AS "count" FROM `'.PBR_DB_DBN.'`.`reservation` AS r WHERE r.`arrhe`=3 AND r.`year`>=:iYear4';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':iYear1',$iYear,PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iYear2',$iYear,PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iYear3',$iYear,PDO::PARAM_INT);
            $pPDOStatement->bindValue(':iYear4',$iYear,PDO::PARAM_INT);
            // Execute
            $pPDOStatement->execute();
            // Fetch
            $iReturn = array();
            $iIndex = 0;
            while( $tRow = $pPDOStatement->fetch(PDO::FETCH_ASSOC) )
            {
                $iReturn[$tRow['label']] = $tRow['count'];
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

?>
