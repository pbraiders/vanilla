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
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-11 - add password check field
 *                                        add SetPasswordCheck()
 *                                        update IsValidNew()
 *                                        update IsValidUpdate()
 *                                        update ReadInput()
 *                                        update SetUsername()
 * update: Olivier JULLIEN - 2010-06-15 - is not a singleton anymore
 *                                        delete GetInstance()
 *                                        delete DeleteInstance()
 *                                        delete __clone()
 *                                        add constants
 *                                        update GetUsername()
 *                                        update Sanitize()
 *                                        update SanitizeInt()
 *                                        update ReadInput()
 *                                        update SetUsername()
 *                                        update SetPassword()
 *                                        update SetUsernameCheck()
 *                                        add ResetMe()
 *                                        add IsValidLogin()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CUser
{

    /** Contants
     ***********/
    const IDENTIFIERTAG = 'usi';
    const IDENTIFIERMIN = 0;
    const IDENTIFIERMAX = 65535;

    const USERNAMETAG = 'usr';
    const USERNAMEMIN = 1;
    const USERNAMEMAX = 45;

    const PASSWORDTAG      = 'pwd';
    const PASSWORDCHECKTAG = 'pwdc';
    const PASSWORDMIN      = 1;
    const PASSWORDMAX      = 40;

    const STATETAG = 'sta';
    const STATEMIN = 0;
    const STATEMAX = 1;

    /** Private attributs
     ********************/

    // User identifier
    private $m_iIdentifier = CUser::IDENTIFIERMIN;

    // Username
    private $m_sUsername = '';

    // Password
    private $m_sPassword = '';

    // Password check
    private $m_sPasswordCheck = '';

    // State
    private $m_iState = CUser::STATEMIN;

    /** Private methods
     ******************/

    /**
     * function: SanitizeInt
     * description: return sanitized integer value
     * parameter: INTEGER|iValue - value to sanitize
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - fixed minor bug
     *                                        add min, max and default values
     */
    private function SanitizeInt( $iValue, $iMin, $iMax, $iDefault)
    {
        $iReturn = $iDefault;
        if( is_scalar($iValue) && is_integer($iMin) && is_integer($iMax) )
        {
            if( is_string($iValue) )
            {
                $iValue = trim($iValue);
            }//if( is_string($iValue) )
            if( is_numeric($iValue) )
            {
                $iValue = $iValue + 0;
            }//if( is_numeric($iValue) )
            if( is_integer($iValue) && ($iValue>=$iMin) && ($iValue<=$iMax) )
            {
                $iReturn = $iValue;
            }//if( is_integer($iValue) && ($iValue>=$iMin) && ($iValue<=$iMax) )
        }//if( is_scalar($iValue) && is_integer($iMin) && is_integer($iMax) )
        return $iReturn;
    }

    /**
     * function: Sanitize
     * description: return sanitized value
     * parameter: STRING|sValue  - value to sanitize
     *            STRING|sFilter - regex filter
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - fixed minor bug
     *                                        add min and max length
     */
    private function Sanitize( $sValue, $iMin, $iMax, $sFilter='' )
    {
        $sReturn = '';
        if( is_scalar($sValue) && is_scalar($sFilter) && is_integer($iMin) && is_integer($iMax) )
        {
            // Trim
            $sValue = trim($sValue);
            // Size
            $iSize = mb_strlen( $sValue, 'UTF-8');
            if( ($iSize>=$iMin) && ($iSize<=$iMax) )
            {
                $sReturn = $sValue;
                // Authorized caracteres
                if( !empty($sFilter) )
                {
                    if( 0==preg_match( $sFilter, $sReturn) )
                    {
                        $sReturn = '';
                    }//if( 0==preg_match( $sFilter, $sReturn) )
                }//if( !empty($sFilter) )
            }//if( ($iSize>=$iMin) && ($iSize<=$iMax) )
        }//if(...
        return $sReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __construct(){}

    /**
     * function: __destruct
     * description: destructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __destruct(){}

    /**
     * function: GetUsername
     * description: return the Username
     * parameter: INTEGER|iFilter - 1 if characters should be converted into html entities
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     */
    public function GetUsername($iFilter=0)
    {
        return ((1==$iFilter)?htmlentities($this->m_sUsername,ENT_QUOTES,'UTF-8'):$this->m_sUsername);
    }

   /**
     * function: SetUsername
     * description: set username value
     * parameter: STRING|sValue - username
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - add min and max check
     */
    public function SetUsername( $sValue )
    {
        $this->m_sUsername = $this->Sanitize( $sValue, CUser::USERNAMEMIN, CUser::USERNAMEMAX,GetRegExPatternName() );
    }

    /**
     * function: GetPassword
     * description: return the password value
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetPassword()
    {
        return $this->m_sPassword;
    }

   /**
     * function: SetPassword
     * description: set password value
     * parameter: STRING|sValue - password
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - add min and max check
     */
    public function SetPassword($sValue)
    {
        $this->m_sPassword = $this->Sanitize($sValue, CUser::PASSWORDMIN, CUser::PASSWORDMAX);
    }

    /**
     * function: GetPasswordCheck
     * description: return the password check value
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetPasswordCheck()
    {
        return $this->m_sPasswordCheck;
    }

   /**
     * function: SetPasswordCheck
     * description: set password check value
     * parameter: STRING|sValue - password
     * return: none
     * author: Olivier JULLIEN - 2010-06-11
     * update: Olivier JULLIEN - 2010-06-15 - add min and max check
     */
    public function SetPasswordCheck($sValue)
    {
        $this->m_sPasswordCheck = $this->Sanitize($sValue, CUser::PASSWORDMIN, CUser::PASSWORDMAX);
    }

    /**
     * function: GetIdentifier
     * description: return user identifier
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetIdentifier()
    {
        return (integer)$this->m_iIdentifier;
    }

    /**
     * function: SetIdentifier
     * description: Set user identifier
     * parameter: INTEGER|iValue - identifier
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetIdentifier($iValue)
    {
        $this->m_iIdentifier = $this->SanitizeInt( $iValue, CUser::IDENTIFIERMIN, CUser::IDENTIFIERMAX, 0);
    }

   /**
     * function: GetState
     * description: return user state
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetState()
    {
        return (integer)$this->m_iState;
    }

    /**
     * function: SetState
     * description: Set user state
     * parameter: INTEGER|iValue - state
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetState($iValue)
    {
        $this->m_iState = $this->SanitizeInt( $iValue, CUser::STATEMIN, CUser::STATEMAX, CUser::STATEMIN);
    }

   /**
     * function: IsValidLoguin
     * description: return true if username and passwords are set
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function IsValidLogin()
    {
        return ( (strlen($this->m_sUsername)>0)
              && (strlen($this->m_sPassword)>0) );
    }

   /**
     * function: IsValidNew
     * description: return true if username and passwords are set and valid for a creation
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-11 - add password check
     */
    public function IsValidNew()
    {
        return ( (strlen($this->m_sUsername)>0)
              && (strlen($this->m_sPassword)>0)
              && (strlen($this->m_sPasswordCheck)>0)
              && ($this->m_sPassword===$this->m_sPasswordCheck) );
    }

   /**
     * function: IsValidUpdate
     * description: return true if username, identifier and passwords are set and valid for an update
     * parameter:
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-11 - add password check
     */
    public function IsValidUpdate()
    {
        // Check name and identifier
        $bReturn = ( (strlen($this->m_sUsername)>0) && ($this->m_iIdentifier>0) );
        // Check password
        if( strlen($this->m_sPassword)>0 )
        {
            $bReturn = $bReturn && ($this->m_sPassword===$this->m_sPasswordCheck);
        }
        elseif( strlen($this->m_sPasswordCheck)>0 )
        {
            $bReturn = FALSE;
        }//if( strlen($this->m_sPassword)>0 )
        return $bReturn;
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input new user values
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - add constants
     */
    public function ReadInput($iFilter)
    {
        $bReturn = FALSE;
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            // Get identifier
            if( filter_has_var( $iFilter, CUser::IDENTIFIERTAG) )
            {
                $tFilter = array('options' => array('min_range'=>CUser::IDENTIFIERMIN,
                                                    'max_range'=>CUser::IDENTIFIERMAX) );
                $this->SetIdentifier(filter_input($iFilter,CUser::IDENTIFIERTAG,FILTER_VALIDATE_INT,$tFilter));
            }//if( filter_has_var( $iFilter, CUser::IDENTIFIERTAG) )

            // Get user name, passwords and state (POST only)
            if( $iFilter===INPUT_POST )
            {
                if( filter_has_var( INPUT_POST, CUser::USERNAMETAG) )
                    $this->SetUsername( filter_input( INPUT_POST, CUser::USERNAMETAG, FILTER_UNSAFE_RAW) );

                if( filter_has_var( INPUT_POST, CUser::PASSWORDTAG) )
                {
                    $sBuffer = trim( filter_input( INPUT_POST, CUser::PASSWORDTAG, FILTER_UNSAFE_RAW) );
                    if( strlen($sBuffer)>0 )
                    {
                        $this->SetPassword( sha1($sBuffer) );
                    }
                    else
                    {
                        $this->m_sPassword = '';
                    }//if( strlen($sBuffer)>0 )
                }//if( filter_has_var( INPUT_POST, CUser::PASSWORDTAG) )

                if( filter_has_var( INPUT_POST, CUser::PASSWORDCHECKTAG) )
                {
                    $sBuffer = trim( filter_input( INPUT_POST, CUser::PASSWORDCHECKTAG, FILTER_UNSAFE_RAW) );
                    if( strlen($sBuffer)>0 )
                    {
                        $this->SetPasswordCheck( sha1($sBuffer) );
                    }
                    else
                    {
                        $this->m_sPasswordCheck = '';
                    }//if( strlen($sBuffer)>0 )
                }//if( filter_has_var( INPUT_POST, CUser::PASSWORDCHECKTAG) )

                if( filter_has_var( INPUT_POST, CUser::STATETAG) )
                {
                    $tFilter = array('options' => array('min_range' => CUser::STATEMIN,
                                                        'max_range' => CUser::STATEMAX) );
                    $this->SetState( filter_input( INPUT_POST, CUser::STATETAG, FILTER_VALIDATE_INT, $tFilter) );
                }//if( filter_has_var( INPUT_POST, CUser::STATETAG) )

            }//if( $iFilter===INPUT_POST )

            $bReturn = TRUE;

        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )

        return $bReturn;
    }

   /**
     * function: ResetMe
     * description: Set the default values.
     * parameters: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function ResetMe()
    {
        $this->m_iIdentifier = CUser::IDENTIFIERMIN;
        $this->m_sUsername = '';
        $this->m_sPassword = '';
        $this->m_sPasswordCheck = '';
        $this->m_iState = CUser::STATEMIN;
    }

}
