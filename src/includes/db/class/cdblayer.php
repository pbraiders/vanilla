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
 *************************************************************************/
/*************************************************************************
 * file encoding: UTF-8
 * description: database layer
 * author: Olivier JULLIEN - 2010-05-24
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 *                                        update Open()
 *                                        update and rename ErrorInsert()
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/** Class
 ********/
final class CDBLayer
{

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Driver instance
    private $m_pDriver = NULL;

    // Transaction state
    private $m_bInTransaction = FALSE;

    /** Private methods
     ******************/

    /**
     * function: __construct
     * description: constructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function __construct(){}

    /** Public methods
     *****************/

    /**
     * function: __destruct
     * description: destructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __destruct()
    {
        $this->Close();
    }

   /**
     * function: __clone
     * description: cloning is forbidden
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-05-24 - Remove trigger_error
     */
    public function __clone(){}

   /**
     * function: GetInstance
     * description: create or return the current instance
     * parameter: none
     * return: this
     * author: Olivier JULLIEN - 2010-02-04
     */
    public static function GetInstance()
    {
        if( is_null(self::$m_pInstance) )
        {
            self::$m_pInstance = new CDBLayer();
        }
        return self::$m_pInstance;
    }

   /**
     * function: DeleteInstance
     * description: delete the current instance
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public static function DeleteInstance()
    {
        if( !is_null(self::$m_pInstance) )
        {
            $tmp = self::$m_pInstance;
            self::$m_pInstance = NULL;
            unset($tmp);
        }
    }

   /**
     * function: Open
     * description: Open a database connection
     * parameters: STRING|sDSN      - data server name
     *             STRING|sInputUsr - input username
     *             STRING|sInputPwd - input password
     *             STRING|sLogin    - logged user
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::Add(...)
     * update: Olivier JULLIEN - 2010-06-15 - add sLogin parameter
     */
    public function Open($sDSN, $sInputUsr, $sInputPwd, $sLogin)
    {
        // Initialize
        $bReturn = FALSE;

        // Disconnect
        if( $this->IsOpen() )
        {
            $this->Close();
        }// if( $this->IsOpen() )

        // Connect
        if( IsStringNotEmpty($sDSN) && IsStringNotEmpty($sInputUsr)
         && IsStringNotEmpty($sInputPwd) && IsStringNotEmpty($sLogin) )
        {

            try
            {
                // Set options
                $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
                // Create driver instance
                $this->m_pDriver = new PDO($sDSN, $sInputUsr, $sInputPwd, $options);
                // Set error attributes
                $this->m_pDriver->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $bReturn = TRUE;
            }
            catch(PDOException $e)
            {
                $this->Close();
                $sTitle = __CLASS__ . '::' . __METHOD__ . '()';
                ErrorDBLog( $sLogin, $sTitle, $e->getMessage(), FALSE, FALSE);
            }//try
        }//if ...
        return $bReturn;
    }

   /**
     * function: IsOpen
     * description: return true if the connection is active
     * parameter:none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsOpen()
    {
        return !is_null($this->m_pDriver);
    }

   /**
     * function: Close
     * description: Close the connetion
     * parameter:none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Close()
    {
        $this->m_pDriver = NULL;
    }

   /**
     * function: GetDriver
     * description: return the driver instance
     * parameter: none
     * return: pointer or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetDriver()
    {
        if( $this->IsOpen() )
        {
            return $this->m_pDriver;
        }
        else
        {
            return FALSE;
        }
    }

   /**
     * function: GetLastInsertId
     * description: Returns the ID of the last inserted row or sequence value.
     * parameter: STRING|sName  - Name of the sequence object from which the ID should be returned.
     * return: If a sequence name was not specified for the name parameter, PDO::lastInsertId() returns a string
     *          representing the row ID of the last row that was inserted into the database.
     *         or If a sequence name was specified for the name parameter, PDO::lastInsertId() returns a string
     *          representing the last value retrieved from the specified sequence object.
     *         or FALSE if an error occures.
     *         NOTE: SHOULD BE IN TRY-CATCH section.
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetLastInsertId( $sName = NULL )
    {
        $sReturn = '';
        if( $this->IsOpen() )
        {
            $sReturn = $this->m_pDriver->lastInsertId($sName);
            if( is_numeric($sReturn) )
            {
                $sReturn = $sReturn + 0;
            }//if( is_numeric($sReturn) )
        }//if( $this->IsOpen() )
        return $sReturn;
    }

   /**
     * function: BeginTransaction
     * description: Initiates a transaction.
     * parameter: STRING|sLogin - logged user
     * return: Returns TRUE on success or FALSE on failure.
     * author: Olivier JULLIEN - 2010-06-15
    */
    public function BeginTransaction($sLogin)
    {
        $this->m_bInTransaction = FALSE;
        if( $this->IsOpen() )
        {
            try
            {
                $this->m_bInTransaction = $this->m_pDriver->beginTransaction();
            }
            catch(PDOException $e)
            {
                $sTitle = __CLASS__ . '::' . __METHOD__ . '()';
                ErrorDBLog( $sLogin, $sTitle, $e->getMessage(), FALSE, TRUE);
            }//try
        }//if( $this->IsOpen() )
        return $this->m_bInTransaction;
    }

