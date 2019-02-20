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
 * description: describes a rent
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - Update __clone()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CRent
{

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // identifier
    private $m_iIdentifier = 0;

    // Real count
    private $m_iReal = 0;

    // Planned count
    private $m_iPlanned = 0;

    // Canceled count
    private $m_iCanceled = 0;

    // Max rent
    private $m_iMax = 0;

    // Group Age (1,2,3)
    private $m_iAge = 2;

    // Group Arrhes (0,1,2,3)
    private $m_iArrhes = 0;

    // comment
    private $m_sComment = '';

    // create date
    private $m_sCreationDate = '';

    // create user
    private $m_sCreationUser = '';

    // update date
    private $m_sUpdateDate = '';

    // update user
    private $m_sUpdateUser = '';

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
        $sReturn='';
        if( is_scalar($sValue) && is_scalar($sFilter) )
        {
			// Trim
			$sReturn = trim($sValue);
            // Authorized caracteres
            if( !empty($sFilter) )
            {
                if( 0==preg_match($sFilter,$sValue) )
                {
                    $sReturn='';
                }//if( 0==preg_match($sFilter,$sValue) )
            }//if( !empty($sFilter) )
        }//if( is_scalar($sValue) && is_scalar($sFilter) )
        return $sReturn;
    }

    /** Public methods
     *****************/

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
            self::$m_pInstance = new CRent();
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
     * function: Getter
     * description: Accessor
     * parameter: INTEGER|iFilter - 1 if characters should be converted into html entities
     * return: INTEGER or STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetIdentifier(){return (integer)$this->m_iIdentifier;}
    public function GetCountReal(){return (integer)$this->m_iReal;}
    public function GetCountPlanned(){return (integer)$this->m_iPlanned;}
    public function GetCountCanceled(){return (integer)$this->m_iCanceled;}
    public function GetMax(){return (integer)$this->m_iMax;}
    public function GetAge(){return (integer)$this->m_iAge;}
    public function GetArrhes(){return (integer)$this->m_iArrhes;}
    public function GetComment($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sComment,ENT_COMPAT,'UTF-8'):$this->m_sComment);}
    public function GetCreationDate($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sCreationDate,ENT_COMPAT,'UTF-8'):$this->m_sCreationDate);}
    public function GetCreationUser($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sCreationUser,ENT_COMPAT,'UTF-8'):$this->m_sCreationUser);}
    public function GetUpdateDate($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sUpdateDate,ENT_COMPAT,'UTF-8'):$this->m_sUpdateDate);}
    public function GetUpdateUser($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sUpdateUser,ENT_COMPAT,'UTF-8'):$this->m_sUpdateUser);}

   /**
     * function: Setter
     * description: Accessor
     * parameter: STRING|sValue or INTEGER|iValue - value to add.
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetIdentifier($iValue){$this->m_iIdentifier=$this->SanitizeInt($iValue);}
    public function SetCountReal($iValue){$this->m_iReal=$this->SanitizeInt($iValue);}
    public function SetCountPlanned($iValue){$this->m_iPlanned=$this->SanitizeInt($iValue);}
    public function SetCountCanceled($iValue){$this->m_iCanceled=$this->SanitizeInt($iValue);}
    public function SetMax($iValue){$this->m_iMax=$this->SanitizeInt($iValue);}
    public function SetAge($iValue)
    {
        $this->m_iAge=2;
        $iValue=$this->SanitizeInt($iValue);
        if(($iValue>=0) && ($iValue<4))
        {
            $this->m_iAge=$iValue;
        }//if(($iValue>=0) && ($iValue<4))
    }
    public function SetArrhes($iValue)
    {
        $this->m_iArrhes=0;
        $iValue=$this->SanitizeInt($iValue);
        if(($iValue>=0) && ($iValue<4))
        {
            $this->m_iArrhes=$iValue;
        }//if(($iValue>=0) && ($iValue<4))
    }
    public function SetComment($sValue)
    {
        if( mb_strlen($sValue,'UTF-8')>300 )
        {
            $sValue=TruncMe($sValue,300);
        }
        $this->m_sComment=$this->Sanitize($sValue);
	 }
    public function SetCreationDate($sValue){$this->m_sCreationDate=$this->Sanitize($sValue);}
    public function SetCreationUser($sValue){$this->m_sCreationUser=$this->Sanitize($sValue);}
    public function SetUpdateDate($sValue){$this->m_sUpdateDate=$this->Sanitize($sValue);}
    public function SetUpdateUser($sValue){$this->m_sUpdateUser=$this->Sanitize($sValue);}

   /**
     * function: ReadInput
     * description: Read GET or POST input rent values
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ReadInput($iFilter)
    {
        $bReturn = TRUE;
        if( ($iFilter===INPUT_GET) && filter_has_var(INPUT_GET,'rei') )
        {
            $this->SetIdentifier(filter_input(INPUT_GET,'rei',FILTER_VALIDATE_INT));
        }
        elseif( $iFilter===INPUT_POST )
        {
            if( filter_has_var(INPUT_POST,'rer') )
                $this->SetCountReal(filter_input(INPUT_POST,'rer',FILTER_VALIDATE_INT));
            if( filter_has_var(INPUT_POST,'rep') )
                $this->SetCountPlanned(filter_input(INPUT_POST,'rep',FILTER_VALIDATE_INT));
            if( filter_has_var(INPUT_POST,'rec') )
                $this->SetCountCanceled(filter_input(INPUT_POST,'rec',FILTER_VALIDATE_INT));
            if(  filter_has_var(INPUT_POST,'rea') )
            {
                $tFilter = array('options' => array('min_range' => 1, 'max_range' => 3));
                $this->SetAge(filter_input(INPUT_POST,'rea',FILTER_VALIDATE_INT,$tFilter));
            }
            if( filter_has_var(INPUT_POST,'reh') )
            {
                $tFilter = array('options' => array('min_range' => 0, 'max_range' => 3));
                $this->SetArrhes(filter_input(INPUT_POST,'reh',FILTER_VALIDATE_INT,$tFilter));
            }
            if( filter_has_var(INPUT_POST,'rek') )
                $this->SetComment(filter_input(INPUT_POST,'rek',FILTER_UNSAFE_RAW));
            if( filter_has_var(INPUT_POST,'rei') )
                $this->SetIdentifier(filter_input(INPUT_POST,'rei',FILTER_VALIDATE_INT));
            if( filter_has_var(INPUT_POST,'max') )
                $this->SetMax(filter_input(INPUT_POST,'max',FILTER_VALIDATE_INT));
        }
        else
        {
            $bReturn = FALSE;
        }//if
        return $bReturn;
    }

}
define('PBR_RENT_LOADED',1);
?>
