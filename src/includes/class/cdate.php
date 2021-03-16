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
 * description: describes date for calendar
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-15 - is not a singleton anymore
 *                                        delete GetInstance()
 *                                        delete DeleteInstance()
 *                                        update GetMonthName()
 *                                        update SetRequestYear()
 *                                        update SetRequestMonth()
 *                                        update SetRequestDay()
 *                                        update GetDayName()
 *                                        update IsSame()
 *                                        add SanitizeInt()
 *                                        add TAG constants
 *                                        update ReadInput()
 *                                        add ReadInputDay()
 *                                        add ReadInputMonth()
 *                                        add ReadInputYear()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CDate
{
    /** Constants
     ***********/
    const MINYEAR         = 2000;
    const MAXYEAR         = 2043;
    const MONTHTAG        = 'rem';
    const YEARTAG         = 'rey';
    const DAYTAG          = 'red';
    const CURRENTMONTHTAG = 'cum';
    const CURRENTYEARTAG  = 'cuy';
    const CURRENTDAYTAG   = 'cud';
    const NAVGOTOTAG      = 'go';
    const NAXNEXTTAG      = 'nex';
    const NAXPREVTAG      = 'pre';

    /** Private attributs
     ********************/

    // Current date
    private $m_iCurrentMonth;
    private $m_iCurrentYear;
    private $m_iCurrentDay;

    // Requested date
    private $m_iRequestMonth;
    private $m_iRequestYear;
    private $m_iRequestDay;

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

   /**
     * function: ReadInputMonth
     * description: Read GET or POST input month value
     * parameters: INTEGER|$iFilter - Filter to apply
     *              STRING|sTAG     - month tag
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function ReadInputMonth( $iFilter, $sTag)
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET)) && filter_has_var( $iFilter, $sTag) )
        {
            $tFilter = array('options' => array('min_range' => 1, 'max_range' => 12));
            $this->SetRequestMonth( filter_input( $iFilter, $sTag, FILTER_VALIDATE_INT, $tFilter));
            $bReturn = TRUE;
        }//if(...
        return $bReturn;
    }

   /**
     * function: ReadInputYear
     * description: Read GET or POST input year value
     * parameters: INTEGER|$iFilter - Filter to apply
     *              STRING|sTAG     - year tag
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function ReadInputYear( $iFilter, $sTag)
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET)) && filter_has_var( $iFilter, $sTag) )
        {
            $tFilter = array('options' => array('min_range' => CDate::MINYEAR, 'max_range' => CDate::MAXYEAR));
            $this->SetRequestYear( filter_input( $iFilter, $sTag, FILTER_VALIDATE_INT, $tFilter));
            $bReturn = TRUE;
        }//if(...
        return $bReturn;
    }

   /**
     * function: ReadInputDay
     * description: Read GET or POST input day value
     * parameters: INTEGER|$iFilter - Filter to apply
     *              STRING|sTAG     - day tag
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function ReadInputDay( $iFilter, $sTag)
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET)) && filter_has_var( $iFilter, $sTag) )
        {
            $tFilter = array('options' => array('min_range' => 1, 'max_range' => 31));
            $this->SetRequestDay( filter_input( $iFilter, $sTag, FILTER_VALIDATE_INT, $tFilter));
            $bReturn = TRUE;
        }//if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET)) && filter_has_var( $iFilter, $sTag) )
        return $bReturn;
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
        $this->m_iCurrentMonth=$this->m_iRequestMonth=date('n');
        $this->m_iCurrentYear=$this->m_iRequestYear=date('Y');
        $this->m_iCurrentDay=$this->m_iRequestDay=date('j');
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
     * function: PreviousRequestYear
     * description: Set request year -1
     * parameter: none
     * return: FALSE if reach the min
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function PreviousRequestYear()
    {
        $bReturn = FALSE;
        if( $this->m_iRequestYear>CDate::MINYEAR )
        {
            $this->m_iRequestYear--;
            $bReturn = TRUE;
        }//if( $this->m_iRequestYear>CDate::MINYEAR )
        return $bReturn;
    }

   /**
     * function: NextRequestYear
     * description: Set request year +1
     * parameter: none
     * return: FALSE if reach the max
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function NextRequestYear()
    {
        $bReturn = FALSE;
        if( $this->m_iRequestYear<CDate::MAXYEAR )
        {
            $this->m_iRequestYear++;
            $bReturn = TRUE;
        }//if( $this->m_iRequestYear<CDate::MAXYEAR )
        return $bReturn;
    }

    /**
     * function: GetCurrentYear
     * description: return the current year
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetCurrentYear() { return (integer)$this->m_iCurrentYear; }

    /**
     * function: GetCurrentMonth
     * description: return the current month
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetCurrentMonth() { return (integer)$this->m_iCurrentMonth; }

    /**
     * function: GetCurrentDay
     * description: return the current day
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetCurrentDay() { return (integer)$this->m_iCurrentDay; }

    /**
     * function: GetRequestYear
     * description: return the requested year
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetRequestYear() { return (integer)$this->m_iRequestYear; }

    /**
     * function: GetRequestMonth
     * description: return the request month
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetRequestMonth() { return (integer)$this->m_iRequestMonth; }

    /**
     * function: GetRequestDay
     * description: return the request day
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetRequestDay() { return (integer)$this->m_iRequestDay; }

   /**
     * function: SetRequestYear
     * description: Set requested year
     * parameter: INTEGER|$iValues - year
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - Sanitize
     */
    public function SetRequestYear( $iValue )
    {
        if( $this->SanitizeInt( $iValue, CDate::MINYEAR, CDate::MAXYEAR)===TRUE )
        {
            $this->m_iRequestYear = $iValue;
        }//if( $this->SanitizeInt( $iValue, CDate::MINYEAR, CDate::MAXYEAR)===TRUE )
    }

   /**
     * function: SetRequestMonth
     * description: Set requested month
     * parameter: INTEGER|$iValues - month
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - Sanitize
     */
    public function SetRequestMonth( $iValue )
    {
        if( $this->SanitizeInt( $iValue, 1, 12)===TRUE  )
        {
            $this->m_iRequestMonth = $iValue;
        }//if( $this->SanitizeInt( $iValue, 1, 12)===TRUE  )/
    }

   /**
     * function: SetRequestDay
     * description: Set requested Day
     * parameter: INTEGER|$iValues - day
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetRequestDay( $iValue )
    {
        if( $this->SanitizeInt( $iValue, 1, 31)===TRUE )
        {
            $this->m_iRequestDay = $iValue;
        }//if( $this->SanitizeInt( $iValue, 1, 31)===TRUE )
    }

   /**
     * function: PreviousRequestMonth
     * description: Set request month -1
     * parameter: none
     * return: FALSE if year reach the min
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function PreviousRequestMonth()
    {
        $bReturn=TRUE;
        if( $this->m_iRequestMonth<=1 )
        {
            // January case, change year
            if( $this->PreviousRequestYear()===TRUE )
            {
                $this->m_iRequestMonth=12;
            }
            else
            {
                // Reach the min
                $bReturn=FALSE;
            }//if( $this->PreviousRequestYear()===TRUE )
        }
        else
        {
            // Normal case
            $this->m_iRequestMonth--;
        }
        return $bReturn;
    }

   /**
     * function: NextRequestMonth
     * description: Set request month +1
     * parameter: none
     * return: FALSE if year reach the max
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function NextRequestMonth()
    {
        $bReturn=TRUE;
        if( $this->m_iRequestMonth>=12 )
        {
            // December case, change year
            if( $this->NextRequestYear()===TRUE )
            {
                $this->m_iRequestMonth=1;
            }
            else
            {
                // Reach the max
                $bReturn=FALSE;
            }//if( $this->NextRequestYear()===TRUE )
        }
        else
        {
            // Normal case
            $this->m_iRequestMonth++;
        }
        return $bReturn;
    }

   /**
     * function: IsSame
     * description: return TRUE if current date = requested date
     * parameter: INTEGER|$iDay - day
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsSame( $iDay )
    {
        $bReturn=FALSE;
        if( $this->SanitizeInt( $iDay, 1, 31)===TRUE )
        {
            if( ($this->m_iCurrentMonth==$this->m_iRequestMonth) &&
                ($this->m_iCurrentYear==$this->m_iRequestYear) &&
                ($this->m_iCurrentDay==$iDay) )
            {
                $bReturn=TRUE;
            }//if
        }//if( $this->SanitizeInt( $iDay, 1, 31)===TRUE )
        return $bReturn;
    }

   /**
     * function:  GetDayName
     * description: return a day nome or all the day name
     * parameter: INTEGER|$iValue - day
     * return: STRING or ARRAY
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - sanitize
     */
    public function GetDayName($iValue=7)
    {
        $tDayName = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        if( $this->SanitizeInt( $iValue, 0, 6)===TRUE )
        {
            $tReturn = $tDayName[$iValue];
        }
        else
        {
            $tReturn = 'UNKNOWN';
        }//if( $this->SanitizeInt( $iValue, 0, 6)===TRUE )
        return $tReturn;
    }

   /**
     * function: GetMonthName
     * description: return a month name
     * parameter: INTEGER|$iValue - month
     *            INTEGER|iFilter - 1 if characters should be converted into html entities
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     *                                        sanitize
     */
    public function GetMonthName( $iValue, $iFilter=0 )
    {
        $tMonthName = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
        //Build
        if( $this->SanitizeInt( $iValue, 1, 12)===TRUE )
        {
            $sReturn = $tMonthName[$iValue-1];
        }
        else
        {
            $sReturn = 'UNKNOWN';
        }//if( $this->SanitizeInt( $iValue, 1, 12)===TRUE )
        // Sanitize
        if( 1===$iFilter )
        {
            $sReturn=htmlentities($sReturn,ENT_QUOTES,'UTF-8');
        }//if( 1===$iFilter )
        return $sReturn;
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input date values
     * parameters: INTEGER|iFilter - Filter to apply
     *             BOOLEAN|bGetDAy - if true read day values
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use constants
     */
    public function ReadInput( $iFilter, $bGetDay=FALSE )
    {
        $bReturn = FALSE;
        if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        {
            if( filter_has_var( $iFilter, CDate::NAXNEXTTAG ) )
            {
                /** Navigation: next month
                 *************************/
                $bReturn = $this->ReadInputMonth( $iFilter, CDate::CURRENTMONTHTAG);
                $bReturn = $bReturn && $this->ReadInputYear( $iFilter, CDate::CURRENTYEARTAG);
                $this->NextRequestMonth();
            }
            elseif( filter_has_var( $iFilter, CDate::NAXPREVTAG ) )
            {
                /** Navigation: previous month
                 *****************************/
                $bReturn = $this->ReadInputMonth( $iFilter, CDate::CURRENTMONTHTAG);
                $bReturn = $bReturn && $this->ReadInputYear( $iFilter, CDate::CURRENTYEARTAG);
                $this->PreviousRequestMonth();
            }
            else
            {
                /** Case: Normal
                 ***************/
                // Read year
                $bReturn = $this->ReadInputYear( $iFilter, CDate::YEARTAG);
                // Read month
                $bReturn = $bReturn && $this->ReadInputMonth( $iFilter, CDate::MONTHTAG);
                // Read day
                if( $bReturn && $bGetDay )
                    $bReturn = $this->ReadInputDay( $iFilter, CDate::DAYTAG);
            }
        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )

        return $bReturn;
    }

}
