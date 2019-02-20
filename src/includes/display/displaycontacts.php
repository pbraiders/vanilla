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
 *                  - $tRecordset
 *                  - $sSearch
 *                  - $CPaging
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_PAGE_LOADED') )
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
function BuildContact(&$tRecord)
{
    // Build show href
    $sShow=PBR_URL.'contact.php?act=show';

    if( is_array($tRecord) && array_key_exists('contact_id', $tRecord)
                           && array_key_exists('contact_lastname', $tRecord)
                           && array_key_exists('contact_firstname', $tRecord)
                           && array_key_exists('contact_tel', $tRecord)
                           && array_key_exists('contact_addresscity', $tRecord) )
    {
        $sBuffer='<li><a href="'.$sShow.'&amp;cti='.$tRecord['contact_id'].'" title="Voir les informations">';
        $sBuffer.='<span>'.htmlentities($tRecord['contact_lastname'],ENT_QUOTES,'UTF-8').' ';
        $sBuffer.=htmlentities($tRecord['contact_firstname'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['contact_tel'],ENT_QUOTES,'UTF-8');
        if( array_key_exists('contact_addresscity', $tRecord) && strlen($tRecord['contact_addresscity'])>0 )
        {
            $sBuffer.=' &#8226; '.htmlentities($tRecord['contact_addresscity'],ENT_QUOTES,'UTF-8');
        }//city
        if( array_key_exists('contact_comment', $tRecord) && strlen($tRecord['contact_comment'])>0 )
        {
            $sBuffer.='</span><span class="hide"> &#8226; '.htmlentities(TruncMe($tRecord['contact_comment'],50),ENT_QUOTES,'UTF-8');
        }//comment
        echo $sBuffer.'</span></a></li>',"\n";
    }//if( is_array($tRecord) && array_key_exists(...
}

    /** Initialize
     *************/
    if(!isset($sSearch)) $sSearch='';

    /** Build href
     *************/
    $sExport=PBR_URL.'contactsexport.php?act=export';
    $sPagingHRef=PBR_URL.'contacts.php?';
    $sPagingHRefExport='';

    /** Build search input
     *********************/
    if( strlen($sSearch)>0 )
    {
        $sPagingHRefExport='&amp;act=search&amp;ctl='.rawurlencode($sSearch);
        $sExport.='&amp;ctl='.rawurlencode($sSearch);
        $sSearch=htmlentities($sSearch,ENT_QUOTES,'UTF-8');
    }//if( !empty($sSearch) )
?>
 <div id="PAGE">
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en bas de la page" name="pagetop" href="#pagebottom">&#8595;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
   <li><a title="Cr&#233;er un contact" href="<?php echo PBR_URL;?>contactnew.php">Cr&#233;er un contact</a></li>
  </ul>
  <hr/>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <h1>Contacts</h1>
   <form id="FORMCONTACTS" method="get" action="<?php echo PBR_URL;?>contacts.php">
    <fieldset class="fieldsetsub fieldsetform">
     <legend class="legendmain">Chercher</legend>
     <input type="hidden" name="act" value="search" />
     <ul>
      <li class="label required">Nom</li>
      <li><input id="contactsname" class="inputText" type="text" value="<?php echo $sSearch; ?>" maxlength="40" size="10" name="ctl"/></li>
      <li class="help"><em>Utilisez le joker * pour des recherches partielles.</em></li>
      <li class="listbuttonitem"><input class="inputButton" type="submit" value="Envoyer" /></li>
     </ul>
    </fieldset>
   </form>
   <fieldset class="fieldsetmain">
    <legend class="legendmain">Liste des contacts</legend>
    <ul class="navigation menu">
     <li><a title="Exporter" href="<?php echo $sExport; ?>" target="_blank">Exporter</a></li>
<?php
    if( CPaging::GetInstance()->GetMax()>1 )
    {
        if( CPaging::GetInstance()->GetCurrent()>1 )
        {
            $sBuffer='<li><a title="Page pr&#233;c&#233;dente" href="';
            $sBuffer.=$sPagingHRef.'pag='.(CPaging::GetInstance()->GetCurrent()-1).$sPagingHRefExport;
            $sBuffer.='">Page pr&#233;c&#233;dente</a></li>';
            echo $sBuffer,"\n";
        }//if( CPaging::GetInstance()->GetCurrent()>1 )
        if( CPaging::GetInstance()->GetCurrent()<CPaging::GetInstance()->GetMax() )
        {
            $sBuffer='<li><a title="Page suivante" href="';
            $sBuffer.=$sPagingHRef.'pag='.(CPaging::GetInstance()->GetCurrent()+1).$sPagingHRefExport;
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
            BuildContact($tRecord);
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
   <li><a title="Cr&#233;er un contact" href="<?php echo PBR_URL;?>contactnew.php">Cr&#233;er un contact</a></li>
  </ul>
