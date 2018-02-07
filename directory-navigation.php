<?php

//----------------------------------------------------------------------
//
//  FILE:  directory-navigation.php"
//
//
//  TO-DO:
//
//     [ ]  add symbolic link file type detection, and optional skipping
//          of symbolic links in build_tree() function, 2018-01-24.
//
//
//
//  IDEAS ON FILE PRESENTATION . . .
//
//  PHP based directory tree browsing functions
//
//  Basic web page tree view, text links . . . this is the default
//  view presented with each directory a symlink to call the
//  tree browser function with that directory as the base directory:
//
//
//  base-directory
//    sub-directory-1
//      another-directory-a
//      another-directory-b
//      another-directory-c
//    sub-directory-2
//      another-directory-d
//    sub-directory-3
//
//
//  Basic tree view with file counts added:
//
//  base-directory (3)
//    sub-directory-1 (3)
//      another-directory-a (n)
//      another-directory-b (m)
//      another-directory-c (l)
//    sub-directory-2 (1)
//      another-directory-d (k)
//    sub-directory-3 (0)
//
//
//  Tree view with first few filenames in directories shown:
//
//  base-directory
//    sub-directory-1
//      another-directory-a
//        file 1  file 2  file 3  file 4 ...
//      another-directory-b
//        file 1  file 2  file 3 ...
//      another-directory-c
//    sub-directory-2
//      another-directory-d
//        file 1  file 2 ...
//    sub-directory-3
//      file 1 file 2 ...
//
//
//  Tree view fully expanded with filenames one per line:
//
//  base-directory
//    sub-directory-1
//      another-directory-a
//        file 1
//        file 2
//        file 3
//        file 4
//      another-directory-b
//        file 1
//        file 2
//        file 3
//      another-directory-c
//    sub-directory-2
//      another-directory-d
//        file 1
//        file 2
//    sub-directory-3
//      file 1
//      file 2
//
//
//
//
//



//----------------------------------------------------------------------
// - SECTION - PHP include directives
//----------------------------------------------------------------------

    require_once '/opt/nn/lib/php/defines-nn.php';

    require_once '/opt/nn/lib/php/diagnostics-nn.php';

    require_once '/opt/nn/lib/php/file-and-directory-routines.php';



//----------------------------------------------------------------------
// - SECTION - PHP script constants
//----------------------------------------------------------------------

// 2018-01-23 - added to improve source code readability in function
// tree_browser().
// NOTE:  these shorter defined names or labels will collide if defined
// elsewhere in this local PHP library.  - TMH

    define("FILE_NAME", KEY_NAME__SITE_NAVIGATION__TREE_BROWSER_FILE_NAME);
    define("FILE_STATUS", KEY_NAME__SITE_NAVIGATION__TREE_BROWSER_FILE_STATUS);
    define("FILE_TYPE", KEY_NAME__SITE_NAVIGATION__TREE_BROWSER_FILE_TYPE);
    define("FILE_PATH_IN_BASE_DIR", KEY_NAME__SITE_NAVIGATION__TREE_BROWSER_FILE_PATH_IN_BASE_DIR);
    define("FILE_DEPTH_IN_BASE_DIR", KEY_NAME__SITE_NAVIGATION__TREE_BROWSER_FILE_DEPTH_IN_BASE_DIR);
    define("FILE_COUNT", KEY_NAME__DIRECTORY_NAVIGATION__COUNT_OF_REGULAR_FILES);

    define("FILE_CHECKED", KEY_VALUE__FILE_STATUS__CHECKED);
    define("FILE_NOT_CHECKED", KEY_VALUE__FILE_STATUS__NOT_CHECKED);

//    define("FILE_LIMIT_OF_TREE_BROWSER", 4096);
    define("FILE_LIMIT_OF_TREE_BROWSER", 2048);

// Note:  best place PHP defines outside of functions, as functions
//  called more than once by one and same calling script lead to PHP
//  interpreter / engine notice of defined value already defined on
//  line x . . .   - TMH


// 2018-02-06 - For development work:
    define ("KEY_NAME__NON_EMPTY_DIRECTORY_ENTRIES", "non-empty-directory-entries");
    define ("KEY_NAME__DIRECTORY_ENTRIES", "directory-entries");




//----------------------------------------------------------------------
// - SECTION - PHP functions
//----------------------------------------------------------------------

