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
 * description: directory and file management
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

abstract class CDirectoryFileManagement
{

    /** Protected attributs
     **********************/

    // File name and path
    protected $m_sFile = FALSE;

    // File descriptor
    protected $m_pFile = FALSE;

    /** Protected methods
     ********************/

    /**
     * function: DirectoryCreate
     * description: Creates the folder if not exists.
     * parameter: STRING|$sFolder - log folder path.
     * return: BOOLEAN - FALSE if cannot create the folder.
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DirectoryCreate( $sFolder )
    {
        $bReturn = FALSE;
        if( IsStringNotEmpty( $sFolder, GetRegExPatternDirectory())===TRUE )
        {
            $bReturn = is_dir($sFolder);
            if( !$bReturn )
            {
                $bReturn = mkdir( $sFolder, 0770);
            }//if( !$bReturn )
        }//if( IsStringNotEmpty( $sFolder, GetRegExPatternDirectory())===TRUE )
        return $bReturn;
    }

    /** Public methods
     *****************/

   /**
     * function: IsOpen
     * description: test if the file is open
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function IsOpen()
    {
        $bReturn=FALSE;
        if( ($this->m_pFile!=FALSE) && is_resource($this->m_pFile) )
        {
            $bReturn=TRUE;
        }//if( ($this->m_pFile!=FALSE) && is_resource($this->m_pFile) )
        return $bReturn;
    }

   /**
     * function: Close
     * description: Close the file
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    final public function Close()
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
     * function: Open
     * description: manages directory and files, createss and open file
     * parameter: STRING|$sFolder - Folder name and path
     *            STRING|$sFile   - File name
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function Open( $sFolder, $sFile)
    {
        $bReturn = FALSE;
        if( $this->DirectoryManagement($sFolder) )
        {
            $bReturn = $this->FileManagement( $sFolder, $sFile);
        }//if( $this->DirectoryManagement($sFolder) )
        return $bReturn;
    }

    /**
     * function: GetFilename
     * description: return the file name
     * parameter: none
     * return: STRING - STRING or FALSE if file is not open
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function GetFilename()
    {
        return $this->m_sFile;
    }


    /** Abstract methods
     *******************/

    /**
     * function: DirectoryManagement
     * description: manages directory
     * parameter: STRING|$sFolder - directory path and name.
     * return: BOOLEAN - TRUE or FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    abstract protected function DirectoryManagement($sFolder);

    /**
     * function: FileManagement
     * description: manages file(s)
     * parameter: STRING|$sFolder - directory path and name.
     *            STRING|$sFile   - file name.
     * return: BOOLEAN - TRUE or FALSE if an error occurer
     * author: Olivier JULLIEN - 2010-06-15
     */
    abstract protected function FileManagement( $sFolder, $sFile);

}
