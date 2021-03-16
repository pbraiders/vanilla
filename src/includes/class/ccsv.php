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
 * description: describe an csv file
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

define('PBR_EXPORT_DIR','export');

final class CCSV extends CDirectoryFileManagement
{

    /** Contants
     ***********/
    const EXPORTFILELIFETIME=60;
    const EXPORTFILEPREFIX='csv';

    /** Protected methods
     ********************/

    /**
     * function: DirectoryManagement
     * description: create directory if not exists. Delete old file
     * parameter: STRING|$sFolder - directory path and name.
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DirectoryManagement($sFolder)
    {
        // Create folder
        $bReturn = $this->DirectoryCreate($sFolder);

        // Delele old files
        if( $bReturn )
        {
            $sFolder = trim($sFolder).'/{'.CCSV::EXPORTFILEPREFIX.'*}';
            $pFiles = glob( $sFolder, GLOB_BRACE );
            if( $pFiles!==FALSE )
            {
                $iTime = time() - CCSV::EXPORTFILELIFETIME;
                foreach( $pFiles as $sFile )
                {
                    if( (filemtime($sFile)<=$iTime) || (filectime($sFile)<=$iTime) )
                    {
                        unlink($sFile);
                    }//if( (filemtime($sFile)<=$iTime) || (filectime($sFile)<=$iTime) )
                }//foreach( $pFiles as $sFile )
            }//if( $pFiles!==FALSE )
        }//if( $bReturn )

        return $bReturn;
    }

    /**
     * function: FileManagement
     * description: create and open new one.
     * parameter: STRING|$sFolder - directory path and name.
     *            STRING|$sFile   - not used.
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function FileManagement( $sFolder, $sFile)
    {
        // Initialize
        $bReturn = FALSE;
        // Test parameter
        if( IsStringNotEmpty( $sFolder, GetRegExPatternDirectory() )===TRUE )
        {
            // Build name
            $this->m_sFile = tempnam( $sFolder, CCSV::EXPORTFILEPREFIX);
            if( $this->m_sFile!=FALSE )
            {
                $this->m_pFile = fopen( $this->m_sFile, 'wb');
            }//if( $this->m_sFile!=FALSE )
            $bReturn = $this->IsOpen();
        }//if( IsStringNotEmpty( $sFolder, GetRegExPatternDirectory() )===TRUE )
        return $bReturn;
    }

    /** Public methods
     *****************/

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function __construct(){}

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
    public function __clone() {}

   /**
     * function: DeleteFile
     * description: Delete the file
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function DeleteFile()
    {
        $bReturn=TRUE;
        if( $this->m_sFile!==FALSE )
        {
            $bReturn=unlink($this->m_sFile);
        }//if( $this->IsOpen()===TRUE )
        return $bReturn;
    }

    /**
     * function: Write
     * description: Write an array into the file
     * parameter: ARRAY|tRows - lines to add
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function Write( &$tRows )
    {
        $bReturn = FALSE;
        if( (is_array($tRows)===TRUE) && ($this->IsOpen()===TRUE) )
        {
            $bReturn = TRUE;
            foreach( $tRows as $tRow )
            {
                if( fputcsv( $this->m_pFile, $tRow, ',', '"') === FALSE )
                {
                    $bReturn = FALSE;
                    break;
                }//if( fputcsv(...
            }//foreach( $tRows as $tRow )
            if( $bReturn === TRUE )
            {
                fflush($this->m_pFile);
            }//if( $bReturn===TRUE )
        }//if( is_array($tRows) && $this->IsOpen() )
        return $bReturn;
    }

    /**
     * function: WriteLine
     * description: Write a line into the file
     * parameter: ARRAY|tRow - line to add
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function WriteLine( &$tRow )
    {
        $bReturn = FALSE;
        if( (is_array($tRow)===TRUE) && ($this->IsOpen()===TRUE) )
        {
            $bReturn = TRUE;
            if( fputcsv( $this->m_pFile, $tRow, ',', '"') === FALSE )
            {
                $bReturn = FALSE;
            }//if( fputcsv(...
            if( $bReturn === TRUE )
            {
                fflush($this->m_pFile);
            }//if( $bReturn===TRUE )
        }//if( is_array($tRows) && $this->IsOpen() )
        return $bReturn;
    }

}