function show_file_hierarchy_paths($rname, $file_hierarchy)
{
//----------------------------------------------------------------------
//
//                   - - - DEVELOPMENT ROUTINE - - -
//
//
//  PURPOSE:  to each noted file regardless of file type, in the passed
//    directory and file hierarchy.  Paths of each file shown too.
//
//----------------------------------------------------------------------


    $file_entry_count = count($file_hierarchy);

    $lbuf = "";
 
    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_dev = DIAGNOSTICS_OFF;

    $rname = "show_file_hierarchy_paths";


    show_diag($rname, "-", $dflag_announce);
    show_diag($rname, "received hash with $file_entry_count file entries,", $dflag_announce);
    show_diag($rname, "showing noted files and their paths:", $dflag_announce);
    show_diag($rname, "-", $dflag_announce);

    echo "<pre>\n";

    foreach ( $file_hierarchy as $key => $value )
    {
        if ( is_array($value))
        {
            $lbuf = "hash entry $key holds filename and path to file:";
            show_diag($rname, $lbuf, $dflag_dev);
            echo $lbuf . "\n";

            $lbuf = "      path plus file:  " . $value[FILE_PATH_IN_BASE_DIR] . "/" . $value[FILE_NAME];
            show_diag($rname, $lbuf, $dflag_dev);
            echo $lbuf . "\n";

            $lbuf = "                path:  " . $value[FILE_PATH_IN_BASE_DIR];
            show_diag($rname, $lbuf, $dflag_dev);
            echo $lbuf . "\n";

            $lbuf = "           file type:  " . $value[FILE_TYPE];
            show_diag($rname, $lbuf, $dflag_dev);
            echo $lbuf . "\n";

            $lbuf = "  regular file count:  " . $value[FILE_COUNT];
            echo $lbuf . "\n\n";
        }
//        else
//        {
//            print_r($value);
//        }
    }

    echo "</pre>\n";

    show_diag($rname, "-", $dflag_dev);
    show_diag($rname, "done.", $dflag_dev);
}





