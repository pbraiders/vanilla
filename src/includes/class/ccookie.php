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
 * description: describe a cookie
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/** Class
 ********/
class CCookie
{

    /** Contants
     ***********/
    const USER='user';
    const SESSION='session';

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Name
    private $m_sName='pbraiders';

    // Expire
    private $m_iExpire=36000;

    // Path
    private $m_sPath='/';

    // Domain
    private $m_sDomain='';

    // Secure
    private $m_iSecure = 0;

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
            if( preg_match('/^[[:alnum:]@\.\-_]+$/',$sValue) )
            {
                $bReturn=TRUE;
            }//if( preg_match('/^[[:alnum:]@\.\-_]+$/',$sValue) )
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
     */
    public function __clone()
    {
        trigger_error( 'Attempting to clone CCookie', E_USER_NOTICE );
    }

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
            self::$m_pInstance = new CCookie();
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
     * function: Read
     * description: Read the cookie.
     * parameter: none
     * return: FALSE or array('username','sessionid')
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Read()
    {
        $tabReturn = FALSE;
        if( filter_has_var(INPUT_COOKIE,$this->m_sName) )
        {
            $tabReturn = array();
            list($tabReturn[CCookie::USER],$tabReturn[CCookie::SESSION]) = @unserialize( $_COOKIE[$this->m_sName] );
            if( ($this->Sanitize($tabReturn[CCookie::USER])===FALSE) ||
                ($this->Sanitize($tabReturn[CCookie::SESSION])===FALSE) )
            {
                $tabReturn = FALSE;
            }
        }// if...
        return $tabReturn;
    }

    /**
     * function: Write
     * description: Write a cookie.
     * parameter: STRING|sUsername  - user name
     *            STRING|sSessionId - session id
     *           INTEGER|iExpire    - expire time in seconds (optionnal)
     *            see Sanitize function for allowed charateres
     * return: FALSE or TRUE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Write($sUsername, $sSessionId, $iExpire=NULL)
    {
        $bReturn = FALSE;
        if( ($this->Sanitize($sUsername)===TRUE) &&
            ($this->Sanitize($sSessionId)===TRUE) )
        {
            // Default expiration time
            if( !is_int($iExpire) )
			{
	            $iExpire = time() + $this->m_iExpire;
            }//if( !is_int($iExpire) )
            // Send cookie
            $bReturn = setcookie($this->m_sName
                                ,@serialize( array($sUsername,$sSessionId) )
                                ,$iExpire
                                ,$this->m_sPath.'; HttpOnly'
                                ,$this->m_sDomain
                                ,$this->m_iSecure);
        }// if...
        return $bReturn;
    }

    /**
     * function: Delete
     * description: Delete a cookie.
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Delete()
    {
        setcookie($this->m_sName);
        unset($_COOKIE[$this->m_sName]);
    }

}
?>
