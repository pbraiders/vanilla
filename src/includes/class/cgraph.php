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
 * description: Describes common ressources for charts
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') || !defined('PBR_FONT_PATH') )
    die('-1');

abstract class CGraph
{

    /** Constants
     ************/
    const WIDTH_MIN          = 800;
    const HEIGHT_DEFAULT     = 450; // check CSS file if updated
    const RECORD_MAX         = 36;
    const MARGIN_LEFT        = 50;
    const MARGIN_RIGHT       = 10;
    const MARGIN_BOTTOM      = 50;
    const MARGIN_TOP         = 20;
    const GRID_LINE_DEFAULT  = 10;
    const GRID_Y_GAP         = 10;
    const GRID_MARGIN_RIGHT  = 5;
    const FONT_DEFAULT       = 2;
    const FONT_GAP           = 5;
    const FONT_MARGIN_LEFT   = 10;

    /** Protected attributes
     ***********************/

    // Resource
    protected $m_rImage = null;

    // Image colors
    protected $m_tColors = array();

    // Image width
    protected $m_iWidth = CGraph::WIDTH_MIN;

    // Image height
    protected $m_iHeight = CGraph::HEIGHT_DEFAULT;

    // Axis origin
    protected $m_tAxisOrigin = array();

    // Drawing origin
    protected $m_tDrawingOrigin = array();

    // Scales
    protected $m_tScales = array();

    /** Private methods
     ******************/

    /**
     * function: SupportFreeType
     * description: return true if the installed version og GD support freetype.
     * parameter: none
     * return: BOOLEAN
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function SupportFreeType()
    {
        $bReturn = FALSE;
        $tBuffer = gd_info();
        foreach( $tBuffer as $key=>$value )
        {
            if( (strcasecmp( $key, 'FreeType Support')==0) && ($value===TRUE) )
            {
                $tBuffer2 = get_extension_funcs('gd');
                if( in_array( 'imagefttext', $tBuffer2 ) )
                {
                   $bReturn = TRUE;
                }//if( in_array(...
                break;
            }//if( (strcasecmp(...
        }//foreach(...
        return $bReturn;
    }

    /** Protected methods
     ********************/