function &build_tree($caller, $base_directory, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  . . .
//
//
//
//  NOTES ON IMPLEMENTATION:  this routine a non-recursive algorithm
//   which builds a flattened hash of a directory and file structure.
//
//
//
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

//    $show_usage = false;

    $handle = NULL;                      // . . . file handle to directory to search for navigation menu item files,

    $current_dir_has_files_to_process = 'true';

    $files_noted = 0;

    $file_limit_not_reached = 'true';

    $files_in_current_dir = array();

    $key = 0;

    $key_to_present_directory = 0;       // . . . key used to store count of files in containing directory hash entry

    $file = KEY_VALUE__DIRECTORY_NAVIGATION__TREE_BROWSER__DEFAULT_FILENAME;

    $file_type = KEY_VALUE__SITE_NAVIGATION__TREE_BROWSER__DEFAULT_FILE_TYPE;

    $current_path = ".";   // . . . this var was named 'file_path_in_base_dir' - TMH

    $file_depth_in_base_dir = 0;

    $navigable_tree = array();           // . . . PHP hash of hashes to hold navigation menu items and to return,


    $count_of_regular_files = 0;

    $count_of_directories = 0;



// diagnostics and formatting:

    $dmsg = "";                          // . . . local diagnostics message string for development and debugging,

    $term = "<br />\n";

    $dflag_announce = DIAGNOSTICS_ON;    // . . . diagnostics flag for development-related run time comments,
    $dflag_dev      = DIAGNOSTICS_ON;    // . . . diagnostics flag for development-related run time comments,
    $dflag_format   = DIAGNOSTICS_ON;
    $dflag_verbose  = DIAGNOSTICS_OFF;    // . . . diagnostics flag for verbose messages during development,
    $dflag_warning  = DIAGNOSTICS_ON;    // . . . diagnostics flag to toggle warnings in this routine,
    $dflag_summary  = DIAGNOSTICS_ON;

    $dflag_open_dir    = DIAGNOSTICS_ON;

    $dflag_note_file   = DIAGNOSTICS_ON;
    $dflag_check_file  = DIAGNOSTICS_ON;
    $dflag_files_limit = DIAGNOSTICS_OFF;
    $dflag_files_count = DIAGNOSTICS_ON;

    $dflag_file_count_per_directory = DIAGNOSTICS_ON;
    $dflag_base_directory_file_list = DIAGNOSTICS_ON;

//    $rname = "tree_browser";
    $rname = "build_tree";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    if ( 0 )
    {
echo "ZZZ $rname ZZZ turning off most diagnostics!<br />\n";
//    $dflag_announce = DIAGNOSTICS_OFF;    // . . . diagnostics flag for development-related run time comments,
    $dflag_dev      = DIAGNOSTICS_OFF;    // . . . diagnostics flag for development-related run time comments,
    $dflag_format   = DIAGNOSTICS_OFF;
    $dflag_verbose  = DIAGNOSTICS_OFF;    // . . . diagnostics flag for verbose messages during development,
    $dflag_warning  = DIAGNOSTICS_OFF;    // . . . diagnostics flag to toggle warnings in this routine,
    $dflag_summary  = DIAGNOSTICS_OFF;

    $dflag_open_dir    = DIAGNOSTICS_OFF;

    $dflag_note_file   = DIAGNOSTICS_OFF;
    $dflag_check_file  = DIAGNOSTICS_OFF;
    $dflag_files_limit = DIAGNOSTICS_OFF;
    $dflag_files_count = DIAGNOSTICS_OFF;

//    $dflag_file_count_per_directory = DIAGNOSTICS_OFF;
    }

    show_diag($rname, "- 2018-01-23 - ROUTINE IMPLEMENTATION UNDERWAY -", $dflag_dev);
    show_diag($rname, "starting,", $dflag_dev);
    show_diag($rname, "called by '$caller' with base directory '$base_directory',", $dflag_dev);


//----------------------------------------------------------------------
// - STEP - sanity checking base directory from caller:
//----------------------------------------------------------------------

    if ( !(is_dir($base_directory)) )
    {
        show_diag($rname, "- WARNING - caller's base directory doesn't appear to be valid!",
          $dflag_warning);
        show_diag($rname, "- WARNING - returning early . . .",
          $dflag_warning);
        return;
    }


    if ( array_key_exists(KEY_NAME__FILE_DEPTH_IN_BASE_DIR, $_SESSION) )
        { }
    else
        { $_SESSION[KEY_NAME__FILE_DEPTH_IN_BASE_DIR] = 0; }


    if ( array_key_exists(KEY_NAME__FILE_DEPTH_GREATEST_VALUE, $_SESSION) )
        { }
    else
        { $_SESSION[KEY_NAME__FILE_DEPTH_GREATEST_VALUE] = 0; }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    show_diag($rname, "calling for list of files in base directory '$base_directory' . . .", $dflag_open_dir);
    $files_in_current_dir =& list_of_filenames_by_pattern($rname, $base_directory, "/(.*)/");
    sort($files_in_current_dir);
//    array_push($files_in_current_dir, "---MARKER---");

    if ( $dflag_dev )
    {
        echo "In loop 1, from base directory got file list:<br />\n";
        echo "<pre>\n";
        print_r($files_in_current_dir);
        echo "</pre>\n";
    }


// Loop set up:

    $current_dir_has_files_to_process = 'true';

    $index_to_latest_not_checked = 0;

    $files_noted = 0;

    if ( $files_noted < FILE_LIMIT_OF_TREE_BROWSER )
        { $file_limit_not_reached = 'true'; }
    else
        { $file_limit_not_reached = 'false'; }

//
// Variable 'current_path' which used to be 'file_path_in_base_dir'
// holds the notion of a path which starts at calling code's base
// directory, and changes per subdirectories found as we map files
// -- directories and other file types -- starting at that path:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $current_path = $base_directory;


// Primary file-processing loop:

    while (( $current_dir_has_files_to_process == 'true' ) && ( $file_limit_not_reached == 'true' ))
    {
//        show_diag($rname, "flag \$current_dir_has_files_to_process:", $dflag_dev);
//        var_dump($current_dir_has_files_to_process);
//        var_dump($file_limit_not_reached);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - add files from latest checked directory in tree to present:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "Loop 1 - Noting file types in path '$current_path':", $dflag_note_file);

        if ( $dflag_base_directory_file_list )
        {
            echo "From current path got file list:<br />\n";
            echo "<pre>\n";
            print_r($files_in_current_dir);
            echo "</pre>\n";
        }


//----------------------------------------------------------------------
// - NOTES - Nested loop 1 implementation
//----------------------------------------------------------------------

// The first nested loop takes a given path or directory and notes all
// files in this path by adding them as data structure entries to a PHP
// hash, also known as an 'ordered map'.  Loop 1 gives numeric keys to
// the entries of this hash, which increment from 0 inclusive once for
// each file noted.
//
// When all files have been noted by loop 1 then script execution moves
// to loop 2, which searched the hash of files for directories.  For
// each directory loop 2 finds it returns script execution to loop 1 so
// that those directory's files are added to the hash.
//
// Loop 1 and loop 2 can see which files have been reviewed or checked 
// by loop 2.  Loop 1 always adds all files in the current path, but
// loop 2 does not always check all files in the file tree hash.
// Loop 2 is designed to direct script execution back to loop one at
// the first un-checked directory loop 2 finds in the hash.
//
// Because of this, on the second and successive times when loop 2
// executes it may be checking a group of files which fall under two
// or more distinct paths.  To update file counts per directory in
// this scenario, loop 2 must compare strings -- file paths -- of each
// file it checks.  It must compare file paths to noted directores
// until a matchis found.  This is inefficient.  For loop 2 to know
// up front in which directory its regular files are found, loop 2
// needs to review all unchecked files every time it is visited.  Then
// and only then is the case assured that loop 2 will have unchecked
// files from one directory.
//
// To achieve this, loop 2 need keep track of which unchecked directory
// it first encounters after loop 2 begins execution.  Loop 2 may find
// multiple directories while checking hash entries during one of its
// executions.  That is ok, so long as loop 2 is sure to check all
// unchecked files, all directories, where the first un-checked
// directory found is the path which is set before script execution
// resumes at loop 1.
//
// To improve efficiency, loop 2 can skip hash entries before the
// latest un-checked directory.  That is, loop 2 need not ask whether
// files have been checked earlier in the hash than the latest
// un-checked directory, because loop 2 checks all non-directory files
// plus the latest un-checked directory, before the PHP interpreter
// passes script control back to loop 1.
//

//----------------------------------------------------------------------
// - FEATURE - Nested loop 1:
//----------------------------------------------------------------------

        foreach ( $files_in_current_dir as $key => $file )
        {

            if (( $file == "." ) || ( $file == ".." ))
            {
                show_diag($rname, "skipping relative directory '$file' . . .", $dflag_note_file);
                continue;
            }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - Check file types . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $file_type = KEY_VALUE__FILE_TYPE__IS_NOT_DETERMINED;

            $path_to_latest_file = $current_path . "/" . $file;

            show_diag($rname, "checking file type of '$path_to_latest_file' . . .", $dflag_note_file);

//            if ( is_dir($path_to_latest_file) )
            if ( is_dir($path_to_latest_file) && !is_link($path_to_latest_file) )
            {
                show_diag($rname, "- zz1 - noting directory '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_DIRECTORY;
                ++$count_of_directories;

                show_diag($rname, "for development purposes noting directory hash entry in PHP session variable,", $dflag_dev);
                array_push($_SESSION[KEY_NAME__DIRECTORY_ENTRIES], $key_name);

            }

            if ( is_file($path_to_latest_file) )
            {
                show_diag($rname, "- zz2 - noting file '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_FILE;
                ++$count_of_regular_files;

                ++$navigable_tree[$key_to_present_directory][FILE_COUNT];
//                $navigable_tree[$key_to_present_directory][FILE_COUNT] = ($navigable_tree[$key_to_present_directory][FILE_COUNT] + 1);

                if ( $dflag_file_count_per_directory )
                {
                    $lbuf = "incrementing file count of present directory, noted in hash entry $key_to_present_directory to " .  $navigable_tree[$key_to_present_directory][FILE_COUNT] . ",";
                    show_diag($rname, $lbuf, $dflag_file_count_per_directory);
                    $_SESSION[KEY_NAME__NON_EMPTY_DIRECTORY_ENTRIES][$key_to_present_directory] = $navigable_tree[$key_to_present_directory][FILE_COUNT];
                }
            }


            if ( is_link($path_to_latest_file) )
            {
                show_diag($rname, "- zz3 - noting symlink '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_SYMBOLIC_LINK;
                ++$count_of_regular_files;

                ++$navigable_tree[$key_to_present_directory][FILE_COUNT];
            }



            if ( !(file_exists($path_to_latest_file)) )
            {
                show_diag($rname, "- zz3 - noting file '$path_to_latest_file' does not exist,", $dflag_note_file);
                $file_type = "non-existent";
            }


//
//----------------------------------------------------------------------
// - 2018-01-30 - NEED REVIEW:
// NOTE:  WE MAY WANT TO ADD CODE TO TEST FOR SYMBOLIC LINKS, AND
//   OPTIONALLY SKIP THEM IN BUILDING FILE TREE HIERARCHIES.  - TMH
//----------------------------------------------------------------------
//



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - figure file depth in base directory, used to determine
//          whether to provide link to parent directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $file_depth_in_base_dir = substr_count($current_path, "/");

            $_SESSION[KEY_NAME__FILE_DEPTH_IN_BASE_DIR] = $file_depth_in_base_dir;

            if ( $file_depth_in_base_dir > $_SESSION[KEY_NAME__FILE_DEPTH_GREATEST_VALUE] )
            {
                $_SESSION[KEY_NAME__FILE_DEPTH_GREATEST_VALUE] = $file_depth_in_base_dir;
            }


            $key_name = $files_noted;

            $navigable_tree[$key_name] = nn_tree_browser_entry($rname);
            $navigable_tree[$key_name][FILE_NAME] = $file;
            $navigable_tree[$key_name][FILE_STATUS] = KEY_VALUE__SITE_NAVIGATION__TREE_BROWSER__DEFAULT_FILE_STATUS;
            $navigable_tree[$key_name][FILE_TYPE] = $file_type;
            $navigable_tree[$key_name][FILE_PATH_IN_BASE_DIR] = $current_path;
            $navigable_tree[$key_name][FILE_DEPTH_IN_BASE_DIR] = $file_depth_in_base_dir;
            $navigable_tree[$key_name][FILE_COUNT] = 0;

            ++$files_noted;

        } // end of loop 1 to note files in current directory

        if ( $dflag_format )
            { echo $term; }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - review most recently added files, advancing to next
//          unchecked directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "Loop 2 - Checking noted files for directories to map:", $dflag_check_file);
        show_diag($rname, "-----------------------------------------------------", $dflag_check_file);
        show_diag($rname, "hash element pointer set to $index_to_latest_not_checked,",
          $dflag_check_file);

//
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// PROBLEM - Foreach construct puts us back at the start of the growing
//  array of noted files.  Not a big deal when there are few files, but
//  will cause poor server/system performance when there are large
//  numbers of files.  The WHILE construct tests a variable we've
//  created in this function, which tracks where the next not checked,
//  not processed file appears in the hash of noted files . . .
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
//
//        foreach ( $navigable_tree as $key => $noted_file )

        if ( isset($navigable_tree[$index_to_latest_not_checked]) )
        {
            $noted_file = $navigable_tree[$index_to_latest_not_checked];
            $path_to_latest_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];
        }
        else
        {
            $current_dir_has_files_to_process = 'false';
        }


// - FEATURE - Nested loop 2:

        while ( ($noted_file[FILE_STATUS] == FILE_NOT_CHECKED) && ($current_dir_has_files_to_process == 'true') )
        {
            show_diag($rname, "checking file '" . $path_to_latest_file . "',", $dflag_check_file);
            $navigable_tree[$index_to_latest_not_checked][FILE_STATUS] = FILE_CHECKED;
            show_diag($rname, "marked file '" . $path_to_latest_file . "' as '" . $navigable_tree[$index_to_latest_not_checked][FILE_STATUS] . "',",
              $dflag_check_file);
            ++$index_to_latest_not_checked;


            if ( $noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            {

//
// ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! !
// Here note hash key name of this latest directory, so we can
// amend this entry with count of regular files found in it:
//
// 2018-02-07 - SOMETHING IS INCORRECT WITH THE SETTING OF 'KEY_TO_PRESENT_DIRECTORY',
//   +  This hash entry pointer is somehow not pointing to the directores
//   +  which contain files and need their file counts incremented.
//   +  Ted debugging this problem now . . .
//
//   Noting that $key_name is assigned a value just one place -- good --
//   and that place is at the point of loop 2 adding a file to the 
//   file tree hash.  Loop 1 executes until all files in $current_path
//   have been noted.  Therefore $key_name will sometimes point too far
//   along the hash of files to indicate the directory which loop 2
//   is ...
//
//
// ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! !
//

                $key_to_present_directory = $key_name;


                show_diag($rname, "in loop 2 file " . $noted_file[FILE_NAME] . " is a directory at hash entry $key_name,", $dflag_check_file);
                show_diag($rname, "in loop 2 breaking out of file review loop . . .", $dflag_check_file);
//                $current_path = $current_path . "/" . $noted_file[FILE_NAME];
                $current_path = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];

                break;
            }


// 2018-02-07 - this block looks exclusively diagnostic . . . and logic is faulty here
//  +  with regular files also testing true for the second outcome of the final
//  +  IF-test:

            if ( 0 )
            {
                if ( $noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_FILE )
                {
                    show_diag($rname, "'" . $noted_file[FILE_NAME] . "' is a regular file,",
                      $dflag_check_file);
                }

                if ( $noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_SYMBOLIC_LINK )
                {
                    show_diag($rname, "'" . $noted_file[FILE_NAME] . "' is a symbolic link,",
                      $dflag_check_file);
                }

                if ( $noted_file[FILE_TYPE] == "non-existent" )
                {
                    show_diag($rname, "'" . $noted_file[FILE_NAME] . "' is non-existent,",
                      $dflag_check_file);
                }
                else
                {
                    show_diag($rname, "'" . $noted_file[FILE_NAME] . "' is neither directory nor regular file, (though PHP is_dir() returns 'true' for symlinks which point to directories)",
                      $dflag_check_file);
                }
            }

//
// 2018-02-07 - index to latest not checked hash entry was updated
//  +  early in this second-in-series nested loop, and here we use that
//  +  index again in test:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            if ( isset($navigable_tree[$index_to_latest_not_checked]) )
            {
                show_diag($rname, "\$navigable_tree[$index_to_latest_not_checked] is set,<br />\n",
                  $dflag_check_file);
                $noted_file = $navigable_tree[$index_to_latest_not_checked];
            }
            else
            {
                show_diag($rname, "\$navigable_tree[$index_to_latest_not_checked] is not set,<br />\n",
                  $dflag_check_file);
                $current_dir_has_files_to_process = 'false';
                break;
            }

            $noted_file = $navigable_tree[$index_to_latest_not_checked];
            $path_to_latest_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];

        } // end of loop 2 to check noted files



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - if latest noted file is directory, read its contents:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "-", $dflag_check_file);
        show_diag($rname, "After loop 2 the \"file review\" loop, hash element pointer set to $index_to_latest_not_checked,",
          $dflag_check_file);

        $path_to_latest_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];

        if ( is_dir($path_to_latest_file) )
        {
            show_diag($rname, "code execution to return to loop 1 to note files in '$path_to_latest_file',", $dflag_open_dir);
            $files_in_current_dir =& list_of_filenames_by_pattern($rname, $path_to_latest_file, "/(.*)/");
            show_diag($rname, "back from routine to read files in current directory \$path_to_lastest_file holds '$path_to_latest_file',", $dflag_open_dir);
            sort($files_in_current_dir);
//            array_push($files_in_current_dir, "---MARKER---");
        }
        else
        {
            show_diag($rname, "path to latest file checked in loop 2, file '$path_to_latest_file' is not a directory,", $dflag_open_dir);
            $current_dir_has_files_to_process = 'false';
        }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - check whether routine-defined sane file limit reached:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        if ( $files_noted < FILE_LIMIT_OF_TREE_BROWSER )
        {
            $file_limit_not_reached = 'true';
//            show_diag($rname, "--- PHP says $files_noted < " . FILE_LIMIT_OF_TREE_BROWSER . " ---\n", $dflag_files_limit);
        }
        else
        {
            $file_limit_not_reached = 'false';
//            show_diag($rname, "--- PHP says $files_noted => " . FILE_LIMIT_OF_TREE_BROWSER . " ---\n", $dflag_files_limit);
        }

        show_diag($rname, "so far have noted $files_noted files and defined value FILE_LIMIT_OF_TREE_BROWSER set to " .
          FILE_LIMIT_OF_TREE_BROWSER . ",${term}${term}", $dflag_files_count);



// NEED TO ADJUST THIS TEST:

//        $current_dir_has_files_to_process = 'false';

    } // end WHILE loop to iterate over files in calling code's base directory



    if ( $dflag_summary )
    {
        echo $term . $term;
        echo "After reaching or passing file limit, array navigable_tree() holds:<br />\n";
        echo "<pre>\n";
        print_r($navigable_tree);
        echo "</pre>\n";
        echo $term . $term;

        echo "Noted filenames from the navigable tree hash:<br />\n";
        echo "<pre>\n";
        foreach ( $navigable_tree as $key => $file_entry )
        {
            echo "$key => " . $file_entry[FILE_NAME] . "\n";
        }
        echo "</pre>\n";
    }



    $_SESSION["zzz_count_of_directories"] = $count_of_directories;
    $_SESSION["zzz_count_of_regular_files"] = $count_of_regular_files;


    show_diag($rname, "returning array to calling code . . .", $dflag_dev);

    echo $term;

    return $navigable_tree;

} // end function build_tree()




