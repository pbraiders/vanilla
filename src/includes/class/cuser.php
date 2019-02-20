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
 * description: describes an user
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CUser
{

    /** Constant
     ***********/
    const DEFAULT_USER='visitor';
    const DEFAULT_SESSION='1';

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Username
    private $m_sUsername = NULL;

    // Session id
    private $m_sSession = NULL;

    // User db identifier (used only in "no stored procedure" mode)
    private $m_iIdentifier = 0;

    // Authentified
    private $m_bAuth=FALSE;

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
     * return: BOOLEAN| true or false
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function Sanitize($sValue, $sFilter='')
    {
        $bReturn=FALSE;
        if( is_scalar($sValue) && is_scalar($sFilter) )
        {
            if( !empty($sFilter) )
            {
                if( preg_match($sFilter,$sValue)>0 )
                {
                    $bReturn=TRUE;
                }//if( preg_match($sFilter,$sValue) )
            }
            else
            {
                $bReturn=TRUE;
            }//if( !empty($sFilter) )
        }//if( is_scalar($sValue) )
        return $bReturn;
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
     */
    public function __clone()
    {
        trigger_error( 'Attempting to clone CUser', E_USER_NOTICE );
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
            self::$m_pInstance = new CUser();
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
     * function: GetUsername
     * description: return the Username
     * parameter: none
     * return: STRING or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetUsername()
    {
        return (!is_null($this->m_sUsername)?$this->m_sUsername:FALSE);
    }

   /**
     * function: SetUsername
     * description: set username value
     * parameter: STRING|sValue - username
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetUsername( $sValue )
    {
        $this->UnsetAuthentication();
        $this->m_sUsername=NULL;
        if( $this->Sanitize($sValue,'/^[[:alnum:]@\.\-_éèêëẽēÉÈÊËẼĒáàâäãāåÁÀÂÄÃĀÅíìîïĩīÍÌÎÏĨĪúùûüũūÚÙÛÜŨŪóòôöõðōÓÒÔÖÕÐŌýÿÝŸçÇñÑœŒ]+$/')===TRUE )
        {
            $this->m_sUsername = $sValue;
        }//if( $this->Sanitize($sValue)===TRUE )
    }

    /**
     * function: GetSession
     * description: return the session value
     * parameter: none
     * return: STRING or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetSession()
    {
        return (!is_null($this->m_sSession)?$this->m_sSession:FALSE);
    }

   /**
     * function: SetSession
     * description: set session value
     * parameter: STRING|sValue - session
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetSession( $sValue )
    {
        $this->UnsetAuthentication();
        $this->m_sSession=NULL;
        if( $this->Sanitize($sValue,'/^[[:alnum:]_]+$/')===TRUE )
        {
            $this->m_sSession = $sValue;
        }//if( $this->Sanitize($sValue)===TRUE )
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
        return (integer)$this->m_iIdentifier;
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
        return $this->IsValid() && $this->m_bAuth;
    }

   /**
     * function: SetAuthentication
     * description: set authentiiation to true
     * parameter: INTEGER|iUserId - user db identifier
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetAuthentication($iUserId)
    {
    	if( is_integer($iUserId) && ($iUserId>0) )
        {
        	$this->m_bAuth = TRUE;
        	$this->m_iIdentifier=$iUserId;
        }
        else
        {
        	$this->m_bAuth = FALSE;
            $this->m_iIdentifier=0;
        }//if( is_integer($iUserId) && ($iUserId>0) )
    }

   /**
     * function: UnsetAuthentication
     * description: set authentiiation to false
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function UnsetAuthentication()
    {
    	$this->m_iIdentifier=0;
        $this->m_bAuth = FALSE;
    }

   /**
     * function: IsValid
     * description: return true if username and session are set
     * parameter:
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsValid()
    {
        return (!is_null($this->m_sSession) && !is_null($this->m_sUsername) );
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
        $this->m_sUsername=NULL;
        $this->m_sSession=NULL;
    }
}
?>
