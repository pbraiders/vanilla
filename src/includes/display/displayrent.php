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
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_RENT_LOADED') || !defined('PBR_CONTACT_LOADED') || !defined('PBR_DATE_LOADED') )
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
        if($iCode===2)
        {
            $sBuffer.='<p class="success">Enregistrement r&#233;ussi.</p>';
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

    /** Build form title
     *******************/
    $sFormTitle=CDate::GetInstance()->GetRequestDay().' ';
    $sFormTitle.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth(),1).' ';
    $sFormTitle.=CDate::GetInstance()->GetRequestYear().' - ';
    $sFormTitle.=CContact::GetInstance()->GetLastName(1).' '.CContact::GetInstance()->GetFirstName(1);

    /** Build calendar href
     **********************/
    $sCalendarHRefMonth=NULL;
    if( CDate::GetInstance()->GetRequestYear()!=CDate::GetInstance()->GetCurrentYear()
        || CDate::GetInstance()->GetRequestMonth()!=CDate::GetInstance()->GetCurrentMonth() )
    {
        $sCalendarHRefMonth='<li><a title="Retourner &#224; ce mois" href="'.PBR_URL.'?act=calendar';
        $sCalendarHRefMonth.='&amp;rey='.CDate::GetInstance()->GetRequestYear();
        $sCalendarHRefMonth.='&amp;rem='.CDate::GetInstance()->GetRequestMonth().'">';
        $sCalendarHRefMonth.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth(),1).' ';
        $sCalendarHRefMonth.=CDate::GetInstance()->GetRequestYear().'</a></li>';
    }//if...
    $sCalendarHRefDay='<li><a title="Retourner &#224; ce jour" href="'.PBR_URL.'day.php?act=show';
    $sCalendarHRefDay.='&amp;rey='.CDate::GetInstance()->GetRequestYear();
    $sCalendarHRefDay.='&amp;rem='.CDate::GetInstance()->GetRequestMonth();
    $sCalendarHRefDay.='&amp;red='.CDate::GetInstance()->GetRequestDay().'">';
    $sCalendarHRefDay.=CDate::GetInstance()->GetRequestDay().' ';
    $sCalendarHRefDay.=CDate::GetInstance()->GetMonthName(CDate::GetInstance()->GetRequestMonth(),1).' ';
    $sCalendarHRefDay.=CDate::GetInstance()->GetRequestYear().'</a></li>';

    /** Build form action
     ********************/
    $sFormAction=PBR_URL.'rent.php';

    /** Build default rent values
     ****************************/
    $iCountReal=(CRent::GetInstance()->GetCountReal()==0?'':CRent::GetInstance()->GetCountReal());
    $iCountPlanned=(CRent::GetInstance()->GetCountPlanned()==0?'':CRent::GetInstance()->GetCountPlanned());
    $iCountCanceled=(CRent::GetInstance()->GetCountCanceled()==0?'':CRent::GetInstance()->GetCountCanceled());
    $iAge=CRent::GetInstance()->GetAge();
    $iArrhes=CRent::GetInstance()->GetArrhes();
    $iMax=CRent::GetInstance()->GetMax();

    /** Build create and update info
     *******************************/
    $sHelpCreate='<li class="help"><em>Cr&#233&#233 par '.CRent::GetInstance()->GetCreationUser(1).' le '.CRent::GetInstance()->GetCreationDate(1).'</em></li>';
    if( strlen(CRent::GetInstance()->GetUpdateDate())>0)
    {
        $sHelpUpdate='<li class="help"><em>Modifi&#233 par '.CRent::GetInstance()->GetUpdateUser(1).' le '.CRent::GetInstance()->GetUpdateDate(1).'</em></li>';
    }
    else
    {
        $sHelpUpdate='';
    }

?>
 <div id="PAGE">
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en bas de la page" name="pagetop" href="#pagebottom">&#8595;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php
    // Display calendar hrer
    if( !is_null($sCalendarHRefMonth) )
    {
        echo $sCalendarHRefMonth,"\n";
    }//if( !is_null($sCalendarHRefMonth) )
    echo $sCalendarHRefDay,"\n";
