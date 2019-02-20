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
 *                  - CNewUser
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-06-11 - add password check
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_NEWUSER_LOADED') )
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
        if( $iCode===1 )
        {
            $sBuffer.='<p class="error">Le nom d&#39;utilisateur ou les mots de passe que vous avez saisis sont incorrects.</p>';
        }
        elseif($iCode===2)
        {
            $sBuffer.='<p class="success">Enregistrement r&#233;ussi.</p>';
        }
        elseif($iCode===3)
        {
            $sBuffer.='<p class="error">Cet utilisateur existe d&#233;j&#224;.</p>';
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
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
    if( is_array($tRecord) && array_key_exists('user_id', $tRecord)
                           && array_key_exists('user_name', $tRecord)
                           && array_key_exists('user_lastvisit', $tRecord)
                           && array_key_exists('user_state', $tRecord) )
    {
        $sBuffer='<li>';
        $sBuffer.='<a href="'.PBR_URL.'users.php?act=select&amp;usi='.$tRecord['user_id'].'" title="Modifier">';
        $sBuffer.='<span>'.htmlentities($tRecord['user_name'],ENT_QUOTES,'UTF-8').' &#8226; ';
        if( strlen($tRecord['user_lastvisit'])>0 )
        {
            $sBuffer.=htmlentities($tRecord['user_lastvisit'],ENT_QUOTES,'UTF-8').' &#8226; ';
        }
        else
        {
            $sBuffer.='JAMAIS &#8226; ';
        }//if( strlen($tRecord['user_lastvisit'])>0 )
        if($tRecord['user_state']==1) $sBuffer.='Actif';
        if($tRecord['user_state']!=1) $sBuffer.='Non actif';
        $sBuffer.='</span></a></li>';
        echo $sBuffer,"\n";
    }//if( is_array($tRecord) && array_key_exists(
}
    /** Build form action
     ********************/
    $sFormAction=PBR_URL.'users.php';

    /** Case select
     **************/
    if( $sAction=='select' )
    {
    	// Build title
    	$sFormTitle=CNewUser::GetInstance()->GetUsername(1);
        // Build legend
        $sFormLegend='Modifier';
    	// Build hidden control
    	$sFormHidden='<input type="hidden" name="act" value="update" />';
        $sFormHidden.='<input type="hidden" name="usr" value="'.CNewUser::GetInstance()->GetUsername().'" />';
        $sFormHidden.='<input type="hidden" name="usi" value="'.CNewUser::GetInstance()->GetIdentifier().'" />';
        // Build name label
        $sDisable='disabled="disabled"';
        $sLabelName='Nom';
        $sHelp='';
        // Build state control
        $sState='<li class="label required">Actif</li>';
        $sState.='<li class="radio"><input id="userstate" class="inputCheckbox" type="checkbox" value="1" name="sta" ';
        if( CNewUser::GetInstance()->GetState()===1 )
        {
            $sState.='checked="checked"';
        }//if( CNewUser::GetInstance()->GetState()===1 )
        $sState.=' /></li>';
    }
    else
    {
    	// Build title
    	$sFormTitle='Utilisateurs';
        // Build legend
        $sFormLegend='Nouvel utilisateur';
	    // Build hidden control
    	$sFormHidden='<input type="hidden" name="act" value="new" />';
        // Build name label
        $sDisable='';
        $sLabelName='Nom*';
        $sHelp='<li class="help"><em>* Seuls les caract&#232;res alphanum&#233;riques, &quot;@&quot;,&quot;.&quot;,&quot;-&quot; et &quot;_&quot; sont autoris&#233;s.</em></li>';
        // Build state control
        $sState='';
    }//if( $sAction=='select' )

    // Build default username
    $sFormUsername=CNewUser::GetInstance()->GetUsername(1);

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
   <h1><?php echo $sFormTitle; ?></h1>
   <form id="FORMUSR" method="post" action="<?php echo $sFormAction;?>">
    <fieldset class="fieldsetsub fieldsetform">
     <legend class="legendmain"><?php echo $sFormLegend; ?></legend>
<?php echo $sFormHidden,"\n"; ?>
     <ul>
      <li class="label required"><?php echo $sLabelName; ?></li>
      <li><input id="loginusr" class="inputText" type="text" value="<?php echo $sFormUsername; ?>" maxlength="45" size="10" name="usr" <?php echo $sDisable;?> /></li>
<?php if(!empty($sHelp)) echo $sHelp,"\n"; ?>
      <li class="label required">Mot de passe</li>
      <li><input id="loginpwd" class="inputText" type="password" value="" maxlength="40" size="10" name="pwd"/></li>
      <li class="label required">Confirmez</li>
      <li><input id="loginpwdc" class="inputText" type="password" value="" maxlength="40" size="10" name="pwdc"/></li>
<?php if(!empty($sState)) echo $sState,"\n"; ?>    
      <li class="listbuttonitem"><input class="inputButton" type="submit" value="Envoyer" name="new"/></li>
     </ul>
    </fieldset>
   </form>
   <a name="pagemiddle"></a>
   <fieldset>
    <legend class="legendmain">Liste des utilisateurs</legend>
    <ul class="records">
     <li class="first"><span>Nom &#8226; Derni&#232;re visite &#8226; Actif</span></li>
<?php
    if( is_array($tRecordset) )
    {
        foreach( $tRecordset as $tRecord )
        {
            BuildUser($tRecord);
        }//foreach( $tRecordset as $tRecord )
    }//if( is_array($tRecordset) )
?>
    </ul>
   </fieldset>
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
