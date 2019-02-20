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
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CPaging
{
    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // Current page
    private $m_iCurrent=1;
    // Maximum number of pages
    private $m_iMax=1;
    // Offset of the first row to return
    private $m_iOffset=0;
    // Maximum number of rows to return
    private $m_iLimit=16777215;

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
            self::$m_pInstance = new CPaging();
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
     */
    public function SetCurrent( $iValue )
    {
    	$this->m_iCurrent = 1;
		if( is_int($iValue) && ($iValue>1) )
		{
			$this->m_iCurrent = (integer)$iValue;
		}//if( is_int($iValue) && ($iValue>1) )
    }

    /**
     * function: SetMax
     * description: Set the maximum number of page
     * parameter: INTEGER|$iValues - maximum number of page
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetMax( $iValue )
    {
    	$this->m_iMax = 1;
		if( is_int($iValue) && ($iValue>1) )
		{
                $this->m_iMax = (integer)$iValue;
		}//if( is_int($iValue) && ($iValue>1) )
    }

    /**
     * function: SetOffset
     * description: Set the offset of the first row to return
     * parameter: INTEGER|$iValues - offset of the first row to return
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetOffset( $iValue )
    {
    	$this->m_iOffset = 0;
		if( is_int($iValue) && ($iValue>0) )
		{
                $this->m_iOffset = (integer)$iValue;
		}//if( is_int($iValue) && ($iValue>0) )
    }

    /**
     * function: SetLimit
     * description: Set the maximum number of rows to return
     * parameter: INTEGER|$iValues - maximum number of rows to return
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetLimit( $iValue )
    {
    	$this->m_iLimit = 16777215;
		if( is_int($iValue) && ($iValue>0) )
		{
                $this->m_iLimit = (integer)$iValue;
		}//if( is_int($iValue) && ($iValue>0) )
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
     */
    public function Compute( $iLimit, $iRows )
    {
		$this->SetLimit( $iLimit );
    	if( is_int($iRows) )
        {
			$this->m_iMax = ceil( $iRows / $this->m_iLimit );
            if( $this->m_iCurrent > $this->m_iMax )
            {
				$this->m_iCurrent=1;
            }//if( $this->m_iCurrent > $this->m_iMax )
            $this->m_iOffset = $this->m_iLimit * ($this->m_iCurrent-1);
        }//if( is_int($iRows) )
    }

   /**
     * function: ReadInput
     * description: Read GET input page value
     * parameters: none
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ReadInput()
    {
        $bReturn = FALSE;
        if( filter_has_var(INPUT_GET, 'pag') )
        {
        	$this->SetCurrent( (integer)filter_input( INPUT_GET, 'pag', FILTER_VALIDATE_INT) );
            $bReturn = TRUE;
        }//if( filter_has_var(INPUT_GET, 'pag') )
        return $bReturn;
    }

}
define('PBR_PAGE_LOADED',1);
?>
