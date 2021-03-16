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
 * description: Describes line chart
 * author: Olivier JULLIEN - 2010-06-15
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

final class CGLine extends CGraph
{

    /** Constants
     ************/
    const RECORD_GAP         = 21;
    const FONT_MARGIN_BOTTOM = 18;
    const FONT_MARGIN_TOP    = 10;

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
            $this->m_tColors['gray_lite'] = imagecolorallocate( $this->m_rImage, 0xEA, 0xEA, 0xEA);
            $bReturn = TRUE;
        }
        return $bReturn;
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
        $bReturn = FALSE;
        $tPrevious = $tCurrent = array( 'x'=>$this->m_tDrawingOrigin['x'], 'y'=>$this->m_tDrawingOrigin['y'] );

        if( is_array($tRecordset) && is_integer($iMin) && ($iMin>=0) )
        {

            // Count
            $iCount = count($tRecordset);

            // Compute horizontal scale
            $this->m_tScales['x'] = ($this->m_iWidth - $this->m_tDrawingOrigin['x'] - CGraph::MARGIN_RIGHT) / ($iCount+1);

            // Draw
            $bFirst = TRUE;
            foreach( $tRecordset as $iKey=>$iValue )
            {

                // Save previous position
                $tPrevious = $tCurrent;

                // Compute current position
                $tCurrent['x'] += $this->m_tScales['x'];
                $tCurrent['y'] = $this->m_tDrawingOrigin['y'] - (($iValue-$iMin) / $this->m_tScales['y']);

                // Draw line
                if( !$bFirst )
                {
                    $bReturn = $this->DrawLine( $tPrevious['x'], $tPrevious['y'], $tCurrent['x'], $tCurrent['y'], $this->m_tColors['black'] );
                }
                else
                {
                    $bReturn = TRUE;
                    $bFirst = FALSE;
                }// Draw line

                // Draw point
                $bReturn = $bReturn && $this->DrawFilledEllipse( $tCurrent['x'], $tCurrent['y'], 6, 6, $this->m_tColors['red'] );

                // Draw vertical line
                if( $iCount>CGraph::RECORD_MAX )
                {
                    $bReturn = $bReturn && $this->DrawLine( $tCurrent['x'], $tCurrent['y']+3, $tCurrent['x'], $this->m_tDrawingOrigin['y']-2, $this->m_tColors['gray_lite'] );
                }

                // Draw value
                $iX = $tCurrent['x'] - $this->ComputeFontGap(strlen($iValue));
                $bReturn = $bReturn && $this->DrawString( $iX, $tCurrent['y'] - CGLine::FONT_MARGIN_BOTTOM, $iValue, $this->m_tColors['blue'] );

                // Draw key
                $bReturn = $bReturn && $this->DrawStringTTF( 8, 315, $tCurrent['x'], $this->m_tAxisOrigin['y']+CGLine::FONT_MARGIN_TOP, $iKey, $this->m_tColors['black'] );

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
        $this->m_tColors['gray_lite'] = 0;
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
            $iPlus = ($iCount-CGraph::RECORD_MAX)*CGLine::RECORD_GAP;
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
            if( count( $tRecordset['values'] )>0 )
            {
                // Draw grid
                if( $tRecordset['info']['min']==$tRecordset['info']['max'] )
                {
                    $iYLine = 2;
                    $tRecordset['info']['min'] = 0;
                }
                elseif( ($tRecordset['info']['max']-$tRecordset['info']['min'])<$iYLine )
                {
                    $iYLine = $tRecordset['info']['max']-$tRecordset['info']['min'];
                }//
                $bReturn = $this->DrawGrid($tRecordset['info']['min'],$tRecordset['info']['max'],$iYLine);
                // Draw points
                $bReturn = $bReturn && $this->DrawPoints( $tRecordset['values'], $tRecordset['info']['min'] );
            }//if( count( $tRecordset['values'] )>0 )
        }
        return $bReturn;
    }

}
