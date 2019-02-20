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
 * description: Display the log delete page.
 *              The following object(s) should exist:
 *                  - $sToken
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') )
    die('-1');

/**
  * function: BuildToken
  * description: Build and display the token.
  * parameters: STRING|sValue - token value
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildToken($sValue)
{
	echo '<input type="hidden" name="tok" value="'.$sValue.'" />',"\n";
}

    // Build form title
    $sFormTitle='Supprimer les logs';
?>
 <div id="PAGE">
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <div id="CONTENT">
   <h1><?php echo $sFormTitle;?></h1>
   <form id="FORMCONFIRM" method="get" action="<?php echo PBR_URL;?>logsdelete.php">
    <fieldset class="fieldsetform">
     <legend class="legendmain">Confirmer la suppression</legend>
     <input type="hidden" name="act" value="delete" />
     <?php if(isset($sToken)) BuildToken($sToken); ?>
     <div id="MESSAGE">
      <p class="error">Tous les logs vont &ecirc;tre supprim&eacute;s.</p>
     </div>
     <ul>
      <li class="listbuttonitem">
       <input class="inputButton" type="submit" value="Supprimer" name="delete"/>&nbsp;
       <input class="inputButton" type="submit" value="&nbsp;Annuler&nbsp;&nbsp;" name="cancel"/>
      </li>
     </ul>
    </fieldset>
   </form>
  </div>