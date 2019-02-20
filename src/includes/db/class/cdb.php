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
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

define('PBR_DB_DIR','function-nosp');

/** Class
 ********/
class CDb
{
    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // PDO instance
    private $m_pPDO = NULL;

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
            self::$m_pInstance = new CDb();
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
            $tmp=self::$m_pInstance;
            self::$m_pInstance=NULL;
            unset($tmp);
        }
    }

   /**
     * function: Open
     * description: Open a database connection
     * parameters: string|sDSN - data server name
     *             string|sUsr - username
     *             string|sPwd - password
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-05-24 - use ErrorDBLog instead of CErrorList::Add(...)
     */
    public function Open($sDSN, $sUsr, $sPwd)
    {
        $bReturn=FALSE;
        // Disconnect
        if( $this->IsOpen() )
        {
            $this->Close();
        }// if( $this->IsOpen() )

        // Connect
        if( isset($sDSN) && is_string($sDSN) && (strlen(trim($sDSN))>0)
         && isset($sUsr) && is_string($sUsr) && (strlen(trim($sUsr))>0)
         && isset($sPwd) && is_string($sPwd) && (strlen(trim($sPwd))>0) )
        {
            try
            {
                // mysql connection
                $options=array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
                $this->m_pPDO = new PDO($sDSN, $sUsr, $sPwd, $options);
                $this->m_pPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $bReturn=TRUE;
            }
            catch(PDOException $e)
            {
		        $sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
		        $sTitle=__CLASS__ . '::' . __METHOD__ . '()';
                ErrorDBLog( $sUser, $sTitle, $e->getMessage(), FALSE, FALSE);
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
        return !is_null($this->m_pPDO);
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
        $this->m_pPDO=NULL;
    }

   /**
     * function: PDO
     * description: return the pdo instance
     * parameter: none
     * return: pdo or false
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function PDO()
    {
        if( $this->IsOpen() )
        {
            return $this->m_pPDO;
        }
        else
        {
            return FALSE;
        }
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
     */
    public function ErrorInsert( $sDBName, $sLogin, $sType, $sTitle, $sDescription)
    {
        $bReturn=FALSE;
        $sType=strtoupper($sType);
        $sSQL='INSERT INTO `'.$sDBName.'`.`log`(`logged`,`username`,`type`,`title`,`description`,`mysqluser`,`mysqlcurrentuser`) VALUES (SYSDATE(),:sLogin,:sType,:sTitle,:sDescription,USER(),CURRENT_USER())';

        if( $this->IsOpen() )
        {
            try
            {
                // Prepare
                $pPDOStatement = $this->m_pPDO->prepare($sSQL);
                // Bind
                $pPDOStatement->bindParam(':sLogin',$sLogin,PDO::PARAM_STR,45);
                $pPDOStatement->bindParam(':sType',$sType,PDO::PARAM_STR,15);
                $pPDOStatement->bindParam(':sTitle',$sTitle,PDO::PARAM_LOB);
                $pPDOStatement->bindParam(':sDescription',$sDescription,PDO::PARAM_LOB);
                // Execute
                $pPDOStatement->execute();
                $bReturn=TRUE;
            }
            catch(PDOException $e)
            {
		        $sUser=(CUser::GetInstance()->GetUsername()===FALSE?CUser::DEFAULT_USER:CUser::GetInstance()->GetUsername());
		        $sTitle= __CLASS__ . '::' . __METHOD__ .'()';
                ErrorDBLog( $sUser, $sTitle, $e->getMessage(), FALSE, FALSE);
            }//try
            $pPDOStatement=NULL;
        }//if( $this->IsOpen() )
        return $bReturn;
    }

}
define('PBR_DB_LOADED',1);
?>