?>
   <li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
  </ul>
  <hr/>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <h1><?php echo $sFormTitle;?></h1>
   <form id="FORMRENT" method="post" action="<?php echo $sFormAction;?>">
    <input type="hidden" name="act" value="update" />
    <input type="hidden" name="rei" value="<?php echo CRent::GetInstance()->GetIdentifier();?>" />
    <fieldset class="fieldsetform">
     <legend class="legendmain">Contact</legend>
     <ul>
      <li class="label">Nom</li>
      <li><input id="contactlastname" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetLastName(1);?>" maxlength="40" size="10" name="ctl" disabled="disabled" /></li>
      <li class="label">Pr&eacute;nom</li>
      <li><input id="contactfirstname" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetFirstName(1);?>" maxlength="40" size="10" name="ctf" disabled="disabled" /></li>
      <li class="label">T&#233;l&#233;phone</li>
      <li><input id="contactphone" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetTel(1);?>" maxlength="40" size="10" name="ctp" disabled="disabled" /></li>
      <li class="label">Email</li>
      <li><input id="contactemail" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetEmail(1);?>" maxlength="255" size="10" name="cte" disabled="disabled" /></li>
      <li class="label">Adresse</li>
      <li><input id="contactaddress_1" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetAddress(1);?>" maxlength="255" size="10" name="cta" disabled="disabled" /></li>
      <li class="label hide">&nbsp;</li>
      <li><input id="contactaddress_2" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetAddressMore(1);?>" maxlength="255" size="10" name="ctm" disabled="disabled" /></li>
      <li class="label">Ville</li>
      <li><input id="contactcity" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetCity(1);?>" maxlength="255" size="10" name="ctc" disabled="disabled" /></li>
      <li class="label">Code postal</li>
      <li><input id="contactzip" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetZip(1);?>" maxlength="8" size="10" name="ctz" disabled="disabled" /></li>
     </ul>
    </fieldset>
    <fieldset class="fieldsetform">
     <legend class="legendmain">R&#233;servation</legend>
     <fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
      <legend>Taille du groupe</legend>
      <ul>
       <li class="labelF real">R&#233;el</li>
       <li><input id="rentsizereal" class="inputText" type="text" value="<?php echo $iCountReal;?>" maxlength="3" size="3" name="rer" /></li>
       <li class="labelF planned">Suppos&#233;</li>
       <li><input id="rentsizeplanned" class="inputText" type="text" value="<?php echo $iCountPlanned;?>" maxlength="3" size="3" name="rep" /></li>
       <li class="labelF canceled">Annul&#233;e</li>
       <li><input id="rentsizecanceled" class="inputText" type="text" value="<?php echo $iCountCanceled;?>" maxlength="3" size="3" name="rec" /></li>
       <li class="labelF maximum">Maximum</li>
       <li><input id="maxsize" class="inputText" type="text" value="<?php echo $iMax;?>" maxlength="3" size="3" name="max" /></li>
      </ul>
     </fieldset>
     <fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
      <legend>&#194;ge</legend>
      <ul>
       <li class="radio"><input id="rentage1" class="inputRadio" type="radio" name="rea" value="1" <?php if($iAge===1) echo 'checked="checked"';?> />16-25 ans</li>
       <li class="radio"><input id="rentage2" class="inputRadio" type="radio" name="rea" value="2" <?php if(($iAge===2)||($iAge===0)) echo 'checked="checked"';?> />26-35 ans</li>
       <li class="radio"><input id="rentage3" class="inputRadio" type="radio" name="rea" value="3" <?php if($iAge===3) echo 'checked="checked"';?> />35 ans et +</li>
       <li class="label hide">&nbsp;</li>
      </ul>
     </fieldset>
     <fieldset class="fieldsetsub fieldsetform">
      <legend>Arrhes</legend>
      <ul>
       <li class="radio"><input id="rentarrhre1" class="inputRadio" type="radio" name="reh" value="1" <?php if($iArrhes===1) echo 'checked="checked"';?> />Esp&#232;ce</li>
       <li class="radio"><input id="rentarrhre2" class="inputRadio" type="radio" name="reh" value="2" <?php if($iArrhes===2) echo 'checked="checked"';?> />Ch&#232;que</li>
       <li class="radio"><input id="rentarrhre3" class="inputRadio" type="radio" name="reh" value="3" <?php if($iArrhes===3) echo 'checked="checked"';?> />CB</li>
       <li class="radio"><input id="rentarrhre4" class="inputRadio" type="radio" name="reh" value="0" />Aucune</li>
      </ul>
     </fieldset>
     <ul>
<?php if( strlen($sHelpCreate)>0 ) echo $sHelpCreate,"\n"; ?>
<?php if( strlen($sHelpUpdate)>0 ) echo $sHelpUpdate,"\n"; ?>
     </ul>
    </fieldset>
    <fieldset class="fieldsetsub fieldsetform">
     <legend>Commentaires</legend>
     <textarea cols="30" rows="5" class="inputTextarea" id="rentcomment" name="rek"><?php echo CRent::GetInstance()->GetComment(1);?></textarea>
      <p class="small"><em>300 caract&#232;res ou moins</em></p>
    </fieldset>
    <ul class="listbuttons">
     <li>
      <input class="inputButton" type="submit" value="Enregistrer" name="new"/>&nbsp;
      <input class="inputButton" type="submit" value="&nbsp;Supprimer" name="del"/>
     </li>
    </ul>
   </form>
  </div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php
    // Display calendar hrer
    if( !is_null($sCalendarHRefMonth) )
    {
        echo $sCalendarHRefMonth,"\n";
    }//if( !is_null($sCalendarHRefMonth) )
    echo $sCalendarHRefDay,"\n";
?>
   <li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
  </ul>
