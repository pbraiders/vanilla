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
 *                  - CUser
 *                  - $iMessageCode
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($pUser) || !isset($iMessageCode) && !isset($sToken) || !isset($pHeader) )
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

    /** Build default username
     *************************/
    $sUsername = $pUser->GetUsername();
    if( mb_strlen($sUsername)<=0 )
    {
        $sUsername = CAuth::GetInstance()->GetUsername();
    }//if( mb_strlen($sUsername)<=0 )
    $sUsername = htmlentities($sUsername,ENT_QUOTES,'UTF-8');

?>
<div id="PAGE" class="login">
<div id="HEADER"></div>
<div id="CONTENT">
<?php BuildMessage($iMessageCode); ?>
<form id="FORMLOGIN" method="post" action="<?php echo PBR_URL;?>login.php">
<fieldset class="fieldsetform">
<legend class="legendmain">Connexion</legend>
<input type="hidden" name="<?php echo CAction::ACTIONTAG; ?>" value="login"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
<input type="hidden" name="<?php echo CPHPSession::TOKENTAG; ?>" value="<?php echo $sToken; ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
<ul>
<li class="label required">Nom</li>
<li><input id="loginusr" class="inputText" type="text" value="<?php echo $sUsername; ?>" maxlength="<?php echo CUser::USERNAMEMAX; ?>" size="10" name="<?php echo CUser::USERNAMETAG; ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?></li>
<li class="label required">Mot de passe</li>
<li><input id="loginpwd" class="inputText" type="password" value="" maxlength="<?php echo CUser::PASSWORDMAX; ?>" size="10" name="<?php echo CUser::PASSWORDTAG; ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?></li>
<li class="listbuttonitem"><input class="inputButton" type="submit" value="Connexion" name="login"<?php echo $pHeader->GetCloseTag(),"\n"; ?></li>
</ul>
</fieldset>
</form>
</div>
