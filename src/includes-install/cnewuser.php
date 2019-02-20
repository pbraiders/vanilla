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
 * author: Olivier JULLIEN - 2010-05-24
 * update: Olivier JULLIEN - 2010-06-11 - update SetUsername()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CNewUser
{
    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Username
    private $m_sUsername = NULL;

    // Password 1
    private $m_sPassword1 = NULL;

    // Password 2
    private $m_sPassword2 = NULL;

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
    	$this->m_sUsername=$this->Sanitize($sValue,GetRegExPatternName());
    }

    /**
     * function: GetPassword
     * description: return the password value
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetPassword(){return $this->m_sPassword1;}

   /**
     * function: SetPassword1
     * description: set password1 value
     * parameter: STRING|sValue - password
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetPassword1($sValue){$this->m_sPassword1=$this->Sanitize($sValue);}

   /**
     * function: SetPassword2
     * description: set password2 value
     * parameter: STRING|sValue - password
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetPassword2($sValue){$this->m_sPassword2=$this->Sanitize($sValue);}

   /**
     * function: IsValidNew
     * description: return true if username and password are set
     * parameter:
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsValidNew()
    {
        return ( !is_null($this->m_sUsername) && (strlen($this->m_sUsername)>0)
        	  && !is_null($this->m_sPassword1) && (strlen($this->m_sPassword1)>0)
              && !is_null($this->m_sPassword2) && (strlen($this->m_sPassword2)>0)
			  && ($this->m_sPassword1===$this->m_sPassword2) );
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
		if( $iFilter===INPUT_POST )
		{
			// Get user name (only POST)
			if( filter_has_var(INPUT_POST,'usr') )
			{
				$this->SetUsername(filter_input(INPUT_POST,'usr',FILTER_UNSAFE_RAW));
			}//if( filter_has_var(INPUT_POST,'usi') )

            // Get Password1 (only POST)
            if( filter_has_var(INPUT_POST,'pwd1') )
            {
                $sBuffer=trim(filter_input(INPUT_POST,'pwd1',FILTER_UNSAFE_RAW));
                if( strlen($sBuffer)>0 )
                {
                    $this->SetPassword1(sha1($sBuffer));
                }
                else
                {
                    $this->m_sPassword1=NULL;
                }//if( strlen($sBuffer)>0 )
            }//if( filter_has_var(INPUT_POST,'pwd1') )

			// Get Password2 (only POST)
			if( filter_has_var(INPUT_POST,'pwd2') )
			{
				$sBuffer=trim(filter_input(INPUT_POST,'pwd2',FILTER_UNSAFE_RAW));
				if( strlen($sBuffer)>0 )
				{
					$this->SetPassword2(sha1($sBuffer));
				}
				else
				{
					$this->m_sPassword2=NULL;
				}//if( strlen($sBuffer)>0 )
			}//if( filter_has_var(INPUT_POST,'pwd2') )

            $bReturn = TRUE;

		}//if( $iFilter===INPUT_POST )

        return $bReturn;
    }

}
define('PBR_NEWUSER_LOADED',1);
?>
