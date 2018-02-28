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

    $dflag_dev = DIAGNOSTICS_OFF;
    $dflag_pound = DIAGNOSTICS_OFF;
    $rname = "thumbnail_safe_filename";
 

    show_diag($rname, "working with filename '<b>$filename</b>' . . .", $dflag_dev);

    $thumbnail_safe_name = $filename;

    $thumbnail_safe_name = preg_replace('/#/', '%23', $thumbnail_safe_name);
      show_diag($rname, "after replacing /#/ safer filename holds '$thumbnail_safe_name',", $dflag_pound);


// account for some special and specific plant naming conventions:
    $thumbnail_safe_name = preg_replace('/OB-/', 'OB-minus', $thumbnail_safe_name);

// modify opening square brackets preceded by a space character:
    $thumbnail_safe_name = preg_replace('/ \[/', '--', $thumbnail_safe_name);
      show_diag($rname, "after replacing / \[/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

// modify closing square brackets followed by a space character:
    $thumbnail_safe_name = preg_replace('/\] /', '--', $thumbnail_safe_name);
      show_diag($rname, "after replacing /\] / safer filename holds '$thumbnail_safe_name',", $dflag_dev);

// remove remaining square brackets:
    $thumbnail_safe_name = preg_replace('/\[/', '', $thumbnail_safe_name);
      show_diag($rname, "after replacing /\[/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/\]/', '', $thumbnail_safe_name);
      show_diag($rname, "after replacing /\]/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);


// modify opening parentheses preceded by a space character:
    $thumbnail_safe_name = preg_replace('/ \(/', '--', $thumbnail_safe_name);
      show_diag($rname, "after replacing / \(/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

// modify closing parentheses followed by a space character:
    $thumbnail_safe_name = preg_replace('/\) /', '--', $thumbnail_safe_name);
      show_diag($rname, "after replacing /\) / safer filename holds '$thumbnail_safe_name',", $dflag_dev);

// remove remaining open and close parentheses:
    $thumbnail_safe_name = preg_replace('/\(/', '', $thumbnail_safe_name);
      show_diag($rname, "after replacing /\(/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

    $thumbnail_safe_name = preg_replace('/\)/', '', $thumbnail_safe_name);
      show_diag($rname, "after replacing /\)/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);


// change single dash between uppercase alphabetics to two dashes:
    $thumbnail_safe_name = preg_replace('/([A-Z])-([A-Z])/', '$1--$2', $thumbnail_safe_name);

// change "space dash space" character sequence to two dashes:
    $thumbnail_safe_name = preg_replace('/ - /', '--', $thumbnail_safe_name);

// change a period followed by a space to one dash:
    $thumbnail_safe_name = preg_replace('/\. /', '-', $thumbnail_safe_name);


// change remaining space characters to dashes:
    $thumbnail_safe_name = preg_replace('/ /', '-', $thumbnail_safe_name);
    show_diag($rname, "after replacing / / safer filename holds '$thumbnail_safe_name',", $dflag_dev);


// remove single quotes:
    $thumbnail_safe_name = preg_replace('/\'/', '', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\'/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

// remove commas:
    $thumbnail_safe_name = preg_replace('/,/', '', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\'/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);

// substitute plus character with short phrase:
    $thumbnail_safe_name = preg_replace('/\+/', '-plus', $thumbnail_safe_name);
    show_diag($rname, "after replacing /\+/ safer filename holds '$thumbnail_safe_name',", $dflag_dev);


// replace two or more successive periods with a single period:
    $thumbnail_safe_name = preg_replace('/\.(\.)+/', '.', $thumbnail_safe_name);


// remove period characters from filename:
//    $thumbnail_safe_name = preg_replace('/([A-Z])\.-/', '$1-', $thumbnail_safe_name);
//    $thumbnail_safe_name = preg_replace('/([a-zA-Z])\./', '$1-', $thumbnail_safe_name);

//    $thumbnail_safe_name = preg_replace('/-jpg$/', '.jpg', $thumbnail_safe_name);

//    $thumbnail_safe_name = preg_replace('/([^\.])+\.([^\.])*/', '$1-$2', $thumbnail_safe_name);


// https://stackoverflow.com/questions/6314007/remove-all-decimals-but-last . . .
// ...use regex with positive look ahead:

    $thumbnail_safe_name = preg_replace('/\.(?=.*\.)/', '-', $thumbnail_safe_name);


    return $thumbnail_safe_name;

}





?>
