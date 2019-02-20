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
 * description: Display the calendar page.
 *              The following object(s) should exist:
 *                  - CAuth (class)
 *                  - $pDate (instance of CDate)
 *                  - $tRecordset (array)
 *                  - $bAdmin (boolean)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * W3C: This document was successfully checked as XHTML 1.0 Strict!
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($pDate) || !is_array($tRecordset) || !is_bool($bAdmin) )
    die('-1');

/**
  * function: ExtractRentsInfo
  * description: Extract rent values
  * parameters: ARRAY|$tRecordset - recordset (should have keys: day,real,planned,canceled,max)
  *           INTEGER|iDay        - requested day
  * return: array(real,planned,canceled,max)
  * author: Olivier JULLIEN - 2010-02-04
  */
function ExtractRentsInfo( &$tRecord, $iDay )
{
	$tReturn = array( 0, 0, 0, 0);
    if( is_array($tRecord) && array_key_exists('day', $tRecord)
                           && array_key_exists('real', $tRecord)
                           && array_key_exists('planned', $tRecord)
                           && array_key_exists('canceled', $tRecord)
                           && array_key_exists('max', $tRecord) )
    {
        if( $tRecord['day']==$iDay )
        {
            $tReturn= array((integer)$tRecord['real']
                           ,(integer)$tRecord['planned']
                           ,(integer)$tRecord['canceled']
                           ,(integer)$tRecord['max']);
        }//if( $tRecord['day']==$iDay )
    }//if( is_array($tRecord) && array_key_exists(...
    return $tReturn;
}

/**
  * function: BuildDay
  * description: Build a day block
  * parameters: INTEGER|iDayName  - day name
                INTEGER|iDayNum   - day number
                INTEGER|iMonth    - month
                INTEGER|iYear     - year
                INTEGER|iReal     - real rent count
                INTEGER|iPlanned  - planned rent count
                INTEGER|iCanceled - canceled rent count
                INTEGER|iMax      - max rent count
                INTEGER|iState    - 0:out of month, 1:normal, 2:current day
  * return: BOOLEAN - TRUE or FALSE
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildDay($iDayName,$iDayNum,$iMonth,$iYear,$iReal,$iPlanned,$iCanceled,$iMax,$iState)
{
    global $tDayHeadings;
    global $pDate;

    /** Verify parameters
     ********************/
    if( !is_integer($iDayName) || !is_integer($iDayNum) || !is_integer($iMonth) || !is_integer($iYear) ||
        !is_integer($iReal) || !is_integer($iPlanned) || !is_integer($iCanceled) || !is_integer($iMax) ||
        !is_integer($iState) || ($iMonth<0) || ($iMonth>12) || ($iYear<0) || ($iDayName<0)  ||
        ($iDayName>6) || ($iDayNum<0)  || ($iDayNum>31) || ($iReal<0) || ($iPlanned<0) || ($iCanceled<0) ||
        ($iMax<0) || ($iState<0) || ($iState>2) )
    {
        $iState=0;
    }//if( !is_integer(...

    /** Initialize
     *************/
    if( $iState>0 )
    {
        $iTotal=$iReal+$iPlanned;
    }//if( $iState>0 )

    /** Build
     ********/
    if( $iState===0 )
    {
        $sBuffer='<li class="day"><ul class="dayinfo"><li></li><li></li><li></li></ul></li>';
    }
    else
    {
        $sHref=PBR_URL.'day.php?'.CDate::YEARTAG.'='.$iYear.'&amp;'.CDate::MONTHTAG.'='.$iMonth.'&amp;'.CDate::DAYTAG.'='.$iDayNum;
        $sBuffer='<li class="day"><ul class="dayinfo">';
        if( $iState===2 )
        {
            $sBuffer.='<li class="daydatenow">';
        }
        else
        {
            $sBuffer.='<li class="daydate">';
        }//if( $iState===2 )
        $sBuffer.='<a href="'.$sHref.'">';
        $sBuffer.='<span class="dayname">'.$pDate->GetDayName($iDayName).'</span>';
        $sBuffer.='<span class="daynum">'.$iDayNum.'</span></a></li>';
        $sBuffer.='<li class="dayrent"><a href="'.$sHref.'">';
        if( $iReal>0 )
        {
            $sBuffer.='<span class="real">'.$iReal.'</span>';
        }//if( $iReal>0 )
        if( $iPlanned>0 )
        {
            $sBuffer.='<span class="planned">'.$iPlanned.'</span>';
        }//if( $iPlanned>0 )
        if( $iCanceled>0 )
        {
            $sBuffer.='<span class="canceled">'.$iCanceled.'</span>';
        }//if( $iCanceled>0 )
        if( $iTotal==0 )
        {
            $sBuffer.='&nbsp;</a></li>';
            $sBuffer.='<li class="daytotal"><a href="'.$sHref.'">&nbsp;</a></li>';
        }
        else
        {
            $sBuffer.='</a></li>';
            $sBuffer.='<li class="daytotal"><a href="'.$sHref.'">'.$iTotal.' / '.$iMax.'</a></li>';
        }//if( $iTotal==0 )
        $sBuffer.='</ul></li>';
    }//if( $iState===0 )
    return $sBuffer;
}

