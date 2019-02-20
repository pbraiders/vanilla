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
 * description: Display the login page.
 *              The following object(s) should exist:
 *                  - $sToken
 *                  - $sUsername
 *                  - $iMessageCode
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
  */
function BuildMessage($iCode)
{
    if( $iCode>0 )
    {
        $sBuffer='<div id="MESSAGE">';
        if($iCode===1)
        {
            $sBuffer.='<p class="error">Le nom d&#39;utilisateur ou le mot de passe que vous avez saisi est incorrect.</p>';
        }//if($iCode===1)
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

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

?>
 <div id="PAGE" class="login">
  <div id="HEADER"></div>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <form id="FORMLOGIN" method="post" action="<?php echo PBR_URL;?>login.php">
    <fieldset class="fieldsetform">
     <legend class="legendmain">Connexion</legend>
     <input type="hidden" name="act" value="login" />
     <?php if(isset($sToken)) BuildToken($sToken); ?>
     <ul>
      <li class="label required">Nom</li>
      <li><input id="loginusr" class="inputText" type="text" value="<?php echo (isset($sUsername)?htmlentities($sUsername,ENT_QUOTES,'UTF-8'):''); ?>" maxlength="45" size="10" name="usr"/></li>
      <li class="label required">Mot de passe</li>
      <li><input id="loginpwd" class="inputText" type="password" value="" maxlength="40" size="10" name="pwd"/></li>
      <li class="listbuttonitem"><input class="inputButton" type="submit" value="Connexion" name="login"/></li>
     </ul>
    </fieldset>
   </form>
  </div>
