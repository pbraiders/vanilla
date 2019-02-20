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
 * description: Display the day page for printing.
 *              The following objects should exist:
 *                  - CDate
 *                  - $tRecordset
 *                  - $sFormTitle
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_DATE_LOADED') )
    die('-1');

/**
  * function: BuildCurrentRent
  * description: Build and display a rent
  * parameters: ARRAY|tRecord     - recordset
  *             (should have keys: reservation_id, reservation_real, reservation_planned
  *                              , reservation_canceled, reservation_arrhes, contact_lastname
  *                              , contact_firstname, contact_phone)
  *           INTEGER|bPageBreak  - true if page break
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildCurrentRent(&$tRecord, $bPageBreak)
{
    if( is_array($tRecord) && array_key_exists('reservation_id', $tRecord)
                           && array_key_exists('reservation_real', $tRecord)
                           && array_key_exists('reservation_planned', $tRecord)
                           && array_key_exists('reservation_canceled', $tRecord)
                           && array_key_exists('reservation_arrhes', $tRecord)
                           && array_key_exists('contact_lastname', $tRecord)
                           && array_key_exists('contact_firstname', $tRecord)
                           && array_key_exists('contact_phone', $tRecord)
                           && array_key_exists('reservation_comment', $tRecord))
    {
        $sBuffer='<li>';
        if( $tRecord['reservation_id']==0 )
        {
            // Resume
            $sBuffer.='<span class="real">&nbsp;'.$tRecord['reservation_real'].'</span>';
            $sBuffer.='<span class="planned">&nbsp;'.$tRecord['reservation_planned'].'</span>';
            $sBuffer.='<span class="canceled">&nbsp;'.$tRecord['reservation_canceled'].'</span>';
            $iTotal=(integer)$tRecord['reservation_real']+(integer)$tRecord['reservation_planned'];
            $sBuffer.='<span>Total: '.$iTotal.' / '.$tRecord['reservation_arrhes'].'</span><hr/>';
        }
        else
        {
            // Rent
            $sBuffer.='<span class="real">&nbsp;';
            $sBuffer.=($tRecord['reservation_real']==0?'0':$tRecord['reservation_real']);
            $sBuffer.='</span><span class="planned">&nbsp;';
            $sBuffer.=($tRecord['reservation_planned']==0?'0':$tRecord['reservation_planned']);
            $sBuffer.='</span><span class="canceled">&nbsp;';
            $sBuffer.=($tRecord['reservation_canceled']==0?'0':$tRecord['reservation_canceled']);
            $sBuffer.='</span>';
            $sBuffer.='<span class="name">'.htmlentities($tRecord['contact_lastname'],ENT_QUOTES,'UTF-8').' ';
            $sBuffer.=htmlentities($tRecord['contact_firstname'],ENT_QUOTES,'UTF-8').' &#8226; ';
            $sBuffer.=htmlentities($tRecord['contact_phone'],ENT_QUOTES,'UTF-8');
            if($tRecord['reservation_arrhes']==1) $sBuffer.=' &#8226; Esp&#232;ce';
            if($tRecord['reservation_arrhes']==2) $sBuffer.=' &#8226; Ch&#232;que';
            if($tRecord['reservation_arrhes']==3) $sBuffer.=' &#8226; CB';
            // Comment
            $sBuffer.='</span><p class="comment';
            if( $bPageBreak )
            {
                $sBuffer.=' pagebreakafter';
            }//if( $bPageBreak )
            $sBuffer.='">';
            if( strlen($tRecord['reservation_comment'])>0 )
            {
                $sBuffer.=htmlentities($tRecord['reservation_comment'],ENT_QUOTES,'UTF-8');
            }
            else
            {
                $sBuffer.='&nbsp;';
            }//if( strlen($tRecord['reservation_comment'])>0 )
            $sBuffer.='</p>';
        }//if( $tRecord['reservation_id']==0 )
        $sBuffer.='</li>';
        echo $sBuffer,"\n";
    }//if( is_array($tRecord) && array_key_exists(
}

?>
  <div id="PAGE">
   <h1><?php echo htmlentities($sFormTitle,ENT_COMPAT,'UTF-8');?></h1>
   <hr/>
   <ul>
<?php
    if( is_array($tRecordset) )
    {
        $iIndex=1;
        foreach( $tRecordset as $tRecord )
        {
            if( $iIndex>PBR_PRINT_BREAK )
            {
                BuildCurrentRent($tRecord,true);
                $iIndex=1;
            }
            else
            {
                BuildCurrentRent($tRecord,false);
            }//if( $iIndex>PBR_PRINT_BREAK )
            $iIndex++;
        }//foreach( $tRecordset as $tRecord )
    }//if( is_array($tRecordset) )
?>
   </ul>
  </div><!--PAGE-->
 </body>
</html>
