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
 * description: describes values for paging
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - Update __clone()
 * update: Olivier JULLIEN - 2010-06-15 - is not a singleton anymore
 *                                        delete GetInstance()
 *                                        delete DeleteInstance()
 *                                        delete __clone
 *                                        add TAG constants
 *                                        update ReadInput()
 *                                        add SanitizeInt()
 *                                        update Compute()
 *                                        update SetLimit()
 *                                        update SetOffset()
 *                                        update SetMax()
 *                                        update SetCurrent()
 *                                        add ResetMe()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CPaging
{

    /** Constants
     ***********/
    const PAGETAG   = 'pag';
    const PAGEMIN   = 1;
    const PAGEMAX   = 16777215;
    const OFFSETMIN = 0;
    const OFFSETMAX = 16777215;

    /** Private attributs
     ********************/

    // Current page
    private $m_iCurrent;

    // Maximum number of pages
    private $m_iMax;

    // Offset of the first row to return
    private $m_iOffset;

    // Maximum number of rows to return
    private $m_iLimit;

    /** Private methods
     ******************/

    /**
     * function: SanitizeInt
     * description: return sanitized integer value.
     * parameter: INTEGER|iValue - value to sanitize
     *            INTEGER|iMin   - value min value
     *            INTEGER|iMax   - value max value
     * return: INTEGER - sanitized value or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function SanitizeInt( $iValue, $iMin, $iMax, $iDefault)
    {
        $iReturn = FALSE;
        // Sanitize
        if( is_integer($iMin) && is_integer($iMax) && is_integer($iDefault) )
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
            }
            else
            {
                $iReturn = $iDefault;
            }//if( is_integer($iValue) && ($iValue>=$iMin) && ($iValue<=$iMax) )
        }//if( is_integer($iMin) && is_integer($iMax) && is_integer($iDefault) )
        return $iReturn;
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
    public function __construct()
    {
        $this->ResetMe();
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
     * function: GetCurrent
     * description: return the current page
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetCurrent() { return (integer)$this->m_iCurrent; }

    /**
     * function: GetMax
     * description: return the maximum number of page
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetMax() { return (integer)$this->m_iMax; }

    /**
     * function: GetOffset
     * description: return the offset of the first row to return
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetOffset() { return (integer)$this->m_iOffset; }

    /**
     * function: GetLimit
     * description: return the maximum number of rows to return
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetLimit() { return (integer)$this->m_iLimit; }

    /**
     * function: SetCurrent
     * description: Set the current page
     * parameter: INTEGER|$iValues - current page
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - sanitize
     */
    public function SetCurrent( $iValue )
    {
        $this->m_iCurrent = $this->SanitizeInt( $iValue, CPaging::PAGEMIN, CPaging::PAGEMAX, CPaging::PAGEMIN);
    }

    /**
     * function: SetMax
     * description: Set the maximum number of page
     * parameter: INTEGER|$iValues - maximum number of page
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - sanitize
     */
    public function SetMax( $iValue )
    {
        $this->m_iMax = $this->SanitizeInt( $iValue, CPaging::PAGEMIN, CPaging::PAGEMAX, CPaging::PAGEMIN);
    }

    /**
     * function: SetOffset
     * description: Set the offset of the first row to return
     * parameter: INTEGER|$iValues - offset of the first row to return
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - sanitize
     */
    public function SetOffset( $iValue )
    {
        $this->m_iOffset = $this->SanitizeInt( $iValue, CPaging::OFFSETMIN, CPaging::OFFSETMAX, CPaging::OFFSETMIN);
    }

    /**
     * function: SetLimit
     * description: Set the maximum number of rows to return
     * parameter: INTEGER|$iValues - maximum number of rows to return
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - sanitize
     */
    public function SetLimit( $iValue )
    {
        $this->m_iLimit = $this->SanitizeInt( $iValue, 1, CPaging::OFFSETMAX, CPaging::OFFSETMAX);;
    }

    /**
     * function: Compute
     * description: Compute the maximum number of pages and
     *              the offset of the first row to return from
     *              the number of records and the maximum number
     *              of rows to return
     * parameter: INTEGER|$iLimit - maximum number of rows to return
     *            INTEGER|$iRows  - number of records
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - sanitize
     */
    public function Compute( $iLimit, $iRows )
    {
        // Set the limit
        $this->SetLimit( $iLimit );

        // Sanitize the numbers of records
        $iRows = $this->SanitizeInt( $iRows, CPaging::OFFSETMIN, CPaging::OFFSETMAX, CPaging::OFFSETMIN);

        // Compute
        $iBuffer = (integer)ceil( $iRows / $this->m_iLimit );
        $this->SetMax($iBuffer);
        if( $this->m_iCurrent > $this->m_iMax )
        {
            $this->SetCurrent(1);
        }//if( $this->m_iCurrent > $this->m_iMax )
        $this->SetOffset($this->m_iLimit * ($this->m_iCurrent-1));
    }

   /**
     * function: ReadInput
     * description: Read GET input page value
     * parameters: none
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use constant
     */
    public function ReadInput()
    {
        $bReturn = FALSE;
        if( filter_has_var( INPUT_GET, CPaging::PAGETAG) )
        {
            $tFilter = array('options' => array('min_range' => CPaging::PAGEMIN,
                                                'max_range' => CPaging::PAGEMAX) );
            $this->SetCurrent( filter_input( INPUT_GET, CPaging::PAGETAG, FILTER_VALIDATE_INT, $tFilter));
            $bReturn = TRUE;
        }//if( filter_has_var(INPUT_GET, 'pag') )
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
        $this->m_iCurrent = CPaging::PAGEMIN;
        $this->m_iMax     = CPaging::PAGEMIN;
        $this->m_iOffset  = CPaging::OFFSETMIN;
        $this->m_iLimit   = CPaging::OFFSETMAX;
    }

}
