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
 * description: Display the contacts page.
 *              The following object(s) should exist:
 *                  - $tRecordset (array)
 *                  - $pSearch (instance of CContact)
 *                  - $pPaging (instance of CPaging)
 *                  - $iMessageCode (integer)
 *                  - $pOrder (instance of COption)
 *                  - $pSort (instance of COption)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($pPaging) || !is_array($tRecordset) || !is_integer($iMessageCode) || !isset($pSearch) || !isset($pOrder) || !isset($pSort) || !isset($pHeader) )
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
            $sBuffer.='<p class="success">Enregistrement r&#233;ussi.</p>';
        }
        elseif( $iCode===2 )
        {
            $sBuffer.='<p class="error">Il est impossible de g&#233;n&#233;rer le fichier d&#039;export.</p>';
        }//if( $iMessageCode===1 )
        elseif( $iCode===3 )
        {
            $sBuffer.='<p class="success">Suppression r&#233;ussie.</p>';
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

/**
  * function: BuildContact
  * description: Build and display a contact
  * parameters:  ARRAY|tRecord - recordset
  *             (should have keys: contact_id, contact_lastname, contact_firstname
  *                              , contact_tel, contact_addresscity)
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildContact( &$tRecord )
{
    // Build show href
    $sShow = PBR_URL.'contact.php?';

    if( is_array($tRecord) && array_key_exists('contact_id', $tRecord)
                           && array_key_exists('contact_lastname', $tRecord)
                           && array_key_exists('contact_firstname', $tRecord)
                           && array_key_exists('contact_tel', $tRecord)
                           && array_key_exists('contact_addresscity', $tRecord) )
    {
        $sBuffer = '<li><a href="'.$sShow.CContact::IDENTIFIERTAG.'='.$tRecord['contact_id'].'" title="Voir les informations">';
        $sBuffer .= '<span>'.htmlentities($tRecord['contact_lastname'],ENT_QUOTES,'UTF-8').' ';
        $sBuffer .= htmlentities($tRecord['contact_firstname'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer .= htmlentities($tRecord['contact_tel'],ENT_QUOTES,'UTF-8');
        if( strlen($tRecord['contact_addresscity'])>0 )
        {
            $sBuffer .= ' &#8226; '.htmlentities($tRecord['contact_addresscity'],ENT_QUOTES,'UTF-8');
        }//city
        if( array_key_exists('contact_comment', $tRecord) && strlen($tRecord['contact_comment'])>0 )
        {
            $sBuffer .= '</span><span class="hide"> &#8226; '.htmlentities(TruncMe($tRecord['contact_comment'],50),ENT_QUOTES,'UTF-8');
        }//comment
        echo $sBuffer.'</span></a></li>',"\n";
    }//if( is_array($tRecord) && array_key_exists(...
}

    /** Build href
     *************/
    $sHRefExport = PBR_URL.'contacts.php?'.CAction::ACTIONTAG.'=export';
    $sPagingHRef = PBR_URL.'contacts.php?';
    $sPagingHRefSearch = '';

    /** Build options
     ****************/
    $sPagingHRef .= $pOrder->GetName().'='.$pOrder->GetValue();
    $sPagingHRef .= '&amp;'.$pSort->GetName().'='.$pSort->GetValue();
    $sHRefExport .= '&amp;'.$pOrder->GetName().'='.$pOrder->GetValue();
    $sHRefExport .= '&amp;'.$pSort->GetName().'='.$pSort->GetValue();


    /** Build search input
     *********************/
    if( strlen($pSearch->GetLastName())>0 )
    {
        $sLastNameEncoded = $pSearch->GetLastName(2);
        $sPagingHRefSearch = '&amp;'.CAction::ACTIONTAG.'=search';
        $sPagingHRefSearch .= '&amp;'.CContact::LASTNAMETAG.'='.$sLastNameEncoded;
        $sHRefExport .= '&amp;'.CContact::LASTNAMETAG.'='.$sLastNameEncoded;
    }//if( strlen($sSearch)>0 ))

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
<?php if(strlen($pSearch->GetLastName())>0) { ?>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
<?php }//if(strlen($pSearch->GetLastName())>0) ?>
<li><a title="Cr&#233;er un contact" href="<?php echo PBR_URL;?>contactnew.php">Cr&#233;er un contact</a></li>
</ul>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1>Contacts</h1>
<form id="FORMCONTACTS" method="get" action="<?php echo PBR_URL;?>contacts.php">
<fieldset class="fieldsetsub fieldsetform">
<legend class="legendmain">Chercher</legend>
<input type="hidden" name="<?php echo CAction::ACTIONTAG; ?>" value="search"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
<ul>
<li class="label required">Nom</li>
<li><input id="contactsname" class="inputText" type="text" value="<?php echo $pSearch->GetLastName(1); ?>" maxlength="<?php echo CContact::LASTNAMEMAX; ?>" size="10" name="<?php echo CContact::LASTNAMETAG; ?>"<?php echo $pHeader->GetCloseTag(); ?></li>
<li class="help"><em>Utilisez le joker * pour des recherches partielles.</em></li>
<li class="label">Trier par</li>
<li><select class="inputSelect" name="<?php echo $pOrder->GetName(); ?>">
<?php
    /** Display "order by" option
     ****************************/
    $sLabel = array('Nom','Date de cr&eacute;ation');
    for( $iIndex=0; $iIndex<2; $iIndex++ )
    {
        $iCurrent = $iIndex+1;
        $sBuffer='<option value="'.$iCurrent.'"';
        if( $pOrder->GetValue()==$iCurrent )
        {
            $sBuffer.=' selected="selected"';
        }//if( $pDate->GetRequestMonth()==$iCurrentMonth )
        $sBuffer .= '>'.$sLabel[$iIndex].'</option>';
        echo $sBuffer,"\n";
    }//for( $iIndex=0;$iIndex<2;$iIndex++)
?>
</select></li>
<li class="label">Ordre</li>
<li><select class="inputSelect" name="<?php echo $pSort->GetName(); ?>">
<?php
    /** Display "sort" option
     ************************/
    $sLabel = array('Croissant','D&eacute;croissant');
    for( $iIndex=0; $iIndex<2; $iIndex++ )
    {
        $iCurrent = $iIndex+1;
        $sBuffer='<option value="'.$iCurrent.'"';
        if( $pSort->GetValue()==$iCurrent )
        {
            $sBuffer.=' selected="selected"';
        }//if( $pDate->GetRequestMonth()==$iCurrentMonth )
        $sBuffer .= '>'.$sLabel[$iIndex].'</option>';
        echo $sBuffer,"\n";
    }//for( $iIndex=0;$iIndex<2;$iIndex++)
?>
</select></li>
<li class="listbuttonitem"><input class="inputButton" type="submit" value="Envoyer"<?php echo $pHeader->GetCloseTag(); ?></li>
</ul>
</fieldset>
</form>
<fieldset class="fieldsetmain">
<?php

    /** Legend
     *********/
    $sBuffer  = '<legend class="legendmain">Liste des contacts';
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
        echo '<ul class="navigation menu">',"\n";
        echo '<li><a title="Exporter" href="'.$sHRefExport.'">Exporter</a></li>',"\n";

        if( $pPaging->GetMax()>1 )
        {
            // First page
            if( $pPaging->GetCurrent()>2 )
            {
                $sBuffer='<li><a title="Premi&#232;re page" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'=1'.$sPagingHRefSearch;
                $sBuffer.='">&#171; Premi&#232;re page</a></li>';
                echo $sBuffer,"\n";
            }//if( $pPaging->GetCurrent()>2 )
            // Previous page
            if( $pPaging->GetCurrent()>1 )
            {
                $sBuffer='<li><a title="Page pr&#233;c&#233;dente" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'='.($pPaging->GetCurrent()-1).$sPagingHRefSearch;
                $sBuffer.='">&#8249; Page pr&#233;c&#233;dente</a></li>';
                echo $sBuffer,"\n";
            }//if( CPaging::GetInstance()->GetCurrent()>1 )
            // Next page
            if( $pPaging->GetCurrent()<$pPaging->GetMax() )
            {
                $sBuffer='<li><a title="Page suivante" href="';
                $sBuffer.=$sPagingHRef.'&amp;'.CPaging::PAGETAG.'='.($pPaging->GetCurrent()+1).$sPagingHRefSearch;
                $sBuffer.='">Page suivante &#8250;</a></li>';
                echo $sBuffer,"\n";
            }//if( $pPaging->GetCurrent()<$pPaging->GetMax() )
            // Last page
            if( ($pPaging->GetMax()-$pPaging->GetCurrent())>1 )
            {
                $sBuffer = '<li><a title="Derni&#232;re page" href="';
                $sBuffer .= $sPagingHRef.'&amp;'.CPaging::PAGETAG.'='.$pPaging->GetMax().$sPagingHRefSearch;
                $sBuffer .= '">Derni&#232;re page &#187;</a></li>';
                echo $sBuffer,"\n";
            }//if( ($pPaging->GetMax()-$pPaging->GetCurrent())>1 )

        }//if( $pPaging->GetMax()>1 )
        echo '</ul>',"\n";

        /** Content
         **********/
        echo '<ul class="records">',"\n";
        foreach( $tRecordset as $tRecord )
        {
            BuildContact($tRecord);
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
<li><a title="Afficher le calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<?php if(strlen($pSearch->GetLastName())>0) { ?>
<li><a title="Afficher tous les contacts" href="<?php echo PBR_URL;?>contacts.php">Contacts</a></li>
<?php }//if(strlen($pSearch->GetLastName())>0) ?>
<li><a title="Cr&#233;er un contact" href="<?php echo PBR_URL;?>contactnew.php">Cr&#233;er un contact</a></li>
</ul>
