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
 * description: describes option
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class COption
{
    /** Constants
     ***********/
    const VALUEMIN = 1;
    const VALUEMAX = 2;
    const OPTTAG = 'op';

    /** Private attributs
     ********************/

    // Option's name
    private $m_sName  = COption::OPTTAG;
    // Option's value
    private $m_iValue = COption::VALUEMIN;
    // Minimun value allowed
    private $m_iMin = COption::VALUEMIN;
    // Maximun value allowed
    private $m_iMax = COption::VALUEMAX;

    /** Private methods
     ******************/

    /**
     * function: SanitizeInt
     * description: return sanitized integer value.
     * parameter: INTEGER|iValue - value to sanitize
     *            INTEGER|iMin   - value min value
     *            INTEGER|iMax   - value max value
     * return: BOOLEAN - TRUE or FALSE if the value is not valid
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function SanitizeInt( &$iValue, $iMin, $iMax)
    {
        $bReturn = FALSE;
        if( is_integer($iMin) && is_integer($iMax) )
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
                $bReturn = TRUE;
            }//if( is_integer($iValue) && ($iValue>=$iMin) && ($iValue<=$iMax) )
        }//if( is_integer($iMin) && is_integer($iMax) )
        return $bReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __construct
     * description: constructor
     * parameter: STRING|sName - option's name
     *           INTEGER|iMin  - minimum value allowed
     *           INTEGER|iMex  - maximun value allowed
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __construct( $sName, $iMin=COption::VALUEMIN, $iMax=COption::VALUEMAX )
    {
        if( is_scalar($sName) && is_integer($iMin) && is_integer($iMax) )
        {
            $sBuffer = trim($sName);
            $this->m_sName = COption::OPTTAG.$sBuffer;
            $this->m_iMin = $iMin;
            $this->m_iMax = $iMax;
            $this->m_iValue = $iMin;
        }//if( is_scalar($sName) )
    }

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
    public function __clone() {}

    /**
     * function: GetValue
     * description: return the value
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetValue() { return (integer)$this->m_iValue; }

    /**
     * function: GetName
     * description: return the name
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetName() { return $this->m_sName; }

   /**
     * function: SetValue
     * description: Set value
     * parameter: INTEGER|$iValue - value
     * return: none
     * author: Olivier JULLIEN - 2010-62-15
     */
    public function SetValue( $iValue )
    {
        $this->m_iValue = $this->m_iMin;
        if( $this->SanitizeInt( $iValue, $this->m_iMin, $this->m_iMax)===TRUE )
        {
            $this->m_iValue = $iValue;
        }//if( $this->SanitizeInt(...
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input option value
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function ReadInput( $iFilter )
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET)) && filter_has_var( $iFilter, $this->m_sName) )
        {
            $tFilter = array('options' => array('min_range' => $this->m_iMin, 'max_range' => $this->m_iMax));
            $this->SetValue( filter_input( $iFilter, $this->m_sName, FILTER_VALIDATE_INT, $tFilter));
            $bReturn = TRUE;
        }//if(...
        return $bReturn;
    }

}

?>
