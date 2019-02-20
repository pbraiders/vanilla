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
 * description: Contains temporary PHP session values
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-15 - update Sanitize()
 *                                        update GetToken()
 *                                        update SetToken()
 *                                        add constants
 *                                        add ValidInput()
 *                                        add GenerateSessionId()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/** Class
 ********/
final class CPHPSession
{

    /** Contants
     ***********/
    const TOKEN    = 'token';
    const TOKENTAG = 'tok';

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
     * parameter: STRING|sValue  - value to sanitize
     *            STRING|sFilter - regex filter
     * return: STRING sanitized value or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - add filter parameter
     */
    private function Sanitize( $sValue, $sFilter)
    {
        $sReturn = FALSE;
        if( is_scalar($sValue) && is_scalar($sFilter) )
        {
            // Trim
            $sReturn = trim($sValue);
            // Authorized caracteres
            if( !empty($sFilter) )
            {
                if( 0==preg_match( $sFilter, $sReturn) )
                {
                    $sReturn = FALSE;
                }//if( 0==preg_match( $sFilter, $sReturn) )
            }//if( !empty($sFilter) )
        }//if( is_scalar($sValue) && is_scalar($sFilter) ))
        return $sReturn;
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
            self::$m_pInstance = new CPHPSession();
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
     * function: GenerateSessionId
     * description: Generate a session identifier
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GenerateSessionId()
    {
        return md5(uniqid(rand()));
    }

    /**
     * function: ReadToken
     * description: return the _SESSION token value
     * parameter: none
     * return: STRING or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use of GetRegExPatternSession()
     */
    public function ReadToken()
    {
        $sReturn = FALSE;
        if( isset($_SESSION) && isset($_SESSION[CPHPSession::TOKEN]) )
        {
            $sReturn = $this->Sanitize( $_SESSION[CPHPSession::TOKEN], GetRegExPatternSession() );
        }//if( isset($_SESSION) && isset($_SESSION[CPHPSession::TOKEN]) )
        return $sReturn;
    }

   /**
     * function: WriteToken
     * description: Generates and set the token value into _SESSION
     * return: STRING the generated value or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use of GetRegExPatternSession()
     *                                        generate session value
     *                                        add return value
     */
    public function WriteToken()
    {
        $sReturn = FALSE;
        if( isset($_SESSION) )
        {
            CPHPSession::CleanToken();
            $sReturn = $this->Sanitize( $this->GenerateSessionId(), GetRegExPatternSession() );
            if( $sReturn!==FALSE )
            {
                $_SESSION[CPHPSession::TOKEN] = $sReturn;
            }//if( $sReturn!==FALSE )
        }//if( isset($_SESSION) )
        return $sReturn;
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
            unset($_SESSION[CPHPSession::TOKEN]);
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

   /**
     * function: ValidInput
     * description: Read GET or POST input token value
     *              and check with the one registered in the PHP session
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function ValidInput($iFilter)
    {
        // Initialize
        $bReturn = FALSE;
        $sInput = FALSE;
        $sToken = FALSE;

        // Read input
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            if( filter_has_var( $iFilter, CPHPSession::TOKENTAG) )
            {
                $sInput = $this->Sanitize( filter_input( $iFilter, CPHPSession::TOKENTAG, FILTER_SANITIZE_SPECIAL_CHARS), GetRegExPatternSession() );
            }//if( filter_has_var( $iFilter, CPHPSession::TOKENTAG) )
        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )

        // Read PHP session
        $sToken = $this->ReadToken();

        // Analyse
        if( ($sInput!==FALSE) && ($sToken!==FALSE) && ($sInput===$sToken) )
        {
            $bReturn = TRUE;
        }//if( ($sInput!==FALSE) && ($sToken!==FALSE) && ($sInput===$sToken) )

        return $bReturn;
    }

}

define('PBR_SESSION_LOADED',1);

?>
