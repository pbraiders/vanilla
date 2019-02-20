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
 * description: describes max rent per month list object
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CMaxRentPerMonthList implements Iterator
{

    /** Constant
     ***********/
    const PARAMETERTAG = 'pa';
    const RENTMAX      = 999;
    const RENTMIN      = 0;

    /** Private attributs
     ********************/

    // Month and value
    public $m_Month = array();
    public $m_Value = array();

    // Current index
    public $m_iIndex = 0;

    /** Private methods
     ******************/

    /**
     * function: SanitizeInt
     * description: return sanitized integer value
     * parameter: INTEGER|iValue - value to sanitize
     *            INTEGER|iMin - min value allowed
     *            INTEGER|iMax - max value allowed
     * return: INTEGER or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function SanitizeInt( $iValue, $iMin, $iMax)
    {
        $iReturn = FALSE;
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
                $iReturn = $iValue;
            }//if( is_integer($iValue) && ($iValue>=) && ($iValue<=) )
        }//if( is_integer($iMin) && is_integer($iMax) )
        return $iReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __construct
     * description: constructor, initialize members variables
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function __construct(){}

    /**
     * function: __destruct
     * description: destructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function __destruct()
    {
        $this->Clean();
    }

    /**
     * function: current
     * description: return the current element
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function current()
    {
        return $this->m_Value[$this->m_iIndex];
    }

    /**
     * function: next
     * description: move forward to next element
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function next()
    {
        $this->m_iIndex += 1 ;
    }

    /**
     * function: valid
     * description: Check if there is a current element after calls to rewind() or next().
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function valid()
    {
        return isset($this->m_Value[$this->m_iIndex]);
    }

    /**
     * function: key
     * description: return the key of the current element.
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function key()
    {
        return $this->m_Month[$this->m_iIndex];
    }

    /**
     * function: rewind
     * description: rewind the Iterator to the first element.
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function rewind()
    {
        $this->m_iIndex = 0;
    }

    /**
     * function: GetCount
     * description: return the count of element of the list
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function GetCount()
    {
        return count($this->m_Value);
    }

   /**
     * function: Add
     * description: Add the month number and the max of rents
     * parameter: INTEGER|iMonth - month number
     *            INTEGER|iMax   - value
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function Add( $iMonth, $iMax)
    {
        // Sanitize
        $bReturn = FALSE;
        $iMonth = $this->SanitizeInt( $iMonth, 1, 12);
        $iMax = $this->SanitizeInt( $iMax, CMaxRentPerMonthList::RENTMIN, CMaxRentPerMonthList::RENTMAX);
        // Insert
        if( ($iMonth!==FALSE) && ($iMax!==FALSE) )
        {
            $this->m_Month[] = $iMonth;
            $this->m_Value[] = $iMax;
            $bReturn = TRUE;
        }
        return $bReturn;
    }

   /**
     * function: Clean
     * description: clean the list
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function Clean()
    {
        $this->m_Month = array();
        $this->m_Value = array();
        $this->m_iIndex = 0;
    }

   /**
     * function: ReadInput
     * description: Read POST input of parameter values
     * parameters: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function ReadInput()
    {
        $tFilter = array('options' => array('min_range'=>CMaxRentPerMonthList::RENTMIN,
                                            'max_range'=>CMaxRentPerMonthList::RENTMAX) );
        for( $iIndex = 1; $iIndex < 13; $iIndex++ )
        {
            $sBuffer = CMaxRentPerMonthList::PARAMETERTAG.$iIndex;
            if( filter_has_var( INPUT_POST, $sBuffer) )
            {
                $this->Add( $iIndex, filter_input( INPUT_POST, $sBuffer, FILTER_VALIDATE_INT, $tFilter) );
            }//if( filter_has_var( INPUT_POST, $sBuffer) )
        }//for( $iIndex = 1; $iIndex < 13; $iIndex++ )
    }

}

?>
