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
 * description: Display the rent delete page.
 *              The following object(s) should exist:
 *                  - $pRent (instance of CRent)
 *                  - $pDate (instance of CDate)
 *                  - $pContact ( instance of CContact)
 *                  - $sToken (string)
 * author: Olivier JULLIEN - 2010-02-04
 * W3C: This document was successfully checked as XHTML 1.0 Strict!
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !isset($sToken) || !isset($pContact) || !isset($pDate) || !isset($pRent) )
    die('-1');

    // Build form title
    $sFormTitle  = $pDate->GetRequestDay().' ';
    $sFormTitle .= $pDate->GetMonthName( $pDate->GetRequestMonth(), 1).' ';
    $sFormTitle .= $pDate->GetRequestYear().' - ';
    $sFormTitle .= $pContact->GetLastName(1).' '.$pContact->GetFirstName(1);
?>
<div id="PAGE">
<div id="HEADER">
<p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
</div>
<hr/>
<div id="CONTENT">
<h1><?php echo $sFormTitle;?></h1>
<form id="FORMCONFIRM" method="post" action="<?php echo PBR_URL;?>rentdelete.php">
<fieldset class="fieldsetform">
<legend class="legendmain">Confirmer la suppression</legend>
<input type="hidden" name="<?php echo CPHPSession::TOKENTAG; ?>" value="<?php echo $sToken; ?>" />
<input type="hidden" name="<?php echo CRent::IDENTIFIERTAG; ?>" value="<?php echo $pRent->GetIdentifier(); ?>" />
<div id="MESSAGE">
<p class="error">Cette r&eacute;servation va &ecirc;tre supprim&eacute;e.</p>
</div>
<ul>
<li class="listbuttonitem"><input class="inputButton" type="submit" value="Supprimer" name="con"/>&nbsp;<input class="inputButton" type="submit" value="&nbsp;Annuler&nbsp;&nbsp;" name="can"/></li>
</ul>
</fieldset>
</form>
</div>