function present_files($caller, $file_hierarchy, $options)
{

// VAR BEGIN

    $display_limit = 0;

    $count_elements_shown = 0;

    $hide_empty_dirs = 0;

// diagnostics:

    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_get = DIAGNOSTICS_ON;
    $dflag_session_var = DIAGNOSTICS_ON;
    $dflag_info = DIAGNOSTICS_ON;

    $dflag_links = DIAGNOSTICS_ON;

    $rname = "present_files";

// VAR END


    if ( 1 )
    {
    $dflag_dev = DIAGNOSTICS_OFF;
    $dflag_get = DIAGNOSTICS_OFF;
    $dflag_session_var = DIAGNOSTICS_OFF;
    }


    show_diag($rname, "- STUB FUNCTION - implementation underway 2018-01-24 -", $dflag_dev);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//    [3] => Array
//        (
//            [tree_browser_filename] => d.txt
//            [tree_browser_file_status] => checked
//            [tree_browser_file_type] => file
//            [tree_browser_file_path_in_base_dir] => test-dir/ccc
//            [tree_browser_file_depth_in_base_dir] => 1
//        )
//
//  So we've got this array of files and their attributes.  With this 
//  array we can iterate over every top-level element in the array and
//  create a serial stream of URLs with mark-up, which point optionally
//  to the script which calls this function.  We can add $_GET type
//  parameters to the URLs.  We can change the URL based on file type.
//
//  When a noted file is of type 'directory', a URL will or may be of
//  the form:
//
//     https://[site_name]/path/to/current/dir/[base_directory]/[file_path_in_base_dir]?base_dir=[file_path_in_base_dir]
//
//  When a noted file is a directory which contains some regular files
//  and no directories, then the script-generated URL may point to an
//  alternate script which presents a file list or photo gallery type
//  page . . .
//

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $key = 0;
    $noted_file;

    $name = "";
    $type = "";
    $path = "";
    $depth = 0;

    $indent = "";
    $html_two_space_indent = "&nbsp;&nbsp;";
    $url = "";
    $site = "https://neelanurseries.com";
    $path_from_doc_root = "sandbox";
    $script_name = $options["script_name"];



    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__LIMIT_ELEMENTS_TO_SHOW, $_SESSION) )
    {
        $display_limit = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__LIMIT_ELEMENTS_TO_SHOW];
    }

    if ( $display_limit > 0 )
    {
        show_diag($rname, "- INFO - display limit throttled so we'll show just",
          $dflag_info);
        show_diag($rname, "- INFO - $display_limit elements from file hierarchy hash table:",
          $dflag_info);
    }

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_EMPTY_DIRS, $_SESSION) )
    {
        $hide_empty_dirs = 1;
    }



    foreach ($file_hierarchy as $key => $noted_file)
    {
        $name = $noted_file[FILE_NAME];
        $type = $noted_file[FILE_TYPE];
        $path = $noted_file[FILE_PATH_IN_BASE_DIR];
        $depth = $noted_file[FILE_DEPTH_IN_BASE_DIR];

        $indent = str_repeat($html_two_space_indent, $depth);

        show_diag($rname, "looking at hash as file hierarchy where \$key $key holds filename $name,",
          $dflag_dev);

        if ( $type == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
        {
//            $url = $site . "/" . $path_from_doc_root . "/" . $path . "/" . $name;
            $url = $site . "/" . $path_from_doc_root . "/$script_name?base_dir=$path/$name";
            echo "($key) <a href=\"$url\">$name</a><br />\n";
        }

        if ( $type == KEY_VALUE__FILE_TYPE__IS_FILE )
        {
            $name = preg_replace('/#/', '%23', $name);
            $url = $site . "/$path_from_doc_root/$path/$name";
            echo "($key) <a href=\"$url\">$name</a><br />\n";
        }

        if ( $display_limit > 0 )
        {
            ++$count_elements_shown;
            if ( $count_elements_shown >= $display_limit ) { break; }
        }
    }



} // end function present_files()





