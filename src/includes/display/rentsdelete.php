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
 * description: Display the rents delete page.
 *              The following object(s) should exist:
 *                  - $sToken (string)
 *                  - $iYear (integer)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * W3C: This document was successfully checked as XHTML 1.0 Strict!
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($sToken) || !isset($iYear)  )
    die('-1');
?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<hr/>
<div id="CONTENT">
<h1>Supprimer les anciennes r&#233;servations</h1>
<form id="FORMCONFIRM" method="post" action="<?php echo PBR_URL;?>rentsdelete.php">
<fieldset class="fieldsetform">
<legend class="legendmain">Confirmer la suppression</legend>
<input type="hidden" name="<?php echo CDate::YEARTAG; ?>" value="<?php echo $iYear; ?>" />
<input type="hidden" name="<?php echo CPHPSession::TOKENTAG; ?>" value="<?php echo $sToken; ?>" />
<div id="MESSAGE">
<p class="error">Toutes les r&#233;servations ant&#233;rieures &#224; <?php echo $iYear; ?> vont &ecirc;tre supprim&#233;es.</p>
</div>
<ul>
<li class="listbuttonitem">
<input class="inputButton" type="submit" value="Supprimer" name="con"/>&nbsp;
<input class="inputButton" type="submit" value="&nbsp;Annuler&nbsp;&nbsp;" name="can"/>
</li>
</ul>
</fieldset>
</form>
</div>
