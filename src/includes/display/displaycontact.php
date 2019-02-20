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
 * description: Display the contact page.
 *              The following objects should exist:
 *                  - CContact
 *                  - CDate
 *                  - $sAction (new|show|update)
 *                  - tRecordset
 *                  - CPaging
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_CONTACT_LOADED')  || !defined('PBR_DATE_LOADED') )
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
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

/**
  * function: BuildCurrentRent
  * description: Build and display a rent
  * parameters: ARRAY|tRecord    - recordset
  *                              should have keys: (reservation_id, reservation_year,
  *                              reservation_month, reservation_day, reservation_real, reservation_planned,
  *                              reservation_canceled, reservation_arrhes, reservation_age, reservation_comment)
  *           INTEGER|iPagingMax - Max page count
  *            INTEGER|iIndex     - Record count
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildCurrentRent(&$tRecord, $iPagingMax, $iIndex)
{
    if( is_array($tRecord) && array_key_exists('reservation_id', $tRecord)
                           && array_key_exists('reservation_year', $tRecord)
                           && array_key_exists('reservation_month', $tRecord)
                           && array_key_exists('reservation_day', $tRecord)
                           && array_key_exists('reservation_real', $tRecord)
                           && array_key_exists('reservation_planned', $tRecord)
                           && array_key_exists('reservation_canceled', $tRecord)
                           && array_key_exists('reservation_arrhes', $tRecord)
                           && array_key_exists('reservation_age', $tRecord) )
    {
        if( ($iIndex<=1) && ($iPagingMax<=1) )
        {
            // Do not display the blue line
            $sBuffer='<li class="first">';
        }
        else
        {
            $sBuffer='<li>';
        }
        $sBuffer.='<a href="'.PBR_URL.'rent.php?act=show&amp;rei='.$tRecord['reservation_id'].'" title="Modifier la r&#233;servation">';
        $sBuffer.=$tRecord['reservation_day'].' ';
        $sBuffer.=CDate::GetInstance()->GetMonthName((integer)$tRecord['reservation_month'],1).' ';
        $sBuffer.=$tRecord['reservation_year'].' ';
        if( $tRecord['reservation_real']>0 )
        {
            $sBuffer.='<span class="real">'.$tRecord['reservation_real'].'&nbsp;</span>';
        }
        else
        {
            $sBuffer.='<span class="empty hide">&nbsp;</span>';
        }//real
        if( $tRecord['reservation_planned']>0 )
        {
            $sBuffer.='<span class="planned">'.$tRecord['reservation_planned'].'&nbsp;</span>';
        }
        else
        {
            $sBuffer.='<span class="empty hide">&nbsp;</span>';
        }//planned
        if( $tRecord['reservation_canceled']>0 )
        {
            $sBuffer.='<span class="canceled">'.$tRecord['reservation_canceled'].'&nbsp;</span>';
        }
        else
        {
            $sBuffer.='<span class="empty hide">&nbsp;</span>';
        }//canceled
        if($tRecord['reservation_age']==1) $sBuffer.=' &#8226; 16-25 ans';
        if(($tRecord['reservation_age']==2)||($tRecord['reservation_age']==0)) $sBuffer.=' &#8226; 26-35 ans';
        if($tRecord['reservation_age']==3) $sBuffer.=' &#8226; 35 ans et +';
        if($tRecord['reservation_arrhes']==1) $sBuffer.=' &#8226; Esp&#232;ce';
        if($tRecord['reservation_arrhes']==2) $sBuffer.=' &#8226; Ch&#232;que';
        if($tRecord['reservation_arrhes']==3) $sBuffer.=' &#8226; CB';
        if( array_key_exists('reservation_comment', $tRecord) && strlen($tRecord['reservation_comment'])>0 )
        {
                $sBuffer.='<span class="hide"> &#8226; '.htmlentities(TruncMe($tRecord['reservation_comment'],50),ENT_QUOTES,'UTF-8');
        }//comment
        $sBuffer.='</a></li>';
        echo $sBuffer,"\n";
    }//if( is_array($tRecord) && array_key_exists(
}

    /** Build paging href
     ********************/
    if( $sAction=='show' )
    {
        $sPagingHRef=PBR_URL.'contact.php?act=show';
        $sPagingHRef.='&amp;cti='.CContact::GetInstance()->GetIdentifier();
    }
    else
    {
        $sPagingHRef='';
    }//if( $sAction=='show' )

    /** Building form depends of action
     **********************************/
    $sHelpCreate='';
    $sHelpUpdate='';
    if( $sAction=='new' )
    {
        // Build title
        $sFormTitle='Nouveau contact';
        // Build form action
        $sFormAction=PBR_URL.'contactnew.php';
    }
    else
    {
        // Build title
        $sFormTitle=CContact::GetInstance()->GetLastName(1).' '.CContact::GetInstance()->GetFirstName(1);
        // Build form action
        $sFormAction=PBR_URL.'contact.php?act=update&cti='.CContact::GetInstance()->GetIdentifier();
        // Build create date
        $sHelpCreate='      <li class="help"><em>Créé par '.CContact::GetInstance()->GetCreationUser(1).' le '.CContact::GetInstance()->GetCreationDate(1).'</em></li>';
        // Build update date
        if( strlen(CContact::GetInstance()->GetUpdateDate())>0)
        {
            $sHelpUpdate='      <li class="help"><em>Modifié par '.CContact::GetInstance()->GetUpdateUser(1).' le '.CContact::GetInstance()->GetUpdateDate(1).'</em></li>';
        }//if( strlen(CContact::GetInstance()->GetUpdateDate())>0)
    }//if( $sAction=='new' )
