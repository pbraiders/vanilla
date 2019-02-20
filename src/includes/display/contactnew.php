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
 * description: Display the new contact page.
 *              The following objects should exist:
 *                  - $pContact ( instance of CContact)
 *                  - $iMessageCode (integer)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_integer($iMessageCode) || !isset($pContact) || !isset($pHeader) )
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
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

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
<li><a title="Afficher le calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
</ul>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1>Nouveau contact</h1>
<form id="FORMCONTACT" method="post" action="<?php echo PBR_URL;?>contactnew.php">
<fieldset class="fieldsetform">
<legend class="legendmain">Informations</legend>
<ul>
<li class="label required">Nom</li>
<li><input id="contactlastname" class="inputText" type="text" value="<?php echo $pContact->GetLastName(1); ?>" maxlength="<?php echo CContact::LASTNAMEMAX; ?>" size="10" name="<?php echo CContact::LASTNAMETAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label required">Pr&eacute;nom</li>
<li><input id="contactfirstname" class="inputText" type="text" value="<?php echo $pContact->GetFirstName(1); ?>" maxlength="<?php echo CContact::FIRSTNAMEMAX; ?>" size="10" name="<?php echo CContact::FIRSTNAMETAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label required">T&#233;l&#233;phone</li>
<li><input id="contactphone" class="inputText" type="text" value="<?php echo $pContact->GetTel(1);?>" maxlength="<?php echo CContact::TELMAX; ?>" size="10" name="<?php echo CContact::TELTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Email</li>
<li><input id="contactemail" class="inputText" type="text" value="<?php echo $pContact->GetEmail(1);?>" maxlength="<?php echo CContact::EMAILMAX; ?>" size="10" name="<?php echo CContact::EMAILTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Adresse</li>
<li><input id="contactaddress_1" class="inputText" type="text" value="<?php echo $pContact->GetAddress(1);?>" maxlength="<?php echo CContact::ADDRESSMAX; ?>" size="10" name="<?php echo CContact::ADDRESSTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label hide">&nbsp;</li>
<li><input id="contactaddress_2" class="inputText" type="text" value="<?php echo $pContact->GetAddressMore(1);?>" maxlength="<?php echo CContact::ADDRESSMOREMAX; ?>" size="10" name="<?php echo CContact::ADDRESSMORETAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Ville</li>
<li><input id="contactcity" class="inputText" type="text" value="<?php echo $pContact->GetCity(1);?>" maxlength="<?php echo CContact::CITYMAX; ?>" size="10" name="<?php echo CContact::CITYTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="label">Code postal</li>
<li><input id="contactzip" class="inputText" type="text" value="<?php echo $pContact->GetZip(1);?>" maxlength="<?php echo CContact::ZIPMAX; ?>" size="10" name="<?php echo CContact::ZIPTAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
</ul>
</fieldset>
<ul class="listbuttons">
<li class="listbuttonitem">
<input class="inputButton" type="submit" value="Enregistrer" name="new"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
</li>
</ul>
</form>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagebottom'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Afficher le calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
</ul>
