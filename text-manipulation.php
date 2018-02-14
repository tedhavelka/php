<?php



//----------------------------------------------------------------------
// - SECTION - PHP include directives
//----------------------------------------------------------------------

    require_once '/opt/nn/lib/php/defines-nn.php';

    require_once '/opt/nn/lib/php/diagnostics-nn.php';




function &url_safe_filename($caller, $filename, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to sanitize filenames so they are URL-safe, able to be
//   used in web URLs without confusing web browsers with
//   metacharacters.
//
//  NOTE 2018-02-14 - ROUTINE IMPLEMENTATION UNDERWAY, HAVE YET TO
//   +  DETERMINE ALL CHARACTERS AND PATTERNS WHICH MAY APPEAR IN
//   +  FILENAMES AND CAUSE TROUBLE IN WEB URLS . . . - TMH
//
//
//
//----------------------------------------------------------------------


// diagnostics:

    $dflag_verbose = DIAGNOSTICS_ON;

    $rname = "url_safe_filename";



    $url_safe_filename = preg_replace('/#/', '%23', $filename);

    return $url_safe_filename;
}



?>
