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
 * description: log file managment
 * author: Olivier JULLIEN - 2010-05-24
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') || !defined('PBR_LOG_DIR') )
    die('-1');

class CLog
{

    /** Contants
     ***********/
    const LOGFILENAME='pbraider_log';
    const LOGFILESIZE=3145728;

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // File descriptor
    private $m_pFile = FALSE;

    /** Private methods
     ******************/

    /**
     * function: FolderManagment
     * description: Creates the folder log if not exists.
     * parameter: STRING|$sFolder - log folder path.
     * return: BOOLEAN - FALSE if cannot create the folder.
     * author: Olivier JULLIEN - 2010-05-24
     */
    private function FolderManagment( $sFolder )
    {
        $bReturn = FALSE;

        if( is_string($sFolder) && (strlen($sFolder)>0) )
        {
            $bReturn = TRUE;
            if( !is_dir($sFolder) )
            {
                if( !mkdir($sFolder) )
                {
                   $bReturn = FALSE;
                }//if( !mkdir($sFolder) )
            }//if( !is_dir($sFolder) )
        }//if( is_string($sFolder) && (strlen($sFolder)>0) )
        return $bReturn;
    }

    /**
     * function: FileManagment
     * description: Save, create and open log file
     * parameter: STRING|$sFile - log file.
     * return: BOOLEAN - FALSE if cannot create the file.
     * author: Olivier JULLIEN - 2010-05-24
     */
    private function FileManagment( $sFile )
    {
        // Initialize
        $bReturn = FALSE;
        $iFileSize = FALSE;

        // Clears file status cache
        clearstatcache();

        if( is_string($sFile) && (strlen($sFile)>0) )
        {
            $bReturn = TRUE;

            // File test
            if( is_file($sFile) )
            {
                // Size test
                $iFileSize = filesize($sFile);
                if( $iFileSize>=CLog::LOGFILESIZE )
                {
                    // Rename and open
                    $sFilenameNew = $sFile.'_'.date('Ymd_His');
                    rename( $sFile, $sFilenameNew );
                    $bReturn=$this->Open($sFile,'w+b');
                }
                else
                {
                    // Open
                    $bReturn=$this->Open($sFile,'a+b');
                }//if( $iFileSize>=LOGFILESIZE )
            }
            else
            {
                // Open
                $bReturn=$this->Open($sFile,'w+b');
            }// if( is_file($sFile) )
        }// if( is_string($sFile) && (strlen($sFile)>0) )
        return $bReturn;
    }

   /**
     * function: Open
     * description: open the file
     * parameter: STRING|$sFile - path and name file to open
     *            STRING|$sMode - open mode
     * return: none
     * author: Olivier JULLIEN - 2010-05-24
     */
    private function Open($sFile,$sMode)
    {
        if( is_string($sFile) && (strlen($sFile)>0)
         && is_string($sMode) && (strlen($sMode)>0))
        {
            $this->m_pFile=fopen($sFile,$sMode);
        }//if( ...
    }

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-05-24
     */
    private function __construct()
    {
        if( defined('PBR_LOG') && (1==PBR_LOG) )
        {
            $sBuffer=PBR_PATH.'/'.PBR_LOG_DIR;
            if( $this->FolderManagment($sBuffer) )
            {
                $sBuffer.='/'.CLog::LOGFILENAME;
                $this->FileManagment($sBuffer);
            }//if( $this->FolderManagment($sBuffer) )
        }//if( defined('PBR_LOG') && (1==PBR_LOG) )
    }

    /** Public methods
     *****************/

    /**
     * function: __destruct
     * description: destructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-05-24
     */
    public function __destruct()
    {
        $this->Close();
    }

   /**
     * function: __clone
     * description: cloning is forbidden
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-05-24
     */
    public function __clone(){}

   /**
     * function: GetInstance
     * description: create or return the current instance
     * parameter: none
     * return: this
     * author: Olivier JULLIEN - 2010-05-24
     */
    public static function GetInstance()
    {
        if( is_null(self::$m_pInstance) )
        {
            self::$m_pInstance = new CLog();
        }
        return self::$m_pInstance;
    }

   /**
     * function: DeleteInstance
     * description: delete the current instance
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-05-24
     */
    public static function DeleteInstance()
    {
        if( !is_null(self::$m_pInstance) )
        {
            $tmp=self::$m_pInstance;
            self::$m_pInstance=NULL;
            unset($tmp);
        }
    }

   /**
     * function: IsOpen
     * description: test if the file is open
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-05-24
     */
    public function IsOpen()
    {
        $bReturn=TRUE;
        if( $this->m_pFile===FALSE )
        {
            $bReturn=FALSE;
        }//if( $this->m_pFile===FALSE )
        return $bReturn;
    }

   /**
     * function: Close
     * description: Close the file
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-05-24
     */
    public function Close()
    {
        $bReturn=TRUE;
        if( $this->IsOpen()===TRUE )
        {
            $bReturn=fclose($this->m_pFile);
            $this->m_pFile=FALSE;
        }//if( $this->IsOpen()===TRUE )
        return $bReturn;
    }

    /**
     * function: Write
     * description: Write into the file
     * parameter: STRING|$sUser - logged user
     *            STRING|$sType - log type (warning, error)
     *            STRING|$sLog  - line to log
     * return: none
     * author: Olivier JULLIEN - 2010-05-24
     */
    public function Write( $sUser, $sType, $sLog )
    {
        if( is_string($sLog) && (strlen($sLog)>0)
         && is_string($sType) && (strlen($sType)>0)
         && $this->IsOpen()===TRUE )
        {
            $sBuffer='['.date('D M d G:i:s Y').'] ['.$sType.'] [client '.GetIP(1).'] [user '.$sUser.'] '.$sLog."\n";
            fwrite( $this->m_pFile, $sBuffer);
        }//if(...
    }

}
define('PBR_LOG_LOADED',1);
?>
