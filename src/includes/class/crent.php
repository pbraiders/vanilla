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
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-15 - is not a singleton anymore
 *                                        delete GetInstance()
 *                                        delete DeleteInstance()
 *                                        add TAG constants
 *                                        update ReadInput()
 *                                        update Getters
 *                                        update SanitizeInt()
 *                                        update Sanitize()
 *                                        add ResetMe()
 *                                        add ReadInputIdentifier()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CRent
{

    /** Contants
     ***********/
    const IDENTIFIERTAG = 'rei';
    const IDENTIFIERMIN = 0;
    const IDENTIFIERMAX = 16777215;

    const REALTAG     = 'rer';
    const PLANNEDTAG  = 'rep';
    const CANCELEDTAG = 'rec';
    const RPCMIN      = 0;
    const RPCMAX      = 999;

    const AGETAG     = 'rea';
    const AGEMIN     = 1;
    const AGEDEFAULT = 2;
    const AGEMAX     = 3;

    const ARRHESTAG = 'reh';
    const ARRHESMIN = 0;
    const ARRHESMAX = 3;

    const COMMENTTAG    = 'rek';
    const COMMENTLENGTH = 300;

    const MAXTAG = 'max';
    const MAXMIN = 0;
    const MAXMAX = 999;

    /** Private attributs
     ********************/

    // identifier
    private $m_iIdentifier = CRent::IDENTIFIERMIN;

    // Real count
    private $m_iReal = CRent::RPCMIN;

    // Planned count
    private $m_iPlanned = CRent::RPCMIN;

    // Canceled count
    private $m_iCanceled = CRent::RPCMIN;

    // Max rent
    private $m_iMax = CRent::MAXMIN;

    // Group Age (1,2,3)
    private $m_iAge = CRent::AGEDEFAULT;

    // Group Arrhes (0,1,2,3)
    private $m_iArrhes = CRent::ARRHESMIN;

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
     * function: SanitizeInt
     * description: return sanitized integer value
     * parameter: INTEGER|iValue - value to sanitize
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - fixed minor bug
     *                                        add min, max and default values
     */
    private function SanitizeInt($iValue, $iMin, $iMax, $iDefault)
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
            }//if( is_integer($iValue) )
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
     */
    private function Sanitize($sValue, $sFilter='')
    {
        $sReturn = '';
        if( is_scalar($sValue) && is_scalar($sFilter) )
        {
			// Trim
			$sReturn = trim($sValue);
            // Authorized caracteres
            if( !empty($sFilter) )
            {
                if( 0==preg_match( $sFilter, $sReturn) )
                {
                    $sReturn = '';
                }//if( 0==preg_match( $sFilter, $sReturn) )
            }//if( !empty($sFilter) )
        }//if( is_scalar($sValue) && is_scalar($sFilter) )
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
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     */
    public function GetIdentifier(){return (integer)$this->m_iIdentifier;}
    public function GetCountReal(){return (integer)$this->m_iReal;}
    public function GetCountPlanned(){return (integer)$this->m_iPlanned;}
    public function GetCountCanceled(){return (integer)$this->m_iCanceled;}
    public function GetMax(){return (integer)$this->m_iMax;}
    public function GetAge(){return (integer)$this->m_iAge;}
    public function GetArrhes(){return (integer)$this->m_iArrhes;}
    public function GetComment($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sComment,ENT_QUOTES,'UTF-8'):$this->m_sComment);}
    public function GetCreationDate($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sCreationDate,ENT_QUOTES,'UTF-8'):$this->m_sCreationDate);}
    public function GetCreationUser($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sCreationUser,ENT_QUOTES,'UTF-8'):$this->m_sCreationUser);}
    public function GetUpdateDate($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sUpdateDate,ENT_QUOTES,'UTF-8'):$this->m_sUpdateDate);}
    public function GetUpdateUser($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sUpdateUser,ENT_QUOTES,'UTF-8'):$this->m_sUpdateUser);}

   /**
     * function: Setter
     * description: Accessor
     * parameter: STRING|sValue or INTEGER|iValue - value to add.
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetIdentifier($iValue)
    {
        $this->m_iIdentifier = $this->SanitizeInt( $iValue, CRent::IDENTIFIERMIN, CRent::IDENTIFIERMAX, 0);
    }
    public function SetCountReal($iValue)
    {
        $this->m_iReal = $this->SanitizeInt( $iValue, CRent::RPCMIN, CRent::RPCMAX, 0);
    }
    public function SetCountPlanned($iValue)
    {
        $this->m_iPlanned = $this->SanitizeInt( $iValue, CRent::RPCMIN, CRent::RPCMAX, 0);
    }
    public function SetCountCanceled($iValue)
    {
        $this->m_iCanceled = $this->SanitizeInt( $iValue, CRent::RPCMIN, CRent::RPCMAX, 0);
    }
    public function SetMax($iValue)
    {
        $this->m_iMax = $this->SanitizeInt( $iValue, CRent::MAXMIN, CRent::MAXMAX, 0);
    }
    public function SetAge($iValue)
    {
        $this->m_iAge = $this->SanitizeInt( $iValue, CRent::AGEMIN, CRent::AGEMAX, CRent::AGEDEFAULT);
    }
    public function SetArrhes($iValue)
    {
        $this->m_iArrhes = $this->SanitizeInt( $iValue, CRent::ARRHESMIN, CRent::ARRHESMAX, 0);
    }
    public function SetComment($sValue)
    {
        $sValue = TruncMe( $sValue, CRent::COMMENTLENGTH);
        $this->m_sComment = $this->Sanitize($sValue);
	}
    public function SetCreationDate($sValue){$this->m_sCreationDate=$this->Sanitize($sValue);}
    public function SetCreationUser($sValue){$this->m_sCreationUser=$this->Sanitize($sValue,GetRegExPatternName());}
    public function SetUpdateDate($sValue){$this->m_sUpdateDate=$this->Sanitize($sValue);}
    public function SetUpdateUser($sValue){$this->m_sUpdateUser=$this->Sanitize($sValue,GetRegExPatternName());}

   /**
     * function: ReadInputIdentifier
     * description: Read GET or POST input rent identifier
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
    */
    public function ReadInputIdentifier( $iFilter )
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET))
         && filter_has_var( $iFilter, CRent::IDENTIFIERTAG ) )
        {
            $tFilter = array('options' => array('min_range' => CRent::IDENTIFIERMIN,
                                                'max_range' => CRent::IDENTIFIERMAX) );
            $this->SetIdentifier( filter_input( $iFilter, CRent::IDENTIFIERTAG, FILTER_VALIDATE_INT, $tFilter));
            if( $this->GetIdentifier()>0 )
            {
                $bReturn = TRUE;
            }//if( $this->GetIdentifier()>0 )
        }//if(...
        return $bReturn;
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input rent values
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ReadInput($iFilter)
    {
        $bReturn = FALSE;
        if( ($iFilter===INPUT_GET) || ($iFilter===INPUT_POST) )
        {
            $this->ReadInputIdentifier($iFilter);
            if( filter_has_var( $iFilter, CRent::REALTAG) )
            {
                $tFilter = array('options' => array('min_range' => CRent::RPCMIN, 'max_range' => CRent::RPCMAX) );
                $this->SetCountReal( filter_input( $iFilter, CRent::REALTAG, FILTER_VALIDATE_INT,$tFilter));
            }
            if( filter_has_var( $iFilter, CRent::PLANNEDTAG) )
            {
                $tFilter = array('options' => array('min_range' => CRent::RPCMIN, 'max_range' => CRent::RPCMAX) );
                $this->SetCountPlanned( filter_input($iFilter,CRent::PLANNEDTAG,FILTER_VALIDATE_INT,$tFilter));
            }
            if( filter_has_var( $iFilter, CRent::CANCELEDTAG) )
            {
                $tFilter = array('options' => array('min_range' => CRent::RPCMIN, 'max_range' => CRent::RPCMAX) );
                $this->SetCountCanceled(filter_input($iFilter,CRent::CANCELEDTAG,FILTER_VALIDATE_INT,$tFilter));
            }
            if(  filter_has_var( $iFilter, CRent::AGETAG) )
            {
                $tFilter = array('options' => array('min_range'=>CRent::AGEMIN, 'max_range'=>CRent::AGEMAX));
                $this->SetAge( filter_input( $iFilter, CRent::AGETAG, FILTER_VALIDATE_INT, $tFilter));
            }
            if( filter_has_var( $iFilter, CRent::ARRHESTAG) )
            {
                $tFilter = array('options' => array('min_range'=>CRent::ARRHESMIN, 'max_range'=>CRent::ARRHESMAX));
                $this->SetArrhes( filter_input( $iFilter, CRent::ARRHESTAG, FILTER_VALIDATE_INT, $tFilter));
            }
            if( filter_has_var( $iFilter, CRent::COMMENTTAG) )
            {
                $this->SetComment( filter_input( $iFilter, CRent::COMMENTTAG, FILTER_UNSAFE_RAW));
            }
            if( filter_has_var( $iFilter, CRent::MAXTAG) )
            {
                $tFilter = array('options' => array('min_range'=>CRent::MAXMIN, 'max_range'=>CRent::MAXMAX));
                $this->SetMax( filter_input( $iFilter, CRent::MAXTAG, FILTER_VALIDATE_INT,$tFilter));
            }
            $bReturn = TRUE;
        }//if
        return $bReturn;
    }

   /**
     * function: ResetMe
     * description: Set the default value.
     * parameters: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function ResetMe()
    {
        $this->m_iIdentifier = CRent::IDENTIFIERMIN;
        $this->m_iReal = CRent::RPCMIN;
        $this->m_iPlanned = CRent::RPCMIN;
        $this->m_iCanceled = CRent::RPCMIN;
        $this->m_iMax = CRent::MAXMIN;
        $this->m_iAge = CRent::AGEDEFAULT;
        $this->m_iArrhes = CRent::ARRHESMIN;
        $this->m_sComment = '';
        $this->m_sCreationDate = '';
        $this->m_sCreationUser = '';
        $this->m_sUpdateDate = '';
        $this->m_sUpdateUser = '';
    }

}

?>
