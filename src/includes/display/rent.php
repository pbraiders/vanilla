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
 *                  - $pRent (instance of CRent)
 *                  - $pDate (instance of CDate)
 *                  - $pContact ( instance of CContact)
 *                  - $iMessageCode (integer)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_integer($iMessageCode) || !isset($pRent) || !isset($pContact) || !isset($pDate) || !isset($pHeader) )
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

    /** Encode data
     **************/
    $sEncodedLastName  = $pContact->GetLastName(1);
    $sEncodedFirstName = $pContact->GetFirstName(1);
    $sEncodedMonthName = $pDate->GetMonthName( $pDate->GetRequestMonth(), 1);

    /** Build form title
     *******************/
    $sFormTitle  = $pDate->GetRequestDay().' '.$sEncodedMonthName.' '.$pDate->GetRequestYear().' - ';
    $sFormTitle .= $sEncodedLastName.' '.$sEncodedFirstName;

    /** Build menu href
     ******************/
    $sContactHRef = PBR_URL.'contact.php?'.CContact::IDENTIFIERTAG.'='.$pContact->GetIdentifier();
    $sCalendarHRefMonth = '';
    if( ($pDate->GetRequestYear()!=$pDate->GetCurrentYear())
        || ($pDate->GetRequestMonth()!=$pDate->GetCurrentMonth()) )
    {
        $sCalendarHRefMonth  = '<li><a title="Aller &#224; ce mois" href="'.PBR_URL;
        $sCalendarHRefMonth .= '?'.CDate::YEARTAG.'='.$pDate->GetRequestYear();
        $sCalendarHRefMonth .= '&amp;'.CDate::MONTHTAG.'='.$pDate->GetRequestMonth().'">';
        $sCalendarHRefMonth .= $sEncodedMonthName.' '.$pDate->GetRequestYear().'</a></li>';
    }//if...
    $sCalendarHRefDay  = '<li><a title="Aller &#224; ce jour" href="'.PBR_URL.'day.php';
    $sCalendarHRefDay .='?'.CDate::YEARTAG.'='.$pDate->GetRequestYear();
    $sCalendarHRefDay .= '&amp;'.CDate::MONTHTAG.'='.$pDate->GetRequestMonth();
    $sCalendarHRefDay .= '&amp;'.CDate::DAYTAG.'='.$pDate->GetRequestDay().'">';
    $sCalendarHRefDay .= $pDate->GetRequestDay().' '.$sEncodedMonthName.' '.$pDate->GetRequestYear().'</a></li>';

    /** Build default rent values
     ****************************/
    $iCountReal     = ( $pRent->GetCountReal()==0 ? '' : $pRent->GetCountReal() );
    $iCountPlanned  = ( $pRent->GetCountPlanned()==0 ? '' : $pRent->GetCountPlanned() );
    $iCountCanceled = ( $pRent->GetCountCanceled()==0 ? '' : $pRent->GetCountCanceled() );
    $iAge    = $pRent->GetAge();
    $iArrhes = $pRent->GetArrhes();
    $iMax    = $pRent->GetMax();

    /** Build create and update info
     *******************************/
    $sHelpUpdate = '';
    $sHelpCreate = '<li class="help"><em>Cr&#233;&#233; par '.$pRent->GetCreationUser(1).' le '.$pRent->GetCreationDate(1).'</em></li>';
    if( strlen( $pRent->GetUpdateDate() )>0 )
    {
        $sHelpUpdate = '<li class="help"><em>Modifi&#233; par '.$pRent->GetUpdateUser(1).' le '.$pRent->GetUpdateDate(1).'</em></li>';
    }//if( strlen( $pRent->GetUpdateDate() )>0 )

?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagetop'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller en bas de la page" href="#pagebottom">&#8595;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Aller au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php
    // Display calendar hrer
    if( !empty($sCalendarHRefMonth) )
    {
        echo $sCalendarHRefMonth,"\n";
    }//if( !empty($sCalendarHRefMonth) )
    echo $sCalendarHRefDay,"\n";