    /**
     * function: Commit
     * description: Commits a transaction.
     * parameter: STRING|sLogin - logged user
     * return: Returns TRUE on success or FALSE on failure.
     * author: Olivier JULLIEN - 2010-06-15
    */
    public function Commit($sLogin)
    {
        $bReturn = FALSE;
        if( ($this->IsOpen()===TRUE) && ($this->m_bInTransaction===TRUE) )
        {
            try
            {
                $bReturn = $this->m_pDriver->commit();
            }
            catch(PDOException $e)
            {
                $sTitle = __CLASS__ . '::' . __METHOD__ . '()';
                ErrorDBLog( $sLogin, $sTitle, $e->getMessage(), FALSE, TRUE);
            }//try
        }//if( ($this->IsOpen()===TRUE) && ($this->m_bInTransaction===TRUE) )
        $this->m_bInTransaction = FALSE;
        return $bReturn;
    }

   /**
     * function: Rollback
     * description: Rolls back a transaction.
     * parameter: STRING|sLogin - logged user
     * return: Returns TRUE on success or FALSE on failure.
     * author: Olivier JULLIEN - 2010-06-15
    */
    public function Rollback($sLogin)
    {
        $bReturn = FALSE;
        if( ($this->IsOpen()===TRUE) && ($this->m_bInTransaction===TRUE) )
        {
            try
            {
                $bReturn = $this->m_pDriver->rollback();
            }
            catch(PDOException $e)
            {
                $sTitle = __CLASS__ . '::' . __METHOD__ . '()';
                ErrorDBLog( $sLogin, $sTitle, $e->getMessage(), FALSE, TRUE);
            }//try
        }//if( ($this->IsOpen()===TRUE) && ($this->m_bInTransaction===TRUE) )
        $this->m_bInTransaction = FALSE;
        return $bReturn;
    }

   /**
     * function: ErrorInsert
     * description: Insert error message in the database
     * parameter: STRING|sDBName      - database name
     *            STRING|sLogin       - logged user
     *            STRING|sType        - error type
     *            STRING|sTitle       - error title
     *            STRING|sDescription - error description
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::Add(...)
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    public function ErrorInsert( $sDBName, $sLogin, $sType, $sTitle, $sDescription)
    {
         // Initialize
        $bReturn = FALSE;

        if( $this->IsOpen() && IsStringNotEmpty($sDBName)
         && IsStringNotEmpty($sLogin) && IsStringNotEmpty($sType)
         && IsStringNotEmpty($sTitle) && IsStringNotEmpty($sDescription) )
        {

            $sType = strtoupper($sType);
            $sSQL='INSERT INTO `'.$sDBName.'`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(),:sLogin,:sType,:sTitle,:sDescription,USER(),CURRENT_USER())';

            try
            {
                // Prepare
                $pPDOStatement = $this->m_pDriver->prepare($sSQL);
                // Bind
                $pPDOStatement->bindValue(':sLogin',$sLogin,PDO::PARAM_STR);
                $pPDOStatement->bindValue(':sType',$sType,PDO::PARAM_STR);
                $pPDOStatement->bindValue(':sTitle',$sTitle,PDO::PARAM_LOB);
                $pPDOStatement->bindValue(':sDescription',$sDescription,PDO::PARAM_LOB);
                // Execute
                $pPDOStatement->execute();
                $bReturn = TRUE;
            }
            catch(PDOException $e)
            {
                $sTitle = __CLASS__ . '::' . __METHOD__ .'()';
                ErrorDBLog( $sLogin, $sTitle, $e->getMessage(), FALSE, FALSE);
            }//try

            $pPDOStatement = NULL;

        }//if ...
        return $bReturn;
    }

   /**
     * function: GetInfo
     * description: Get client info
     * parameter:none
     * return: STRING - sanitized
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetInfo()
    {
        if( function_exists('mysql_get_client_info') ) {
            $sReturn = mysql_get_client_info();
        } else {
            $sReturn = $this->m_pDriver->getAttribute( constant('PDO::ATTR_CLIENT_VERSION') );
        }
        return htmlspecialchars($sReturn);
    }


}

define('PBR_DB_LOADED',1);
