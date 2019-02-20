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
 * description: install database
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_PATH') || !defined('PBR_DB_LOADED') || !defined('PBR_NEWUSER_LOADED') )
    die(-1);

/**
  * function: ReadSQLFile
  * description: Read a sql file
  * parameters: STRING|sFile  - file name
  *             STRING|sText  - file content
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-02-04
  */
function ReadSQLFile( $sFile , &$sText )
{
    // Initialize
    $pFile=NULL;
    $bReturn=FALSE;
    $sPath=PBR_PATH.'/includes-install/database/'.$sFile.'.sql';
    // Verify
    $bReturn=is_file($sPath);
    // Open
    if( $bReturn )
    {
        $pFile=fopen( $sPath, "rb" );
        // Read
        if( $pFile!==FALSE )
        {
            $sText=fread( $pFile, filesize($sPath) );
            $bReturn=TRUE;
        }//if( $pFile!=FALSE )
        fclose($pFile);
    }//if( $bReturn )
    return $bReturn;
}

/**
  * function: ExecuteSQL
  * description: Execute the sql
  * parameters: STRING|sSQL - sql command
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-02-04
  */
function ExecuteSQL( &$sSQL )
{
    $bReturn=FALSE;
    if( is_scalar($sSQL) && (strlen($sSQL)>0) )
    {
        try
        {
            // Prepare
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
            // Execute
            $pPDOStatement->execute();
            $bReturn=TRUE;
        }
        catch(PDOException $e)
        {
            echo '<p>'.$e->getMessage().'</p>',"\n";
        }//try
        // Free resource
        $pPDOStatement=NULL;
    }//if( is_scalar($sSQL) && strlen($sSQL)>0 )
    return $bReturn;
}

/**
  * function: CreateSchema
  * description: Create schema
  * parameters: none
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-02-04
  */
function CreateSchema()
{
    $bReturn=FALSE;
    $sSQL=$sText='';
    // Read file
    if( ReadSQLFile('schema', $sText)==TRUE )
    {
        // Replace database name
        $sSQL=str_replace('_PBR_DB_DBN_',PBR_DB_DBN,$sText);
        // Execute sql
        $bReturn=ExecuteSQL($sSQL);
    }//if( ReadSQLFile('schema', $sSQL) )
    return $bReturn;
}

/**
  * function: CreateAdmin
  * description: Create admin
  * parameters: none
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-02-04
  */
function CreateAdmin()
{
    $bReturn=FALSE;
    if( CNewUser::GetInstance()->IsValidNew()==TRUE )
    {
        try
        {
            // Prepare
            $sSQL='INSERT INTO `'.PBR_DB_DBN.'`.`user` (`login`, `password`, `registered`, `role`, `last_visit`, `state`) VALUES ( :sName, :sPassword, SYSDATE(), 10, NULL, 1)';
            $pPDOStatement = CDb::GetInstance()->PDO()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindParam(':sName',CNewUser::GetInstance()->GetUsername(),PDO::PARAM_STR,45);
            $pPDOStatement->bindParam(':sPassword',CNewUser::GetInstance()->GetPassword(),PDO::PARAM_STR,40);
            // Execute
            $pPDOStatement->execute();
            $bReturn=TRUE;
        }
        catch(PDOException $e)
        {
            echo '<p>'.$e->getMessage().'</p>',"\n";
        }//try
        // Free resource
        $pPDOStatement=NULL;
    }//if( CNewUser::GetInstance()->IsValidNew() )

    return $bReturn;
}

/**
  * function: LoadData
  * description: Load the data
  * parameters: none
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-02-04
  */
function LoadData()
{
    $bReturn=FALSE;
    $sSQL=$sText='';
    $tFiles=array('data-config');

    // Read files
    foreach( $tFiles as $sFile )
    {
        // Read file
        if( ReadSQLFile($sFile, $sText)==TRUE )
        {
            // Replace database name
            $sSQL=str_replace('_PBR_DB_DBN_',PBR_DB_DBN,$sText);
            // Execute sql
            $bReturn=ExecuteSQL($sSQL);
            if( $bReturn===FALSE )
            {
                break;
            }// if( ExecuteSQL($sSQL)===FALSE )
        }
        else
        {
            $bReturn=FALSE;
            break;
        }//if( ReadSQLFile('schema', $sSQL) )
    }//foreach( $tFiles as $sFile )
    return $bReturn;
}

/**
  * function: CreateStoredProc
  * description: Create stored procedure
  * parameters: none
  * return: BOOLEAN
  * author: Olivier JULLIEN - 2010-02-04
  */