?>
 <div id="PAGE">
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller &#224; l'historique" name="pagetop" href="#pagemiddle">&#8595;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
   <li><a title="Retourner &#224; la liste des contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
  </ul>
  <hr/>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <h1><?php echo $sFormTitle;?></h1>
   <form id="FORMCONTACT" method="post" action="<?php echo $sFormAction;?>">
    <fieldset class="fieldsetform">
     <legend class="legendmain">Informations</legend>
<?php if($sAction=='new') {?>
     <input type="hidden" name="act" value="new" />
<?php }//if( $sAction=='new' )?>
     <ul>
      <li class="label required">Nom</li>
      <li><input id="contactlastname" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetLastName(1);?>" maxlength="40" size="10" name="ctl"/></li>
      <li class="label required">Pr&eacute;nom</li>
      <li><input id="contactfirstname" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetFirstName(1);?>" maxlength="40" size="10" name="ctf" /></li>
      <li class="label required">T&#233;l&#233;phone</li>
      <li><input id="contactphone" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetTel(1);?>" maxlength="40" size="10" name="ctp" /></li>
      <li class="label">Email</li>
      <li><input id="contactemail" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetEmail(1);?>" maxlength="255" size="10" name="cte" /></li>
      <li class="label">Adresse</li>
      <li><input id="contactaddress_1" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetAddress(1);?>" maxlength="255" size="10" name="cta" /></li>
      <li class="label hide">&nbsp;</li>
      <li><input id="contactaddress_2" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetAddressMore(1);?>" maxlength="255" size="10" name="ctm" /></li>
      <li class="label">Ville</li>
      <li><input id="contactcity" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetCity(1);?>" maxlength="255" size="10" name="ctc" /></li>
      <li class="label">Code postal</li>
      <li><input id="contactzip" class="inputText" type="text" value="<?php echo CContact::GetInstance()->GetZip(1);?>" maxlength="8" size="10" name="ctz" /></li>
<?php if( strlen($sHelpCreate)>0 ) echo $sHelpCreate,"\n"; ?>
<?php if( strlen($sHelpUpdate)>0 ) echo $sHelpUpdate,"\n"; ?>
     </ul>
    </fieldset>
<?php if($sAction=='show') { ?>
    <fieldset class="fieldsetform">
     <legend class="legendmain">Commentaires</legend>
     <textarea cols="30" rows="5" class="inputTextarea" id="rentcomment" name="ctk"><?php echo CContact::GetInstance()->GetComment(1);?></textarea>
     <p><em>300 caract&#232;res ou moins</em></p>
    </fieldset>
<?php }//if( $sAction=='show') ?>
    <ul class="listbuttons">
     <li class="listbuttonitem">
      <input class="inputButton" type="submit" value="Enregistrer" name="new"/>
<?php if($sAction!='new') {?>
      &nbsp;<input class="inputButton" type="submit" value="&nbsp;Supprimer" name="del"/></li>
<?php }//if( $sAction!='new' )?>
     </li>
    </ul>
   </form>
   <a name="pagemiddle"></a>
<?php if( $sAction=='show') { ?>
   <fieldset>
    <legend class="legendmain">Historique</legend>
<?php
    if( CPaging::GetInstance()->GetMax()>1 )
    {
        echo '<ul class="navigation menu">',"\n";
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
        echo '</ul>',"\n";
    }//if( CPaging::GetInstance()->GetMax()>1 )
?>
    <ul class="records">
<?php
    if( is_array($tRecordset) )
    {
        $iIndex=1;
        foreach( $tRecordset as $tRecord )
        {
            BuildCurrentRent($tRecord,CPaging::GetInstance()->GetMax(),$iIndex++);
        }//foreach( $tRecordset as $tRecord )
    }//if( is_array($tRecordset) )
?>
    </ul>
   </fieldset>
<?php }//if( $sAction=='show') ?>
  </div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
   <li><a title="Retourner &#224; la liste des contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
  </ul>
