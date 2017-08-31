<?php
//======================================================================
//
//  PROJECT:  PHP library routines at Neela, de Ted
//
//  FILE:  page_building_routines.php
//
//  STARTED:  2017 August 30
//
//
//  NOTES ON IMPLEMENTATION:
//
//
//  REFERENCES:
//
//    *  http://php.net/manual/en/functions.returning-values.php
//       . . . also gives example in PHP of returning reference to array.
//
//    *  http://php.net/manual/en/function.preg-match.php
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



// NOTE - This seeming file-scoped variable didn't appear to pass its
//  value to code in first function written on 2017-AUG-30:

$term = "<br />\n";


function warn($caller, $message)
{
    echo "<font color=\"maroon\">\n";
    echo "$caller: &nbsp;$message<br />\n";
    echo "</font>\n";
}




function &list_of_filenames_by_pattern($caller, $path_to_search, $pattern_to_match)
{
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  2017-08-30 WED - This function started by Ted,
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// We'll return an array of all matching filenames to calling code, in
// this variable:

    $filenames_matching_pattern = array();

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




// DEV - show parameters passed to us from calling code:

    echo "called by '$caller'," . $term;
    echo "caller sends path set to '$path_to_search'," . $term;
    echo "and pattern to match holding '$pattern_to_match'." . $term . $term;

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

}



?>
