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
 * description: Display the day page.
 *              The following objects should exist:
 *                  - CContact
 *                  - CRent
 *                  - CDate
 *                  - $sFormTitle
 *                  - $tRecordset
 *                  - CPaging
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_CONTACT_LOADED') || !defined('PBR_DATE_LOADED') || !defined('PBR_RENT_LOADED') || !defined('PBR_PAGE_LOADED') )
    die('-1');

/**
  * function: BuildMessage
  * description: Build and display a message.
  * parameters: INTEGER|iCode - message code
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildMessage($iCode)
{
    if( $iCode>0 )
    {
        $sBuffer='<div id="MESSAGE">';
        if( $iCode===1 )
        {
            $sBuffer.='<p class="error">Le nom,le pr&#233;nom et le num&#233;ro de t&#233;l&#233;phone doivent &ecirc;tre renseign&eacute;s.</p>';
        }
        elseif($iCode===2)
        {
            $sBuffer.='<p class="success">Enregistrement r&#233;ussi.</p>';
        }
        elseif($iCode===3)
        {
            $sBuffer.='<p class="success">Suppression r&#233;ussie.</p>';
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

/**
  * function: BuildCurrentRent
  * description: Build and display a rent
  * parameters: ARRAY|tRecord - recordset
  *             (should have keys: reservation_id, reservation_real, reservation_planned
  *                              , reservation_canceled, reservation_arrhes, contact_lastname
  *                              , contact_firstname, contact_phone)
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildCurrentRent(&$tRecord)
{
    global $sPrintHRef;
    if( is_array($tRecord) && array_key_exists('reservation_id', $tRecord)
                           && array_key_exists('reservation_real', $tRecord)
                           && array_key_exists('reservation_planned', $tRecord)
                           && array_key_exists('reservation_canceled', $tRecord)
                           && array_key_exists('reservation_arrhes', $tRecord)
                           && array_key_exists('contact_lastname', $tRecord)
                           && array_key_exists('contact_firstname', $tRecord)
                           && array_key_exists('contact_phone', $tRecord) )
    {
        $sBuffer='<li>';
        if( $tRecord['reservation_id']==0 )
        {
            // Resume
            $sBuffer.='<a title="Imprimer les r&#233;servations" href="'.$sPrintHRef.'" target="_blank">';
            $sBuffer.='<div class="real">'.$tRecord['reservation_real'].'</div>';
            $sBuffer.='<div class="planned">'.$tRecord['reservation_planned'].'</div>';
            $sBuffer.='<div class="canceled">'.$tRecord['reservation_canceled'].'</div>';
            $iTotal=(integer)$tRecord['reservation_real']+(integer)$tRecord['reservation_planned'];
            $sBuffer.='<span>Total: '.$iTotal.' / '.$tRecord['reservation_arrhes'].'</span></a>';
        }
        else
        {
            // Rent
            $sBuffer.='<a href="'.PBR_URL.'rent.php?act=show&amp;rei='.$tRecord['reservation_id'].'" title="Modifier la r&#233;servation">';
            $sBuffer.='<div class="';
            $sBuffer.=($tRecord['reservation_real']==0?'empty hide">&nbsp;':'real">'.$tRecord['reservation_real']);
            $sBuffer.='</div><div class="';
            $sBuffer.=($tRecord['reservation_planned']==0?'empty hide">&nbsp;':'planned">'.$tRecord['reservation_planned']);
            $sBuffer.='</div><div class="';
            $sBuffer.=($tRecord['reservation_canceled']==0?'empty hide">&nbsp;':'canceled">'.$tRecord['reservation_canceled']);
            $sBuffer.='</div>';
            $sBuffer.='<span>'.htmlentities($tRecord['contact_lastname'],ENT_QUOTES,'UTF-8').' ';
            $sBuffer.=htmlentities($tRecord['contact_firstname'],ENT_QUOTES,'UTF-8').' &#8226; ';
            $sBuffer.=htmlentities($tRecord['contact_phone'],ENT_QUOTES,'UTF-8');
            if($tRecord['reservation_arrhes']==1) $sBuffer.=' &#8226; Esp&#232;ce';
            if($tRecord['reservation_arrhes']==2) $sBuffer.=' &#8226; Ch&#232;que';
            if($tRecord['reservation_arrhes']==3) $sBuffer.=' &#8226; CB';
            if( array_key_exists('reservation_comment', $tRecord) && strlen($tRecord['reservation_comment'])>0 )
            {
                $sBuffer.='</span><span class="hide"> &#8226; '.htmlentities(TruncMe($tRecord['reservation_comment'],50),ENT_QUOTES,'UTF-8');
            }//commment
            $sBuffer.='</span></a>';
        }//if( $tRecord['reservation_id']==0 )
        $sBuffer.='</li>';
        echo $sBuffer,"\n";
    }//if( is_array($tRecord) && array_key_exists(
}

    /** Build print href
     *******************/
    $sPrintHRef=PBR_URL.'dayprint.php?act=print';
    $sPrintHRef.='&amp;rey='.CDate::GetInstance()->GetRequestYear();
    $sPrintHRef.='&amp;rem='.CDate::GetInstance()->GetRequestMonth();
    $sPrintHRef.='&amp;red='.CDate::GetInstance()->GetRequestDay();

    /** Build select href
     ********************/
    $sSelectHRef=PBR_URL.'select.php?act=search';
    $sSelectHRef.='&amp;rey='.CDate::GetInstance()->GetRequestYear();
    $sSelectHRef.='&amp;rem='.CDate::GetInstance()->GetRequestMonth();
    $sSelectHRef.='&amp;red='.CDate::GetInstance()->GetRequestDay();

    /** Build calendar href
     **********************/
    $sCalendarHRef=NULL;
    if( CDate::GetInstance()->GetRequestYear()!=CDate::GetInstance()->GetCurrentYear()
        || CDate::GetInstance()->GetRequestMonth()!=CDate::GetInstance()->GetCurrentMonth() )
    {
        $sCalendarHRef='<li><a title="Retourner &#224; ce mois" href="'.PBR_URL.'?act=calendar';
        $sCalendarHRef.='&amp;rey='.CDate::GetInstance()->GetRequestYear();
        $sCalendarHRef.='&amp;rem='.CDate::GetInstance()->GetRequestMonth().'">';
        $sCalendarHRef.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth(),1).' ';
        $sCalendarHRef.=CDate::GetInstance()->GetRequestYear().'</a></li>';
    }//if...

    /** Build paging href
     ********************/
    $sPagingHRef=PBR_URL.'day.php?';
    $sPagingHRef.='rey='.CDate::GetInstance()->GetRequestYear();
    $sPagingHRef.='&amp;rem='.CDate::GetInstance()->GetRequestMonth();
    $sPagingHRef.='&amp;red='.CDate::GetInstance()->GetRequestDay();
    if( $sAction=='select' )
    {
        $sPagingHRef.='&amp;act=select&amp;cti='.CContact::GetInstance()->GetIdentifier();
    }
    else
    {
        $sPagingHRef.='&amp;act=show';
    }//if( $sAction=='select' )

    /** Build form action
     ********************/
    $sFormAction=PBR_URL.'day.php';
    if( $sAction=='select' )
    {
        $sDisable='disabled="disabled"';
    }
    else
    {
        $sDisable='';
    }//

    /** Build default rent values
     ****************************/
    $iCountReal=(CRent::GetInstance()->GetCountReal()==0?'':CRent::GetInstance()->GetCountReal());
    $iCountPlanned=(CRent::GetInstance()->GetCountPlanned()==0?'':CRent::GetInstance()->GetCountPlanned());
    $iAge=CRent::GetInstance()->GetAge();
    $iArrhes=CRent::GetInstance()->GetArrhes();

