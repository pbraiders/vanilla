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
 * description: Display the install page.
 *              The following object(s) should exist:
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
    global $sPHPVersionRequired,$sMYSQLVersionRequired,$sPHPVersion,$sMYSQLVersion;
    if( $iCode>=0 )
    {
        $sBuffer='<div id="MESSAGE">';
        if($iCode===1)
        {
            $sBuffer.='<p class="error">Impossible de se connecter &#224; la base de donn&#233;es. V&#233;rifier les param&#232;tres de connexion.</p>';
        }
        elseif($iCode===2)
        {
            $sBuffer.='<p class="error">PBRaiders est d&#233;j&#224; install&#233;e. Si vous souhaitez la re-installer, vous devez d&#146;abord la d&#233;truire.</p>';
        }
        elseif($iCode===3)
        {
            $sBuffer.='<p class="error">Installation impossible. PBRaiders n&#233;cessite PHP '.$sPHPVersionRequired.' et MYSQL '.$sMYSQLVersionRequired.'. Vous utilisez PHP '.$sPHPVersion.' et MYSQL '.$sMYSQLVersion.'</p>';
        }
        elseif($iCode===4)
        {
            $sBuffer.='<p class="error">Le nom d&#39;utilisateur ou le mot de passe que vous avez saisi est incorrect.</p>';
        }
        elseif($iCode===0)
        {
            $sBuffer.='<p class="success">Bienvenue dans PBRaiders.</p>';
        }//if($iCode===1)
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

?>
 <div id="PAGE" class="login">
  <div id="HEADER"></div>
  <div id="CONTENT">
<?php
    BuildMessage($iMessageCode);
    if( ($iMessageCode==0) || ($iMessageCode==4) )
    {
?>
   <form id="FORMINSTALL" method="post" action="<?php echo PBR_URL;?>install.php">
    <input type="hidden" name="act" value="install" />
    <fieldset class="fieldsetform">
     <legend class="legendmain">Informations</legend>
      <p>PHP version: <b><?php echo $sPHPVersion; ?></b></p>
      <p>MYSQL version: <b><?php echo $sMYSQLVersion; ?></b></p>
      <p>Addresse du serveur: <b><?php echo PBR_DB_HOST; ?></b></p>
      <p>Nom de la base de donn&#233;es: <b><?php echo PBR_DB_DBN; ?></b></p>
      <p>Nom de l&#146;utilisateur de la base de donn&#233;es: <b><?php echo PBR_DB_USR; ?></b></p>
    </fieldset>
    <fieldset class="fieldsetform">
     <legend class="legendmain">Administrateur</legend>
     <p>Veuillez saisir le nom et le mot de passe de l'utilisateur administrateur de PBRaiders. Pour le nom, seuls les caract&#232;res alphanum&#233;riques, &quot;@&quot;,&quot;.&quot;,&quot;-&quot; et &quot;_&quot; sont autoris&#233;s.</p>
     <ul>
      <li class="label required">Nom</li>
      <li><input id="loginusr" class="inputText" type="text" value="" maxlength="45" size="10" name="usr"/></li>
      <li class="label required">Mot de passe</li>
      <li><input id="loginpwd" class="inputText" type="password" value="" maxlength="40" size="10" name="pwd"/></li>
      <li class="listbuttonitem"><input class="inputButton" type="submit" value="Lancer l'installation" name="install"/></li>
     </ul>
    </fieldset>
   </form>
<?php }//if... ?>
  </div>
