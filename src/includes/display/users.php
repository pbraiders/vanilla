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
 *                  - $tRecordset (array)
 *                  - $pUser (instance of CUser or null)
 *                  - $iMessageCode (integer)
 *                  - $pHeader (instance of CHeader)
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-11 - add password check
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if (!defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_AUTH_LOADED') || !is_array($tRecordset) || !is_integer($iMessageCode) || !isset($pHeader))
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
    if ($iCode > 0) {
        $sBuffer = '<div id="MESSAGE">';
        if ($iCode === 1) {
            $sBuffer .= '<p class="error">Le nom d&#39;utilisateur ou les mots de passe que vous avez saisis sont incorrects.</p>';
        } elseif ($iCode === 2) {
            $sBuffer .= '<p class="success">Enregistrement r&#233;ussi.</p>';
        } elseif ($iCode === 3) {
            $sBuffer .= '<p class="error">Cet utilisateur existe d&#233;j&#224;.</p>';
        } //if( $iMessageCode===1 )
        $sBuffer .= '</div>';
        echo $sBuffer, "\n";
    } //if( $iCode>0 )
}

/**
 * function: BuildUser
 * description: Build and display an user
 * parameters: ARRAY|tRecord - recordset
 *             (should have keys: user_id, user_name, user_lastvisit, user_state)
 * return: none
 * author: Olivier JULLIEN - 2010-02-04
 */
function BuildUser(&$tRecord)
{
    if (
        is_array($tRecord) && array_key_exists('user_id', $tRecord)
        && array_key_exists('user_name', $tRecord)
        && array_key_exists('user_lastvisit', $tRecord)
        && array_key_exists('user_state', $tRecord)
    ) {
        $sBuffer = '<li>';
        $sBuffer .= '<a href="' . PBR_URL . 'users.php?' . CAction::ACTIONTAG . '=select&amp;' . CUser::IDENTIFIERTAG . '=' . $tRecord['user_id'] . '" title="Modifier">';
        $sBuffer .= '<span>' . htmlentities($tRecord['user_name'], ENT_QUOTES, 'UTF-8') . ' &#8226; ';
        if (strlen($tRecord['user_lastvisit']) > 0) {
            $sBuffer .= htmlentities($tRecord['user_lastvisit'], ENT_QUOTES, 'UTF-8') . ' &#8226; ';
        } else {
            $sBuffer .= 'JAMAIS &#8226; ';
        } //if( strlen($tRecord['user_lastvisit'])>0 )
        if ($tRecord['user_state'] == 1) $sBuffer .= 'Actif';
        if ($tRecord['user_state'] != 1) $sBuffer .= 'Non actif';
        $sBuffer .= '</span></a></li>';
        echo $sBuffer, "\n";
    } //if( is_array($tRecord) && array_key_exists(
}
/** Build form action
 ********************/
$sFormAction   = PBR_URL . 'users.php';
$sFormUsername = '';

/** Build user data
 ******************/
if (isset($pUser) && ($pUser->GetIdentifier() > 0)) {
    // Build username
    $sFormUsername = $pUser->GetUsername(1);
    // Build title
    $sFormTitle    = $sFormUsername;
    // Build legend
    $sFormLegend   = 'Modifier';
    // Build hidden control
    $sFormHidden   = '<input type="hidden" name="' . CAction::ACTIONTAG . '" value="update"' . $pHeader->GetCloseTag();
    $sFormHidden  .= '<input type="hidden" name="' . CUser::USERNAMETAG . '" value="' . $pUser->GetUsername() . '"' . $pHeader->GetCloseTag();
    $sFormHidden  .= '<input type="hidden" name="' . CUser::IDENTIFIERTAG . '" value="' . $pUser->GetIdentifier() . '"' . $pHeader->GetCloseTag();
    // Build name label
    $sDisable      = 'disabled="disabled"';
    $sLabelName    = 'Nom';
    $sHelp         = '';
    // Build state control
    $sState        = '<li class="label required">Actif</li>';
    $sState       .= '<li class="radio"><input id="userstate" class="inputCheckbox" type="checkbox" value="1" name="' . CUser::STATETAG . '" ';
    if ($pUser->GetState() === 1) {
        $sState   .= 'checked="checked"';
    } //if( $pUser->GetState()===1 )
    $sState .= $pHeader->GetCloseTag() . '</li>';
} else {
    // Build username
    if (isset($pUser)) {
        $sFormUsername = $pUser->GetUsername(1);
    } //if( isset($pUser) )
    // Build title
    $sFormTitle  = 'Utilisateurs';
    // Build legend
    $sFormLegend = 'Nouvel utilisateur';
    // Build hidden control
    $sFormHidden = '<input type="hidden" name="' . CAction::ACTIONTAG . '" value="new"' . $pHeader->GetCloseTag();
    // Build name label
    $sDisable    = '';
    $sLabelName  = 'Nom*';
    $sHelp       = '<li class="help"><em>* Seuls les caract&#232;res alphanum&#233;riques, &quot;@&quot;,&quot;.&quot;,&quot;-&quot; et &quot;_&quot; sont autoris&#233;s.</em></li>';
    // Build state control
    $sState      = '';
} //if( isset($pUser) )

