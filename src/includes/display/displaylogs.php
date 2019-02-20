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
 * description: Display the logs page.
 *              The following object(s) should exist:
 *                  - $tRecordset
 *                  - $CPaging
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_PAGE_LOADED') )
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
        if( $iCode===3 )
        {
            $sBuffer.='<p class="success">Suppression r&#233;ussie.</p>';
        }//if( $iMessageCode===1 )
        $sBuffer.='</div>';
        echo $sBuffer,"\n";
    }//if( $iCode>0 )
}

/**
  * function: BuildLog
  * description: Build and display a log
  * parameters:  ARRAY|tRecord - recordset
  *             (should have keys: log_date, log_user, log_type, log_description,
  *              log_mysqluser, log_mysqlcurrentuser )
  *            INTEGER|iPagingMax - Max page count
  *            INTEGER|iIndex     - Record count
  * return: none
  * author: Olivier JULLIEN - 2010-02-04
  */
function BuildLog(&$tRecord, $iPagingMax, $iIndex)
{
    if( is_array($tRecord) && array_key_exists('log_date', $tRecord)
                           && array_key_exists('log_user', $tRecord)
                           && array_key_exists('log_type', $tRecord)
                           && array_key_exists('log_title', $tRecord)
                           && array_key_exists('log_description', $tRecord)
                           && array_key_exists('log_mysqluser', $tRecord)
                           && array_key_exists('log_mysqlcurrentuser', $tRecord) )
    {
        if( ($iIndex<=1) && ($iPagingMax<=1) )
        {
            // Do not display the blue line
            $sBuffer='<li class="first">';
        }
        else
        {
            $sBuffer='<li>';
        }
        $sBuffer.='<span>'.htmlentities($tRecord['log_date'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_user'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_type'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_title'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_description'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_mysqluser'],ENT_QUOTES,'UTF-8').' &#8226; ';
        $sBuffer.=htmlentities($tRecord['log_mysqlcurrentuser'],ENT_QUOTES,'UTF-8');
        echo $sBuffer.'</span></li>',"\n";
    }//if( is_array($tRecord) && array_key_exists(...
}
    /** Build href
     *************/
    $sPagingHRef=PBR_URL.'logs.php?act=show';
?>
 <div id="PAGE">
  <div id="HEADER"><h5><em>Connect&#233; en tant que <?php echo htmlentities(CUser::GetInstance()->GetUsername(),ENT_QUOTES,'UTF-8');?></em></h5></div>
  <hr/>
  <ul class="navigation menu">
   <li><a title="Aller en bas de la page" name="pagetop" href="#pagebottom">&#8595;</a></li>
   <li><a title="Se d&#233;connecter" href="<?php echo PBR_URL;?>logout.php">D&#233;connexion</a></li>
   <li><a title="Retourner au calendrier" href="<?php echo PBR_URL;?>">Calendrier</a></li>
   <li><a title="Configurer" href="<?php echo PBR_URL;?>parameters.php?act=show">Param&#232;tres</a></li>
   <li><a title="Gestion des utilisateurs" href="<?php echo PBR_URL;?>users.php?act=show">Utilisateurs</a></li>
   <li><a title="Voir les logs" href="<?php echo PBR_URL;?>logs.php?act=show">Logs</a></li>
  </ul>
  <hr/>
  <div id="CONTENT">
   <?php if(isset($iMessageCode)) BuildMessage($iMessageCode); ?>
   <h1>Logs</h1>
   <form id="FORMLOGS" method="get" action="<?php echo PBR_URL;?>logs.php">
    <fieldset class="fieldsetsub fieldsetform">
     <legend class="legendmain">Effacer tous les logs</legend>
     <input type="hidden" name="act" value="delete" />
     <ul>
      <li class="listbuttonitem"><input class="inputButton" type="submit" value="Supprimer" /></li>
     </ul>
    </fieldset>
   </form>
   <fieldset class="fieldsetmain">
    <legend class="legendmain">Logs</legend>
    <ul class="navigation menu">
<?php
    if( CPaging::GetInstance()->GetMax()>1 )
    {
        if( CPaging::GetInstance()->GetCurrent()>1 )
        {
            $sBuffer='<li><a title="Page pr&#233;c&#233;dente" href="';
            $sBuffer.=$sPagingHRef.'&amp;pag='.(CPaging::GetInstance()->GetCurrent()-1);
            $sBuffer.='">Page pr&#233;c&#233;dente</a></li>';
            echo $sBuffer,"\n";
        }//if( CPaging::GetInstance()->GetCurrent()>1 )
        if( CPaging::GetInstance()->GetCurrent()<CPaging::GetInstance()->GetMax() )
        {
            $sBuffer='<li><a title="Page suivante" href="';
            $sBuffer.=$sPagingHRef.'&amp;pag='.(CPaging::GetInstance()->GetCurrent()+1);
            $sBuffer.='">Page suivante</a></li>';
            echo $sBuffer,"\n";
        }//if( CPaging::GetInstance()->GetCurrent()<CPaging::GetInstance()->GetMax() )
    }//if( CPaging::GetInstance()->GetMax()>1 )
?>
    </ul>
    <ul class="records">
<?php
    if( is_array($tRecordset) )
    {
        $iIndex=1;
        foreach( $tRecordset as $tRecord )
        {
            BuildLog($tRecord,CPaging::GetInstance()->GetMax(),$iIndex++);
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
