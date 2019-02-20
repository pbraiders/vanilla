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
 * description: Display the graphs page.
 *              The following objects should exist:
 *                  - $tGraphs (array)
 *                  - $pChoice (instance of COption)
 *                  - $pInterval (instance of COption)
 *                  - $sTitle (string)
 * author: Olivier JULLIEN - 2010-06-15
 * W3C: This document was successfully checked as XHTML 1.0 Strict!
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_array($tGraphs) || !isset($pChoice) || !isset($pInterval) || !isset($sTitle) )
    die('-1');

    // Default image source
    $sSource = '';

?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<hr/>
<ul class="navigation menu">
<li><a title="Aller aux r&#233;servations courantes" name="pagetop" href="#pagemiddle">&#8595;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php">Param&#232;tres</a></li>
<li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php">Utilisateurs</a></li>
<li><a title="Voir les graphes" href="<?php echo PBR_URL;?>graphs.php">Graphes</a></li>
<li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php">Logs</a></li>
</ul>
<hr/>
<div id="CONTENT">
<h1><?php echo htmlentities($sTitle,ENT_QUOTES,'UTF-8'); ?></h1>
<form id="FORMSELECT" method="post" action="<?php echo PBR_URL;?>graphs.php">
<fieldset class="fieldsetsub fieldsetform">
<legend>S&#233;lectionner</legend>
<ul>
<li class="label required">Graphe</li>
<li><select class="inputSelect" name="<?php echo $pChoice->GetName(); ?>">
<?php
    /** Display graphs list
     **********************/
    foreach( $tGraphs as $iKey => $tGraph )
    {
        $sBuffer='<option value="'.$iKey.'"';
        if( $pChoice->GetValue()==$iKey )
        {
            $sBuffer.=' selected="selected"';
            $sSource = $tGraph[1];
        }//selected
        $sBuffer .= '>'.htmlentities($tGraph[0].' '.$tGraph[3],ENT_QUOTES,'UTF-8').'</option>';
        echo $sBuffer,"\n";
    }
?>
</select></li>
<li class="label">Intervalle</li>
<li><input id="interval" class="inputTextS" type="text" value="<?php if( $pInterval->GetValue()>0) echo $pInterval->GetValue(); ?>" maxlength="3" size="3" name="<?php echo $pInterval->GetName(); ?>"/></li>
<li class="listbuttonitem"><input class="inputButton" type="submit" value="&nbsp;Afficher&nbsp;" /></li>
</ul>
</fieldset>
</form>
<?php
    /** image
     ********/
    if( !empty($sSource) )
    {
        $sSource = PBR_URL.'graphs/'.$sSource.'.php?'.$pInterval->GetName().'='.$pInterval->GetValue();
        echo '<div class="graph">',"\n";
        echo '<img src="'.$sSource.'" alt="Aucune donn&#233;e &#224; afficher" />',"\n";
        echo '</div>',"\n";
    }
?>
<form id="FORMDELETE" method="post" action="<?php echo $sSource; ?>">
<ul class="listbuttons"><li><input class="inputButton" type="submit" value="&nbsp;Exporter&nbsp;" name="exp"/></li></ul>
</form>
</div>
<hr/>
<ul class="navigation menu">
<li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
<li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
<li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
<li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php">Param&#232;tres</a></li>
<li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php">Utilisateurs</a></li>
<li><a title="Voir les graphes" href="<?php echo PBR_URL;?>graphs.php">Graphes</a></li>
<li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php">Logs</a></li>
</ul>
