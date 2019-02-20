<?php
/***************************************************************************
 * file: stat.php
 * description: contain functions and variables for statistics
 * author: oju - 2010-02-04
 ***************************************************************************/
if ( !defined('PBR_VERSION') || !defined('PBR_PATH') )
    die('-1');

    if( !defined('PBR_STAT') )
    {
        define('PBR_STAT','ON');
    }

    /** Memory usage
     ***************/
    function GetMemoryUsage()
    {
        $iBytes = memory_get_usage();
        $iMByte = round( ($iBytes/1024/1024), 2);
        $sBuffer = $iMByte.' MByte(s)';
        $sBuffer = $sBuffer.' - '.$iBytes.' Byte(s)';
        return $sBuffer;
    }

    /** Initialize global parameters
     *******************************/
    unset( $global_iTime );

    /** Started time
     ***************/
    list($tps_usec, $tps_sec) = explode(" ",microtime());
    $global_iTime = ((float)$tps_usec + (float)$tps_sec);

    /** Define shutdown function
     ***************************/
    function StatMe()
    {
        // Define global variables
        global $global_iTime;

        // Compute time
        if( isset( $global_iTime ) )
        {
            list($tps_usec, $tps_sec) = explode(" ",microtime());
            $tps_end = ((float)$tps_usec + (float)$tps_sec);
            $iSecond = $tps_end - $global_iTime;
            $sTrace = '=== STAT: second=['.$iSecond.'] === '.GetMemoryUsage().' ===' ;
        }
        else
        {
            $sTrace = '=== STAT: second=[none] === '.GetMemoryUsage().' ===' ;
        }
        trigger_error( $sTrace, E_USER_NOTICE );
    }

    /** Register shutdown function
     *****************************/
    register_shutdown_function('StatMe');
?>