?>
 <div id="PAGE">
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller aux r&#233;servations courantes" name="pagetop" href="#pagemiddle">&#8595;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php
    // Display calendar hrer
    if( !is_null($sCalendarHRef) )
    {
        echo $sCalendarHRef,"\n";
    }//if( !is_null($sCalendarHRef) )
?>
   <li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
  </ul>
  <hr/>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <h1><?php echo htmlentities($sFormTitle,ENT_COMPAT,'UTF-8');?></h1>
   <form id="FORMDAY" method="post" action="<?php echo $sFormAction;?>">
    <fieldset>
     <legend class="legendmain">Nouvelle r&#233;servation</legend>
     <fieldset class="fieldsetsub fieldsetform">
      <legend>Contact</legend>
<?php
    if( $sAction=='select' )
    {
        echo '      <input type="hidden" name="act" value="newselected" />',"\n";
        echo '      <input type="hidden" name="cti" value="'.CContact::GetInstance()->GetIdentifier().'" />',"\n";
    }
    else
    {
        echo '      <input type="hidden" name="act" value="new" />',"\n";
    }//if( $sAction=='select' )
?>
      <input type="hidden" name="red" value="<?php echo CDate::GetInstance()->GetRequestDay(); ?>" />
      <input type="hidden" name="rem" value="<?php echo CDate::GetInstance()->GetRequestMonth(); ?>" />
      <input type="hidden" name="rey" value="<?php echo CDate::GetInstance()->GetRequestYear(); ?>" />
      <ul>
       <li class="label required">Nom</li>
       <li><input id="contactlastname" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetLastName(1);?>" maxlength="40" size="10" name="ctl" <?php echo $sDisable;?> /></li>
       <li class="navigation"><a title="Choisir un contact" href="<?php echo $sSelectHRef;?>"><em>Choisir dans la liste</em></a></li>
       <li class="label required">Pr&eacute;nom</li>
       <li><input id="contactfirstname" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetFirstName(1);?>" maxlength="40" size="10" name="ctf" <?php echo $sDisable;?> /></li>
       <li class="label required">T&#233;l&#233;phone</li>
       <li><input id="contactphone" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetTel(1);?>" maxlength="40" size="10" name="ctp" <?php echo $sDisable;?> /></li>
       <li class="label">Email</li>
       <li><input id="contactemail" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetEmail(1);?>" maxlength="255" size="10" name="cte" <?php echo $sDisable;?> /></li>
       <li class="label">Adresse</li>
       <li><input id="contactaddress_1" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetAddress(1);?>" maxlength="255" size="10" name="cta" <?php echo $sDisable;?>  /></li>
       <li class="label hide">&nbsp;</li>
       <li><input id="contactaddress_2" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetAddressMore(1);?>" maxlength="255" size="10" name="ctm" <?php echo $sDisable;?>  /></li>
       <li class="label">Ville</li>
       <li><input id="contactcity" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetCity(1);?>" maxlength="255" size="10" name="ctc" <?php echo $sDisable;?>  /></li>
       <li class="label">Code postal</li>
       <li><input id="contactzip" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetZip(1);?>" maxlength="8" size="10" name="ctz" <?php echo $sDisable;?>  /></li>
      </ul>
     </fieldset>
     <fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
      <legend>Taille du groupe</legend>
      <ul>
       <li class="labelF real">R&#233;el</li>
       <li><input id="rentsizereal" class="inputText" type="text" value="<?php echo $iCountReal;?>" maxlength="3" size="3" name="rer" /></li>
       <li class="labelF planned">Suppos&#233;</li>
       <li><input id="rentsizeplanned" class="inputText" type="text" value="<?php echo $iCountPlanned;?>" maxlength="3" size="3" name="rep" /></li>
       <li class="label hide">&nbsp;</li>
      </ul>
     </fieldset>
     <fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
      <legend>&#194;ge</legend>
      <ul>
       <li class="radio"><input id="rentage1" class="inputRadio" type="radio" name="rea" value="1" <?php if($iAge===1) echo 'checked="checked"';?> />16-25 ans</li>
       <li class="radio"><input id="rentage2" class="inputRadio" type="radio" name="rea" value="2" <?php if(($iAge===2)||($iAge===0)) echo 'checked="checked"';?> />26-35 ans</li>
       <li class="radio"><input id="rentage3" class="inputRadio" type="radio" name="rea" value="3" <?php if($iAge===3) echo 'checked="checked"';?> />35 ans et +</li>
      </ul>
     </fieldset>
     <fieldset class="fieldsetsub fieldsetform">
      <legend>Arrhes</legend>
      <ul>
       <li class="radio"><input id="rentarrhre1" class="inputRadio" type="radio" name="reh" value="1" <?php if($iArrhes===1) echo 'checked="checked"';?> />Esp&#232;ce</li>
       <li class="radio"><input id="rentarrhre2" class="inputRadio" type="radio" name="reh" value="2" <?php if($iArrhes===2) echo 'checked="checked"';?> />Ch&#232;que</li>
       <li class="radio"><input id="rentarrhre3" class="inputRadio" type="radio" name="reh" value="3" <?php if($iArrhes===3) echo 'checked="checked"';?> />CB</li>
      </ul>
     </fieldset>
     <ul class="listbuttons"><li><input class="inputButton" type="submit" value="&nbsp;&nbsp;&nbsp;Cr&#233;er&nbsp;&nbsp;&nbsp;" name="actnewrent"/></li></ul>
    </fieldset>
   </form>
   <a name="pagemiddle"></a>
   <fieldset>
    <legend class="legendmain">R&#233;servations courantes</legend>
    <ul class="navigation menu">
     <li><a title="Imprimer les r&#233;servations" href="<?php echo $sPrintHRef;?>" target="_blank">Imprimer</a></li>