?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<hr/>
<ul class="navigation menu">
<li><a title="Aller en bas de la page" name="pagetop" href="#pagebottom">&#8595;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
<?php if( $bAdmin===TRUE ){ ?>
<li><a title="Options" href="<?php echo PBR_URL;?>parameters.php">Options</a></li>
<?php }//if( $bAdmin===TRUE ) ?>
</ul>
<hr/>
<div id="CONTENT">
<form id="FORMCALENDAR" method="post" action="<?php echo PBR_URL;?>">
<?php
    echo '<h1>'.$pDate->GetMonthName( $pDate->GetRequestMonth() ,1).' '.$pDate->GetRequestYear().'</h1>',"\n";
?>
<ul id="CALENDARNAVIGATION" class="listbuttons">
<li>
<input type="hidden" name="<?php echo CDate::CURRENTYEARTAG; ?>" value="<?php echo $pDate->GetRequestYear();?>" />
<input type="hidden" name="<?php echo CDate::CURRENTMONTHTAG; ?>" value="<?php echo $pDate->GetRequestMonth();?>" />
<input class="inputButton" type="submit" value="&nbsp;&nbsp;&#60;&#60;&nbsp;&nbsp;" name="<?php echo CDate::NAXPREVTAG; ?>"/>
<input class="inputButton" type="submit" value="&nbsp;&nbsp;&#62;&#62;&nbsp;&nbsp;" name="<?php echo CDate::NAXNEXTTAG; ?>"/>
</li>
<li><select class="inputSelect" name="<?php echo CDate::MONTHTAG; ?>">
<?php
    /** Display month list
     *********************/
    for( $iIndex=0; $iIndex<12; $iIndex++ )
    {
        $iCurrentMonth = $iIndex+1;
        $sBuffer='<option value="'.$iCurrentMonth.'"';
        if( $pDate->GetRequestMonth()==$iCurrentMonth )
        {
            $sBuffer.=' selected="selected"';
        }//if( $pDate->GetRequestMonth()==$iCurrentMonth )
        $sBuffer .= '>'.$pDate->GetMonthName( $iCurrentMonth, 1).'</option>';
        echo $sBuffer,"\n";
    }//for( $iIndex=0;$iIndex<12;$iIndex++)
?>
</select></li>
<li><select class="inputSelect" name="<?php echo CDate::YEARTAG; ?>">
<?php
    /** Display year list
     ********************/
    $iStart = $pDate->GetRequestYear()-3;
    $iEnd = $pDate->GetRequestYear()+2;
    if( $pDate->GetRequestYear()>($pDate->GetCurrentYear()+2) )
    {
        $iStart = $pDate->GetCurrentYear();
    }//if( $pDate->GetRequestYear()>($pDate->GetCurrentYear()+2) )
    if( $pDate->GetRequestYear()<($pDate->GetCurrentYear()-2) )
    {
        $iEnd = $pDate->GetCurrentYear();
    }//if( $pDate->GetRequestYear()<($pDate->GetCurrentYear()-2) )
    if( $iStart<CDate::MINYEAR )
    {
        $iStart = CDate::MINYEAR;
    }//if( $iStart<CDate::MINYEAR )
    if($iEnd>CDate::MAXYEAR)
    {
        $iEnd = CDate::MAXYEAR;
    }//if($iEnd>CDate::MAXYEAR)
    // Display
    for( $iIndex=$iStart; $iIndex<=$iEnd; $iIndex++ )
    {
        $sBuffer='<option value="'.$iIndex.'"';
        if( $pDate->GetRequestYear()==$iIndex )
        {
            $sBuffer.=' selected="selected"';
        }//if( $pDate->GetRequestYear()==$iIndex )
        $sBuffer.='>'.$iIndex.'</option>';
        echo $sBuffer,"\n";
    }//for( $iIndex=$iStart;$iIndex<=$iEnd;$iIndex++)
