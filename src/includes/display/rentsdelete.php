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
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($sToken) || !isset($iYear) || !isset($pHeader) )
    die('-1');
?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<?php echo $pHeader->GetHR(),"\n"; ?>
<div id="CONTENT">
<h1>Supprimer les anciennes r&#233;servations</h1>
<form id="FORMCONFIRM" method="post" action="<?php echo PBR_URL;?>rentsdelete.php">
<fieldset class="fieldsetform">
<legend class="legendmain">Confirmer la suppression</legend>
<input type="hidden" name="<?php echo CDate::YEARTAG; ?>" value="<?php echo $iYear; ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
<input type="hidden" name="<?php echo CPHPSession::TOKENTAG; ?>" value="<?php echo $sToken; ?>"<?php echo $pHeader->GetCloseTag(),"\n"; ?>
<div id="MESSAGE">
<p class="error">Toutes les r&#233;servations ant&#233;rieures &#224; <?php echo $iYear; ?> vont &ecirc;tre supprim&#233;es.</p>
</div>
<ul>
<li class="listbuttonitem">
<input class="inputButton" type="submit" value="Supprimer" name="con"<?php echo $pHeader->GetCloseTag(); ?>&nbsp;<input class="inputButton" type="submit" value="Annuler" name="can"<?php echo $pHeader->GetCloseTag(); ?>
</li>
</ul>
</fieldset>
</form>
</div>
