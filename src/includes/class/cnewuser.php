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
 * description: describes a new user
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - Update __clone()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CNewUser
{
    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // User identifier
    private $m_iIdentifier = 0;

    // Username
    private $m_sUsername = NULL;

    // Password
    private $m_sPassword = NULL;

    // State
    private $m_iState=0;

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

    /**
     * function: SanitizeInt
     * description: return sanitized integer value
     * parameter: INTEGER|iValue - value to sanitize
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function SanitizeInt($iValue)
    {
        $iReturn=0;
        if( is_numeric($iValue) )
        {
            $iReturn=$iValue+0;
        }//if( is_numeric($iValue) )
        return $iReturn;
    }

    /**
     * function: Sanitize
     * description: return sanitized value
     * parameter: STRING|sValue  - value to sanitize
     *            STRING|sFilter - regex filter
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function Sanitize($sValue, $sFilter='')
    {
        $sReturn=NULL;
        if( is_scalar($sValue) && is_scalar($sFilter) )
        {
			// Trim
			$sReturn = trim($sValue);
            // Authorized caracteres
            if( !empty($sFilter) )
            {
                if( 0==preg_match($sFilter,$sValue) )
                {
                    $sReturn=NULL;
                }//if( 0==preg_match($sFilter,$sValue) )
            }//if( !empty($sFilter) )
        }//if( is_scalar($sValue) && is_scalar($sFilter) )
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
            self::$m_pInstance = new CNewUser();
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
     * parameter: INTEGER|iFilter - 1 if characters should be converted into html entities
     * return: STRING or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetUsername($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sUsername,ENT_COMPAT,'UTF-8'):$this->m_sUsername);}

   /**
     * function: SetUsername
     * description: set username value
     * parameter: STRING|sValue - username
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetUsername( $sValue )
    {
    	$this->m_sUsername=$this->Sanitize($sValue,'/^[[:alnum:]@\.\-_éèêëẽēÉÈÊËẼĒáàâäãāåÁÀÂÄÃĀÅíìîïĩīÍÌÎÏĨĪúùûüũūÚÙÛÜŨŪóòôöõðōÓÒÔÖÕÐŌýÿÝŸçÇñÑœŒ]+$/');
    }

    /**
     * function: GetPassword
     * description: return the password value
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetPassword(){return $this->m_sPassword;}

   /**
     * function: SetPassword
     * description: set password value
     * parameter: STRING|sValue - password
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetPassword($sValue){$this->m_sPassword=$this->Sanitize($sValue);}

    /**
     * function: GetIdentifier
     * description: return user identifier
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetIdentifier(){return (integer)$this->m_iIdentifier;}

    /**
     * function: SetIdentifier
     * description: Set user identifier
     * parameter: INTEGER|iValue - identifier
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetIdentifier($iValue){$this->m_iIdentifier=$this->SanitizeInt($iValue);}

   /**
     * function: GetState
     * description: return user state
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetState(){return (integer)$this->m_iState;}

    /**
     * function: SetState
     * description: Set user state
     * parameter: INTEGER|iValue - state
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetState($iValue)
    {
    	$this->m_iState=$this->SanitizeInt($iValue);
        if( ($this->m_iState<0) || ($this->m_iState>1) )
        {
        	$this->m_iState=0;
        }//if( (m_iState<0) || (m_iState>1) )
    }

   /**
     * function: IsValidNew
     * description: return true if username and password are set
     * parameter:
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsValidNew()
    {
        return (!is_null($this->m_sUsername) && (strlen($this->m_sUsername)>0)
             && !is_null($this->m_sPassword) && (strlen($this->m_sPassword)>0) );
    }

   /**
     * function: IsValidUpdate
     * description: return true if username and identifier are set
     * parameter:
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsValidUpdate()
    {
        return (!is_null($this->m_sUsername) && (strlen($this->m_sUsername)>0) && ($this->m_iIdentifier>0) );
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input new user values
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ReadInput($iFilter)
    {
        $bReturn = FALSE;
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            // Get identifier
            if( filter_has_var($iFilter,'usi') )
            {
                $this->SetIdentifier(filter_input($iFilter,'usi',FILTER_VALIDATE_INT));
            }//if( filter_has_var($iFilter,'usi') )

            if( $iFilter===INPUT_POST )
            {
            	// Get user name (only POST)
	            if( filter_has_var(INPUT_POST,'usr') )
	            {
	            	$this->SetUsername(filter_input(INPUT_POST,'usr',FILTER_UNSAFE_RAW));
                }//if( filter_has_var(INPUT_POST,'usi') )

            	// Get Password (only POST)
	            if( filter_has_var(INPUT_POST,'pwd') )
	            {
                    $sBuffer=trim(filter_input(INPUT_POST,'pwd',FILTER_UNSAFE_RAW));
                    if( strlen($sBuffer)>0 )
                    {
    	            	$this->SetPassword(sha1($sBuffer));
                    }
                    else
                    {
                        $this->m_sPassword=NULL;
                    }//if( strlen($sBuffer)>0 )
                }//if( filter_has_var(INPUT_POST,'usi') )

	            // Get State (only POST)
	            if( filter_has_var(INPUT_POST,'sta') )
	            {
                	$tFilter = array('options' => array('min_range' => 0, 'max_range' => 1));
	            	$this->SetState(filter_input(INPUT_POST,'sta',FILTER_VALIDATE_INT,$tFilter));
                }//if( filter_has_var(INPUT_POST,'usi') )
             }//if( $iFilter===INPUT_POST )

            $bReturn = TRUE;

        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        return $bReturn;
    }

}
define('PBR_NEWUSER_LOADED',1);
?>