?>
</select></li>
<li>
<input class="inputButton" type="submit" value="&nbsp;Aller&nbsp;" name="<?php echo CDate::NAVGOTOTAG; ?>"/>
</li>
</ul>
</form>
<div id="DAYSHEADER">
<ul>
<?php
    /** Display day name
     *******************/
    for( $iIndex=0; $iIndex<7; $iIndex++ )
    {
        echo '<li>'.$pDate->GetDayName($iIndex).'</li>',"\n";
    }//for( $iIndex=0;$iIndex<7;$iIndex++)
?>
    </ul>
   </div>
<?php
    /** Compute the current date unix timestamp
     ******************************************/
    $iTimestamp = mktime( 0, 0, 0, $pDate->GetRequestMonth(), 1 ,$pDate->GetRequestYear() );

    /** Compute the day of the week
     ******************************/
    $iDayOfTheWeek = date( 'w', $iTimestamp )-1;
    if( $iDayOfTheWeek<0 )
    {
        $iDayOfTheWeek = 6;
    }// if( $iDayOfTheWeek<0 )

    /** Compute Number of days in the given month
     ********************************************/
    $iNumberOfDaysInMonth = date( 't', $iTimestamp );

    /** Display last days of the previous month
     ******************************************/
    echo '<ul class="days">',"\n";
    for( $iIndex=0; $iIndex<$iDayOfTheWeek; $iIndex++ )
    {
        echo BuildDay( 0, 0, 0, 0, 0, 0, 0, 0, 0 ),"\n";
    }//for( $iIndex=0;$iIndex<$iDayOfTheWeek;$iIndex++ )

    /** Display the days of the month
     ********************************/
    for( $iIndex=1; $iIndex<=$iNumberOfDaysInMonth; $iIndex++ )
    {
        $iState = 1;
        $iReal = $iPlanned = $iCanceled = $iMax = 0;
        // Extract rent values
        if( isset($tRecordset[$iIndex]) )
        {
            list($iReal,$iPlanned,$iCanceled,$iMax) = ExtractRentsInfo( $tRecordset[$iIndex], $iIndex);
        }//if( isset($tRecordset[$iIndex]) )
        // Current date
        if( $pDate->IsSame($iIndex)===TRUE )
        {
            $iState=2;
        }//if( $pDate->IsSame()===TRUE )
        echo BuildDay($iDayOfTheWeek++,$iIndex,$pDate->GetRequestMonth(),$pDate->GetRequestYear(),$iReal,$iPlanned,$iCanceled,$iMax,$iState),"\n";
        // End of the week
        if( $iDayOfTheWeek>6 )
        {
            $iDayOfTheWeek=0;
            echo '</ul>',"\n";
            // More days to display
            if( $iIndex<$iNumberOfDaysInMonth )
            {
                echo '<ul class="days">',"\n";
            }//if( $iIndex<$iNumberOfDaysInMonth )
        }//if( $iDayOfTheWeek>6 )
    }//for( $iIndex=1;$iIndex<=$iNumberOfDaysInMonth;$iIndex++ )

    /** Display the remain days of the week
     **************************************/
    if( $iDayOfTheWeek>0 )
    {
        for( $iDayOfTheWeek;$iDayOfTheWeek<=6;$iDayOfTheWeek++ )
        {
            echo BuildDay(0,0,0,0,0,0,0,0,0),"\n";
        }//for( $iDayOfTheWeek;$iDayOfTheWeek<=6;$iDayOfTheWeek++ )
        echo '</ul>',"\n";
    }//if( $iDayOfTheWeek>0 )
?>
</div>
<hr/>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
<?php if( $bAdmin===TRUE ){ ?>
<li><a title="Options" href="<?php echo PBR_URL;?>parameters.php">Options</a></li>
<?php }//if( $bAdmin===TRUE ) ?>
</ul>
