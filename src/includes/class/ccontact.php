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
 * description: describes a contact
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-15 - is not a singleton anymore
 *                                        add TAG constants
 *                                        delete GetInstance()
 *                                        delete DeleteInstance()
 *                                        update Getters
 *                                        update SetComment()
 *                                        update ReadInput()
 *                                        update SanitizeInt()
 *                                        update Sanitize()
 *                                        add ResetMe()
 *                                        add ReadInputLastName()
 *                                        add ReadInputIdentifier()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CContact
{

    /** Contants
     ***********/
    const IDENTIFIERTAG = 'cti';
    const IDENTIFIERMIN = 0;
    const IDENTIFIERMAX = 16777215;

    const LASTNAMETAG = 'ctl';
    const LASTNAMEMIN = 1;
    const LASTNAMEMAX = 40;

    const FIRSTNAMETAG = 'ctf';
    const FIRSTNAMEMIN = 1;
    const FIRSTNAMEMAX = 40;

    const TELTAG = 'ctp';
    const TELMIN = 1;
    const TELMAX = 40;

    const EMAILTAG = 'cte';
    const EMAILMIN = 0;
    const EMAILMAX = 255;

    const ADDRESSTAG = 'cta';
    const ADDRESSMIN = 0;
    const ADDRESSMAX = 255;

    const ADDRESSMORETAG = 'ctm';
    const ADDRESSMOREMIN = 0;
    const ADDRESSMOREMAX = 255;

    const CITYTAG = 'ctc';
    const CITYMIN = 0;
    const CITYMAX = 255;

    const ZIPTAG = 'ctz';
    const ZIPMIN = 0;
    const ZIPMAX = 8;

    const COMMENTTAG    = 'ctk';
    const COMMENTLENGTH = 300;

    /** Private attributs
     ********************/

    // identifier
    private $m_iIdentifier = CContact::IDENTIFIERMIN;

    // last name
    private $m_sLastName = '';

    // first name
    private $m_sFirstName = '';

    //  tel
    private $m_sTel = '';

    //  email
    private $m_sEmail = '';

    //  address
    private $m_sAddress = '';

    //  address_more
    private $m_sAdressMore = '';

    //  city
    private $m_sCity = '';

    //  zip
    private $m_sZip = '';

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
                }//if( 0==preg_match($sFilter, $sReturn) )
            }//if( !empty($sFilter) )
        }//if(...
        return $sReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __construct
     * description: constructor, initializes private attributs
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
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     */
    public function GetIdentifier(){return (integer)$this->m_iIdentifier;}
    public function GetLastName($iFilter=0)
    {
        $sReturn = $this->m_sLastName;
        if( $iFilter==1 )
        {
            $sReturn = htmlentities($this->m_sLastName,ENT_QUOTES,'UTF-8');
        }
        elseif( $iFilter==2 )
        {
            $sReturn = rawurlencode($this->m_sLastName);
        }
        else
        {
            $sReturn = $this->m_sLastName;
        }
        return $sReturn;
    }
    public function GetFirstName($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sFirstName,ENT_QUOTES,'UTF-8'):$this->m_sFirstName);}
    public function GetTel($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sTel,ENT_QUOTES,'UTF-8'):$this->m_sTel);}
    public function GetEmail($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sEmail,ENT_QUOTES,'UTF-8'):$this->m_sEmail);}
    public function GetAddress($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sAddress,ENT_QUOTES,'UTF-8'):$this->m_sAddress);}
    public function GetAddressMore($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sAdressMore,ENT_QUOTES,'UTF-8'):$this->m_sAdressMore);}
    public function GetCity($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sCity,ENT_QUOTES,'UTF-8'):$this->m_sCity);}
    public function GetZip($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sZip,ENT_QUOTES,'UTF-8'):$this->m_sZip);}
    public function GetComment($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sComment,ENT_QUOTES,'UTF-8'):$this->m_sComment);}
    public function GetCreationDate($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sCreationDate,ENT_QUOTES,'UTF-8'):$this->m_sCreationDate);}
    public function GetCreationUser($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sCreationUser,ENT_QUOTES,'UTF-8'):$this->m_sCreationUser);}
    public function GetUpdateDate($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sUpdateDate,ENT_QUOTES,'UTF-8'):$this->m_sUpdateDate);}
    public function GetUpdateUser($iFilter=0){return ((1==$iFilter)?htmlentities($this->m_sUpdateUser,ENT_QUOTES,'UTF-8'):$this->m_sUpdateUser);}

   /**
     * function: Setter
     * description: Accessor
     * parameter: STRING
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - update SetComment - test input parameter
     */
    public function SetIdentifier($iValue)
    {
        $this->m_iIdentifier = $this->SanitizeInt( $iValue , CContact::IDENTIFIERMIN, CContact::IDENTIFIERMAX, 0);
    }
    public function SetLastName($sValue){$this->m_sLastName=$this->Sanitize($sValue);}
    public function SetFirstName($sValue){$this->m_sFirstName=$this->Sanitize($sValue);}
    public function SetTel($sValue){$this->m_sTel=$this->Sanitize($sValue);}
    public function SetEmail($sValue){$this->m_sEmail=$this->Sanitize($sValue);}
    public function SetAddress($sValue){$this->m_sAddress=$this->Sanitize($sValue);}
    public function SetAddressMore($sValue){$this->m_sAdressMore=$this->Sanitize($sValue);}
    public function SetCity($sValue){$this->m_sCity=$this->Sanitize($sValue);}
    public function SetZip($sValue){$this->m_sZip=$this->Sanitize($sValue);}
    public function SetComment($sValue)
    {
        $sValue = TruncMe( $sValue, CContact::COMMENTLENGTH);
        $this->m_sComment = $this->Sanitize($sValue);
	}
    public function SetCreationDate($sValue){$this->m_sCreationDate=$this->Sanitize($sValue);}
    public function SetCreationUser($sValue){$this->m_sCreationUser=$this->Sanitize($sValue,GetRegExPatternName());}
    public function SetUpdateDate($sValue){$this->m_sUpdateDate=$this->Sanitize($sValue);}
    public function SetUpdateUser($sValue){$this->m_sUpdateUser=$this->Sanitize($sValue,GetRegExPatternName());}

   /**
     * function: MandatoriesAreFilled
     * description: return true if mandatory fields are filled
     * parameter: none
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function MandatoriesAreFilled()
    {
        return ( (strlen($this->m_sLastName)>0) && (strlen($this->m_sFirstName)>0) && (strlen($this->m_sTel)>0) );
    }

   /**
     * function: IsValid
     * description: return true if mandatory fields are filled and identifier is >0
     * parameter: none
     * return: TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsValid()
    {
        return ( (strlen($this->m_sLastName)>0) && (strlen($this->m_sFirstName)>0)
                && (strlen($this->m_sTel)>0) &&  $this->GetIdentifier()>0 );
    }

   /**
     * function: ReadInputLastName
     * description: Read GET or POST input contact lastname
     * parameters: INTEGER|$iFilter - Filter to apply
     *             BOOLEAN|$bRUD    - if TRUE then uses raw url decode
     *                                only valid with INPUT_GET
     * return: BOOLEAN| TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
    */
    public function ReadInputLastName( $iFilter, $bRUD=FALSE )
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET))
         && filter_has_var( $iFilter, CContact::LASTNAMETAG ) )
        {
            $sBuffer = filter_input( $iFilter, CContact::LASTNAMETAG, FILTER_UNSAFE_RAW );
            if( ($iFilter===INPUT_GET) && ($bRUD===TRUE) )
            {
                $this->m_sLastName = rawurldecode( trim($sBuffer) );
            }
            else
            {
                $this->SetLastName( $sBuffer );
            }
            if( strlen($this->m_sLastName)>0 )
            {
                $bReturn = TRUE;
            }//if( ($iFilter===INPUT_GET) && ($bRUD===TRUE) )
        }//if(...
        return $bReturn;
    }

   /**
     * function: ReadInputIdentifier
     * description: Read GET or POST input contact identifier
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
    */
    public function ReadInputIdentifier( $iFilter )
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET))
         && filter_has_var( $iFilter, CContact::IDENTIFIERTAG ) )
        {
            $tFilter = array('options' => array('min_range' => CContact::IDENTIFIERMIN,
                                                'max_range' => CContact::IDENTIFIERMAX) );
            $this->SetIdentifier( filter_input( $iFilter, CContact::IDENTIFIERTAG, FILTER_VALIDATE_INT, $tFilter));
            if( $this->GetIdentifier()>0 )
            {
                $bReturn = TRUE;
            }//if( $this->GetIdentifier()>0 )
        }//if(...
        return $bReturn;
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input contact values
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use constants
     *                                        add filter
    */
    public function ReadInput($iFilter)
    {
        $bReturn = FALSE;
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            $this->ReadInputIdentifier( $iFilter );
            $this->ReadInputLastName( $iFilter );
            if(filter_has_var( $iFilter, CContact::FIRSTNAMETAG))
                $this->SetFirstName( filter_input( $iFilter, CContact::FIRSTNAMETAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::TELTAG))
                $this->SetTel(filter_input( $iFilter, CContact::TELTAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::EMAILTAG))
                $this->SetEmail(filter_input( $iFilter, CContact::EMAILTAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::ADDRESSTAG))
                $this->SetAddress( filter_input( $iFilter, CContact::ADDRESSTAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::ADDRESSMORETAG))
                $this->SetAddressMore( filter_input( $iFilter, CContact::ADDRESSMORETAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::CITYTAG))
                $this->SetCity( filter_input( $iFilter, CContact::CITYTAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::ZIPTAG))
                $this->SetZip( filter_input( $iFilter, CContact::ZIPTAG, FILTER_UNSAFE_RAW));
            if(filter_has_var( $iFilter, CContact::COMMENTTAG))
                $this->SetComment( filter_input( $iFilter, CContact::COMMENTTAG, FILTER_UNSAFE_RAW));
            $bReturn = TRUE;
        }
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
        $this->m_iIdentifier = CContact::IDENTIFIERMIN;
        $this->m_sLastName = '';
        $this->m_sFirstName = '';
        $this->m_sTel = '';
        $this->m_sEmail = '';
        $this->m_sAddress = '';
        $this->m_sAdressMore = '';
        $this->m_sCity = '';
        $this->m_sZip = '';
        $this->m_sComment = '';
        $this->m_sCreationDate = '';
        $this->m_sCreationUser = '';
        $this->m_sUpdateDate = '';
        $this->m_sUpdateUser = '';
    }

}

?>
