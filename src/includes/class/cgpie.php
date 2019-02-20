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
 * description: Describes pie chart
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CGPie extends CGraph
{

    /** Constants
     ************/
    const LABEL_GAP        = 22;
    const LABEL_SIZE       = 16;
    const LABEL_FONT       = 4;
    const LABEL_MARGE_LEFT = 10;
    const VERTICAL_GAP     = 16;
    const ANGLE_START      = 270;

    /** Private variables
     *******************/
    private $m_tLabels = array();

    /** Protected methods
     ********************/

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
        if(  parent::AllocateImageColor()===TRUE )
        {
            $this->m_tColors['s3']  = imagecolorallocate( $this->m_rImage, 0xFE, 0xDE, 0x58 );
            $this->m_tColors['s3d'] = imagecolorallocate( $this->m_rImage, 0x99, 0x99, 0x00 );
            $this->m_tColors['s1']  = imagecolorallocate( $this->m_rImage, 0x98, 0xD8, 0xA5 );
            $this->m_tColors['s1d'] = imagecolorallocate( $this->m_rImage, 0x34, 0x74, 0x41 );
            $this->m_tColors['s2']  = imagecolorallocate( $this->m_rImage, 0xFF, 0x40, 0x40 );
            $this->m_tColors['s2d'] = imagecolorallocate( $this->m_rImage, 0x99, 0x00, 0x00 );
            $this->m_tColors['s4']  = imagecolorallocate( $this->m_rImage, 0xEE, 0xEE, 0xEE );
            $this->m_tColors['s4d'] = imagecolorallocate( $this->m_rImage, 0x8A, 0x8A, 0x8A );
            $this->m_tColors['s5']  = imagecolorallocate( $this->m_rImage, 0xA2, 0xD2, 0xE0 );
            $this->m_tColors['s5d'] = imagecolorallocate( $this->m_rImage, 0x3E, 0x6E, 0x7C );
            $this->m_tColors['s0']  = imagecolorallocate( $this->m_rImage, 0xF6, 0x92, 0x3D );
            $this->m_tColors['s0d'] = imagecolorallocate( $this->m_rImage, 0x92, 0x2E, 0x00 );
            $this->m_tColors['s6']  = imagecolorallocate( $this->m_rImage, 0xF1, 0xD5, 0xD1 );
            $this->m_tColors['s6d'] = imagecolorallocate( $this->m_rImage, 0x8D, 0x71, 0x6D );
            $bReturn = TRUE;
        }
        return $bReturn;
    }

    /** Private methods
     ******************/

    /**
     * function: DrawLabel
     * description: Draw a label.
     * parameter: INTEGER|iX      - x-coordinate of the upper left corner.
     *            INTEGER|iY      - y-coordinate of the upper left corner.
     *            INTEGER|iColor  - A color identifier.
     *            INTEGER|iValue  - The value to be written.
     *             STRING|sString - The string to be written.
     * return: BOOLEAN - FALSE if an error occures
     * author: Olivier JULLIEN - 2010-06-15
     */
    private function DrawLabel( $iX, $iY, $iColor, $iValue, $sString )
    {
        $bReturn = FALSE;
        if( is_scalar($iValue) && is_scalar($sString) )
        {
            // Draw rectangles
            $bReturn = $this->DrawFilledRectangle( $iX, $iY, $iX+CGPie::LABEL_SIZE, $iY+CGPie::LABEL_SIZE, $iColor );
            $bReturn = $bReturn && $this->DrawRectangle( $iX, $iY, $iX+CGPie::LABEL_SIZE, $iY+CGPie::LABEL_SIZE, $this->m_tColors['black'] );
            // Format value
            $sBuffer = number_format( $iValue, 1, ',', '.' ).'%';
            if( $iValue<0.1 )
            {
                $sBuffer = ' '.$sBuffer;
            }//if( ...
            if( array_key_exists($sString,$this->m_tLabels) )
            {
                $sString = $this->m_tLabels[$sString];
            }//if( ...
            $sBuffer .= ' '.$sString;
            // Write value and label
            $bReturn = $bReturn && $this->DrawString( $iX+CGPie::LABEL_SIZE+CGPie::LABEL_MARGE_LEFT, $iY, $sBuffer, $this->m_tColors['black'], CGPie::LABEL_FONT );
        }//if( is_scalar(...
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
     * function: SetLabel
     * description: Set the human readable label
     * parameter: ARRAY|tLabels - labels
     * return: none
     * author: Olivier JULLIEN - 2010-06-15
     */
    public function SetLabel( &$tLabels )
    {
        if( is_array($tLabels) )
        {
            $this->m_tLabels = $tLabels;
        }
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
        // Create a new true color image
        $bReturn = $this->CreateImageTrueColor();
        // Allocate colors for the image
        $bReturn = $bReturn && $this->AllocateImageColor();
        // Fill in background
        $bReturn = $bReturn && $this->FillInImageBackground();
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
        $iRecordsetCount = $iRecordsetSum = 0;
        if( is_array($tRecordset) )
        {
            $iRecordsetCount = count($tRecordset);
            $iRecordsetSum = array_sum($tRecordset);
            $bReturn = TRUE;
        }//if( is_array($tRecordset) )

        if( ($iRecordsetCount>0) && ($iRecordsetSum>0) )
        {
            /** Draw arc
             ***********/

            // Compute center of the ellipse
            $this->m_tDrawingOrigin['x'] = round($this->m_iWidth/3);
            $this->m_tDrawingOrigin['y'] = round($this->m_iHeight/2);

            // Compute width and height
            $iWidth = $this->m_tDrawingOrigin['x']*2-max(CGraph::MARGIN_LEFT,CGraph::MARGIN_RIGHT,CGraph::MARGIN_BOTTOM,CGraph::MARGIN_TOP);
            $iHeight = round($iWidth/2);

            // Compute slices
            $tSlice = array();
            $iRecordsetCount = count($tRecordset);
            $iRecordsetSum = array_sum($tRecordset);
            $iRecordsetMin = min(array_keys($tRecordset));
            $iRecordsetMax = max(array_keys($tRecordset));
            $iAngleStart = CGPie::ANGLE_START;
            $iValue = 0;
            for( $iIndex=$iRecordsetMin; $iIndex<=$iRecordsetMax; $iIndex++)
            {
                $iValue += $tRecordset[$iIndex];
                $iAngleEnd = ceil(($iValue/$iRecordsetSum)*360) + CGPie::ANGLE_START;
                $tSlice[] = array( $iAngleStart, $iAngleEnd, $this->m_tColors['s'.$iIndex.'d'], $this->m_tColors['s'.$iIndex] );
                $iAngleStart = $iAngleEnd;
            }

            // Draw side
            for( $iVerticalCenter = $this->m_tDrawingOrigin['y'] + CGPie::VERTICAL_GAP; $iVerticalCenter > $this->m_tDrawingOrigin['y']; $iVerticalCenter-- )
            {
                for( $iIndex=0; $iIndex<$iRecordsetCount; $iIndex++ )
                {
                    if( $tSlice[$iIndex][0] != $tSlice[$iIndex][1] )
                    {
                        $bReturn = $bReturn && $this->DrawFilledArc( $this->m_tDrawingOrigin['x'], $iVerticalCenter, $iWidth, $iHeight, $tSlice[$iIndex][0], $tSlice[$iIndex][1], $tSlice[$iIndex][2] );
                    }//if(...
                    if( !$bReturn )
                        break;
                }//if(...
                if( !$bReturn )
                    break;
            }//Draw side

            // Draw slices
            for( $iIndex=0; $iIndex<$iRecordsetCount; $iIndex++ )
            {
                if( $tSlice[$iIndex][0] != $tSlice[$iIndex][1] )
                {
                    $bReturn = $bReturn && $this->DrawFilledArc( $this->m_tDrawingOrigin['x'], $this->m_tDrawingOrigin['y'], $iWidth, $iHeight, $tSlice[$iIndex][0], $tSlice[$iIndex][1], $tSlice[$iIndex][3] );
                }//if(...
                if( !$bReturn )
                    break;
            }//Draw slices

            /** Draw label
             *************/
            $iDrawingX = $this->m_tDrawingOrigin['x']*2;
            $iDrawingY = $this->m_tDrawingOrigin['y'] - ($iRecordsetCount*CGPie::LABEL_GAP/2);
            $iIndex = $iRecordsetMin;
            foreach( $tRecordset as $sKey=>$iValue )
            {
                $iValue = 100*$iValue/$iRecordsetSum;
                $bReturn = $bReturn && $this->DrawLabel( $iDrawingX, $iDrawingY, $this->m_tColors['s'.$iIndex], $iValue, $sKey );
                $iDrawingY += CGPie::LABEL_GAP;
                $iIndex++;
                if( !$bReturn )
                    break;
            }//foreach(....

        }//if( is_array(...
        return $bReturn;
    }

}

?>