?>
<div id="PAGE">
    <div id="HEADER">
        <p><em><small>Connect&#233; en tant que <?php echo CAuth::GetInstance()->GetUsername(1); ?></small></em></p>
    </div>
    <?php echo $pHeader->GetHR(), "\n"; ?>
    <?php echo $pHeader->GetAnchor('pagetop'), "\n"; ?>
    <ul class="navigation menu">
        <li><a title="Aller &#224; la liste des utilisateurs" href="#pagemiddle">&#8595;</a></li>
        <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL; ?>logout.php">D&#233;connexion</a></li>
        <li><a title="Retourner au calendrier" href="<?php echo PBR_URL; ?>">Calendrier</a></li>
        <li><a title="Configurer" href="<?php echo PBR_URL; ?>parameters.php">Param&#232;tres</a></li>
        <li><a title="Voir les graphes" href="<?php echo PBR_URL; ?>graphs.php">Graphes</a></li>
        <li><a title="Voir les logs" href="<?php echo PBR_URL; ?>logs.php">Logs</a></li>
    </ul>
    <?php echo $pHeader->GetHR(), "\n"; ?>
    <div id="CONTENT">
        <?php BuildMessage($iMessageCode); ?>
        <h1><?php echo $sFormTitle; ?></h1>
        <form id="FORMUSR" method="post" action="<?php echo $sFormAction; ?>">
            <fieldset class="fieldsetsub fieldsetform">
                <legend class="legendmain"><?php echo $sFormLegend; ?></legend>
                <?php echo $sFormHidden, "\n"; ?>
                <ul>
                    <li class="label required"><?php echo $sLabelName; ?></li>
                    <li><input id="loginusr" class="inputText" type="text" value="<?php echo $sFormUsername; ?>" maxlength="<?php echo CUser::USERNAMEMAX; ?>" size="10" name="<?php echo CUser::USERNAMETAG; ?>" <?php echo $sDisable . $pHeader->GetCloseTag(); ?></li>
                        <?php if (!empty($sHelp)) echo $sHelp, "\n"; ?>
                    <li class="label required">Mot de passe</li>
                    <li><input id="loginpwd" class="inputText" type="password" value="" maxlength="<?php echo CUser::PASSWORDMAX; ?>" size="10" name="<?php echo CUser::PASSWORDTAG; ?>" <?php echo $pHeader->GetCloseTag(); ?></li>
                    <li class="label required">Confirmez</li>
                    <li><input id="loginpwdc" class="inputText" type="password" value="" maxlength="<?php echo CUser::PASSWORDMAX; ?>" size="10" name="<?php echo CUser::PASSWORDCHECKTAG; ?>" <?php echo $pHeader->GetCloseTag(); ?></li>
                        <?php if (!empty($sState)) echo $sState, "\n"; ?>
                    <li class="listbuttonitem"><input class="inputButton" type="submit" value="Envoyer" name="new" <?php echo $pHeader->GetCloseTag(); ?></li>
                </ul>
            </fieldset>
        </form>
        <?php echo $pHeader->GetAnchor('pagemiddle'), "\n"; ?>
        <fieldset>
            <legend class="legendmain">Liste des utilisateurs</legend>
            <ul class="records">
                <li class="first"><span>Nom &#8226; Derni&#232;re visite &#8226; Actif</span></li>
                <?php
                foreach ($tRecordset as $tRecord) {
                    BuildUser($tRecord);
                } //foreach( $tRecordset as $tRecord )
                ?>
            </ul>
        </fieldset>
    </div>
    <?php echo $pHeader->GetHR(), "\n"; ?>
    <?php echo $pHeader->GetAnchor('pagebottom'), "\n"; ?>
    <ul class="navigation menu">
        <li><a title="Aller en haut de la page" href="#pagetop">&#8593;</a></li>
        <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL; ?>logout.php">D&#233;connexion</a></li>
        <li><a title="Retourner au calendrier" href="<?php echo PBR_URL; ?>">Calendrier</a></li>
        <li><a title="Configurer" href="<?php echo PBR_URL; ?>parameters.php">Param&#232;tres</a></li>
        <li><a title="Voir les graphes" href="<?php echo PBR_URL; ?>graphs.php">Graphes</a></li>
        <li><a title="Voir les logs" href="<?php echo PBR_URL; ?>logs.php">Logs</a></li>
    </ul>