<?php
    if( CPaging::GetInstance()->GetMax()>1 )
    {
        if( CPaging::GetInstance()->GetCurrent()>1 )
        {
            $sBuffer='<li><a title="Page pr&#233;c&#233;dente" href="';
            $sBuffer.=$sPagingHRef.'&amp;pag='.(CPaging::GetInstance()->GetCurrent()-1);
            $sBuffer.='">Page pr&#233;c&#233;dente</a></li>';
            echo $sBuffer,"\n";
        }//if( CPaging::GetInstance()->GetCurrent()>1 )
        if( CPaging::GetInstance()->GetCurrent()<CPaging::GetInstance()->GetMax() )
        {
            $sBuffer='<li><a title="Page suivante" href="';
            $sBuffer.=$sPagingHRef.'&amp;pag='.(CPaging::GetInstance()->GetCurrent()+1);
            $sBuffer.='">Page suivante</a></li>';
            echo $sBuffer,"\n";
        }//if( CPaging::GetInstance()->GetCurrent()<CPaging::GetInstance()->GetMax() )
    }//if( CPaging::GetInstance()->GetMax()>1 )
?>
    </ul>
    <ul class="records">
<?php
    if( is_array($tRecordset) )
    {
        foreach( $tRecordset as $tRecord )
        {
            BuildCurrentRent($tRecord);
        }//foreach( $tRecordset as $tRecord )
    }//if( is_array($tRecordset) )
?>
    </ul>
   </fieldset>
  </div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php
    // Display calendar hrer
    if( !is_null($sCalendarHRef) )
    {
        echo $sCalendarHRef,"\n";
    }//if( !is_null($sCalendarHRef) )
?>
   <li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
  </ul>
