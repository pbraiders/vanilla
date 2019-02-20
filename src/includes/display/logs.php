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
 * description: Display the logs page.
 *              The following object(s) should exist:
 *                  - $tRecordset (array)
 *                  - $pPaging (instance of CPaging)
 *                  - $iMessageCode (integer)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($pPaging) || !is_array($tRecordset) || !is_integer($iMessageCode) || !isset($pHeader) )
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
        if( $iCode===3 )
        {
            $sBuffer.='<p class="success">Suppression r&#233;ussie.</p>';
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

/**
  * function: BuildLog
  * description: Build and display a log
  * parameters:  ARRAY|tRecord - recordset
  *             (should have keys: log_date, log_user, log_type, log_description,
  *              log_mysqluser, log_mysqlcurrentuser )
  *            INTEGER|iPagingMax - Max page count
  *            INTEGER|iIndex     - Record count
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildLog( &$tRecord, $iPagingMax, $iIndex)
{
    if( is_array($tRecord) && array_key_exists('log_date', $tRecord)
                           && array_key_exists('log_user', $tRecord)
                           && array_key_exists('log_type', $tRecord)
                           && array_key_exists('log_title', $tRecord)
                           && array_key_exists('log_description', $tRecord)
                           && array_key_exists('log_mysqluser', $tRecord)
                           && array_key_exists('log_mysqlcurrentuser', $tRecord) )
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
        $sBuffer.='<span>'.htmlentities($tRecord['log_date'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_user'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_type'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_title'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_description'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_mysqluser'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_mysqlcurrentuser'],ENT_QUOTES,'UTF-8');
        echo $sBuffer.'</span></li>',"\n";
    }//if( is_array($tRecord) && array_key_exists(...
}
    /** Build href
     *************/
    $sPagingHRef = PBR_URL.'logs.php';

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
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php">Param&#232;tres</a></li>
<li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php">Utilisateurs</a></li>
<li><a title="Voir les graphes" href="<?php echo PBR_URL;?>graphs.php">Graphes</a></li>
</ul>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1>Logs</h1>
<?php if( !empty($tRecordset) ){ ?>
<form id="FORMLOGS" method="post" action="<?php echo PBR_URL;?>logsdelete.php">
<fieldset class="fieldsetsub fieldsetform">
<legend class="legendmain">Effacer tous les logs</legend>
<ul>
<li class="listbuttonitem"><input class="inputButton" type="submit" value="Supprimer"<?php echo $pHeader->GetCloseTag(),"\n"; ?></li>
</ul>
</fieldset>
</form>
<?php }//if( !empty($tRecordset) ) ?>
<fieldset class="fieldsetmain">
<?php

    /** Legend
     *********/
    $sBuffer  = '<legend class="legendmain">Logs';
    if( $pPaging->GetMax()>1 )
    {
        $sBuffer .= ' <em class=hide">(page '.$pPaging->GetCurrent().' sur '.$pPaging->GetMax().')</em>';
    }//if( $pPaging->GetMax()>1 )
    $sBuffer .= '</legend>';
    echo $sBuffer,"\n";

    /** Navigation
     *************/
    if( $pPaging->GetMax()>1 )
    {
        echo '<ul class="navigation menu">',"\n";
        // First page
        if( $pPaging->GetCurrent()>2 )
        {
            $sBuffer='<li><a title="Premi&#232;re page" href="';
            $sBuffer.=$sPagingHRef.'?'.CPaging::PAGETAG.'=1';
            $sBuffer.='">&#171; Premi&#232;re page</a></li>';
            echo $sBuffer,"\n";
        }//if( $pPaging->GetCurrent()>2 )
        // Previous page
        if( $pPaging->GetCurrent()>1 )
        {
            $sBuffer='<li><a title="Page pr&#233;c&#233;dente" href="';
            $sBuffer.=$sPagingHRef.'?'.CPaging::PAGETAG.'='.($pPaging->GetCurrent()-1);
            $sBuffer.='">&#8249; Page pr&#233;c&#233;dente</a></li>';
            echo $sBuffer,"\n";
        }//if( CPaging::GetInstance()->GetCurrent()>1 )
        // Next page
        if( $pPaging->GetCurrent()<$pPaging->GetMax() )
        {
            $sBuffer='<li><a title="Page suivante" href="';
            $sBuffer.=$sPagingHRef.'?'.CPaging::PAGETAG.'='.($pPaging->GetCurrent()+1);
            $sBuffer.='">Page suivante &#8250;</a></li>';
            echo $sBuffer,"\n";
        }//if( $pPaging->GetCurrent()<$pPaging->GetMax() )
        // Last page
        if( ($pPaging->GetMax()-$pPaging->GetCurrent())>1 )
        {
            $sBuffer='<li><a title="Derni&#232;re page" href="';
            $sBuffer.=$sPagingHRef.'?'.CPaging::PAGETAG.'='.$pPaging->GetMax();
            $sBuffer.='">Derni&#232;re page &#187;</a></li>';
            echo $sBuffer,"\n";
        }//if( ($pPaging->GetMax()-$pPaging->GetCurrent())>1 )
        echo '</ul>',"\n";
    }//if( $pPaging->GetMax()>1 )

    /** Content
     **********/
    if( !empty($tRecordset) )
    {
        echo '<ul class="records">',"\n";
        $iIndex = 1;
        foreach( $tRecordset as $tRecord )
        {
            BuildLog( $tRecord, $pPaging->GetMax(), $iIndex++ );
        }//foreach( $tRecordset as $tRecord )
        echo '</ul>',"\n";
    }//if( !empty($tRecordset) )

?>
</fieldset>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagebottom'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php">Param&#232;tres</a></li>
<li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php">Utilisateurs</a></li>
<li><a title="Voir les graphes" href="<?php echo PBR_URL;?>graphs.php">Graphes</a></li>
</ul>
