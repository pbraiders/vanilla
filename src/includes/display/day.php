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
 *                  - $pContact (instance of CContact)
 *                  - $iMessageCode (integer)
 *                  - $sFormTitle (string)
 *                  - $tRecordset (array)
 *                  - $pPaging (instance of CPaging)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * W3C: This document was successfully checked as XHTML 1.0 Strict!
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_integer($iMessageCode) || !isset($pRent) || !isset($pContact) || !isset($pDate) || !isset($sFormTitle) ||!is_array($tRecordset) || !isset($pPaging) )
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
function BuildCurrentRent( &$tRecord, $sPrintHRef)
{
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
            $sBuffer.='<a title="Imprimer les r&#233;servations" href="'.$sPrintHRef.'" >';
            $sBuffer.='<span class="real">'.$tRecord['reservation_real'].'</span>';
            $sBuffer.='<span class="planned">'.$tRecord['reservation_planned'].'</span>';
            $sBuffer.='<span class="canceled">'.$tRecord['reservation_canceled'].'</span>';
            $iTotal=(integer)$tRecord['reservation_real']+(integer)$tRecord['reservation_planned'];
            $sBuffer.='<span>Total: '.$iTotal.' / '.$tRecord['reservation_arrhes'].'</span></a>';
        }
        else
        {
            // Rent
            $sBuffer.='<a href="'.PBR_URL.'rent.php?'.CRent::IDENTIFIERTAG.'='.$tRecord['reservation_id'].'" title="Modifier la r&#233;servation">';
            $sBuffer.='<span class="';
            $sBuffer.=($tRecord['reservation_real']==0?'empty hide">&nbsp;':'real">'.$tRecord['reservation_real']);
            $sBuffer.='</span><span class="';
            $sBuffer.=($tRecord['reservation_planned']==0?'empty hide">&nbsp;':'planned">'.$tRecord['reservation_planned']);
            $sBuffer.='</span><span class="';
            $sBuffer.=($tRecord['reservation_canceled']==0?'empty hide">&nbsp;':'canceled">'.$tRecord['reservation_canceled']);
            $sBuffer.='</span>';
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
    $sPrintHRef  = PBR_URL.'dayprint.php';
    $sPrintHRef .= '?'.CDate::YEARTAG.'='.$pDate->GetRequestYear();
    $sPrintHRef .= '&amp;'.CDate::MONTHTAG.'='.$pDate->GetRequestMonth();
    $sPrintHRef .= '&amp;'.CDate::DAYTAG.'='.$pDate->GetRequestDay();

    /** Build select href
     ********************/
    $sSelectHRef  = PBR_URL.'select.php';
    $sSelectHRef .= '?'.CDate::YEARTAG.'='.$pDate->GetRequestYear();
    $sSelectHRef .= '&amp;'.CDate::MONTHTAG.'='.$pDate->GetRequestMonth();
    $sSelectHRef .= '&amp;'.CDate::DAYTAG.'='.$pDate->GetRequestDay();

    /** Build calendar href
     **********************/
    $sCalendarHRef = '';
    if( $pDate->GetRequestYear()!=$pDate->GetCurrentYear()
     || $pDate->GetRequestMonth()!=$pDate->GetCurrentMonth() )
    {
        $sCalendarHRef  = '<li><a title="Retourner &#224; ce mois" href="'.PBR_URL;
        $sCalendarHRef .= '?'.CDate::YEARTAG.'='.$pDate->GetRequestYear();
        $sCalendarHRef .= '&amp;'.CDate::MONTHTAG.'='.$pDate->GetRequestMonth().'">';
        $sCalendarHRef .= $pDate->GetMonthName( $pDate->GetRequestMonth(), 1 ).' ';
        $sCalendarHRef .= $pDate->GetRequestYear().'</a></li>';
    }//if...

    /** Build paging href
     ********************/
    $sPagingHRef  = PBR_URL.'day.php?';
    $sPagingHRef .= CDate::YEARTAG.'='.$pDate->GetRequestYear();
    $sPagingHRef .= '&amp;'.CDate::MONTHTAG.'='.$pDate->GetRequestMonth();
    $sPagingHRef .= '&amp;'.CDate::DAYTAG.'='.$pDate->GetRequestDay();
    $sDisable = '';
    if( $pContact->GetIdentifier()>0 )
    {
        $sPagingHRef .= '&amp;'.CContact::IDENTIFIERTAG.'='.$pContact->GetIdentifier();
        $sDisable = 'disabled="disabled"';
    }//if( $pContact->GetIdentifier()>0 )

    /** Build default rent values
     ****************************/
    $iCountReal     = ( $pRent->GetCountReal()==0 ? '' : $pRent->GetCountReal() );
    $iCountPlanned  = ( $pRent->GetCountPlanned()==0 ? '' : $pRent->GetCountPlanned() );
    $iAge    = $pRent->GetAge();
    $iArrhes = $pRent->GetArrhes();

?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<hr/>
<ul class="navigation menu">
<li><a title="Aller aux r&#233;servations courantes" name="pagetop" href="#pagemiddle">&#8595;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Aller au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php if( !empty($sCalendarHRef) ) echo $sCalendarHRef,"\n"; ?>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
</ul>
<hr/>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1><?php echo htmlentities( $sFormTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
<form id="FORMDAY" method="post" action="<?php echo PBR_URL.'day.php'; ?>">
<fieldset class="fieldsetform">
<legend class="legendmain">Nouvelle r&#233;servation</legend>
<fieldset class="fieldsetsub fieldsetform">
<legend>Contact</legend>
<?php
    // Identified contact
    if( $pContact->GetIdentifier()>0 )
    {
        echo '<input type="hidden" name="'.CContact::IDENTIFIERTAG.'" value="'.$pContact->GetIdentifier().'" />',"\n";
    }//if( $pContact->GetIdentifier()>0 )
?>
<input type="hidden" name="<?php echo CDate::DAYTAG; ?>" value="<?php echo $pDate->GetRequestDay(); ?>" />
<input type="hidden" name="<?php echo CDate::MONTHTAG; ?>" value="<?php echo $pDate->GetRequestMonth(); ?>" />
<input type="hidden" name="<?php echo CDate::YEARTAG; ?>" value="<?php echo $pDate->GetRequestYear(); ?>" />
<ul>
<li class="label required">Nom</li>
<li><input id="contactlastname" class="inputText" type="text" value="<?php echo $pContact->GetLastName(1); ?>" maxlength="<?php echo CContact::LASTNAMEMAX; ?>" size="10" name="<?php echo CContact::LASTNAMETAG; ?>" <?php echo $sDisable;?> /></li>
<li class="navigation"><a title="Choisir un contact" href="<?php echo $sSelectHRef;?>"><em>Choisir dans la liste</em></a></li>
<li class="label required">Pr&eacute;nom</li>
<li><input id="contactfirstname" class="inputText" type="text" value="<?php echo $pContact->GetFirstName(1); ?>" maxlength="<?php echo CContact::FIRSTNAMEMAX; ?>" size="10" name="<?php echo CContact::FIRSTNAMETAG; ?>" <?php echo $sDisable;?> /></li>
<li class="label required">T&#233;l&#233;phone</li>
<li><input id="contactphone" class="inputText" type="text" value="<?php echo $pContact->GetTel(1);?>" maxlength="<?php echo CContact::TELMAX; ?>" size="10" name="<?php echo CContact::TELTAG; ?>" <?php echo $sDisable;?> /></li>
<li class="label">Email</li>
<li><input id="contactemail" class="inputText" type="text" value="<?php echo $pContact->GetEmail(1);?>" maxlength="<?php echo CContact::EMAILMAX; ?>" size="10" name="<?php echo CContact::EMAILTAG; ?>" <?php echo $sDisable;?> /></li>
<li class="label">Adresse</li>
<li><input id="contactaddress_1" class="inputText" type="text" value="<?php echo $pContact->GetAddress(1);?>" maxlength="<?php echo CContact::ADDRESSMAX; ?>" size="10" name="<?php echo CContact::ADDRESSTAG; ?>" <?php echo $sDisable;?> /></li>
<li class="label hide">&nbsp;</li>
<li><input id="contactaddress_2" class="inputText" type="text" value="<?php echo $pContact->GetAddressMore(1);?>" maxlength="<?php echo CContact::ADDRESSMOREMAX; ?>" size="10" name="<?php echo CContact::ADDRESSMORETAG; ?>" <?php echo $sDisable;?> /></li>
<li class="label">Ville</li>
<li><input id="contactcity" class="inputText" type="text" value="<?php echo $pContact->GetCity(1);?>" maxlength="<?php echo CContact::CITYMAX; ?>" size="10" name="<?php echo CContact::CITYTAG; ?>" <?php echo $sDisable;?> /></li>
<li class="label">Code postal</li>
<li><input id="contactzip" class="inputText" type="text" value="<?php echo $pContact->GetZip(1);?>" maxlength="<?php echo CContact::ZIPMAX; ?>" size="10" name="<?php echo CContact::ZIPTAG; ?>" <?php echo $sDisable;?> /></li>
</ul>
</fieldset>
<fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
<legend>Taille du groupe</legend>
<ul>
<li class="labelF real">R&#233;el</li>
<li><input id="rentsizereal" class="inputText" type="text" value="<?php echo $iCountReal;?>" maxlength="3" size="3" name="<?php echo CRent::REALTAG; ?>" /></li>
<li class="labelF planned">Suppos&#233;</li>
<li><input id="rentsizeplanned" class="inputText" type="text" value="<?php echo $iCountPlanned;?>" maxlength="3" size="3" name="<?php echo CRent::PLANNEDTAG; ?>" /></li>
<li class="label hide">&nbsp;</li>
</ul>
</fieldset>
<fieldset class="fieldsetsub fieldsetform fieldsetformgroup">
<legend>&#194;ge</legend>
<ul>
<li class="radio"><input id="rentage1" class="inputRadio" type="radio" name="<?php echo CRent::AGETAG; ?>" value="1" <?php if($iAge===1) echo 'checked="checked"';?> />16-25 ans</li>
<li class="radio"><input id="rentage2" class="inputRadio" type="radio" name="<?php echo CRent::AGETAG; ?>" value="2" <?php if(($iAge===2)||($iAge===0)) echo 'checked="checked"';?> />26-35 ans</li>
<li class="radio"><input id="rentage3" class="inputRadio" type="radio" name="<?php echo CRent::AGETAG; ?>" value="3" <?php if($iAge===3) echo 'checked="checked"';?> />35 ans et +</li>
</ul>
</fieldset>
<fieldset class="fieldsetsub fieldsetform">
<legend>Arrhes</legend>
<ul>
<li class="radio"><input id="rentarrhre1" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="1" <?php if($iArrhes===1) echo 'checked="checked"';?> />Esp&#232;ce</li>
<li class="radio"><input id="rentarrhre2" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="2" <?php if($iArrhes===2) echo 'checked="checked"';?> />Ch&#232;que</li>
<li class="radio"><input id="rentarrhre3" class="inputRadio" type="radio" name="<?php echo CRent::ARRHESTAG; ?>" value="3" <?php if($iArrhes===3) echo 'checked="checked"';?> />CB</li>
</ul>
</fieldset>
<ul class="listbuttons">
<li><input class="inputButton" type="submit" value="&nbsp;&nbsp;&nbsp;Cr&#233;er&nbsp;&nbsp;&nbsp;" name="new"/></li>
</ul>
</fieldset>
</form>
<a name="pagemiddle"></a>
<fieldset class="fieldsetform">
<?php

    /** Legend
     *********/
    $sBuffer  = '<legend class="legendmain">R&#233;servations courantes';
    if( $pPaging->GetMax()>1 )
    {
        $sBuffer .= ' <em>(page '.$pPaging->GetCurrent().' sur '.$pPaging->GetMax().')</em>';
    }//if( $pPaging->GetMax()>1 )
    $sBuffer .= '</legend>';
    echo $sBuffer,"\n";

    /** Navigation
     *************/
    if( !empty($tRecordset) )
    {
        echo '<ul class="navigation menu">',"\n";
//        echo '<li><a title="Imprimer les r&#233;servations" href="'.$sPrintHRef.'" target="_blank">Imprimer</a></li>',"\n";
        echo '<li><a title="Imprimer les r&#233;servations" href="'.$sPrintHRef.'" >Imprimer</a></li>',"\n";


        if( $pPaging->GetMax()>1 )
        {
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

        }//if( $pPaging->GetMax()>1 )
        echo '</ul>',"\n";

        /** Content
         **********/
        echo '<ul class="records fixedwidth">',"\n";
        $iIndex = 1;
        foreach( $tRecordset as $tRecord )
        {
            BuildCurrentRent( $tRecord, $sPrintHRef);
        }//foreach( $tRecordset as $tRecord )
        echo '</ul>',"\n";

    }//if( !empty($tRecordset) )

?>
</fieldset>
</div>
<hr/>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php if( !empty($sCalendarHRef) ) echo $sCalendarHRef,"\n"; ?>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
</ul>
