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
 * description: describe header properties
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - update __clone()
 * update: Olivier JULLIEN - 2010-06-15 - is not a singleton anymore
 *                                        delete GetInstance()
 *                                        delete DeleteInstance()
 *                                        update GetTitle()
 *                                        update GetDescription()
 *                                        update GetKeyword()
 *                                        add AcceptXML()
 *                                        add AnalyseMIMEType()
 * update: Olivier JULLIEN - 2010-09-01 - add GetCloseTag()
 *                                        add GetBR()
 *                                        add GetHR()
 *                                        add GetAnchor()
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

/** Defines
 **********/
define('PBR_MOBILE_USUAL','/(opera mini|opera mobi|iemobile|fennec|iphone|android|htc|blackberry|midp)/i');
define('PBR_MOBILE_OTHER','/(ipod|smartphone|nintendo dsi|playstation portable|psp|kddi|blazer|symbianos|palm|nokia|sonyericsson|vodafone|mot-|motorola internet browser|docomo)/i');

define('PBR_META_TITLE','PBRaiders');
define('PBR_META_DESC','système de gestion de réservations.');
define('PBR_META_KWRD','paintball,management,rent,gestion,location');
define('PBR_META_RBOT','NOINDEX,NOFOLLOW');

final class CHeader
{

    /** Private attributs
     ********************/

    // Title
    private $m_sTitle = PBR_META_TITLE;

    // Description
    private $m_sDescription = PBR_META_DESC;

    // Keywords
    private $m_sKeywords = PBR_META_KWRD;

    // Robot
    private $m_sRobot = PBR_META_RBOT;

    // Mobile
    private $m_bMobile = FALSE;

    // No Cache
    private $m_bNoCache = FALSE;

    // For print
    private $m_bPrint = FALSE;

    // For MIME type
    private $m_bAcceptXML = FALSE;

    /** Private methods
     ******************/

   /**
     * function: SetMobile
     * description: Analyse user agent
     * parameter: STRING|sUserAgent - user agent
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - improvement
     */
    private function SetMobile($sUserAgent)
    {
        if( IsStringNotEmpty($sUserAgent)===TRUE )
        {
            $iResult=preg_match(PBR_MOBILE_USUAL,$sUserAgent);
            if( ($iResult===FALSE) || ($iResult==0) )
            {
                $iResult=preg_match(PBR_MOBILE_OTHER,$sUserAgent);
            }//if( ($iResult===FALSE) || ($iResult==0) )
            if( $iResult>0 )
            {
                $this->m_bMobile=TRUE;
            }//if( $iResult>0 )
        }//if(...
    }

   /**
     * function: AnalyseMIMEType
     * description: Analyse browser allowed MIME type
     * parameter: STRING|sHttpAccept - mime type list
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function AnalyseMIMEType($sHttpAccept)
    {
        $this->m_bAcceptXML = FALSE;
        if( IsStringNotEmpty($sHttpAccept)===TRUE )
        {
            if( stristr( $sHttpAccept, "application/xhtml+xml" )!==FALSE )
            {
                $this->m_bAcceptXML = TRUE;
            }//if( stristr( $sHttpAccept, "application/xhtml+xml" )!==FALSE )
        }//if( IsStringNotEmpty($sHttpAccept)===TRUE )
    }

    /** Public methods
     *****************/

    /**
     * function: __construct
     * description: constructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __construct()
    {
        $this->SetMobile(GetUserAgent(4));
        $this->AnalyseMIMEType(GetHttpAccept());
    }

    /**
     * function: __destruct
     * description: destructor
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function __destruct(){}

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
     * function: GetTitle
     * description: return the title
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     */
    public function GetTitle() { return htmlentities($this->m_sTitle,ENT_QUOTES,'UTF-8'); }

    /**
     * function: GetDescription
     * description: return the description
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     */
    public function GetDescription() { return htmlentities($this->m_sDescription,ENT_QUOTES,'UTF-8'); }

    /**
     * function: GetKeywords
     * description: return the keywords
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - use ENT_QUOTES instead of ENT_COMPAT
     */
    public function GetKeywords() { return htmlentities($this->m_sKeywords,ENT_QUOTES,'UTF-8'); }

