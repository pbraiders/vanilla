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
 *************************************************************************/
if ( !defined('PBR_VERSION') )
    die('-1');
?>
   <p id="FOOTER">Release CAMP ©JOT 2010</p>
  </div><!--PAGE-->
<?php
    if( defined('PBR_DEBUG') && (1==PBR_DEBUG) )
    {
        echo '<div>',"\n";
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
        echo '</div>',"\n";
    }//if( defined('PBR_DEBUG') && (1==PBR_DEBUG) )
?>
 </body>
</html>