function present_files_conventional_view($caller, $file_hierarchy, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to present the directories and files in the top level
//    of the passed file hierarchy hash. . . .
//
//

//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $i = 0;

    $key = 0;
    $key_in_nested_foreach = 0;
    $file_entry = null;
    $file_entry_in_nested_foreach = null;
    $path = "";

    $hide_empty_dirs = 0;

    $hide_files = 0;


// diagnostics:

    $lbuf = "";

    $indent="&nbsp; &nbsp; &nbsp;";

    $dflag_dev        = DIAGNOSTICS_ON;
    $dflag_warning    = DIAGNOSTICS_ON;
    $dflag_legend     = DIAGNOSTICS_ON;
    $dflag_summary    = DIAGNOSTICS_ON;

    $dflag_empty_dirs = DIAGNOSTICS_ON;
    $dflag_nested_loop_l1 = DIAGNOSTICS_ON;
    $dflag_nested_loop_l2 = DIAGNOSTICS_ON;

    $rname = "present_files_conventional_view";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



    show_diag($rname, "starting,", $dflag_dev);

    if ( !(isset($file_hierarchy)) )
    {
        show_diag($rname, "- WARNING - caller sends file hierarchy array which is not set!", $dflag_warning);
        show_diag($rname, "- WARNING - returning early . . .", $dflag_warning);
        return;
    }

    if ( !(is_array($file_hierarchy)) )
    {
        show_diag($rname, "- WARNING - caller sends file hierarchy object which is not an array!", $dflag_warning);
        show_diag($rname, "- WARNING - returning early . . .", $dflag_warning);
        return;
    }


//----------------------------------------------------------------------
// - STEP - parse options from calling code
//----------------------------------------------------------------------

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_EMPTY_DIRS, $_SESSION) )
    {
        $hide_empty_dirs = 1;
    }

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FILE, $_SESSION) )
    {
        $hide_files = 1;
    }




