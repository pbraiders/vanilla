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
 *                  - $pContact ( instance of CContact)
 *                  - $tRecordset (array)
 *                  - $pPaging (instance of CPaging)
 *                  - $pDate (instance of CDate)
 *                  - $iMessageCode (integer)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_integer($iMessageCode) || !isset($pPaging) || !isset($pContact) || !is_array($tRecordset) || !isset($pDate) || !isset($pHeader) )
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
            $sBuffer .= '<p class="error">Le nom,le pr&#233;nom et le num&#233;ro de t&#233;l&#233;phone doivent &ecirc;tre renseign&eacute;s.</p>';
        }
        elseif($iCode===2)
        {
            $sBuffer .= '<p class="success">Enregistrement r&#233;ussi.</p>';
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
function BuildCurrentRent(&$tRecord, $iPagingMax, $iIndex, CDate $pDate)
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
        $sBuffer.='<a href="'.PBR_URL.'rent.php?'.CRent::IDENTIFIERTAG.'='.$tRecord['reservation_id'].'" title="Modifier la r&#233;servation">';
        $sBuffer.=$tRecord['reservation_day'].' ';
        $sBuffer.= $pDate->GetMonthName( $tRecord['reservation_month'], 1).' ';
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
                $sBuffer.='<span class="hide"> &#8226; '.htmlentities(TruncMe($tRecord['reservation_comment'],50),ENT_QUOTES,'UTF-8').'</span>';
        }//comment
        $sBuffer.='</a></li>';
        echo $sBuffer,"\n";
    }//if( is_array($tRecord) && array_key_exists(
}

    /** Build form
     *************/

    // Build title
    $sFormTitle= $pContact->GetLastName(1).' '.$pContact->GetFirstName(1);

    // Build create date
    $sHelpCreate = '<li class="help"><em>Cr&#233;&#233; par '.$pContact->GetCreationUser(1).' le '.$pContact->GetCreationDate(1).'</em></li>';

    // Build update date
    $sHelpUpdate = '';
    if( strlen($pContact->GetUpdateDate())>0 )
    {
        $sHelpUpdate = '<li class="help"><em>Modifi&#233; par '.$pContact->GetUpdateUser(1).' le '.$pContact->GetUpdateDate(1).'</em></li>';
    }//if( strlen($pContact->GetUpdateDate())>0 )

    // Build paging href
    $sPagingHRef = PBR_URL.'contact.php?'.CContact::IDENTIFIERTAG.'='.$pContact->GetIdentifier();

?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagetop'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller &#224; l'historique" href="#pagemiddle">&#8595;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
<li><a title="Cr&#233;er un contact" href="<?php echo PBR_URL;?>contactnew.php">Cr&#233;er un contact</a></li>
</ul>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1><?php echo $sFormTitle; ?></h1>
<form id="FORMCONTACT" method="post" action="<?php echo PBR_URL;?>contact.php">
<fieldset class="fieldsetform">
<legend class="legendmain">Informations</legend>
<input type="hidden" name="<?php echo CContact::IDENTIFIERTAG; ?>" value="<?php echo $pContact->GetIdentifier(); ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
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
<?php echo $sHelpCreate,"\n"; ?>
<?php if( strlen($sHelpUpdate)>0 ) echo $sHelpUpdate,"\n"; ?>
</ul>
</fieldset>
<fieldset class="fieldsetform">
<legend class="legendmain">Commentaires</legend>
<textarea cols="30" rows="5" class="inputTextarea" id="rentcomment" name="<?php echo CContact::COMMENTTAG; ?>"><?php echo $pContact->GetComment(1);?></textarea>
<p><em><?php echo CContact::COMMENTLENGTH; ?> caract&#232;res ou moins</em></p>
</fieldset>
<ul class="listbuttons"><li class="listbuttonitem"><input class="inputButton" type="submit" value="Enregistrer" name="upd"<?php echo $pHeader->GetCloseTag(); ?>&nbsp;<input class="inputButton" type="submit" value="Supprimer" name="del"<?php echo $pHeader->GetCloseTag(); ?></li>
</ul>
</form>
<a name="pagemiddle"></a>
<?php echo $pHeader->GetAnchor('pagemiddle'),"\n"; ?>
<fieldset class="fieldsetform">
<?php

    /** Legend
     *********/
    $sBuffer  = '<legend class="legendmain">Historique';
    if( $pPaging->GetMax()>1 )
    {
        $sBuffer .= ' <em class="hide">(page '.$pPaging->GetCurrent().' sur '.$pPaging->GetMax().')</em>';
    }//if( $pPaging->GetMax()>1 )
    $sBuffer .= '</legend>';
    echo $sBuffer,"\n";

    /** Navigation
     *************/
    if( !empty($tRecordset) )
    {
        if( $pPaging->GetMax()>1 )
        {
            echo '<ul class="navigation menu">',"\n";
            // First page
            if( $pPaging->GetCurrent()>2 )
            {
                $sBuffer='<li><a title="Premi&#232;re page" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'=1';
                $sBuffer.='">&#171; Premi&#232;re page</a></li>';
                echo $sBuffer,"\n";
            }//if( $pPaging->GetCurrent()>2 )
            // Previous page
            if( $pPaging->GetCurrent()>1 )
            {
                $sBuffer='<li><a title="Page pr&#233;c&#233;dente" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'='.($pPaging->GetCurrent()-1);
                $sBuffer.='">&#8249; Page pr&#233;c&#233;dente</a></li>';
                echo $sBuffer,"\n";
            }//if( $pPaging->GetCurrent()>1 )
            // Next page
            if( $pPaging->GetCurrent()<$pPaging->GetMax() )
            {
                $sBuffer='<li><a title="Page suivante" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'='.($pPaging->GetCurrent()+1);
                $sBuffer.='">Page suivante &#8250;</a></li>';
                echo $sBuffer,"\n";
            }//if( $pPaging->GetCurrent()<$pPaging->GetMax() )
            // Last page
            if( ($pPaging->GetMax()-$pPaging->GetCurrent())>1 )
            {
                $sBuffer='<li><a title="Derni&#232;re page" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'='.$pPaging->GetMax();
                $sBuffer.='">Derni&#232;re page &#187;</a></li>';
                echo $sBuffer,"\n";
            }//if( ($pPaging->GetMax()-$pPaging->GetCurrent())>1 )
            echo '</ul>',"\n";
        }//if( $pPaging->GetMax()>1 )

        /** Content
         **********/
        echo '<ul class="records">',"\n";
        $iIndex = 1;
        foreach( $tRecordset as $tRecord )
        {
            BuildCurrentRent( $tRecord, $pPaging->GetMax(), $iIndex++, $pDate );
        }//foreach( $tRecordset as $tRecord )
        echo '</ul>',"\n";

    }//if( !empty($tRecordset) )

?>
</fieldset>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagebottom'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
<li><a title="Cr&#233;er un contact" href="<?php echo PBR_URL;?>contactnew.php">Cr&#233;er un contact</a></li>
</ul>
