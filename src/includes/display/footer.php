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
 * description: Display the page footer.
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - write error(s)
 * update: Olivier JULLIEN - 2010-06-15 - improvement
 * update: Olivier JULLIEN - 2010-09-01 - HTML 4.01 Strict
 * W3C: This document was successfully checked as XHTML 1.0 Strict
 *      and HTML 4.01 Strict
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

    // Version
    $sVersion = PBR_VERSION;
    $sVersion = trim($sVersion);
    if( !empty($sVersion) )
        $sVersion = 'Version '.$sVersion;
    if( isset($pHeader) && $pHeader->AcceptXML() )
        $sVersion.=' xhtml';
    $sVersion.=' ©JOT 2010';

    // Mobile
    if( isset($pHeader) && $pHeader->IsMobile() && CAuth::GetInstance()->IsAuthenticated() )
    {
        if( CAuth::GetInstance()->GetForceDesktop()===FALSE )
        {
            $sF='Version <b>mobile</b> &#124; <a title="Changer la version" href="'.PBR_URL.'fd.php?op1=1">Classique</a>';
        }
        else
        {
            $sF='Version <a title="Changer la version" href="'.PBR_URL.'fd.php?op1=0">Mobile</a> &#124; <b>Classique</b>';
        }
        echo '<p id="VMOBILE">'.$sF.'</p>',"\n";
    }//Mobile

?>
<p id="FOOTER"><?php echo $sVersion; ?></p>
</div><!--PAGE-->
<?php
    if( defined('PBR_DEBUG') && (1==PBR_DEBUG) )
    {
        echo '<div>',"\n";
        // Display errors list
        if( CErrorList::GetInstance()->GetCount()>0 )
        {
            echo '<p>Erreurs:</p><ol>',"\n";
            foreach( CErrorList::GetInstance() as $key=>$value )
            {
                echo '<li>'.htmlspecialchars($value).'</li>';
            }//foreach( CErrorList::GetInstance() as $key=>$value )
            echo '</ol>',"\n";
        }
        else
        {
            echo '<p>Aucune erreur.</p>',"\n";
        }//if( CErrorList::GetInstance()->GetCount()>0)
        // Display Script usage
        echo '<p>'.DisplayUsage( $fGlobalBeginningTime ).'</p>',"\n";
        echo '</div>',"\n";
    }//if( defined('PBR_DEBUG') && (1==PBR_DEBUG) )

?>
</body>
</html>
