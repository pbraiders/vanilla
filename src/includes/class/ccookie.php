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
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-11 - update Sanitize()
 * update: Olivier JULLIEN - 2010-06-15 - add PBR_LIFETIME_COOKIE constant
 *                                        add FORCEDESKTOP and LANGUAGE contants
 *                                        update __construct)
 *                                        update Sanitize()
 *                                        add SanitizeInt()
 *                                        update Read()
 *                                        update Write()
 *************************************************************************/
if (!defined('PBR_VERSION') || !defined('PBR_LIFETIME_COOKIE'))
    die('-1');

/** Class
 ********/
final class CCookie
{

    /** Contants
     ***********/
    const USER = 'user';
    const SESSION = 'session';
    const LANGUAGE = 'language';
    const FORCEDESKTOP = 'forcedesktop';

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Name
    private $m_sName = 'pbrvanilla132';

    // Expire
    private $m_iExpire = 0;

    // Path
    private $m_sPath = '/';

    // Domain
    private $m_sDomain = '';

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
     * update: Olivier JULLIEN - 2010-06-15 - use of PBR_LIFETIME_COOKIE constant to initialize m_iExpire
     */
    private function __construct()
    {
        $this->m_iExpire = $this->SanitizeInt(PBR_LIFETIME_COOKIE);
    }

    /**
     * function: SanitizeInt
     * description: return sanitized integer value
     * parameter: INTEGER|iValue - value to sanitize
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - fixed minor bug
    /**
     * function: SanitizeInt
     * description: return sanitized integer value
     * parameter: INTEGER|iValue - value to sanitize
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function SanitizeInt($iValue)
    {
        $iReturn = 0;
        if (is_string($iValue)) {
            $iValue = trim($iValue);
        } //if( is_string($iValue) )
        if (is_numeric($iValue)) {
            $iValue = $iValue + 0;
        } //if( is_numeric($iValue) )
        if (is_integer($iValue)) {
            $iReturn = $iValue;
        } //if( is_integer($iValue) )
        return $iReturn;
    }

    /**
     * function: Sanitize
     * description: return true if the data are valid
     * parameter: STRING|sValue - value to test
     * parameter: STRING|sFilter - regular expression
     * return: BOOLEAN| true or false
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - add regular expression parameter
     */
    private function Sanitize($sValue, $sFilter)
    {
        $bReturn = FALSE;
        if (is_scalar($sValue) && is_scalar($sFilter)) {
            // Trim
            $sValue = trim($sValue);
            // Authorized caracteres
            if (preg_match($sFilter, $sValue)) {
                $bReturn = TRUE;
            } //if( preg_match($sFilter,$sValue) )
        } //if( is_scalar($sValue) && is_scalar($sFilter) )
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
    public function __destruct()
    {
    }

    /**
     * function: __clone
     * description: cloning is forbidden
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-05-24 - Remove trigger_error
     */
    public function __clone()
    {
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
        if (is_null(self::$m_pInstance)) {
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
        if (!is_null(self::$m_pInstance)) {
            $tmp = self::$m_pInstance;
            self::$m_pInstance = NULL;
            unset($tmp);
        }
    }

    /**
     * function: Read
     * description: Read the cookie.
     * parameter: none
     * return: FALSE or array('username','sessionid')
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - add LANGUAGE and FORCEDESKTOP constant
     */
    public function Read()
    {
        $tabReturn = FALSE;
        if (filter_has_var(INPUT_COOKIE, $this->m_sName)) {
            $tabReturn = array();
            list($tabReturn[CCookie::USER], $tabReturn[CCookie::SESSION], $tabReturn[CCookie::LANGUAGE], $tabReturn[CCookie::FORCEDESKTOP]) = @unserialize($_COOKIE[$this->m_sName]);
            if (($this->Sanitize($tabReturn[CCookie::USER], GetRegExPatternName()) === FALSE)
                || ($this->Sanitize($tabReturn[CCookie::SESSION], GetRegExPatternSession()) === FALSE)
                || ($this->Sanitize($tabReturn[CCookie::LANGUAGE], GetRegExPatternSession()) === FALSE)
            ) {
                $tabReturn = FALSE;
            } else {
                // Format force desktop value
                if (isset($tabReturn[CCookie::FORCEDESKTOP]) && ($tabReturn[CCookie::FORCEDESKTOP] == 1)) {
                    $tabReturn[CCookie::FORCEDESKTOP] = TRUE;
                } else {
                    $tabReturn[CCookie::FORCEDESKTOP] = FALSE;
                } //Format force desktio value
            } //if( ($this->Sanitize(///
        } //if( filter_has_var(///
        return $tabReturn;
    }

    /**
     * function: Write
     * description: Write a cookie.
     * parameter: STRING|sUsername  - user name
     *            STRING|sSessionId - session id
     *            STRING|sLanguage  - language
     *           BOOLEAN|bForceDesk - force desktop
     *           INTEGER|iExpire    - life time in seconds (optionnal)
     *            see Sanitize function for allowed charateres
     * return: FALSE or TRUE
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - redefine Expire time parameter
     *                                        add language and force reload parameters
     */
    public function Write($sUsername, $sSessionId, $sLanguage, $bForceDesk, $iExpire = NULL)
    {
        $bReturn = FALSE;

        if (($this->Sanitize($sUsername, GetRegExPatternName()) === TRUE)
            && ($this->Sanitize($sSessionId, GetRegExPatternSession()) === TRUE)
            && ($this->Sanitize($sLanguage, GetRegExPatternSession()) === TRUE)
            && is_bool($bForceDesk)
        ) {
            // Default expiration time
            if (!is_int($iExpire)) {
                $iExpire = time() + $this->m_iExpire;
            } else {
                $iExpire = time() + $iExpire;
            } //if( !is_int($iExpire) )
            // Force desktop
            if ($bForceDesk == TRUE)
                $iForceDesk = 1;
            else
                $iForceDesk = 0;
            // Send cookie
            $bReturn = setcookie(
                $this->m_sName,
                @serialize(array($sUsername, $sSessionId, $sLanguage, $iForceDesk)),
                $iExpire,
                $this->m_sPath,
                $this->m_sDomain,
                $this->m_iSecure,
                true
            );
        } // if...
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

define('PBR_COOKIE_LOADED', 1);
