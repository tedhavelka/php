<?php
//======================================================================
//
//  PROJECT:  PHP library routines at Neela, de Ted
//
//  FILE:  file-and-directory-routines.php
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
//    *  http://php.net/manual/en/function.readlink.php
//
//    *  http://php.net/manual/en/function.get-defined-constants.php
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

    require_once '/opt/nn/lib/php/defines-nn.php';
//    require '/opt/nn/lib/php/defines-nn.php';

    require_once '/opt/nn/lib/php/diagnostics-nn.php';

    require_once '/opt/nn/lib/php/text-manipulation.php';






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
//----------------------------------------------------------------------
//
//  2017-08-30 WED - This function started by Ted,
//
//  PURPOSE:
//
//
//  EXPECTS:  A valid path to a readable directory of the filesystem
//    where this code resides and runs.
//
//  RETURNS:  an array of strings, each string represents a discovered
//    filename from the caller's path.
//
//
//
//----------------------------------------------------------------------

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


    $rname = "list_of_filenames_by_pattern";


// DEV - show parameters passed to us from calling code:

/*
    echo "called by '$caller'," . $term;
    echo "caller sends path set to '$path_to_search'," . $term;
    echo "and pattern to match holding '$pattern_to_match'." . $term . $term;
*/

// END DEV


    if ( !(isset($path_to_search)) )
    {
        warn($rname, "WARNING - calling code sends no path or directory to search!");
        warn($rname, "WARNING - returning early . . .");
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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

        closedir($handle);

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

        closedir($handle);

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




function &filename_symlink_entry($caller)
{

    $hash_entry = array();

    $hash_entry[KEY_NAME__FILE_NAME] = KEY_VALUE__DEFAULT_FILENAME;
    $hash_entry[KEY_NAME__SYMLINK_NAME] = KEY_VALUE__DEFAULT_SYMLINK_NAME;
    $hash_entry[KEY_NAME__SYMLINK_STATUS] = KEY_VALUE__SYMLINK_STATUS__NOT_CHECKED;

    return $hash_entry;
}




function &create_symlinks_with_safe_names($caller, $callers_path, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to create safely named (1) symbolic links to files in a
//    calling code specified path, and to return a hash of target file
//   and symlink pairs.
//
//
//  EXPECTS:
//    *  a valid path to a directory
//    *  an optional pattern by which to match filenames needing safe-name symlinks
//
//
//  MODIFIES:
//    *  server side directory to which caller's path points
//
//  RETURNS:
//    *  hash of corresponding filename and symlink pairs
//
//
//----------------------------------------------------------------------


// VAR BEGIN
    $handle = null;

    $current_filename = "";

    $current_path_and_file = "";

    $symlink_prefix = "";

    $symlink_name = "";

    $symlink_result = FALSE;


    $symlinks_noted = array();

    $filenames_and_symlinks = array();

    $count_symlinks_noted = 0;

    $count_symlinks_created = 0;


    $lbuf = "";
    $dflag_announce  = DIAGNOSTICS_ON;
    $dflag_dev       = DIAGNOSTICS_ON;
    $dflag_warning   = DIAGNOSTICS_ON;

    $dflag_loop      = DIAGNOSTICS_ON;
    $dflag_add_entry = DIAGNOSTICS_ON;
    $dflag_summary   = DIAGNOSTICS_ON;

    $dflag_note_symlink           = DIAGNOSTICS_ON;
    $dflag_create_symlink         = DIAGNOSTICS_ON;
    $dflag_symlink_with_prefix    = DIAGNOSTICS_ON;
    $dflag_found_prefixed_symlink = DIAGNOSTICS_ON;
    $dflag_file_found_first       = DIAGNOSTICS_ON;
    $dflag_not_implemented        = DIAGNOSTICS_ON;

    $dflag_debugging_extended = DIAGNOSTICS_ON;

    $rname = "create_symlinks_with_safe_names";
// VAR END


if ( 0 )
//if ( array_key_exists(KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS, $options) && $options[KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS] == DIAGNOSTICS_OFF )
{
    $dflag_announce  = DIAGNOSTICS_OFF;
    $dflag_dev       = DIAGNOSTICS_OFF;
//    $dflag_warning   = DIAGNOSTICS_OFF;

    $dflag_loop      = DIAGNOSTICS_OFF;
    $dflag_add_entry = DIAGNOSTICS_OFF;
    $dflag_summary   = DIAGNOSTICS_OFF;

    $dflag_note_symlink           = DIAGNOSTICS_OFF;
    $dflag_create_symlink         = DIAGNOSTICS_OFF;
    $dflag_symlink_with_prefix    = DIAGNOSTICS_OFF;
    $dflag_found_prefixed_symlink = DIAGNOSTICS_OFF;
    $dflag_file_found_first       = DIAGNOSTICS_OFF;
    $dflag_not_implemented        = DIAGNOSTICS_OFF;
}


    show_diag($rname, "starting,", $dflag_announce);

//    $scripts_path = getcwd();
//    show_diag($rname, "script's current path is '$scripts_path',", $dflag_dev);

    show_diag($rname, "working in path '$callers_path',", $dflag_dev);

    if ( array_key_exists(KEY_NAME__SYMBOLIC_LINK_PREFIX, $options) )
    {
        $symlink_prefix = $options[KEY_NAME__SYMBOLIC_LINK_PREFIX];
    }


    show_diag($rname, "-----", $dflag_debugging_extended);
    show_diag($rname, "Extended debugging 2018-03-05 - issue with defined constants,", $dflag_debugging_extended);
    $lbuf = "In spite of nn defines file include, constant 'KEY_NAME__FILE_NAME' holds '".KEY_NAME__FILE_NAME."',";
    show_diag($rname, $lbuf, $dflag_debugging_extended);
    show_diag($rname, "-----", $dflag_debugging_extended);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// - STEPS:
//    1)  check that path is valid directory
//    2)  open valid director for reading filenames
//    3)  for regular files which are not symlinks figure a safe version of present filename
//    4)  check whether symlink by safe version of filename exists
//    5)  create symlink when not yet present / amend symlink name when safe name already in use
//
//    ...steps 4 and 5 are unclear
//
//  When this routine executes, a symbolic link for thumbnailing
//  purposes may exist.  The routine may find the symlink before the
//  target filename, or the target filename before the symlink.
//
//  When a thumbnailing symlink is found first, we want to note this
//  symlink in the hash we're returning to calling code, which holds
//  all symlinks to image files to be shown as thumbnail size images.
//  We determine a symlink is found first when we can check and see
//  there is no symlinks hash entry with that symlink name already
//  stored.
//
//  The symlinks-and-files hash has numeric keys.  Rather than code a
//  search of this hash, we can create a local hash whose keys are
//  the names of noted symlinks.  Then we can use PHP's function to
//  check whether an array key exists, to determine whether a symlink
//  for thumbnailing is already noted.
//
//  When a regular file is found first, we construct a name of a
//  symlink to this regular file.  Again we check whether the symlink
//  to this file has already been noted.
//
//

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( is_dir($callers_path) )
    {
        if ( $handle = opendir($callers_path) )
        {
            show_diag($rname, "<br />\n", $dflag_loop);

            while (false !== ($current_filename = readdir($handle)))
            {
                show_diag($rname, "- Top of dir-listing WHILE loop -", $dflag_loop);
                show_diag($rname, "looking at file '$current_filename',", $dflag_loop);

                if ( preg_match('/[^\.].*\.[^\.]+/', $current_filename, $matches) )
                {
//                    show_diag($rname, "- Note - looks like non-hidden file,", $dflag_dev);
                    show_diag($rname, "filename does not begin with '.',", $dflag_dev);

                    $current_path_and_file = "$callers_path/$current_filename";

                    if ( is_link($current_path_and_file) )
                    {
                        if ( (strlen($symlink_prefix) > 0) && (strpos($current_filename, $symlink_prefix, 0) == 0) )
                        {
                            show_diag($rname, "found symlink '$current_filename' which has caller's prefix!",
                              $dflag_found_prefixed_symlink);
                            $lbuf = "this symlink points to '" . readlink($current_path_and_file) . "',"; 
                            show_diag($rname, $lbuf, $dflag_found_prefixed_symlink);

                            if ( array_key_exists($current_filename, $symlinks_noted) )
                            {
                                show_diag($rname, "this symbolic link already created and noted in hash to return to caller,",
                                  $dflag_note_symlink);
                            }
                            else
                            {
                                show_diag($rname, "<b>noting this symlink</b> in hash of symlinks and respective files . . .",
                                  $dflag_note_symlink);

                                $symlinks_noted[$current_filename] = 1;

                                $filenames_and_symlinks[$count_symlinks_noted] = filename_symlink_entry($rname);
                                $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__FILE_NAME] = readlink($current_path_and_file);
                                $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__SYMLINK_NAME] = $current_filename; // $symlink_name;
                                $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__SYMLINK_STATUS] = KEY_VALUE__SYMLINK_STATUS__CHECKED;
                                ++$count_symlinks_noted;
                            }
                        }
                        elseif (strlen($symlink_prefix) == 0)
                        {
                            show_diag($rname, "calling code specifies no prefix for symbolic links,", $dflag_no_prefix_specified);
                            show_diag($rname, "not creating symlink to present symbolic link.", $dflag_no_prefix_specified);
                        }
                        elseif (strpos($current_filename, $symlink_prefix, 0) === FALSE)
                        {
                            show_diag($rname, "found symbolic link whose name does not begin with caller's prefix '$symlink_prefix',",
                              $dflag_no_prefix_specified);
                            show_diag($rname, "not creating symlink to present symbolic link.", $dflag_no_prefix_specified);
                        }

                    }
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - ELSE if the current file is not a symlink:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                    elseif ( is_file($current_path_and_file) && !(is_link($current_path_and_file)) )
                    {
                        $RF = "- REGULAR FILE -";
                        show_diag($rname, "$RF file '$current_path_and_file' tests as regular file and not symlink,",
                          $dflag_dev);
                        show_diag($rname, "$RF building symbolic link name to this file for thumbnailing purposes . . .",
                          $dflag_dev);

// create program-safe and shell-safe filename:
                        $symlink_name =& thumbnail_safe_filename($rname, $current_filename, $options);

// prefix the new symlink name when calling code specifies symlink prefix:
                        if ( strlen($symlink_prefix) > 0 )
                        {
                            $symlink_name = "$symlink_prefix$symlink_name";
                        }


                        if ( array_key_exists($symlink_name, $symlinks_noted) )
                        {
                            show_diag($rname, "$RF this symbolic link already created and noted in hash to return to caller,",
                              $dflag_note_symlink);

                            $lbuf = basename(readlink("$callers_path/$symlink_name"));
                            show_diag($rname, "$RF symbolic link points to '$lbuf' and current filename is '$current_filename',",
                              $dflag_file_found_first);

                            if ( $lbuf === $current_filename )
                            {
                                show_diag($rname, "$RF this symlink points to its expected file,",
                                  $dflag_file_found_first);
                            }

                            show_diag($rname, "$RF NOTE: &nbsp;no action implemented to handle thumbnailing symbolic links pointing to wrong files -",
                              $dflag_not_implemented);
                        }
                        else
                        {
                            show_diag($rname, "$RF symlink '$symlink_name' not yet noted,", $dflag_note_symlink);

// create an absolute or relative path to new symlink:
                            $current_path_and_symlink = "$callers_path/$symlink_name";
                            $current_path_and_symlink = $_SERVER[PWD] . $current_path_and_symlink;

                            show_diag($rname, "$RF <b>noting this symlink</b> in hash of symlinks and respective files . . .",
                              $dflag_note_symlink);

                            $symlinks_noted[$symlink_name] = 1;

                            $filenames_and_symlinks[$count_symlinks_noted] = filename_symlink_entry($rname);
                            $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__FILENAME] = $current_filename;
                            $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__SYMLINK_NAME] = $symlink_name;
                            $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__SYMLINK_STATUS] = KEY_VALUE__SYMLINK_STATUS__CHECKED;
                            ++$count_symlinks_noted;

                            $lbuf = "$RF this entry holds symbolic link name '" . $filenames_and_symlinks[$count_symlinks_noted][KEY_NAME__SYMLINK_NAME] . "',";
                            show_diag($rname, $lbuf, $dflag_add_entry);

                            if ( file_exists($current_path_and_symlink) )
                            {
                                show_diag($rname, "$RF NOTE: &nbsp;symbolic link already exists so not recreating,",
                                  $dflag_create_symlink);
                            }
                            else
                            {
                                show_diag($rname, "$RF calling PHP symlink() with target '$current_filename'",
                                  $dflag_create_symlink);
                                show_diag($rname, "$RF and symlink '$current_path_and_symlink' . . .",
                                  $dflag_create_symlink);
                                $symlink_result = symlink($current_filename, "$current_path_and_symlink");

                                if ( $symlink_result == 1 )
                                {
                                    ++$count_symlinks_created;
                                }

                                show_diag($rname, "$RF call to PHP symlink() returns '$symlink_result',",
                                  $dflag_create_symlink);
                                show_diag($rname, "$RF -", $dflag_create_symlink);
                            }

                        } // end IF-block to check whether symbolic link to present file already noted

                    } // end IF-ELSEIF block to process symbolic links one way, and regular files another way