?>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
</ul>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1><?php echo $sFormTitle;?></h1>
<form id="FORMRENT" method="post" action="<?php echo PBR_URL.'rent.php'; ?>">
<fieldset class="fieldsetform">
<legend class="legendmain">Contact</legend>
<input type="hidden" name="<?php echo CRent::IDENTIFIERTAG; ?>" value="<?php echo $pRent->GetIdentifier(); ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
<ul>
<li class="label required">Nom</li>
<li><input id="contactlastname" class="inputText" type="text" value="<?php echo $sEncodedLastName; ?>" maxlength="<?php echo CContact::LASTNAMEMAX; ?>" size="10" name="<?php echo CContact::LASTNAMETAG; ?>"  disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="navigation"><a title="Voir la fiche" href="<?php echo $sContactHRef;?>"><em>Voir la fiche</em></a></li>
<li class="label required">Pr&eacute;nom</li>
<li><input id="contactfirstname" class="inputText" type="text" value="<?php echo $sEncodedFirstName; ?>" maxlength="<?php echo CContact::FIRSTNAMEMAX; ?>" size="10" name="<?php echo CContact::FIRSTNAMETAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label required">T&#233;l&#233;phone</li>
<li><input id="contactphone" class="inputText" type="text" value="<?php echo $pContact->GetTel(1);?>" maxlength="<?php echo CContact::TELMAX; ?>" size="10" name="<?php echo CContact::TELTAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Email</li>
<li><input id="contactemail" class="inputText" type="text" value="<?php echo $pContact->GetEmail(1);?>" maxlength="<?php echo CContact::EMAILMAX; ?>" size="10" name="<?php echo CContact::EMAILTAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Adresse</li>
<li><input id="contactaddress_1" class="inputText" type="text" value="<?php echo $pContact->GetAddress(1);?>" maxlength="<?php echo CContact::ADDRESSMAX; ?>" size="10" name="<?php echo CContact::ADDRESSTAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label hide">&nbsp;</li>
<li><input id="contactaddress_2" class="inputText" type="text" value="<?php echo $pContact->GetAddressMore(1);?>" maxlength="<?php echo CContact::ADDRESSMOREMAX; ?>" size="10" name="<?php echo CContact::ADDRESSMORETAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Ville</li>
<li><input id="contactcity" class="inputText" type="text" value="<?php echo $pContact->GetCity(1);?>" maxlength="<?php echo CContact::CITYMAX; ?>" size="10" name="<?php echo CContact::CITYTAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Code postal</li>
<li><input id="contactzip" class="inputText" type="text" value="<?php echo $pContact->GetZip(1);?>" maxlength="<?php echo CContact::ZIPMAX; ?>" size="10" name="<?php echo CContact::ZIPTAG; ?>" disabled="disabled"<?php echo $pHeader->GetCloseTag(); ?></li>
</ul>
</fieldset>
<fieldset class="fieldsetform">
<legend class="legendmain">R&#233;servation</legend>
<fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
<legend>Taille du groupe</legend>
<ul>
<li class="labelF real">R&#233;el</li>
<li><input id="rentsizereal" class="inputText" type="text" value="<?php echo $iCountReal;?>" maxlength="3" size="3" name="<?php echo CRent::REALTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="labelF planned">Suppos&#233;</li>
<li><input id="rentsizeplanned" class="inputText" type="text" value="<?php echo $iCountPlanned;?>" maxlength="3" size="3" name="<?php echo CRent::PLANNEDTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="labelF canceled">Annul&#233;</li>
<li><input id="rentsizecanceled" class="inputText" type="text" value="<?php echo $iCountCanceled;?>" maxlength="3" size="3" name="<?php echo CRent::CANCELEDTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="labelF maximum">Maximum</li>
<li><input id="maxsize" class="inputText" type="text" value="<?php echo $iMax;?>" maxlength="3" size="3" name="<?php echo CRent::MAXTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
</ul>
</fieldset>
<fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
<legend>&#194;ge</legend>
<ul>
<li class="radio"><input id="rentage1" class="inputRadio" type="radio" name="<?php echo CRent::AGETAG; ?>" value="1" <?php if($iAge===1) echo 'checked="checked"'; echo $pHeader->GetCloseTag(); ?>16-25 ans</li>
<li class="radio"><input id="rentage2" class="inputRadio" type="radio" name="<?php echo CRent::AGETAG; ?>" value="2" <?php if(($iAge===2)||($iAge===0)) echo 'checked="checked"'; echo $pHeader->GetCloseTag(); ?>26-35 ans</li>
<li class="radio"><input id="rentage3" class="inputRadio" type="radio" name="<?php echo CRent::AGETAG; ?>" value="3" <?php if($iAge===3) echo 'checked="checked"'; echo $pHeader->GetCloseTag(); ?>35 ans et +</li>
<li class="label hide">&nbsp;</li>
</ul>
</fieldset>
<fieldset class="fieldsetsub fieldsetform">
<legend>Arrhes</legend>
<ul>
<li class="radio"><input id="rentarrhre1" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="1" <?php if($iArrhes===1) echo 'checked="checked"'; echo $pHeader->GetCloseTag(); ?>Esp&#232;ce</li>
<li class="radio"><input id="rentarrhre2" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="2" <?php if($iArrhes===2) echo 'checked="checked"'; echo $pHeader->GetCloseTag(); ?>Ch&#232;que</li>
<li class="radio"><input id="rentarrhre3" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="3" <?php if($iArrhes===3) echo 'checked="checked"'; echo $pHeader->GetCloseTag(); ?>CB</li>
<li class="radio"><input id="rentarrhre4" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="0"<?php echo $pHeader->GetCloseTag(); ?>Aucune</li>
</ul>
</fieldset>
<ul>
<?php echo $sHelpCreate,"\n"; ?>
<?php if( strlen($sHelpUpdate)>0 ) echo $sHelpUpdate,"\n"; ?>
</ul>
</fieldset>
<!--fieldset class="fieldsetsub fieldsetform"-->
<fieldset class="fieldsetform">
<!--legend>Commentaires</legend-->
<legend class="legendmain">Commentaires</legend>
<textarea cols="30" rows="5" class="inputTextarea" id="rentcomment" name="<?php echo CRent::COMMENTTAG; ?>"><?php echo $pRent->GetComment(1); ?></textarea>
<p class="small"><em><?php echo CRent::COMMENTLENGTH; ?> caract&#232;res ou moins</em></p>
</fieldset>
<ul class="listbuttons">
<li><input class="inputButton" type="submit" value="Enregistrer" name="upd"<?php echo $pHeader->GetCloseTag(); ?>&nbsp;<input class="inputButton" type="submit" value="Supprimer" name="del"<?php echo $pHeader->GetCloseTag(); ?></li>
</ul>
</form>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagebottom'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Aller au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php
    // Display calendar hrer
    if( !empty($sCalendarHRefMonth) )
    {
        echo $sCalendarHRefMonth,"\n";
    }//if( !is_null($sCalendarHRefMonth) )
    echo $sCalendarHRefDay,"\n";
?>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
</ul>