// For development purposes:

    if ( 0 )
    {
        echo "<pre>\n";
        foreach ( $file_hierarchy as $key => $file_entry )
        {
            print_r($file_entry);
            ++$i;
            if ( $i > 40 ) { break; }
        }
        echo "</pre>\n";
    }




    show_diag($rname, "Legend:", $dflag_legend);
    show_diag($rname, "(L1) identifies messages from main iterating loop, foreach construct 1", $dflag_legend);
    show_diag($rname, "(T1) identifies messages are from conditional test 1, in body of loop 1", $dflag_legend);
    show_diag($rname, "(L2) identifies messages from nested foreach construct", $dflag_legend);
    show_diag($rname, "(T2) identifies messages conditional test in body of nested foreach.", $dflag_legend);

    show_diag($rname, "-", $dflag_summary);
    show_diag($rname, "flag 'hide empty dirs' set to $hide_empty_dirs,", $dflag_summary);
    show_diag($rname, "flag 'hide files' set to $hide_files,", $dflag_summary);
    show_diag($rname, "-", $dflag_summary);

    show_diag($rname, "showing dirs followed by files in each directory:", $dflag_dev);
    show_diag($rname, "-", $dflag_dev);

    {
        foreach ( $file_hierarchy as $key => $file_entry )
        {
//            echo "(L1) first foreach looking at element $key:<br />\n";

            if ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            {
                if (( $hide_empty_dirs == 1 ) && ( $file_entry[FILE_COUNT] == 0 ))
                {
// do nothing
                    $lbuf = "'hash entry $key holds '" . $file_entry[FILE_NAME] . "' noted as directory with no files,";
                    show_diag($rname, $lbuf, $dflag_empty_dirs);
                }
                else
                {
                    show_diag($rname, "-zz- found directory which is not empty at hash entry $key,", $dflag_populated_dirs);
                    show_diag($rname, "-zz- path to this dir plus name of this dir are:", $dflag_populated_dirs);
                    $path = $file_entry[FILE_PATH_IN_BASE_DIR] . "/" . $file_entry[FILE_NAME];
                    $file_count = 0;
                    show_diag($rname, "-zz- <font color=\"green\">'$path'</font>,", $dflag_populated_dirs);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// NESTED FOREACH CONSTRUCT:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                    foreach ( $file_hierarchy as $key_in_nested_foreach => $file_entry_in_nested_foreach )
                    {
                        $lbuf = "$indent (L2) top of nested loop looking at hash entry $key_in_nested_foreach . . .";
                        show_diag($rname, $lbuf, $dflag_nested_loop_l2);

                        if ( $file_entry_in_nested_foreach[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_FILE )
                        {
                            if ( $hide_files == 1 )
                            {
// do nothing
echo "-yyyy- hiding file,<br />\n";
                            }
                            else
                            {
                                $lbuf = "$indent (L2) file '" . $file_entry_in_nested_foreach[FILE_NAME] . "',";
                                show_diag($rname, $lbuf, $dflag_nested_loop_l2);

                                show_diag($rname, "$indent (L2) comparing paths:", $dflag_nested_loop_l2);

                                $lbuf = "$indent (L2) 1) $path";
                                show_diag($rname, $lbuf, $dflag_nested_loop_l2);
                                $lbuf = "$indent (L2) 2) " . $file_entry_in_nested_foreach[FILE_PATH_IN_BASE_DIR]; 
                                show_diag($rname, $lbuf, $dflag_nested_loop_l2);
                                show_diag($rname, "-", $dflag_nested_loop_l2);



                                if ( $file_entry_in_nested_foreach[FILE_PATH_IN_BASE_DIR] == $path )
                                {
echo "*** PATHS MATCH ***<br />\n";
                                    echo "&nbsp; &nbsp; &nbsp; (T2) " . $file_entry_in_nested_foreach[FILE_NAME] . "<br />\n";
                                    ++$file_count;
                                }
                            }

                        } // end IF-block to test whether current file is a regular file

                    } // end nested foreach construct, to show files in current directory

                    echo "- YYY - $file_count files in directory <i>$path</i><br />\n";
                }

            } // end IF-block to test whether current file is a directory

        } // end foreach construct, to iterate over files in calling code's file hierarchy

    }




    show_diag($rname, "returning . . .", $dflag_dev);

} // end function present_files_conventional_view()