function CreateStoredProc()
{
    $bReturn=FALSE;
    $sSQL=$sText='';
    $tFiles=array(
                'sp_parameterget'
                 ,'sp_sessionvalid'
                 ,'sp_configget'
                 ,'sp_configupdate'
                 ,'sp_contactdel'
                 ,'sp_contactget'
                 ,'sp_contactrentsget'
                 ,'sp_contactrentsgetcount'
                 ,'sp_contactset'
                 ,'sp_contactsget'
                 ,'sp_contactsgetcount'
                 ,'sp_contactupdate'
                 ,'sp_logsdel'
                 ,'sp_logsget'
                 ,'sp_logsgetcount'
                 ,'sp_maxget'
                 ,'sp_rentcontactset'
                 ,'sp_rentdel'
                 ,'sp_rentget'
                 ,'sp_rentsdel'
                 ,'sp_rentset'
                 ,'sp_rentsget'
                 ,'sp_rentsgetcount'
                 ,'sp_rentsmonthget'
                 ,'sp_rentupdate'
                 ,'sp_sessiondelete'
                 ,'sp_sessionlogoff'
                 ,'sp_sessionset'
                 ,'sp_userget'
                 ,'sp_userset'
                 ,'sp_usersget'
                 ,'sp_userupdate'
                );
    // Read files
    foreach( $tFiles as $sFile )
    {
        // Read file
        if( ReadSQLFile($sFile, $sText)==TRUE )
        {
            // Sanitise text
            $sSQL=str_replace('_PBR_DB_DBN_',PBR_DB_DBN,$sText);
            $sSQL=str_replace('DELIMITER $$','',$sSQL);
            $sSQL=str_replace('$$',';',$sSQL);
            // Execute sql
            $bReturn=ExecuteSQL($sSQL);
            if( $bReturn===FALSE )
            {
                break;
            }// if( ExecuteSQL($sSQL)===FALSE )
        }
        else
        {
            $bReturn=FALSE;
            break;
        }//if( ReadSQLFile('schema', $sSQL) )
    }//foreach( $tFiles as $sFile )
    return $bReturn;
}

?>
 <div id="PAGE" class="login">
  <div id="HEADER"></div>
  <div id="CONTENT">
   <div id="MESSAGE">
<?php

    /** Create schema
     ****************/
    $bSuccess = CreateSchema();
    if( $bSuccess )
    {
        echo '<p class="success">Cr&#233;ation du sch&#233;ma:OK</p>',"\n";
    }
    else
    {
         echo '<p class="error">Cr&#233;ation du sch&#233;ma:ECHEC</p>',"\n";
    }//if( $bSuccess )
    sleep(1);

    /** Create stored procedures
     ***************************/
    if( $bSuccess )
    {
        $bSuccess2 = CreateStoredProc();
        if( $bSuccess2 )
        {
            echo '<p class="success">Cr&#233;ation des proc&#233;dures stock&#233;es:OK</p>',"\n";
            if( PBR_USE_STOREDPROC===0 )
            {
                echo '<p><em>Note: PBRaiders est configur&#233; et est compl&#232;tement op&#233;rationnel pour fonctionner sans. Si vous souhaitez forcer PBRaiders &#224; les utiliser vous devez modifier le fichier config.php.</em></p>',"\n";
            }
            else
            {
                echo '<p><em>Note: PBRaiders est correctement configur&#233; pour les utiliser.</em></p>',"\n";
            }//if( PBR_USE_STOREDPROC===0 )
        }
        else
        {
            echo '<p class="error">Cr&#233;ation des proc&#233;dures stock&#233;es:ECHEC</p>',"\n";
            if( PBR_USE_STOREDPROC===0 )
            {
                echo '<p><em>Note: cela ne pose aucun probl&#232;me car PBRaiders est configur&#233; et est compl&#232;tement op&#233;rationnel pour fonctionner sans.</em></p>',"\n";
            }
            else
            {
                echo '<p class="error"><em>ATTENTION: vous devez modifier le fichier config.php pour forcer PBRaiders &#224; fonctionner sans.</em></p>',"\n";
            }//if( PBR_USE_STOREDPROC===0 )
        }//if( $bSuccess2 )
    }//if( $bSuccess )

    /** Create admin
     ***************/
    if( $bSuccess )
    {
        $bSuccess = CreateAdmin();
        if( $bSuccess )
        {
            echo '<p class="success">Cr&#233;ation de l&#146;administrateur:OK</p>',"\n";
        }
        else
        {
             echo '<p class="error">Cr&#233;ation de l&#146;administrateur:ECHEC</p>',"\n";
        }//if( $bSuccess )
    }//if( $bSuccess )
    sleep(1);

    /** Load data
     ************/
    if( $bSuccess )
    {
        $bSuccess = LoadData();
        if( $bSuccess )
        {
            echo '<p class="success">Chargement des donn&#233es:OK</p>',"\n";
        }
        else
        {
             echo '<p class="error">Chargement des donn&#233es:ECHEC</p>',"\n";
        }//if( $bSuccess )
    }//if( $bSuccess )
    sleep(1);

    if( $bSuccess )
    {
        echo '<p class="success">Installation r&#233ussie.</p>',"\n";
    }
    else
    {
        echo '<p class="error">Echec de l&#146;installation. V&#233;rifiez votre configuration.</p>',"\n";
    }//if( $bSuccess )

?>
   </div>
  </div>
