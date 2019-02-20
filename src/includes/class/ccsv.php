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
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - Update __clone()
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_PATH') || !defined('PBR_EXPORT_DIR') )
    die('-1');

class CCSV
{

    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // file name and path
    private $m_sExportFile=FALSE;

    // file ressource
    private $m_pExportFile=FALSE;

    /** Private methods
     ******************/

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function __construct(){}

    /** Public methods
     *****************/

    /**
     * function: __destruct
     * description: destructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __destruct()
    {
        $this->Close();
        $this->Delete();
    }

   /**
     * function: __clone
     * description: cloning is forbidden
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-05-24 - Remove trigger_error
     */
    public function __clone() {}

   /**
     * function: GetInstance
     * description: create or return the current instance
     * parameter: none
     * return: this
     * author: Olivier JULLIEN - 2010-02-04
     */
    public static function GetInstance()
    {
        if( is_null(self::$m_pInstance) )
        {
            self::$m_pInstance = new CCSV();
        }
        return self::$m_pInstance;
    }

   /**
     * function: DeleteInstance
     * description: delete the current instance
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
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
     * function: Open
     * description: create and open the file
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Open()
    {
        $sPath=PBR_PATH.'/'.PBR_EXPORT_DIR;
        $this->m_sExportFile=tempnam($sPath,'csv');
        if( $this->m_sExportFile!=FALSE )
        {
            $this->m_pExportFile=fopen($this->m_sExportFile,'wb');
        }//if( $this->m_sExportFile!=FALSE )
    }

   /**
     * function: IsOpen
     * description: test if the file is open
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsOpen()
    {
        $bReturn=TRUE;
        if( $this->m_pExportFile===FALSE )
        {
            $bReturn=FALSE;
        }//if( $this->m_pExportFile===FALSE )
        return $bReturn;
    }

   /**
     * function: Close
     * description: Close the file
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Close()
    {
        $bReturn=TRUE;
        if( $this->IsOpen()===TRUE )
        {
            $bReturn=fclose($this->m_pExportFile);
            $this->m_pExportFile=FALSE;
        }//if( $this->IsOpen()===TRUE )
        return $bReturn;
    }

   /**
     * function: Delete
     * description: Delete the file
     * parameter: none
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Delete()
    {
        $bReturn=TRUE;
        if( $this->m_sExportFile!==FALSE )
        {
            $bReturn=unlink($this->m_sExportFile);
        }//if( $this->IsOpen()===TRUE )
        return $bReturn;
    }

    /**
     * function: Write
     * description: Write into the file
     * parameter: ARRAY|tRows - line to add
     * return: BOOLEAN - TRUE or FALSE
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Write( &$tRows )
    {
        $bReturn=FALSE;
        if( (is_array($tRows)===TRUE) && ($this->IsOpen()===TRUE) )
        {
            $bReturn=TRUE;
            foreach( $tRows as $tRow )
            {
                if( fputcsv($this->m_pExportFile, $tRow, ',', '"') === FALSE )
                {
                    $bReturn=FALSE;
                    break;
                }//if( fputcsv(...
            }//foreach( $tRows as $tRow )
        }//if( is_array($tRows) && $this->IsOpen() )
        return $bReturn;
    }

    /**
     * function: GetFilename
     * description: return the file name
     * parameter: none
     * return: STRING -
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetFilename()
    {
        return $this->m_sExportFile;
    }

}
?>
