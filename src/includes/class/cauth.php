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
 * description: describes an authenticated user
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-11 - update SetUsername()
 * update: Olivier JULLIEN - 2010-06-15 - update Sanitize()
 *                                        update SetUsername()
 *                                        update GetUsername()
 *                                        update GetSession()
 *                                        update SetAuthentication()
 *                                        update IsValid()
 *                                        add SetLanguage()
 *                                        add GetLanguage()
 *                                        add SetForceDesktop()
 *                                        add GetForceDesktop()
 *************************************************************************/
if (!defined('PBR_VERSION'))
    die('-1');

final class CAuth
{

    /** Constant
     ***********/
    const DEFAULT_USER     = 'visitor';
    const DEFAULT_SESSION  = '1';
    const DEFAULT_LANGUAGE = 'fr';

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Username
    private $m_sUsername = '';

    // Session id
    private $m_sSession = '';

    // User db identifier (used only in "no stored procedure" mode)
    private $m_iDBIdentifier = 0;

    // Authentified
    private $m_bDBAuth = FALSE;

    // Language
    private $m_sLanguage = CAuth::DEFAULT_LANGUAGE;

    // Force desktop when using a mobile
    private $m_bForceDesktop = FALSE;

    /** Private methods
     ******************/

    /**
     * function: __construct
     * description: constructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function __construct()
    {
        $this->Invalidate();
    }

    /**
     * function: Sanitize
     * description: return true if the data are valid
     * parameter: STRING|sValue - value to test
     *            STRING|sFilter - regular expression
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    private function Sanitize($sValue, $sFilter = '')
    {
        $sReturn = '';
        if (is_scalar($sValue) && is_scalar($sFilter)) {
            // Trim
            $sReturn = trim($sValue);
            // Authorized caracteres
            if (!empty($sFilter)) {
                if (0 == preg_match($sFilter, $sReturn)) {
                    $sReturn = '';
                } //if( 0==preg_match( $sFilter, $sReturn) )
            } //if( !empty($sFilter) )
        } //if( is_scalar($sValue) && is_scalar($sFilter) )
        return $sReturn;
    }

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
        $this->Invalidate();
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
            self::$m_pInstance = new CAuth();
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
     * function: GetUsername
     * description: return the Username
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    public function GetUsername($iFilter = 0)
    {
        return ((1 == $iFilter) ? htmlentities($this->m_sUsername, ENT_QUOTES, 'UTF-8') : $this->m_sUsername);
    }

    /**
     * function: SetUsername
     * description: set username value
     * parameter: STRING|sValue - username
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    public function SetUsername($sValue)
    {
        $this->UnsetAuthentication();
        $this->m_sUsername = $this->Sanitize($sValue, GetRegExPatternName());
    }

    /**
     * function: GetSession
     * description: return the session value
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    public function GetSession()
    {
        return $this->m_sSession;
    }

    /**
     * function: SetSession
     * description: set session value
     * parameter: STRING|sValue - session
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use GetRegExPatternSession
     */
    public function SetSession($sValue)
    {
        $this->UnsetAuthentication();
        $this->m_sSession = $this->Sanitize($sValue, GetRegExPatternSession());
    }

    /**
     * function: GetLanguage
     * description: return the language value
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetLanguage()
    {
        return $this->m_sLanguage;
    }

    /**
     * function: SetLanguage
     * description: Set session value
     * parameter: STRING|sValue - language
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function SetLanguage($sValue)
    {
        $this->m_sLanguage = $this->Sanitize($sValue, GetRegExPatternSession());
        if (empty($this->m_sLanguage))
            $this->m_sLanguage = CAuth::DEFAULT_LANGUAGE;
    }

    /**
     * function: GetForceDesktop
     * description: return the force desktop value
     * parameter: none
     * return:BOOLEAN
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetForceDesktop()
    {
        return $this->m_bForceDesktop;
    }

    /**
     * function: SetForceDesktop
     * description: Set force desktop value
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function SetForceDesktop()
    {
        $this->m_bForceDesktop = !$this->m_bForceDesktop;
    }

    /**
     * function: GetUserBDIdentifier
     * description: return user db identifier
     *              (used in the "no stored procedure" mode
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetUserBDIdentifier()
    {
        return (int)$this->m_iDBIdentifier;
    }

    /**
     * function: IsAuthenticated
     * description: return true if the user is authenticated and valid
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsAuthenticated()
    {
        return ($this->IsValid() && $this->m_bDBAuth);
    }

    /**
     * function: SetAuthentication
     * description: set DB authentiiation to TRUE
     * parameter: INTEGER|iUserId - user db identifier
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    public function SetAuthentication($iUserId)
    {
        $this->UnsetAuthentication();
        if (is_integer($iUserId) && ($iUserId > 0)) {
            $this->m_bDBAuth       = TRUE;
            $this->m_iDBIdentifier = $iUserId;
        } //if( is_integer($iUserId) && ($iUserId>0) )
    }

    /**
     * function: UnsetAuthentication
     * description: set DB authentication to FALSE
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function UnsetAuthentication()
    {
        $this->m_bDBAuth       = FALSE;
        $this->m_iDBIdentifier = 0;
    }

    /**
     * function: IsValid
     * description: return true if username and session are set
     * parameter:
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - recode
     */
    public function IsValid()
    {
        return ((strlen($this->m_sSession) > 0) && (strlen($this->m_sUsername) > 0));
    }

    /**
     * function: Invalidate
     * description: unset variables
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Invalidate()
    {
        $this->UnsetAuthentication();
        $this->m_sUsername = '';
        $this->m_sSession = '';
    }
}

define('PBR_AUTH_LOADED', 1);
