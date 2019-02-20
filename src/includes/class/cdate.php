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
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CDate
{
    /** Constants
     ***********/
    const MINYEAR=2000;
    const MAXYEAR=2043;

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

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
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function __construct()
    {
        $this->m_iCurrentMonth=$this->m_iRequestMonth=date('n');
        $this->m_iCurrentYear=$this->m_iRequestYear=date('Y');
        $this->m_iCurrentDay=$this->m_iRequestDay=date('j');
    }

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
     */
    public function __clone() { trigger_error( 'Attempting to clone CDate', E_USER_NOTICE ); }

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
            self::$m_pInstance = new CDate();
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
     */
    public function SetRequestYear( $iValue )
    {
        if( is_scalar($iValue) )
        {
            if( is_integer($iValue) && ($iValue>=CDate::MINYEAR) && ($iValue<=CDate::MAXYEAR) )
            {
                $this->m_iRequestYear = $iValue;
            }//if( is_integer($sValue) && ($iValue>=CDate::MINYEAR) && ($iValue<=CDate::MAXYEAR) )
        }//if( is_scalar($iValue) )
    }

   /**
     * function: SetRequestMonth
     * description: Set requested month
     * parameter: INTEGER|$iValues - month
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetRequestMonth( $iValue )
    {
        if( is_scalar($iValue) )
        {
            if( is_integer($iValue) && ($iValue>0) && ($iValue<13) )
            {
                $this->m_iRequestMonth = $iValue;
            }//if( is_integer($sValue) && ($iValue>0) && ($iValue<13) )
        }//if( is_scalar($iValue) )
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
        if( is_scalar($iValue) )
        {
            if( is_integer($iValue) && ($iValue>0) && ($iValue<32) )
            {
                $this->m_iRequestDay = $iValue;
            }//if( is_integer($iValue) && ($iValue>0) && ($iValue<32) )
        }//if( is_scalar($iValue) )
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
        if( is_integer($iDay) && ($iDay>0) && ($iDay<32) )
        {
            if( ($this->m_iCurrentMonth==$this->m_iRequestMonth) &&
                ($this->m_iCurrentYear==$this->m_iRequestYear) &&
                ($this->m_iCurrentDay==$iDay) )
            {
                $bReturn=TRUE;
            }
        }
        return $bReturn;
    }

   /**
     * function:  GetDayName
     * description: return a day nome or all the day name
     * parameter: INTEGER|$iValue - day
     * return: STRING or ARRAY
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetDayName($iValue=7)
    {
        $tDayName = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        if( is_integer($iValue) && ($iValue>=0) && ($iValue<7) )
        {
            $tReturn = $tDayName[$iValue];
        }
        else
        {
            $tReturn = 'UNKNOWN';
        }
        return $tReturn;
    }

   /**
     * function: GetMonthName
     * description: return a month name
     * parameter: INTEGER|$iValue - month
     *            INTEGER|iFilter - 1 if characters should be converted into html entities
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetMonthName( $iValue, $iFilter=0 )
    {
        $tMonthName = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
        //Build
        if( is_integer($iValue) && ($iValue>0) && ($iValue<13) )
        {
            $sReturn = $tMonthName[$iValue-1];
        }
        else
        {
            $sReturn = 'UNKNOWN';
        }//if( is_integer($iValue) && ($iValue>0) && ($iValue<13) )
        // Sanitize
        if( 1===$iFilter )
        {
            $sReturn=htmlentities($sReturn,ENT_COMPAT,'UTF-8');
        }//if( 1===$iFilter )
        return $sReturn;
    }

   /**
     * function: ReadInput
     * description: Read GET or POST input date values
     * parameters: INTEGER|$iFilter - Filter to apply
     * return: BOOLEAN| TRUE or FALSE if no input value
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ReadInput($iFilter)
    {
        $bReturn = FALSE;
        if( (($iFilter===INPUT_POST) || ($iFilter===INPUT_GET))
            && filter_has_var($iFilter,'rem') && filter_has_var($iFilter,'rey') )
        {

            // Get day
            if( filter_has_var($iFilter,'red') )
            {
                $tFilter = array('options' => array('min_range' => 1, 'max_range' => 31));
                $this->SetRequestDay(filter_input($iFilter,'red',FILTER_VALIDATE_INT,$tFilter));
            }//if( filter_has_var($iFilter,'red') )

            // Get month
            $tFilter = array('options' => array('min_range' => 1, 'max_range' => 12));
            $this->SetRequestMonth(filter_input($iFilter,'rem',FILTER_VALIDATE_INT,$tFilter));

            // Get year
            $tFilter = array('options' => array('min_range' => CDate::MINYEAR, 'max_range' => CDate::MAXYEAR));
            $this->SetRequestYear(filter_input($iFilter,'rey',FILTER_VALIDATE_INT,$tFilter));

            $bReturn = TRUE;

        }//if( ($iFilter===INPUT_POST) || ($iFilter===INPUT_GET) )
        return $bReturn;
    }

}
define('PBR_DATE_LOADED',1);
?>