    /**
     * function: ComputeFontGap
     * description: Comput font gap for centering string.
     * parameter: INTEGER|iLength - string length.
     * return: INTEGER - gap
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function ComputeFontGap( $iLength )
    {
        $iReturn = 0;
        if( is_integer($iLength) && ($iLength>1) )
        {
            $iReturn = ( CGraph::FONT_GAP * ( round($iLength/2) ) );
        }
        return $iReturn;
    }

    /**
     * function: CreateImageTrueColor
     * description: Create a new true color image.
     * parameter: none
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function CreateImageTrueColor()
    {
        $this->m_rImage = imagecreatetruecolor( $this->m_iWidth, $this->m_iHeight );
        return is_resource($this->m_rImage);
    }

    /**
     * function: AllocateImageColor
     * description: Allocate the colors for the image.
     * parameter: none
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function AllocateImageColor()
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) )
        {
            $this->m_tColors['background'] = imagecolorallocate( $this->m_rImage, 0xF7, 0xF6, 0xFC);
            $this->m_tColors['black'] = imagecolorallocate( $this->m_rImage, 0x00, 0x00, 0x00);
            $this->m_tColors['gray'] = imagecolorallocate( $this->m_rImage, 0xDC, 0xDC, 0xDC);
            $this->m_tColors['red'] = imagecolorallocate( $this->m_rImage, 0xFF, 0x00, 0x00);
            $this->m_tColors['blue'] = imagecolorallocate( $this->m_rImage, 0x00, 0x00, 0xFF);
            $bReturn = TRUE;
        }
        return $bReturn;
    }

    /**
     * function: FillInImageBackground
     * description: Fill in the background of the image.
     * parameter: INTEGER|$iValue - vertical scale (number of lines)
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function FillInImageBackground()
    {
        return $this->DrawFilledRectangle( 0, 0, $this->m_iWidth, $this->m_iHeight, $this->m_tColors['background'] );
    }

    /**
     * function: DrawFilledRectangle
     * description: Draw a filled rectangle.
     * parameter: INTEGER|iX1    - x-coordinate for first point.
     *            INTEGER|iY1    - y-coordinate for first point.
     *            INTEGER|iX2    - x-coordinate for second point.
     *            INTEGER|iY2    - y-coordinate for second point.
     *            INTEGER|iColor - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawFilledRectangle( $iX1, $iY1, $iX2, $iY2, $iColor )
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) && is_integer($iColor) && is_numeric($iX1) && is_numeric($iY1) && is_numeric($iX2) && is_numeric($iY2) )
        {
            $bReturn = imagefilledrectangle( $this->m_rImage, $iX1, $iY1, $iX2, $iY2, $iColor );
        }
        return $bReturn;
    }

    /**
     * function: DrawRectangle
     * description: Draw a rectangle.
     * parameter: INTEGER|iX1    - x-coordinate for first point.
     *            INTEGER|iY1    - y-coordinate for first point.
     *            INTEGER|iX2    - x-coordinate for second point.
     *            INTEGER|iY2    - y-coordinate for second point.
     *            INTEGER|iColor - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawRectangle( $iX1, $iY1, $iX2, $iY2, $iColor )
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) && is_integer($iColor) && is_numeric($iX1) && is_numeric($iY1) && is_numeric($iX2) && is_numeric($iY2) )
        {
            $bReturn = imagerectangle( $this->m_rImage, $iX1, $iY1, $iX2, $iY2, $iColor );
        }
        return $bReturn;
    }

    /**
     * function: DrawLine
     * description: Draws a line between the two given points.
     * parameter: INTEGER|iX1    - x-coordinate for first point.
     *            INTEGER|iY1    - y-coordinate for first point.
     *            INTEGER|iX2    - x-coordinate for second point.
     *            INTEGER|iY2    - y-coordinate for second point.
     *            INTEGER|iColor - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawLine( $iX1, $iY1, $iX2, $iY2, $iColor )
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) && is_integer($iColor) && is_numeric($iX1) && is_numeric($iY1) && is_numeric($iX2) && is_numeric($iY2) )
        {
            $bReturn = imageline( $this->m_rImage, $iX1, $iY1, $iX2, $iY2, $iColor );
        }
        return $bReturn;
    }

    /**
     * function: DrawFilledEllipse
     * description: Draws a filled ellipse centered at the specified coordinate.
     * parameter: INTEGER|iX      - x-coordinate of the center.
     *            INTEGER|iY      - y-coordinate of the center.
     *            INTEGER|iWidth  - The ellipse width.
     *            INTEGER|iHeight - The ellipse height.
     *            INTEGER|iColor  - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawFilledEllipse( $iX, $iY, $iWidth, $iHeight, $iColor )
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) && is_integer($iColor) && is_numeric($iX) && is_numeric($iY) && is_numeric($iWidth) && is_numeric($iHeight) )
        {
            $bReturn = imagefilledellipse( $this->m_rImage, $iX, $iY, $iWidth, $iHeight, $iColor );
        }
        return $bReturn;
    }

    /**
     * function: DrawFilledArc
     * description: Draws a partial arc centered at the specified coordinate and filled it.
     * parameter: INTEGER|iX      - x-coordinate of the center.
     *            INTEGER|iY      - y-coordinate of the center.
     *            INTEGER|iWidth  - The arc width.
     *            INTEGER|iHeight - The arc height.
     *            INTEGER|iStart  - The arc start angle, in degrees.
     *            INTEGER|iEnd    - The arc end angle, in degrees.
     *            INTEGER|iColor  - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawFilledArc( $iX, $iY, $iWidth, $iHeight, $iStart, $iEnd, $iColor )
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) && is_integer($iColor) && is_numeric($iX) && is_numeric($iY) && is_numeric($iWidth) && is_numeric($iHeight) && is_numeric($iStart) && is_numeric($iEnd) )
        {
            $bReturn = imagefilledarc( $this->m_rImage, $iX, $iY, $iWidth, $iHeight, $iStart, $iEnd, $iColor, IMG_ARC_PIE );
        }
        return $bReturn;
    }

    /**
     * function: DrawString
     * description: Draw a string horizontally
     * parameter: INTEGER|iX      - x-coordinate of the upper left corner.
     *            INTEGER|iY      - y-coordinate of the upper left corner.
     *             STRING|sString - The string to be written.
     *            INTEGER|iColor  - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawString( $iX, $iY, $sString, $iColor, $iFont=CGraph::FONT_DEFAULT )
    {
        $bReturn = FALSE;
        if( is_resource($this->m_rImage) && is_integer($iColor) && is_scalar($sString) && is_numeric($iX) && is_numeric($iY) )
        {
            $bReturn = imagestring( $this->m_rImage, $iFont, $iX, $iY, $sString, $iColor);
        }
        return $bReturn;
    }

    /**
     * function: DrawStringTTF
     * description: Write text using TrueType fonts.
     * parameter: FLOAT|fSize   - The font size.
     *            FLOAT|fAngle  - The angle in degrees.
     *          INTEGER|iX      - x-coordinate of the lower left corner.
     *          INTEGER|iY      - y-coordinate of the lower left corner.
     *           STRING|sString - The string to be written.
     *          INTEGER|iColor  - A color identifier.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawStringTTF( $fSize, $fAngle, $iX , $iY , $sString , $iColor )
    {
        $bReturn = FALSE;
        $sFont = PBR_FONT_PATH.'/arial.ttf';
        if( $this->SupportFreeType() && is_readable($sFont) )
        {
            $sFont = PBR_FONT_PATH.'/arial.ttf';
            if( is_resource($this->m_rImage) && is_integer($iColor) && is_scalar($sString) && is_numeric($iX) && is_numeric($iY) && is_numeric($fSize) && is_numeric($fAngle) )
            {
                $tBuffer = imagettftext( $this->m_rImage, $fSize, $fAngle, $iX , $iY, $iColor, $sFont, $sString );
                if( $tBuffer!==FALSE )
                    $bReturn = TRUE;
            }
        }
        else
        {
            $iX = $iX - $this->ComputeFontGap( strlen($sString) );
            $bReturn = $this->DrawString( $iX , $iY , $sString , $iColor );
        }
        return $bReturn;
    }

    /**
     * function: DrawAxis
     * description: Draw vertical and horizontal axis
     * parameter: none
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawAxis()
    {
        $bReturn = FALSE;

        // Compute axis origin
        $this->m_tAxisOrigin['x'] = CGraph::MARGIN_LEFT;
        $this->m_tAxisOrigin['y'] = $this->m_iHeight - CGraph::MARGIN_BOTTOM;

        // Draw axis
        $bReturn = $this->DrawLine( $this->m_tAxisOrigin['x'], $this->m_tAxisOrigin['y'], $this->m_iWidth-CGraph::MARGIN_RIGHT, $this->m_tAxisOrigin['y'], $this->m_tColors['black'] );
        $bReturn = $bReturn && $this->DrawLine( $this->m_tAxisOrigin['x'], $this->m_tAxisOrigin['y'], $this->m_tAxisOrigin['x'], CGraph::MARGIN_TOP, $this->m_tColors['black'] );

        return $bReturn;
    }

    /**
     * function: DrawGrid
     * description: Compute vertical and horizontal scales.
     *              Draw the grid.
     * parameter: INTEGER|iYValueMin - vertical min value
     *            INTEGER|iYValueMax - vertical max value
     *            INTEGER|iYLine     - vertical number of lines
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    protected function DrawGrid( $iYValueMin, $iYValueMax, $iYLine )
    {
        // Initialize
        $bReturn = FALSE;
        $this->m_tDrawingOrigin['x'] = $this->m_tAxisOrigin['x'];
        $this->m_tDrawingOrigin['y'] = $this->m_tAxisOrigin['y'];

        if( is_integer($iYValueMin) && ($iYValueMin>=0) && is_integer($iYValueMax) && ($iYValueMax>0) && is_integer($iYLine) && ($iYLine>0) )
        {

            // Compute vertical scale values
            $iCurrentScaleValue = $iYValueMin;
            $iScaleValue = ( $iYValueMax - $iYValueMin ) / $iYLine;

            // Compute drawing origin
            $this->m_tDrawingOrigin['x'] = $this->m_tAxisOrigin['x'];
            if( $iYValueMin!=0 )
            {
                $this->m_tDrawingOrigin['y'] = $this->m_tAxisOrigin['y'] - CGraph::GRID_Y_GAP;
            }
            else
            {
                $this->m_tDrawingOrigin['y'] = $this->m_tAxisOrigin['y'];
            }//Compute drawing origin

            // Compute the vertical scale
            $this->m_tScales['y'] = ($iYValueMax-$iYValueMin)/($this->m_tDrawingOrigin['y']-CGraph::MARGIN_TOP);

            // Compute gap between lines
            $iStep = ( $this->m_tDrawingOrigin['y'] - CGraph::MARGIN_TOP ) / $iYLine;

            // Compute line lenght
            $iLength = $this->m_iWidth - CGraph::MARGIN_RIGHT - CGraph::GRID_MARGIN_RIGHT;

            // Compute current vertical position
            $iCurrentY = $this->m_tDrawingOrigin['y'];
            if( $iYValueMin==0 )
            {
                // Do not draw the first vertical line
                $iCurrentY -= $iStep;
                $iCurrentScaleValue += $iScaleValue;
            }//Compute current vertical position

            // Draw
            while( $iCurrentY >= CGraph::MARGIN_TOP )
            {
                // Draw the scale
                $bReturn = $this->DrawString( CGraph::FONT_MARGIN_LEFT, $iCurrentY-6, round($iCurrentScaleValue), $this->m_tColors['black'] );
                $iCurrentScaleValue += $iScaleValue;
                // Draw the line
                $bReturn = $bReturn && $this->DrawLine( $this->m_tAxisOrigin['x'], $iCurrentY, $iLength, $iCurrentY, $this->m_tColors['gray'] );
                $iCurrentY -= $iStep;
                // Error
                if( !$bReturn )
                    break;
            }//Draw

        }
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
    public function __construct()
    {
        $this->m_tColors['background'] = 0;
        $this->m_tColors['black'] = 0;
        $this->m_tColors['gray'] = 0;
        $this->m_tColors['red'] = 0;
        $this->m_tColors['blue'] = 0;
    }

    /**
     * function: __destruct
     * description: destructor, initializes private attributs
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function __destruct()
    {
        $this->Destroy();
    }

    /**
     * function: Destroy
     * description: Destroy an image
     * parameter: none
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function Destroy()
    {
        if( is_resource( $this->m_rImage ) )
        {
            imagedestroy( $this->m_rImage );
            $this->m_rImage = null;
        }//if( is_resource(...
    }

    /**
     * function: SetWidth
     * description: Accessor: set image width
     * parameter: Integer
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function SetWidth( $iValue )
    {
        $bReturn = FALSE;
        if( is_integer($iValue) && ($iValue>(CGraph::MARGIN_LEFT+CGraph::MARGIN_RIGHT)) )
        {
            $this->m_iWidth = $iValue;
            $bReturn = TRUE;
        }//if( is_integer(...
        return $bReturn;
    }

    /**
     * function: SetHeight
     * description: Accessor: set image height
     * parameter: Integer
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function SetHeight( $iValue )
    {
        $bReturn = FALSE;
        if( is_integer($iValue) && ($iValue>(CGraph::MARGIN_BOTTOM+CGraph::MARGIN_TOP)) )
        {
            $this->m_iHeight = $iValue;
            $bReturn = TRUE;
        }//if( is_integer(...
        return $bReturn;
    }

    /**
     * function: Output
     * description: Output a PNG image to either the browser or a file
     * parameter: STRING|$sFile - file name and path
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    final public function Output( $sFile='' )
    {
        $bReturn = FALSE;
        if( !empty($sFile) && is_dir(dirname($sFile)) )
        {
            $bReturn = imagepng( $this->m_rImage, $sFile );
        }
        else
        {
            header('Content-type: image/png');
            $bReturn = imagepng( $this->m_rImage );
        }//if( !empty($sFile) && is_dir(dirname($sFile)) )
        return $bReturn;
    }

    /** Abstract methods
     *******************/

    /**
     * function: Initialize
     * description: Allocate the image and the colors. Draw the background and the axis.
     * parameter: INTEGER|iCount - records count
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    abstract public function Initialize($iCount);

    /**
     * function: Draw
     * description: Draw the grid and the points.
     * parameter: ARRAY|tRecordset - array of values (min and max should exists)
     *          INTEGER|$iYLine    - vertical scale (number of lines)
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    abstract public function Draw( &$tRecordset, $iYLine=CGraph::GRID_LINE_DEFAULT );

}
