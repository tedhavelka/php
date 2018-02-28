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




function &thumbnail_safe_filename($caller, $filename, $options)
{

    $dflag_dev = DIAGNOSTICS_ON;
    $rname = "thumbnail_safe_filename";
 

    echo "$rname:  working with filename '<b>$filename</b>' . . .<br />\n";

    $thumbnail_safe_name = preg_replace('/#/', '%23', $filename);
    show_diag($rname, "after replacing /#/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/ /', '-', $thumbnail_safe_name);
    show_diag($rname, "after replacing / / safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/\[/', '', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\[/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/\]/', '', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\]/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/\'/', '', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\'/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/\+/', '-plus', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\+/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/([A-Z])\.-/', '$1-', $thumbnail_safe_name);
    $thumbnail_safe_name = preg_replace('/([A-Z])\./', '$1-', $thumbnail_safe_name);

    $thumbnail_safe_name = preg_replace('/-jpg$/', '.jpg', $thumbnail_safe_name);

    return $thumbnail_safe_name;

}
?>