function present_tree_view($caller, $base_directory, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to take calling code's specified path, and to present
//    it's directories and images files in a web-browsable tree format.
//
//
//  NOTES ON IMPLEMENTATION:  this routine accepts one path to
//    to show, in some specific ways.  To show directories which are
//    located at distinct and separate places on the host's mounted
//    file systems this routine needs to be called once for each
//    directory to show.
//
//
//
//----------------------------------------------------------------------

// VAR BEGIN


    $count_of_files_noted = 0;

    $term = "<br />\n";


// diagnostics:

    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_get = DIAGNOSTICS_ON;
    $dflag_session_var = DIAGNOSTICS_ON;
    $dflag_announce_function_calls = DIAGNOSTICS_ON;

    $rname = "present_tree_view";

// VAR END


    if ( 1 )
    {
    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_get = DIAGNOSTICS_OFF;
    $dflag_session_var = DIAGNOSTICS_OFF;
    }


    show_diag($rname, "starting,", $dflag_dev);

    $file_hierarchy = array();

// - 2018-02-06 TUE - FOR DEVELOPMENT PURPOSES:
    $_SESSION[KEY_NAME__NON_EMPTY_DIRECTORY_ENTRIES] = array();
    $_SESSION[KEY_NAME__DIRECTORY_ENTRIES] = array();


    show_diag($rname, "building tree of directories and files . . .", $dflag_dev);
    $file_hierarchy =& build_tree($caller, $base_directory, $options);

    $count_of_files_noted = count($file_hierarchy);
    show_diag($rname, "function build_tree() returns $count_of_files_noted files noted,", $dflag_dev);

// - 2018-02-06 TUE - FOR DEVELOPMENT PURPOSES:
    show_diag($rname, "file_hierarchy hash table entries of directories holding one or more files:", $dflag_dev);
    if ( 1 )
    {
        echo "<pre>\n";
        print_r($_SESSION[KEY_NAME__NON_EMPTY_DIRECTORY_ENTRIES]);
        echo "</pre>\n";

        echo "hash entries of all directories, those with and without files:";
        echo "<pre>\n";
        print_r($_SESSION[KEY_NAME__DIRECTORY_ENTRIES]);
        echo "</pre>\n";

    }


//    show_diag($rname, "calling stub function to present tree view . . .", $dflag_dev);
//    present_files($caller, $file_hierarchy, $options);

// 2018-01-28 - For development only:
    show_file_hierarchy_paths($rname, $file_hierarchy);
    echo $term . $term;

// 2018-01-29 - For development only:
    show_diag($rname, "calling function to present directories and files in conventional tree view . . .", $dflag_dev);
    present_files_conventional_view($caller, $file_hierarchy, $options);
    echo $term . $term;


    show_diag($rname, "returning to caller . . .", $dflag_dev);

}




// End of file directory-navigation.php

?>
