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
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CLog extends CDirectoryFileManagement
{

    /** Contants
     ***********/
    const LOGFILENAME = 'pbraider_log';
    const LOGFILESIZE = 3145728;

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    /** Private methods
     ******************/

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function __construct(){}

    /** Protected methods
     ********************/

    /**
     * function: DirectoryManagement
     * description: creates directory if not exists
     * parameter: STRING|$sFolder - directory path and name.
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DirectoryManagement($sFolder)
    {
        return $this->DirectoryCreate($sFolder);
    }

    /**
     * function: FileManagement
     * description: open file. If full, rename it, create and open a new one.
     * parameter: STRING|$sFolder - directory path and name.
     *            STRING|$sFile   - file name.
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function FileManagement( $sFolder, $sFile)
    {
        // Initialize
        $bReturn = FALSE;
        $iFileSize = 0;

        // Test parameter
        if( (IsStringNotEmpty( $sFolder, GetRegExPatternDirectory() )===TRUE)
         && (IsStringNotEmpty( $sFile, GetRegExPatternName() )===TRUE) )
        {

            // Build path
            $this->m_sFile = $sFolder.'/'.$sFile;

            // Clears file status cache
            clearstatcache();

            // Check the file
            $bReturn = is_file($this->m_sFile);

            // Check the size
            if( $bReturn )
            {
                $iFileSize = filesize($this->m_sFile);
                if( $iFileSize>=CLog::LOGFILESIZE )
                {
                    // File is full, rename it, create and open a new one
                    $sFileNew = $this->m_sFile.'_'.date('Ymd_His');
                    rename( $this->m_sFile, $sFileNew );
                    $this->m_pFile = fopen( $this->m_sFile, 'w+b');
                }
                else
                {
                    // File is not full, open it
                    $this->m_pFile = fopen( $this->m_sFile, 'a+b');
                }//if( $iFileSize>=LOGFILESIZE )
            }
            else
            {
                // File does not exist, create and open a new one
                $this->m_pFile = fopen( $this->m_sFile, 'w+b');
            }//if( $bReturn )

            $bReturn = $this->IsOpen();

        }//if( IsStringNotEmpty($sPath)===TRUE )
        return $bReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __destruct
     * description: destructor - close the file
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
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
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function __clone(){}

   /**
     * function: GetInstance
     * description: create or return the current instance
     * parameter: none
     * return: this
     * author: Olivier JULLIEN - 2010-06-15
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
     * author: Olivier JULLIEN - 2010-06-15
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
     * function: Write
     * description: Write into the file
     * parameter: STRING|$sUser - logged user
     *            STRING|$sType - log type (warning, error)
     *            STRING|$sLog  - line to log
     * return: INTEGER - number of bytes written or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function Write( $sUser, $sType, $sLog )
    {
        $iReturn = FALSE;
        if( (IsStringNotEmpty($sType)===TRUE)
         && (IsStringNotEmpty($sLog)===TRUE)
         && $this->IsOpen()===TRUE )
        {
            // Convert and trim $sUser
            IsStringNotEmpty($sUser);
            // Build log line
            $sBuffer='['.date('D M d G:i:s Y').'] ['.$sType.'] [client '.GetIP(1).'] [user '.$sUser.'] '.$sLog."\n";
            // Lock
            $bLock = FALSE;
//            $bLock = flock( $this->m_pFile, LOCK_EX);

            // Write
            $iReturn = fwrite( $this->m_pFile, $sBuffer);

            // Flush
            if( $iReturn!==FALSE )
            {
                fflush( $this->m_pFile );
            }//if( $iReturn!==FALSE )

            // Unlock
            if( $bLock===TRUE )
            {
                flock( $this->m_pFile, LOCK_UN);
            }//if( $bLock===TRUE )
        }//if(...
        return $iReturn;
    }

}

define('PBR_LOG_LOADED',1);
