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
 * description: Display the parameters page.
 *              The following objects should exist:
 *                  - $tRecordset (array)
 *                  - $tRecordsetDB (array)
 *                  - $iMessageCode (integer)
 *                  - $pDate (instance of CDate)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_array($tRecordset) || !is_integer($iMessageCode) || !isset($pDate) || !is_array($tRecordsetDB) || !isset($pHeader) )
    die('-1');

/**
  * function: BuildMessage
  * description: Build and display a message.
  * parameters: INTEGER|iCode - message code
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  * update: Olivier JULLIEN - 2010-06-11 - spelling error
  */
function BuildMessage($iCode)
{
    if( $iCode>0 )
    {
        $sBuffer='<div id="MESSAGE">';
        if( $iCode===1 )
        {
            $sBuffer.='<p class="error">Une des valeurs n&#39;est pas valide.</p>';
        }
        elseif($iCode===2)
        {
            $sBuffer.='<p class="success">Suppression r&#233;ussie.</p>';
        }
        elseif($iCode===3)
        {
            $sBuffer.='<p class="error">La date saisie n&#39;est pas valide.</p>';
        }
        elseif($iCode===4)
        {
            $sBuffer.='<p class="success">Enregistrement r&#233;ussi. Les nouvelles r&#233;servations utiliseront ces nouvelles valeurs. Les anciennes r&#233;servations gardent les anciennes valeurs.</p>';
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
<li><a title="Aller aux r&#233;servations courantes" href="#pagemiddle">&#8595;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php">Utilisateurs</a></li>
<li><a title="Voir les graphes" href="<?php echo PBR_URL;?>graphs.php">Graphes</a></li>
<li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php">Logs</a></li>
</ul>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<h1>Param&#232;tres</h1>
<form id="FORMUPDATE" method="post" action="<?php echo PBR_URL;?>parameters.php">
<fieldset class="fieldsetsub">
<legend>Maximum</legend>
<fieldset class="fieldsetform fieldsetformgroup noborder">
<legend class="hide"></legend>
<ul>
<?php
    for( $iIndex=1; $iIndex<5; $iIndex++ )
    {
        echo '<li class="labelF">'.$pDate->GetMonthName($iIndex,1).'</li>',"\n";
        echo '<li><input id="param'.$iIndex.'" class="inputParam" type="text" value="'.$tRecordset[$iIndex].'" maxlength="3" size="3" name="'.CMaxRentPerMonthList::PARAMETERTAG.$iIndex.'"'.$pHeader->GetCloseTag().'</li>',"\n";
    }//for( $iIndex=1; $iIndex<5; $iIndex++ )
?>
</ul>
</fieldset>
<fieldset class="noborder fieldsetform fieldsetformgroup">
<legend class="hide"></legend>
<ul>
<?php
    for( $iIndex=5; $iIndex<9; $iIndex++ )
    {
        echo '<li class="labelF">'.$pDate->GetMonthName($iIndex,1).'</li>',"\n";
        echo '<li><input id="param'.$iIndex.'" class="inputParam" type="text" value="'.$tRecordset[$iIndex].'" maxlength="3" size="3" name="'.CMaxRentPerMonthList::PARAMETERTAG.$iIndex.'"'.$pHeader->GetCloseTag().'</li>',"\n";
    }//for( $iIndex=1; $iIndex<5; $iIndex++ )
?>
</ul>
</fieldset>
<fieldset class="noborder fieldsetform">
<legend class="hide"></legend>
<ul>
<?php
    for( $iIndex=9; $iIndex<13; $iIndex++ )
    {
        echo '<li class="label">'.$pDate->GetMonthName($iIndex,1).'</li>',"\n";
        echo '<li><input id="param'.$iIndex.'" class="inputParam" type="text" value="'.$tRecordset[$iIndex].'" maxlength="3" size="3" name="'.CMaxRentPerMonthList::PARAMETERTAG.$iIndex.'"'.$pHeader->GetCloseTag().'</li>',"\n";
    }//for( $iIndex=1; $iIndex<5; $iIndex++ )
?>
</ul>
</fieldset>
<ul class="listbuttons"><li><input class="inputButton" type="submit" value="Modifier" name="update"<?php echo $pHeader->GetCloseTag(); ?></li></ul>
</fieldset>
</form>
<?php echo $pHeader->GetAnchor('pagemiddle'),"\n"; ?>
<form id="FORMDELETE" method="post" action="<?php echo PBR_URL;?>rentsdelete.php">
<fieldset class="fieldsetsub fieldsetform">
<legend>Purge</legend>
<p>Supprimer les r&#233;servations ant&#233;rieures &#224; l&#39;ann&#233;e: &nbsp;<input id="paramyear" class="inputTextS" type="text" value="" maxlength="4" size="4" name="<?php echo CDate::YEARTAG; ?>"<?php echo $pHeader->GetCloseTag().' ( &le; '.$pDate->GetCurrentYear().' )';?></p>
<ul class="listbuttons"><li><input class="inputButton" type="submit" value="Envoyer"<?php echo $pHeader->GetCloseTag(); ?></li></ul>
</fieldset>
</form>
<fieldset class="fieldsetsub fieldsetform">
<legend>Statistiques</legend>
<?php
    echo '<p><b>Syst&egrave;me d\'exploitation:</b> '.htmlspecialchars(PHP_OS).'</p>',"\n";
    $sBuffer = '<p><b>PHP:</b> '.htmlspecialchars(phpversion()).' ( GD ';
    if( extension_loaded('gd') )
    {
        $sBuffer .= 'version: ';
        $tBuffer = gd_info();
        if( array_key_exists('GD Version',$tBuffer) )
            $sBuffer .= htmlspecialchars($tBuffer['GD Version']);
    }
    else
    {
        $sBuffer .= 'non charg#233;e';
    }//if( extension_loaded('gd') )
    echo $sBuffer .' )</p>',"\n";
    echo '<p><b>Base de donn&eacute;es:</b> Mysql '.CDBLayer::GetInstance()->GetInfo();
    if( ($tRecordsetDB['records']!=0) && ($tRecordsetDB['size']!=0 ) )
    {
        $sBuffer = ' ( '.$tRecordsetDB['size'].', '.$tRecordsetDB['records'].' lignes dont '.$tRecordsetDB['contact'].' contacts et '.$tRecordsetDB['reservation'].' r&#233;servations )';
        echo $sBuffer;
    }
    echo '</p>',"\n";
?>
</fieldset>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<?php echo $pHeader->GetAnchor('pagebottom'),"\n"; ?>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php">Utilisateurs</a></li>
<li><a title="Voir les graphes" href="<?php echo PBR_URL;?>graphs.php">Graphes</a></li>
<li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php">Logs</a></li>
</ul>