    /**
     * function: GetRobot
     * description: return the robot
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function GetRobot() { return $this->m_sRobot; }


   /**
     * function: SetTitle
     * description: Add title value
     * parameter: STRING|sValue - title to add
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - improvement
     */
    public function SetTitle( $sValue )
    {
        if( IsStringNotEmpty($sValue)===TRUE )
        {
            $this->m_sTitle = $this->m_sTitle.' - '.$sValue;
        }//if( IsStringNotEmpty($sValue)===TRUE )
    }

   /**
     * function: SetDescription
     * description: Add description value
     * parameter: STRING|sValue - description to add
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - improvement
     */
    public function SetDescription( $sValue )
    {
        if( IsStringNotEmpty($sValue)===TRUE )
        {
            $this->m_sDescription = $this->m_sDescription.' '.$sValue;
        }//if( IsStringNotEmpty($sValue)===TRUE )
    }

   /**
     * function: Setkeywords
     * description: Add keywords value
     * parameter: STRING|sValue - keywords to add
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     * update: Olivier JULLIEN - 2010-06-15 - improvement
     */
    public function SetKeywords( $sValue )
    {
        if( IsStringNotEmpty($sValue)===TRUE )
        {
            $this->m_sKeywords = $this->m_sKeywords.','.$sValue;
        }//if( IsStringNotEmpty($sValue)===TRUE )
    }

   /**
     * function: IsMobile
     * description: return true if mobile device detected
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsMobile() {return $this->m_bMobile;}

   /**
     * function: AcceptXML
     * description: return true if the browser accept XML application
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function AcceptXML() {return $this->m_bAcceptXML;}

   /**
     * function: IsNoCache
     * description: return true if no cache required
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function IsNoCache() {return $this->m_bNoCache;}

   /**
     * function: SetNoCache
     * description: set no cache to false
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetNoCache() {$this->m_bNoCache=TRUE;}

   /**
     * function: ToPrint
     * description: set Print to true
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ToPrint() {$this->m_bPrint=TRUE;}

   /**
     * function: ForPrinting
     * description: return true if for printing
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function ForPrinting() {return $this->m_bPrint;}

   /**
     * function: SetToDefault
     * description: Set the parameters to default
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-02-04
     */
    public function SetToDefault()
    {
        $this->m_sTitle       = PBR_META_TITLE;
        $this->m_sDescription = PBR_META_DESC;
        $this->m_sKeywords    = PBR_META_KWRD;
        $this->m_sRobot       = PBR_META_RBOT;
        $this->m_bNoCache     = FALSE;
        $this->m_bPrint       = FALSE;
        $this->m_bAcceptXML   = FALSE;
    }

   /**
     * function: GetCloseTag
     * description: return the close tag depending of the doctype
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-09-01
     */
    public function GetCloseTag()
    {
        if( $this->m_bAcceptXML )
            $sReturn = ' />';
        else
            $sReturn = '>';
        return $sReturn;
    }

   /**
     * function: GetBR
     * description: return the BR tag depending of the doctype
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-09-01
     */
    public function GetBR()
    {
        if( $this->m_bAcceptXML )
            $sReturn = '<br />';
        else
            $sReturn = '<br>';
        return $sReturn;
    }

   /**
     * function: GetHR
     * description: return the HR tag depending of the doctype
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-09-01
     */
    public function GetHR()
    {
        if( $this->m_bAcceptXML )
            $sReturn = '<hr />';
        else
            $sReturn = '<hr>';
        return $sReturn;
    }

   /**
     * function: GetAnchor
     * description: return the anchor tag depending of the doctype
     * parameter: none
     * return: STRING
     * author: Olivier JULLIEN - 2010-09-01
     */
    public function GetAnchor($sAnchor)
    {
        if( $this->m_bAcceptXML )
            $sReturn = '<a id="'.$sAnchor.'"></a>';
        else
            $sReturn = '<a name="'.$sAnchor.'"></a>';
        return $sReturn;
    }

}

?>
