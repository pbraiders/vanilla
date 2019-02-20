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
 * description: Display the users page.
 *              The following objects should exist:
 *                  - $tRecordset
 *                  - $iYear
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') )
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
            $sBuffer.='<p class="error">Une des valeurs n&#146;est pas valide.</p>';
        }
        elseif($iCode===2)
        {
            $sBuffer.='<p class="success">Suppression r&#233;ussie.</p>';
        }
        elseif($iCode===3)
        {
            $sBuffer.='<p class="error">La date saisie n&#146;est pas valide.</p>';
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
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller aux r&#233;servations courantes" name="pagetop" href="#pagemiddle">&#8595;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
   <li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php?act=show">Param&#232;tres</a></li>
   <li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php?act=show">Utilisateurs</a></li>
   <li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php?act=show">Logs</a></li>
  </ul>
  <hr/>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <h1>Param&#232;tres</h1>
   <form id="FORMUPDATE" method="post" action="<?php echo PBR_URL;?>parameters.php">
    <input type="hidden" name="act" value="update" />
    <fieldset class="fieldsetsub">
     <legend>Maximum</legend>
     <fieldset class="noborder fieldsetform fieldsetformgroup">
      <ul>
       <li class="labelF">Janvier</li>
       <li><input id="param01" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_1'];?>" maxlength="3" size="3" name="pa1" /></li>
       <li class="labelF">F&eacute;vrier</li>
       <li><input id="param02" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_2'];?>" maxlength="3" size="3" name="pa2" /></li>
       <li class="labelF">Mars</li>
       <li><input id="param03" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_3'];?>" maxlength="3" size="3" name="pa3" /></li>
       <li class="labelF">Avril</li>
       <li><input id="param04" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_4'];?>" maxlength="3" size="3" name="pa4" /></li>
      </ul>
     </fieldset>
     <fieldset class="noborder fieldsetform fieldsetformgroup">
      <ul>
       <li class="labelF">Mai</li>
       <li><input id="param05" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_5'];?>" maxlength="3" size="3" name="pa5" /></li>
       <li class="labelF">Juin</li>
       <li><input id="param06" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_6'];?>" maxlength="3" size="3" name="pa6" /></li>
       <li class="labelF">Juillet</li>
       <li><input id="param07" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_7'];?>" maxlength="3" size="3" name="pa7" /></li>
       <li class="labelF">Ao&ucirc;t</li>
       <li><input id="param08" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_8'];?>" maxlength="3" size="3" name="pa8" /></li>
      </ul>
     </fieldset>
     <fieldset class="noborder fieldsetform">
      <ul>
       <li class="label">Septembre</li>
       <li><input id="param09" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_9'];?>" maxlength="3" size="3" name="pa9" /></li>
       <li class="label">Octobre</li>
       <li><input id="param10" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_10'];?>" maxlength="3" size="3" name="pa10" /></li>
       <li class="label">Novembre</li>
       <li><input id="param11" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_11'];?>" maxlength="3" size="3" name="pa11" /></li>
       <li class="label">D&eacute;cembre</li>
       <li><input id="param12" class="inputParam" type="text" value="<?php echo $tRecordset['max_rent_12'];?>" maxlength="3" size="3" name="pa12" /></li>
      </ul>
     </fieldset>
     <ul class="listbuttons"><li><input class="inputButton" type="submit" value="Modifier" name="update"/></li></ul>
    </fieldset>
   </form>
   <form id="FORMDELETE" method="get" action="<?php echo PBR_URL;?>parameters.php">
    <input type="hidden" name="act" value="delete" />
    <fieldset class="fieldsetsub fieldsetform">
     <legend>Purge</legend>
     <p>Supprimer les r&#233;servations ant&#233;rieures &#224; l&#39ann&#233;e: &nbsp;<input id="paramyear" class="inputTextS" type="text" value="" maxlength="4" size="4" name="rey" /></p>
     <ul class="listbuttons"><li><input class="inputButton" type="submit" value="Envoyer" /></li></ul>
    </fieldset>
   </form>
  </div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en haut de la page" href="#pagetop" name="pagebottom">&#8593;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
   <li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php?act=show">Param&#232;tres</a></li>
   <li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php?act=show">Utilisateurs</a></li>
   <li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php?act=show">Logs</a></li>
  </ul>
