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
 * description: Display the logout page.
 *              The following object(s) should exist:
 *                  - $iMessageCode
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') )
    die('-1');
?>
 <div id="PAGE" class="login">
  <div id="HEADER"></div>
  <div id="CONTENT">
   <div id="MESSAGE">
<?php
    if($iMessageCode===1)
    {
?>
    <p class="error">Votre action a &eacute;chou&eacute; car le service est temporairement inaccessible. Veuillez re-essayer plus tard ...</p>
<?php
    }
    elseif($iMessageCode===2)
    {
?>
    <p class="error">Vous n&#039;&ecirc;tes pas autoris&eacute; &agrave; effectuer cette action. Authentification requise.</p>
    <p><em>Vous pouvez vous reconnecter en cliquant sur le lien ci-dessous</em></p>
    <a title="Se connecter" href="<?php echo PBR_URL;?>login.php">PBRaiders</a>
<?php
    }//elseif($iMessageCode===2)
    else
    {
?>
    <p class="success">Vous &ecirc;tes d&eacute;connect&eacute;.</p>
    <p><em>Vous pouvez vous reconnecter en cliquant sur le lien ci-dessous</em></p>
    <a title="Se connecter" href="<?php echo PBR_URL;?>login.php">PBRaiders</a>
<?php
    }//if( isset($iMessageCode) )
?>
   </div>
  </div>