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
 * description: describes error list object
 * author: Olivier JULLIEN - 2010-02-04
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

class CErrorList implements Iterator
{
    /** Private attributs
     ********************/

    // Singleton
    private static $m_pInstance = NULL;

    // List of errors
    private $m_ErrorList = array();

    // Current index
    private $m_iIndex = 0;

    /** Private methods
     ******************/

    /**
     * function: __construct
     * description: constructor, initialize members variables
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    private function __construct()
    {
        $this->Clean();
    }

    /** Public methods
     *****************/

    /**
     * function: __destruct
     * description: destructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __destruct()
    {
        $this->Clean();
    }

   /**
     * function: __clone
     * description: cloning is forbidden
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __clone()
    {
        trigger_error( 'Attempting to clone CErrorList', E_USER_NOTICE );
    }

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
            self::$m_pInstance = new CErrorList();
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
     * function: current
     * description: return the current element
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function current()
    {
        return $this->m_ErrorList[$this->m_iIndex];
    }

    /**
     * function: next
     * description: move forward to next element
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function next()
    {
        $this->m_iIndex++;
    }

    /**
     * function: valid
     * description: Check if there is a current element after calls to rewind() or next().
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function valid()
    {
        return isset($this->m_ErrorList[$this->m_iIndex]);

    }

    /**
     * function: key
     * description: return the key of the current element.
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function key()
    {
        return $this->m_iIndex;
    }

    /**
     * function: rewind
     * description: rewind the Iterator to the first element.
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function rewind()
    {
        $this->m_iIndex = 0;
    }

    /**
     * function: GetCount
     * description: return the count of element of the list
     * parameter: none
     * return: INTEGER
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetCount()
    {
        return count($this->m_ErrorList);
    }

   /**
     * function: Add
     * description: Add the string in the error list
     * parameter: STRING|sMessage - message
     *            STRING|sFile    - file name
     *           INTEGER|iLine    - line
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Add( $sMessage, $sFile, $iLine)
    {
        if( is_scalar($sMessage) && (strlen($sMessage)>0) )
        {
            $sMessage = 'PBRAIDERS - Error in '.basename($sFile).'(line:'.$iLine.'):'.$sMessage.'.';
            if( !defined('PBR_DEBUG') )
            {
               trigger_error($sMessage,E_USER_WARNING);
            }
            else
            {
                $this->m_ErrorList[] = $sMessage;
            }//if( !defined('PBR_DEBUG') )
        }//if( is_scalar($sMessage) && (strlen($sMessage)>0) )
    }

   /**
     * function: AddDB
     * description: Analyse an database error
     * parameter: INTEGER|iCode    - error code
     *             STRING|sFile    - file name
     *            INTEGER|iLine    - line
     *             STRING|sMessage - message
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function AddDB( $iCode, $sFile, $iLine, $sMessage)
    {
        $sFile=basename($sFile);
        if( ($iCode===FALSE) || ($iCode<0) )
        {
            // Analyse
            if( ($iCode===FALSE) )
            {
                $sBuffer = 'PBRAIDERS - PDOException in '.$sFile.'(line:'.$iLine.'):'.$sMessage.'.';
            }
            elseif( $iCode==-1 )
            {
                $sBuffer = 'PBRAIDERS - Error in '.$sFile.'(line:'.$iLine.'):Bad parameter(s).';
            }
            elseif( ($iCode==-2) || ($iCode==-3) )
            {
                $sBuffer = 'PBRAIDERS - Error in '.$sFile.' (line:'.$iLine.'):Authentication failed.';
            }
            elseif( $iCode==-4 )
            {
                $sBuffer = 'PBRAIDERS - Error in '.$sFile.' (line:'.$iLine.'):Unexpected duplicate data error.';
            }
            else
            {
                $sBuffer = 'PBRAIDERS - Error in '.$sFile.' (line:'.$iLine.'):Unexpected database error.';
            }//if( ...
            // Display
            if( !defined('PBR_DEBUG') )
            {
                // Keept attention to ignore_repeated_errors (php.ini conf value)
                // if ignore_repeated_errors=on => Do not log repeated errors which
                // occures in same file on same line
                trigger_error($sBuffer,E_USER_WARNING);
            }
            else
            {
                $this->m_ErrorList[] = $sBuffer;
            }//if(...
        }//if( ($iCode===FALSE) || ($iCode===FALSE)<0 )
    }

   /**
     * function: Clean
     * description: clean the list
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function Clean()
    {
        $this->m_ErrorList = array();
        $this->m_iIndex = 0;
    }
}
?>
