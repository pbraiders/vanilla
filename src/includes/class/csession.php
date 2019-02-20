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
 * description: Contains temporary session values
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - Update __clone()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/** Class
 ********/
class CSession
{

    /** Contants
     ***********/
    const TOKEN='token';

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    /** Private methods
     ******************/

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function __construct(){}

    /**
     * function: Sanitize
     * description: return true if the data are valid
     * parameter: STRING|sValue - value to test
     * return: BOOLEAN| true or false
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function Sanitize($sValue)
    {
        $bReturn=FALSE;
        if( is_scalar($sValue) )
        {
            if( preg_match('/^[[:alnum:]\-_\.]+$/',$sValue) )
            {
                $bReturn=TRUE;
            }//if( preg_match(...
        }//if( is_scalar($sValue) )
        return $bReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __destruct
     * description: destructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __destruct(){}

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
            self::$m_pInstance = new CSession();
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
     * function: GetToken
     * description: return the token value
     * parameter: none
     * return: STRING or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetToken()
    {
        $bReturn = FALSE;
        if( isset($_SESSION) )
        {
            $bReturn = (isset($_SESSION[CSession::TOKEN])?$_SESSION[CSession::TOKEN]:FALSE);
        }//if( isset($_SESSION) )
        return $bReturn;
    }

   /**
     * function: SetToken
     * description: Set the token value
     * parameter: STRING|sValue - value of the token
     *            see Sanitize function for allowed charaters
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetToken( $sValue )
    {
        if( isset($_SESSION) )
        {
            $this->CleanToken();
            if( $this->Sanitize($sValue)===TRUE )
            {
                $_SESSION[CSession::TOKEN] = $sValue;
            }//if( $this->Sanitize($sValue)===TRUE )
        }//if( isset($_SESSION) )
    }

   /**
     * function: Clean
     * description: destroy all session values
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public static function Clean()
    {
        session_unset();
        session_destroy();
    }

   /**
     * function: CleanToken
     * description: destroy token value
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public static function CleanToken()
    {
        if( isset($_SESSION) )
        {
            unset($_SESSION[CSession::TOKEN]);
        }// if( isset($_SESSION) )
    }

   /**
     * function: CreateSession
     * description: create the session
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public static function CreateSession()
    {
        session_name('pbrsessid');
        session_start();
    }

}
define('PBR_SESSION_LOADED',1);
?>
