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
 *              The following object(s) should exist:
 *                  - $pUser (instance of CUser)
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_URL') || !defined('PBR_PATH') || !defined('PBR_DB_LOADED') || !isset($pUser) )
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
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
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
function CreateAdmin( CUser $pUser)
{
    $bReturn=FALSE;
    if( $pUser->IsValidNew()==TRUE )
    {
        try
        {
            // Prepare
            $sSQL='INSERT INTO `'.PBR_DB_DBN.'`.`user` (`login`, `password`, `registered`, `role`, `last_visit`, `state`) VALUES ( :sName, :sPassword, SYSDATE(), 10, NULL, 1)';
            $pPDOStatement = CDBLayer::GetInstance()->GetDriver()->prepare($sSQL);
            // Bind
            $pPDOStatement->bindValue(':sName',$pUser->GetUsername(),PDO::PARAM_STR);
            $pPDOStatement->bindValue(':sPassword',$pUser->GetPassword(),PDO::PARAM_STR);
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

    /** Create admin
     ***************/
    if( $bSuccess )
    {
        $bSuccess = CreateAdmin($pUser);
        if( $bSuccess )
        {
            echo '<p class="success">Cr&#233;ation de l&#39;administrateur:OK</p>',"\n";
        }
        else
        {
             echo '<p class="error">Cr&#233;ation de l&#39;administrateur:ECHEC</p>',"\n";
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
            echo '<p class="success">Chargement des donn&#233;es:OK</p>',"\n";
        }
        else
        {
             echo '<p class="error">Chargement des donn&#233;es:ECHEC</p>',"\n";
        }//if( $bSuccess )
    }//if( $bSuccess )
    sleep(1);

    if( $bSuccess )
    {
        echo '<p class="success">Installation r&#233;ussie.</p>',"\n";
        echo '<p>Une fois que vous aurez v&#233;rifi&#233; que tout fonctionne correctement, vous devez effacer le fichier install.php et le dossier includes-install</p>',"\n";
    }
    else
    {
        echo '<p class="error">Echec de l&#39;installation. V&#233;rifiez votre configuration.</p>',"\n";
    }//if( $bSuccess )

?>
   </div>
  </div>