// PHP file tests require full path, absolute or relative, in order to give correct answers:

//                    if ( is_file($current_path_and_file) && !(is_link($current_path_and_file)) )


                }
                else
                {
                    show_diag($rname, "- Note - current filename may begin or end with a dot '.',", $dflag_dev);

                } // end IF-block to test for file which starts with a dot

                show_diag($rname, "---<br />\n", $dflag_dev);

            } // end WHILE-block iterating over files in caller-specified directory,


// - STEP - create empty file in caller's directory to indicate need
//  for symbolic links checks, and symlinks created as needed:





            closedir($handle);

        }
        else
        {
            show_diag($rname, "- WARNING - trouble opening caller's path as a directory!",
              $dflag_warning);

        } // end IF-block to check whether we can open caller's directory

    }
    else
    {
        show_diag($rname, "- WARNING - caller's path does not test as a file of type directory!",
          $dflag_warning);
        show_diag($rname, "- WARNING - returning early . . .",
          $dflag_warning);

    } // end IF-block to check whether caller's path points to valid directory


    show_diag($rname, "SUMMARY:  created $count_symlinks_created symbolic links.",
      $dflag_summary);
    if ( $dflag_summary )
    {
        show_diag($rname, "hash of symlinks and filenames to return to caller:", $dflag_summary);
        echo "<pre>\n";
        foreach ($filenames_and_symlinks as $key => $entry)
        {
            echo "$key) " . $filenames_and_symlinks[$key][KEY_NAME__SYMLINK_NAME] . "-->" . $filenames_and_symlinks[$key][KEY_NAME__FILE_NAME] . "<br />";
        }
        echo "</pre>\n";
    }
    show_diag($rname, "returning . . .", $dflag_announce);

    return $filenames_and_symlinks;

} // end function create_symlinks_with_safe_names()





?>
