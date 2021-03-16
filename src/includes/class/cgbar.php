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
 * description: Describes bar chart
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CGBar extends CGraph
{

    /** Constants
     ************/
    const RECORD_MAX         = 24;
    const RECORD_GAP         = 60;
    const FONT_MARGIN_BOTTOM = 18;
    const FONT_MARGIN_TOP    = 10;
    const BAR_MARGIN_LEFT    = 10;
    const BAR_WIDTH          = 15;

    /** Private methods
     ******************/

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
        if( parent::AllocateImageColor()===TRUE )
        {
            $this->m_tColors['real'] = imagecolorallocate( $this->m_rImage, 0x98, 0xD8, 0xA5);
            $this->m_tColors['planned'] = imagecolorallocate( $this->m_rImage, 0xFE, 0xDE, 0x58);
            $this->m_tColors['canceled'] = imagecolorallocate( $this->m_rImage, 0xFF, 0x40, 0x40);
            $bReturn = TRUE;
        }
        return $bReturn;
    }

    /**
     * function: DrawValue
     * description: Draw the value
     * parameter: INTEGER|iX        - x-coordinate for the first point
     *            INTEGER|iY        - y-coordinate for the first point
     *            INTEGER|iBarWidth - bar width
     *            INTEGER|iValue    - value to draw
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function DrawValue( $iX, $iY, $iBarWidth, $iValue )
    {
        $iTX = $iX + round($iBarWidth/2) - $this->ComputeFontGap(strlen($iValue));
        return $this->DrawString( $iTX, $iY - CGBar::FONT_MARGIN_BOTTOM, $iValue, $this->m_tColors['blue'] );
    }

    /**
     * function: DrawBar
     * description: Draw the bar.
     * parameter: INTEGER|iX        - x-coordinate for the first point.
     *            INTEGER|iY        - y-coordinate for the first point.
     *            INTEGER|iColor    - A color identifier.
     *            INTEGER|iBarWidth - bar width
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function DrawBar( $iX, $iY, $iColor, $iBarWidth )
    {
        return $this->DrawFilledRectangle( $iX, $iY, $iX+$iBarWidth, $this->m_tDrawingOrigin['y'], $iColor );
    }

    /**
     * function: DrawPoints
     * description: Draw the points.
     * parameter: ARRAY|tRecordset - array of values (min and max should exists)
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function DrawPoints( &$tRecordset, $iMin )
    {
        // Initialize
        $bReturn = FALSE;
        $tPrevious = $tCurrent = array( 'x'=>$this->m_tDrawingOrigin['x'], 'y'=>$this->m_tDrawingOrigin['y'] );

        if( is_array($tRecordset) && (count($tRecordset)>0) && is_integer($iMin) && ($iMin>=0) )
        {

            // Count
            $iCount = count($tRecordset);

            // Compute horizontal scale
            $this->m_tScales['x'] = ($this->m_iWidth - $this->m_tDrawingOrigin['x'] - CGraph::MARGIN_RIGHT) / ($iCount);

            // Compute bar width
            $iBarWidth = round($this->m_tScales['x']/3.5);

            // Draw
            $bFirst = TRUE;
            foreach( $tRecordset as $iKey=>$tValues )
            {

                // Compute current position
                if( $bFirst )
                {
                    $tCurrent['x'] += CGBar::BAR_MARGIN_LEFT;
                    $bFirst = FALSE;
                }
                else
                {
                    $tCurrent['x'] += $this->m_tScales['x'];
                }

                // Draw real bar
                $iValue = $tValues['real'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - ( ($iValue-$iMin) / $this->m_tScales['y'] );
                $bReturn = $this->DrawBar( $tCurrent['x'], $tCurrent['y'], $this->m_tColors['real'], $iBarWidth);

                // Draw planned bar
                $iValue = $tValues['planned'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - ( ($iValue-$iMin) / $this->m_tScales['y'] );
                $bReturn = $bReturn && $this->DrawBar( $tCurrent['x']+$iBarWidth+1, $tCurrent['y'], $this->m_tColors['planned'], $iBarWidth);

                // Draw canceled bar
                $iValue = $tValues['canceled'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - ( ($iValue-$iMin) / $this->m_tScales['y'] );
                $bReturn = $bReturn && $this->DrawBar( $tCurrent['x']+(2*$iBarWidth)+2, $tCurrent['y'], $this->m_tColors['canceled'], $iBarWidth);

                // Draw real value
                $iValue = $tValues['real'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - ( ($iValue-$iMin) / $this->m_tScales['y'] );
                $bReturn = $bReturn && $this->DrawValue( $tCurrent['x'], $tCurrent['y'], $iBarWidth, $iValue);

                // Draw planned value
                $iValue = $tValues['planned'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - ( ($iValue-$iMin) / $this->m_tScales['y'] );
                $bReturn = $bReturn && $this->DrawValue( $tCurrent['x']+$iBarWidth+1, $tCurrent['y'], $iBarWidth, $iValue);

                // Draw canceled value
                $iValue = $tValues['canceled'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - ( ($iValue-$iMin) / $this->m_tScales['y'] );
                $bReturn = $bReturn && $this->DrawValue($tCurrent['x']+(2*$iBarWidth)+2, $tCurrent['y'], $iBarWidth, $iValue);

                // Draw key
                $bReturn = $bReturn && $this->DrawStringTTF( 8, 315, $tCurrent['x'], $this->m_tAxisOrigin['y']+CGBar::FONT_MARGIN_TOP, $iKey, $this->m_tColors['black'] );

                if( !$bReturn )
                    break;

            }//foreach(...
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
        $this->m_tDrawingOrigin['x'] = $this->m_tAxisOrigin['x'] = 0;
        $this->m_tDrawingOrigin['y'] = $this->m_tAxisOrigin['y'] = CGraph::HEIGHT_DEFAULT;
        $this->m_tScales['x'] = 0;
        $this->m_tScales['y'] = ($this->m_tDrawingOrigin['y'] - CGraph::MARGIN_TOP) / CGraph::GRID_LINE_DEFAULT;
        $this->m_tColors['real'] = 0;
        $this->m_tColors['planned'] = 0;
        $this->m_tColors['canceled'] = 0;
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
     * function: Initialize
     * description: Allocate the image and the colors. Draw the background and the axis.
     * parameter: INTEGER|iCount - records count
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function Initialize( $iCount )
    {
        $bReturn = FALSE;
        // Compute width
        if( $iCount>CGraph::RECORD_MAX )
        {
            $iPlus = ($iCount-CGBar::RECORD_MAX)*CGBar::RECORD_GAP;
            $this->SetWidth( CGraph::WIDTH_MIN+$iPlus );
        }
        // Create a new true color image
        $bReturn = $this->CreateImageTrueColor();
        // Allocate colors for the image
        $bReturn = $bReturn && $this->AllocateImageColor();
        // Fill in background
        $bReturn = $bReturn && $this->FillInImageBackground();
        // Draw axis
        $bReturn = $bReturn && $this->DrawAxis();
        return $bReturn;
    }

    /**
     * function: Draw
     * description: Draw the grid and the points.
     * parameter: ARRAY|tRecordset - array of values (min and max should exists)
     *          INTEGER|$iYLine    - vertical scale (number of lines)
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function Draw( &$tRecordset, $iYLine=CGraph::GRID_LINE_DEFAULT )
    {
        $bReturn = FALSE;
        if( is_array($tRecordset)
         && array_key_exists('info',$tRecordset) && is_array($tRecordset['info'])
         && array_key_exists('min',$tRecordset['info']) && array_key_exists('max',$tRecordset['info'])
         && array_key_exists('values',$tRecordset) )
        {
            // Draw grid
            if( !empty($tRecordset['values']) )
            {
                if( $tRecordset['info']['min']==$tRecordset['info']['max'] )
                {
                    $iYLine = 2;
                    $tRecordset['info']['min'] = 0;
                }
                elseif( ($tRecordset['info']['max']-$tRecordset['info']['min'])<$iYLine )
                {
                    $iYLine = $tRecordset['info']['max']-$tRecordset['info']['min'];
                }
                $bReturn = $this->DrawGrid($tRecordset['info']['min'],$tRecordset['info']['max'],$iYLine);

                // Draw points
                $bReturn = $bReturn && $this->DrawPoints( $tRecordset['values'], $tRecordset['info']['min'] );
            }//not empty
        }
        return $bReturn;

    }

}
