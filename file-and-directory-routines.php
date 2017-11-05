<?php
//======================================================================
//
//  PROJECT:  PHP library routines at Neela, de Ted
//
//  FILE:  page-building-routines.php
//
//  STARTED:  2017 August 30
//
//
//  NOTES ON IMPLEMENTATION:
//
//
//  REFERENCES:
//
//    * REF *  http://php.net/manual/en/functions.returning-values.php
//       . . . also gives example in PHP of returning reference to array.
//
//    * REF *  http://php.net/manual/en/function.preg-match.php
//
//
//
//  AUTHORS AND CONTRIBUTORS:
//
//     * Ted Havelka,  <ted@cs.pdx.edu>,  TMH
//
//
//
//
//======================================================================




//----------------------------------------------------------------------
// - SECTION - PHP include directives
//----------------------------------------------------------------------

    require_once '/opt/nn/lib/php/diagnostics-nn.php';







// NOTE - This seeming file-scoped variable didn't appear to pass its
//  value to code in first function written on 2017-AUG-30:

$term = "<br />\n";






/*
function warn($caller, $message)
{
    echo "<font color=\"maroon\">\n";
    echo "$caller: &nbsp;$message<br />\n";
    echo "</font>\n";
}
*/




function &list_of_filenames_by_pattern($caller, $path_to_search, $pattern_to_match)
{
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  2017-08-30 WED - This function started by Ted,
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// We'll return an array of all matching filenames to calling code, in
// this variable:
    $filenames_matching_pattern = array();

// local variable to act as file handle:
    $handle = NULL;

// local string to hold filenames read one-by-one from passed file system path:
    $current_filename = "";

// local array to hold preg_match() results:
    $matches = array();

// local key to $matches array, also known in PHP as an ordered map:
    $key_to_matches = 1;


// local text string for use during development and diagnostics:
    $term = "<br />\n";




// DEV - show parameters passed to us from calling code:

/*
    echo "called by '$caller'," . $term;
    echo "caller sends path set to '$path_to_search'," . $term;
    echo "and pattern to match holding '$pattern_to_match'." . $term . $term;
*/

// END DEV




    if ( $handle = opendir($path_to_search) )
    {
        while (false !== ($current_filename = readdir($handle)))
        {
//echo "looking at '$current_filename' . . .<br />\n";

            preg_match($pattern_to_match, $current_filename, $matches);
// Test to avoid PHP 'undefined offset' warning:
            if ( $matches )
            {
                if ( $matches[0] )
                {
// Increment our numeric key to local ordered map of matching filenames:
                    ++$key_to_matches;
// Add present filename to the ordered map of name-wise matches:
                    array_push($filenames_matching_pattern, $current_filename);
                }

            } // close IF-statement to check whether array has any elements

        } // close WHILE-loop which iterates while there are files yet to read in caller's path

    }
    else
    {
        warn($rname, "WARNING - unable to open directory '$path_to_search'!");
    }


    return $filenames_matching_pattern;


} // end PHP function &list_of_filenames_by_pattern()





function &list_of_filenames_sorted_by_same_marker(
  $caller,
  $path_to_search, $marker_infix, $number_leading_digits_showing_order)
{

//----------------------------------------------------------------------
//  2017-09-01 WED - This function started by Ted,
//
//  PURPOSE:  to look for filenames with a common infix, and to order
//   the part of those filenames after the infix per the numeric values
//   at the beginning of infix-matched filenames.
//
//----------------------------------------------------------------------

// VARIABLES

// array of filenames to return to caller:
    $filenames_matching_infix= array();

// local text string for use during development and diagnostics:
    $term = "<br />\n";

// local variable to act as file handle:
    $handle = NULL;

// local string to hold filenames read one-by-one from passed file system path:
    $current_filename = "";

// local array to hold preg_match() results:
    $matches = array();

// local key to $matches array, also known in PHP as an ordered map:
    $key_to_matches = 1;

// function or routine name, of this function:
    $rname = "list_of_filenames_sorted_by_same_marker";

// END VARIABLES



// DEV - show parameters passed to us from calling code:

/*
    echo "called by '$caller'," . $term;
    echo "caller sends path set to '$path_to_search'," . $term;
    echo "and pattern to match holding '$pattern_to_match'." . $term . $term;
*/


    if ( $handle = opendir($path_to_search) )
    {
        while (false !== ($current_filename = readdir($handle)))
        {

// echo "looking at '$current_filename' . . .<br />\n";

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  The next pattern is designed to match and extract two substrings
//  from a filename acting as a web site navigation marker.  The first
//  pattern this code expects to find as a number of digits -- this
//  quantity passed to us here by calling code -- and the second pattern
//  to match in a filename acting as marker is an infix, which is what
//  distinguishes a marking filename from names of other files:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//            preg_match($pattern_to_match, $current_filename, $matches);
//            preg_match("/(^[0-9]{$number_leading_digits_showing_order})($marker_infix)(.*)/", $current_filename, $matches);
//            preg_match('/(^[0-9]{ $number_leading_digits_showing_order })($marker_infix)(.*)/', $current_filename, $matches);

            $pattern = '/(^[0-9]{'. $number_leading_digits_showing_order .'})('.$marker_infix.')(.*)/';
            preg_match($pattern, $current_filename, $matches);

// echo "$rname: &nbsp;DEV - pattern we tried to build is " . '/(^[0-9]{'. $number_leading_digits_showing_order .'})('.$marker_infix.')(.*)/' . "\n";
// echo "$rname: &nbsp;DEV - we built pattern: &nbsp;$pattern$term$term";

// Test to avoid PHP 'undefined offset' warning:
            if ( $matches )
            {
                if ( $matches[0] )
                {
// echo "$rname: &nbsp;DEV - matches[0] holds $matches[0]$term";
// echo "$rname: &nbsp;DEV - matches[1] holds $matches[1]$term";
// echo "$rname: &nbsp;DEV - matches[2] holds $matches[2]$term";
// echo "$rname: &nbsp;DEV - matches[3] holds $matches[3]$term";
// . . .
                    $key = $matches[1];
// echo "$rname: &nbsp;DEV - set \$key equal to \$matches[1], \$key holds '$key'" . $term;
// . . .
                    $value = $matches[3];
                    $filenames_matching_infix[$key] = $value;
// echo "$rname: &nbsp;DEV - \$filenames_matching_infix[" . $key . "] holds " . $filename_matching_infix[$key] . $term;
                }

            } // close IF-block which tests whether there are array elements to process

        } // while WHILE-loop to iterate over files in caller's current directory

    }
    else
    {
        warn($rname, "WARNING - unable to open directory '$path_to_search'!");
    }


    echo "$rname:  - DEV - showing array of filenames matching infix:$term";
    nn_show_array($rname, $filenames_matching_infix, "--no-options");
    echo "<br />\n";

    ksort($filenames_matching_infix);
    echo "$rname:  - DEV - showing array of filenames matching infix after sorting by keys:$term";
    nn_show_array($rname, $filenames_matching_infix, "--no-options");

    echo "<br />\n";
    echo "2017-09-20 NOTE - the name of this local PHP library function is poor, as the names in the above array are parsed from the full files, which originally have an infix '$marker_infix' and $number_leading_digits_showing_order digits at the start of each filename.$term$term";

    return $filenames_matching_infix;

}




?>
