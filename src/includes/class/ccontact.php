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
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CContact
{

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // identifier
    private $m_iIdentifier = 0;

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
     */
    public function __clone()
    {
        trigger_error( 'Attempting to clone CContact', E_USER_NOTICE );
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
            self::$m_pInstance = new CContact();
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
     * function: Getter
     * description: Accessor
     * parameter: INTEGER|iFilter - 1 if characters should be converted into html entities
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetIdentifier(){return (integer)$this->m_iIdentifier;}
    public function GetLastName($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sLastName,ENT_COMPAT,'UTF-8'):$this->m_sLastName);}
    public function GetFirstName($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sFirstName,ENT_COMPAT,'UTF-8'):$this->m_sFirstName);}
    public function GetTel($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sTel,ENT_COMPAT,'UTF-8'):$this->m_sTel);}
    public function GetEmail($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sEmail,ENT_COMPAT,'UTF-8'):$this->m_sEmail);}
    public function GetAddress($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sAddress,ENT_COMPAT,'UTF-8'):$this->m_sAddress);}
    public function GetAddressMore($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sAdressMore,ENT_COMPAT,'UTF-8'):$this->m_sAdressMore);}
    public function GetCity($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sCity,ENT_COMPAT,'UTF-8'):$this->m_sCity);}
    public function GetZip($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sZip,ENT_COMPAT,'UTF-8'):$this->m_sZip);}
    public function GetComment($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sComment,ENT_COMPAT,'UTF-8'):$this->m_sComment);}
    public function GetCreationDate($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sCreationDate,ENT_COMPAT,'UTF-8'):$this->m_sCreationDate);}
    public function GetCreationUser($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sCreationUser,ENT_COMPAT,'UTF-8'):$this->m_sCreationUser);}
    public function GetUpdateDate($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sUpdateDate,ENT_COMPAT,'UTF-8'):$this->m_sUpdateDate);}
    public function GetUpdateUser($iFilter=0){return ((1===$iFilter)?htmlentities($this->m_sUpdateUser,ENT_COMPAT,'UTF-8'):$this->m_sUpdateUser);}

   /**
     * function: Setter
     * description: Accessor
     * parameter: STRING
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetIdentifier($iValue){$this->m_iIdentifier=$this->SanitizeInt($iValue);}
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
     * function: ReadInput
     * description: Read POST input date values
     * parameters: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ReadInput()
    {
        if(filter_has_var(INPUT_POST,'ctl'))
            $this->SetLastName(filter_input(INPUT_POST,'ctl',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'ctf'))
            $this->SetFirstName(filter_input(INPUT_POST,'ctf',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'ctp'))
            $this->SetTel(filter_input(INPUT_POST,'ctp',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'cte'))
            $this->SetEmail(filter_input(INPUT_POST,'cte',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'cta'))
            $this->SetAddress(filter_input(INPUT_POST,'cta',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'ctm'))
            $this->SetAddressMore(filter_input(INPUT_POST,'ctm',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'ctc'))
            $this->SetCity(filter_input(INPUT_POST,'ctc',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'ctz'))
            $this->SetZip(filter_input(INPUT_POST,'ctz',FILTER_UNSAFE_RAW));
        if(filter_has_var(INPUT_POST,'ctk'))
            $this->SetComment(filter_input(INPUT_POST,'ctk',FILTER_UNSAFE_RAW));
    }

}
define('PBR_CONTACT_LOADED',1);
?>
