<?php

//----------------------------------------------------------------------
//
//  PROJECT:  local PHP library functions, CMS focused
//
//  FILE:  directory-navigation.php
//
//  STANDING:  2018-02-12 THIS FILE UNDER DEVELOPMENT, NOT YET WORKING
//    NOR RELEASED FOR PRODUCTION USE! - TMH
//
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

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//  NOTES ON DEVELOPMENT / IMPLEMENTATION:
//   As of 2018 late January the basic directory 'walking' works,
//   with the code not written to keep track of file counts per
//   directory visited.  All directories are shown and all files are
//   alternately shown or hidden, but there is no summary count of files
//   per directory.  A total count of files across directories in the
//   given tree walked or mapped is implemented.
//
//   NEED:  create PHP defines to turn on and off named script
//     +  behaviors, such as total file count.
//
//   Counting files per directory is turning out to be non-trivial,
//   given that this PHP code sees files one at a time, and without
//   much relational information to one another.  Ted has so far opted
//   to solve this task in a non-recursive way, execution wise, and a
//   file tree flattened way data structure wise.  It may be that to
//   avoid the possible performance hit of recursion, that the "file
//   tree flattening" code is more complicated than it's worth.  But
//   there must be some clever way to build what's basically a list
//   of files (both regular files and directories) which is by its
//   data structure nature flat, and have that sufficiently present
//   the original tree structure of the files.
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
    define ("KEY_NAME__NON_EMPTY_DIRECTORY_ENTRIES", "non_empty_directory_entries");
    define ("KEY_NAME__DIRECTORY_ENTRIES", "directory_entries");

// 2018-02-13 - File count per directory work:
    define ("KEY_NAME__FILE_COUNT_PER_LOOP_1", "file_count_per_loop_1");
    define ("KEY_NAME__FILE_COUNT_PER_LOOP_2", "file_count_per_loop_2");
//    define ("KEY_NAME__", "");



//----------------------------------------------------------------------
// - SECTION - PHP functions
//----------------------------------------------------------------------

function show_select_attributes_of_file_tree_hash_entries($rname, $file_hierarchy)
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

    $rname = "show_select_attributes_of_file_tree_hash_entries";


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
//   See also large approximate forty line comment block at end of
//   this file for details of this routines algorithm versions 1 and
//   2 . . . .
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


// "file tree" hash related:
    $key = 0;

    $key_to_present_directory = 0;       // . . . key used to store count of files in containing directory hash entry

    $index_to_latest_not_checked = 0;    // . . . used by loop 2 in algorithm version 1, see notes on routine implementation

    $index_to_earliest_not_checked = 0;  // . . . used by loop 2 in algorithm version 2


// 2018-02-09 - poorly named given name of PHP define:
    $file = KEY_VALUE__DIRECTORY_NAVIGATION__TREE_BROWSER__DEFAULT_FILENAME;

    $file_type = KEY_VALUE__SITE_NAVIGATION__TREE_BROWSER__DEFAULT_FILE_TYPE;

// Used to hold starting directory and all subdirectories as routine maps caller's file tree:
    $current_path = ".";   // . . . this var was named 'file_path_in_base_dir' - TMH

    $file_depth_in_base_dir = 0;



// PHP "file tree" hash to return to calling code:
    $navigable_tree = array();


// Summary and reportable info not distilled in the hash itself:
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
    $dflag_minimal  = DIAGNOSTICS_ON;
    $dflag_summary  = DIAGNOSTICS_ON;

    $dflag_open_dir    = DIAGNOSTICS_ON;

    $dflag_note_file   = DIAGNOSTICS_ON;
    $dflag_check_file  = DIAGNOSTICS_ON;
    $dflag_files_limit = DIAGNOSTICS_OFF;
    $dflag_files_count = DIAGNOSTICS_ON;

    $dflag_file_count_per_directory = DIAGNOSTICS_ON;
    $dflag_base_directory_file_list = DIAGNOSTICS_ON;
    $dflag_loop_1 = DIAGNOSTICS_ON;
    $dflag_loop_2 = DIAGNOSTICS_ON;

//    $rname = "tree_browser";
    $rname = "build_tree";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    if ( 1 )
    {
        show_diag($rname, "turning off most diagnostics . . .", $dflag_minimal);
//        $dflag_announce = DIAGNOSTICS_OFF;    // . . . diagnostics flag for development-related run time comments,
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

        $dflag_file_count_per_directory = DIAGNOSTICS_OFF;
        $dflag_base_directory_file_list = DIAGNOSTICS_OFF;
        $dflag_loop_1 = DIAGNOSTICS_OFF;
        $dflag_loop_2 = DIAGNOSTICS_OFF;
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


// main WHILE-loop set up:

    $current_dir_has_files_to_process = 'true';

    $index_to_latest_not_checked = 0;    // . . . loop set up, this value may already by assigned in var block top of routine

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

//    $j = 0;
    while (( $current_dir_has_files_to_process == 'true' ) && ( $file_limit_not_reached == 'true' ))
//    while ( ($current_dir_has_files_to_process == 'true') && ($file_limit_not_reached == 'true') && ($j < 20) )
    {
//        ++$j;

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - add files from latest checked directory
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "Loop 1 - Noting file types in path '$current_path':", $dflag_loop_1);

        if ( $dflag_loop_1 ) // base directory file list and subdir file lists too - TMH
        {
            echo "From current path got file list:<br />\n";
            echo "<pre>\n";
            print_r($files_in_current_dir);
            echo "</pre>\n";
        }


// 2018-02-13 QUESTION:  is there one good variable name which we can
//  employ here, for the variable which is the key to the next file tree
//  hash entry?  How about 'next_file_tree_hash_entry' or
//  'file_tree_hash_pointer'?   - TMH
//

//        $key_name = $files_noted;   // <-- prepare file hash tree pointer for loop 1,
        $file_tree_hash_entry = $files_noted;   // <-- prepare file hash tree pointer for loop 1,

//        $key_to_present_directory = 0;

        show_diag($rname, "entering loop 1 with file tree hash entry = $file_tree_hash_entry,", $dflag_loop_1);
        show_diag($rname, "and key to present directory = $key_to_present_directory,", $dflag_loop_1);


//----------------------------------------------------------------------
// - FEATURE - Nested loop 1:
//----------------------------------------------------------------------

        foreach ( $files_in_current_dir as $key => $file )
        {
            show_diag($rname, "- TOP OF LOOP 1 -", $dflag_loop_1);

            if (( $file == "." ) || ( $file == ".." ))
            {
                show_diag($rname, "skipping relative directory '$file' . . .", $dflag_note_file);
                continue;
            }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - Check file types . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $file_type = KEY_VALUE__FILE_TYPE__IS_NOT_DETERMINED;

            $current_path_and_file = $current_path . "/" . $file;

            show_diag($rname, "checking file type of '$current_path_and_file' . . .", $dflag_note_file);

//            if ( is_dir($current_path_and_file) )
            if ( is_dir($current_path_and_file) && !is_link($current_path_and_file) )
            {
                show_diag($rname, "- zz1 - noting directory '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_DIRECTORY;
                ++$count_of_directories;

                show_diag($rname, "for development purposes noting directory hash entry '$file_tree_hash_entry' in PHP session variable,", $dflag_dev);
                array_push($_SESSION[KEY_NAME__DIRECTORY_ENTRIES], $file_tree_hash_entry);

            }

            if ( is_file($current_path_and_file) )
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


            if ( is_link($current_path_and_file) )
            {
                show_diag($rname, "- zz3 - noting symlink '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_SYMBOLIC_LINK;
                ++$count_of_regular_files;

                ++$navigable_tree[$key_to_present_directory][FILE_COUNT];
            }



            if ( !(file_exists($current_path_and_file)) )
            {
                show_diag($rname, "- zz3 - noting file '$current_path_and_file' does not exist,", $dflag_note_file);
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


//            $file_tree_hash_entry = $files_noted;  // <-- update file tree hash pointer at end of loop 1

            $navigable_tree[$file_tree_hash_entry] = nn_tree_browser_entry($rname);

            $navigable_tree[$file_tree_hash_entry][FILE_NAME] = $file;
            $navigable_tree[$file_tree_hash_entry][FILE_STATUS] = KEY_VALUE__SITE_NAVIGATION__TREE_BROWSER__DEFAULT_FILE_STATUS;
            $navigable_tree[$file_tree_hash_entry][FILE_TYPE] = $file_type;
            $navigable_tree[$file_tree_hash_entry][FILE_PATH_IN_BASE_DIR] = $current_path;
            $navigable_tree[$file_tree_hash_entry][FILE_DEPTH_IN_BASE_DIR] = $file_depth_in_base_dir;
            $navigable_tree[$file_tree_hash_entry][FILE_COUNT] = 0;

            ++$files_noted;
            $file_tree_hash_entry = $files_noted;  // <-- update file tree hash pointer at end of loop 1, needed here or at top of loop 1 :/

        } // end of loop 1 to note files in current directory


        show_diag($rname, "after loop 1 execution array of noted files holds:", $dflag_note_file);
        if ( $dflag_note_file )
        {
            echo "<pre>\n";
            print_r($navigable_tree);
            echo "</pre>\n";
        }

        if ( $dflag_note_file )
            { echo $term; }

        



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - review most recently added files, advancing to next
//          unchecked directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "Loop 2 - Checking noted files for next directory to map:", $dflag_check_file);
        show_diag($rname, "--------------------------------------------------------", $dflag_check_file);
        show_diag($rname, "hash element pointer set to latest file not checked = $index_to_latest_not_checked,",
          $dflag_check_file);
        show_diag($rname, "hash element pointer set to earliest file not checked = $index_to_earliest_not_checked,",
          $dflag_check_file);



// 2018-02-08 - change algorithm 1 to algorithm 2:
//        if ( isset($navigable_tree[$index_to_latest_not_checked]) )

        $hash_pointer_loop_2 = $index_to_earliest_not_checked;

        $unchecked_directory_found = 'false';

        if ( isset($navigable_tree[$index_to_earliest_not_checked]) )
        {
//            $noted_file = $navigable_tree[$index_to_latest_not_checked];
            $noted_file = $navigable_tree[$index_to_earliest_not_checked];
            $current_path_and_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];
        }
        else
        {
            show_diag($rname, "- WARNING - Odd, but found file tree hash element $index_to_earliest_not_checked not set!",
              $dflag_warning);
            show_diag($rname, "- WARNING - this hash element pointer normally points to elements in middle of hash.",
              $dflag_warning);
            show_diag($rname, "- WARNING - this event may indicate a PHP coding error . . . - TMH",
              $dflag_warning);
            $current_dir_has_files_to_process = 'false';
        }


//----------------------------------------------------------------------
// - FEATURE - Nested loop 2:
//
//  Loop 2 purpose:  
//     *  capture hash key of the latest not-checked directory,
//     *  mark this directory checked
//     *  check all remaining not-directory-type files
//
//  The noted file at this entry point to loop 2 is the file at hash
//  entry '$index_to_earliest_not_checked'.
//
//  Loop 2 must check files from file tree hash entry $index_to_earliest_not_checked
//  to hash entry $file_tree_hash_entry.
//----------------------------------------------------------------------

// removing algorithm 1 test:
//        while ( ($noted_file[FILE_STATUS] == FILE_NOT_CHECKED) && ($current_dir_has_files_to_process == 'true') )
//        while ( $current_dir_has_files_to_process == 'true' )

        $loop_2_iteration = 0;   // Variable $i quick and dirty limit setter on loop iterations - TMH

        while ( ($hash_pointer_loop_2 < $file_tree_hash_entry) && ($loop_2_iteration < 50) )
        {
            ++$loop_2_iteration;

            show_diag($rname, "- TOP OF LOOP 2 - iteration $loop_2_iteration, file_tree_hash_entry = $file_tree_hash_entry, hash_pointer_loop_2 = $hash_pointer_loop_2,", $dflag_check_file);
            show_diag($rname, "checking file '" . $current_path_and_file . "',", $dflag_check_file);


// So we have this shortr-hand variable name 'noted_file' which is a
// copy of the present entry in the file tree hash, the entry that we're
// processing.  But this variable is in a sense read-only in that we
// cannot update the file tree hash by writing this short-hand copy
// of the present hash entry.
//
// Above as primer step to loop 2 we set:
//
//    $hash_pointer_loop_2 = $index_to_earliest_not_checked;
//
//    $noted_file = $navigable_tree[$index_to_earliest_not_checked];
//
//
//  At end of loop 2 we set:
//
//    $noted_file = $navigable_tree[$hash_pointer_loop_2];
//

            if ( $noted_file[FILE_STATUS] == FILE_NOT_CHECKED )
            {
//                if ( ($noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY) && ($unchecked_directory_found == 'false') )
                if ( $noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
                {
                    if ( $unchecked_directory_found == 'false' )
                    {
                        show_diag($rname, "at hash entry $hash_pointer_loop_2 looking at first un-checked directory '" . $noted_file[FILE_NAME] .
                          "',", $dflag_check_file);
                        show_diag($rname, "note:  this will be next directory whose contents loop 1 maps.", $dflag_check_file);

                        $unchecked_directory_found = 'true';
//                        $navigable_tree[$index_to_earliest_not_checked][FILE_STATUS] = FILE_CHECKED;
                        $navigable_tree[$hash_pointer_loop_2][FILE_STATUS] = FILE_CHECKED;
// - NOTE - Here set the path from which loop 1 will next read files:
                        $current_path = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];
                        $index_to_earliest_not_checked = $hash_pointer_loop_2;
show_diag($rname, "setting index to earliest-not-checked-file entry to $index_to_earliest_not_checked,", $dflag_check_file);

                        $key_to_present_directory = $hash_pointer_loop_2;
//                        break;
                    }
                    else
                    {
                        show_diag($rname, "at hash entry $file_tree_hash_entry passing over successive un-checked directory '" . $noted_file[FILE_NAME] .
                          "' for time being,", $dflag_check_file);
                    }
                }

                elseif ( $noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_FILE )
                {
                    show_diag($rname, "marking hash entry $hash_pointer_loop_2 checked, points to regular file,", $dflag_check_file);
                    $navigable_tree[$hash_pointer_loop_2][FILE_STATUS] = FILE_CHECKED;
                }

                elseif ( $noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_SYMBOLIC_LINK )
                {
                    show_diag($rname, "marking hash entry $hash_pointer_loop_2 checked, points to symbolic link,", $dflag_check_file);
                    $navigable_tree[$hash_pointer_loop_2][FILE_STATUS] = FILE_CHECKED;
                }

                else
                {
                    show_diag($rname, "marking hash entry $hash_pointer_loop_2 checked, points to unrecognized file type,", $dflag_check_file);
                    $navigable_tree[$hash_pointer_loop_2][FILE_STATUS] = FILE_CHECKED;
                }


// diagnostics:
//                if ( ($noted_file[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY)
//                  && ($noted_file[FILE_STATUS] == FILE_NOT_CHECKED)
//                  && ($unchecked_directory_found == 1) )
//                {
//                    show_diag($rname, "passing over hash entry $hash_pointer_loop_2, points to directory to check later,", $dflag_check_file);
//                }

            } // end IF-block to process files not yet checked
            else
            {
                show_diag($rname, "passing over checked file,", $dflag_dev);
            }
 

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - Update loop 2 test variable and variable used in loop body:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            ++$hash_pointer_loop_2;
            $noted_file = $navigable_tree[$hash_pointer_loop_2];
            $current_path_and_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Note:  until in loop 2 we find an unchecked directory, we move the
//  pointer to the latest unchecked file tree hash entry up, setting
//  this variable equal to the hash pointer intended to belong to this
//  loop number 2:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            if ( $unchecked_directory_found == 'false' )
            {
                $index_to_latest_unchecked = $hash_pointer_loop_2;
            }

        } // end of loop 2 to check noted files



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Note:  If we got through loop 2 and no unchecked directories are
//  found that means we're done noting files to the file tree hash:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        if ( $unchecked_directory_found == 'false' )
        {
            $current_dir_has_files_to_process = 'false';
        }


//
// At this point by design of file mapping algorithm 2, we have checked
// all non-directory type files noted in the file tree hash.  The most
// recent checked directory resides at the end of the path at which we
// want loop 1 to read:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


        if ( $current_dir_has_files_to_process == 'true' )
        {
            show_diag($rname, "After loop 2 updating array named 'files_in_current_dir' using path '$current_path' . . .",
              $dflag_dev);
            $files_in_current_dir =& list_of_filenames_by_pattern($rname, $current_path, "/(.*)/");
        }
        else
        {
            show_diag($rname, "loop 2 finds no unchecked directories, therefore no more files to add to file tree hash!",
              $dflag_dev);
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


    } // end WHILE loop to iterate over files in calling code's base directory



    if ( $dflag_summary )
    {
        echo $term . $term;
        echo "Following execution of file tree mapping loop, hash of files and file attributes holds:<br />\n";
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
//  EXPECTS:
//
//  RETURNS:
//
//  PRODUCES:  HTML hyperlinks to directories and or files, to  be formatted 
//    per page visitor's selection of a given directory or file,
//   and possibly per some file and image gallery display options
//   provided on the web page.  All this formatting yet underway as
//   of 2018-02-13 Tuesday . . .  - TMH
//
//  NOTES ON IMPLEMENTATION:
//   The default "conventional" file tree view which this function
//   produces shows all files in the page visitor's current working
//   directory.
//
//   This function shows directories from the file hierarchy hash as
//   symlinks to the same given page this script helps to create, with
//   with each directory passed to the web server as the new current
//   working directory.  This script uses HTTP 'get' method to do this.
//
//
//
//
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $hide_empty_dirs = 0;

    $hide_files = 0;

// 2018-02-13 - added:
    $files_in_cwd = array();

    $url = "";
    $site = "https://neelanurseries.com";
    $path_from_doc_root = "sandbox";
    $script_name = $options["script_name"];


// diagnostics:

    $lbuf = "";

    $indent="&nbsp; &nbsp; &nbsp;";

    $dflag_dev        = DIAGNOSTICS_ON;
    $dflag_warning    = DIAGNOSTICS_ON;
    $dflag_legend     = DIAGNOSTICS_OFF;
    $dflag_summary    = DIAGNOSTICS_ON;

    $dflag_empty_dirs = DIAGNOSTICS_ON;
    $dflag_nested_loop_l1 = DIAGNOSTICS_ON;
    $dflag_nested_loop_l2 = DIAGNOSTICS_ON;

    $dflag_source_of_cwd = DIAGNOSTICS_ON;

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


// Look for current working directory in a couple of places:

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $_GET) )
    {
        show_diag($rname, "got current working directory via HTTP get() method!", $dflag_source_of_cwd);
        $cwd = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $_SESSION) )
    {
        show_diag($rname, "got current working directory via PHP session variable!", $dflag_source_of_cwd);
        $cwd = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else    
    {
        show_diag($rname, "falling back to current working directory from first entry in file tree hash:", $dflag_source_of_cwd);
        $cwd = $file_hierarchy[0][FILE_PATH_IN_BASE_DIR];
    }

    show_diag($rname, "\$cwd set to '$cwd',", $dflag_dev);



    $basedir = $file_hierarchy[0][FILE_PATH_IN_BASE_DIR];



    {
//
//  Loop 1 to gather files in current working directory, for sorting:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        foreach ( $file_hierarchy as $key => $file_entry )
        {
//            if ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            if ( basename($file_entry[FILE_PATH_IN_BASE_DIR]) == $cwd )
            {
//                echo "<i>file '" . $file_entry[FILE_NAME] . "' in directory '" . $file_entry[FILE_PATH_IN_BASE_DIR] . "',</i><br />\n";
//                $files_in_cwd[$file_entry[FILE_NAME]] = $key;
//                $files_in_cwd[$key] = $file_entry[FILE_NAME];
                $files_in_cwd[$file_entry[FILE_NAME]] = $file_entry;

            } // end IF-block to test whether current file is located in current working directory

        } // end foreach construct, to iterate over files in calling code's file hierarchy


        if ( 0 )
        {
        show_diag($rname, "unsorted files in current directory include:", $dflag_dev);
        echo "<pre>\n";
        print_r($files_in_cwd);
        echo "</pre>\n";

        sort($files_in_cwd);

        show_diag($rname, "same files sorted:", $dflag_dev);
        echo "<pre>\n";
        print_r($files_in_cwd);
        echo "</pre>\n";
        }

        foreach ( $files_in_cwd as $key => $file_entry )
        {
            $path = $file_entry[FILE_PATH_IN_BASE_DIR];
            $filename = $file_entry[FILE_NAME];
            $file_count = $file_entry[FILE_COUNT];

            if ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            {
                $url = "$site/$path_from_doc_root/$script_name?base_dir=$basedir&cwd=$path/$name";
            }
            else
            {
                $url = "$site/$path_from_doc_root/$path/$filename";
            }

            $link_text = $filename;
            $line_to_browser = "<a href=\"$url\">" . $link_text . "</a><br />\n";
            echo $line_to_browser;
        }


    }




    show_diag($rname, "returning . . .", $dflag_dev);

} // end function present_files_conventional_view()





function present_tree_view($caller, $base_directory, $options)  // <-- present tree view of directories and files
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
    if ( 0 )
    {
        show_diag($rname, "\"file tree\" hash entries of non-empty directories:", $dflag_dev);
        echo "<pre>\n";
        print_r($_SESSION[KEY_NAME__NON_EMPTY_DIRECTORY_ENTRIES]);
        echo "</pre>\n";

        show_diag($rname, "all hash entries which note directories:", $dflag_dev);
        echo "<pre>\n";
        print_r($_SESSION[KEY_NAME__DIRECTORY_ENTRIES]);
        echo "</pre>\n";

        show_diag($rname, "file tree hash in full:", $dflag_dev);
        echo "<pre>\n";
        print_r($file_hierarchy);
        echo "</pre>\n";
    }


//    show_diag($rname, "calling stub function to present tree view . . .", $dflag_dev);
//    present_files($caller, $file_hierarchy, $options);

// 2018-01-28 - For development only:
//    show_select_attributes_of_file_tree_hash_entries($rname, $file_hierarchy);
//    echo $term . $term;

// 2018-01-29 - For development only:
    show_diag($rname, "calling function under development to present files in tree view . . .", $dflag_dev);
    present_files_conventional_view($caller, $file_hierarchy, $options);
    echo $term . $term;


    show_diag($rname, "returning to caller . . .", $dflag_dev);

}




// --- END OF CODE ---



// 2018-02-13 - Implementation notes from function build_tree() . . .

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
// to loop 2, which searches the hash of files for directories.  For
// each directory loop 2 finds it returns script execution to loop 1 so
// that those directory's files are added to the hash.  Upon finding a
// noted directory in the hash, however, loop 2 does not immediately
// end and return control to loop 1, but rather checks all remaining
// noted but not checked files in the hash.
//
// Loop 1 and loop 2 can see which files have been reviewed or checked 
// by loop 2.  Loop 1 always adds all files in the current path.  The
// key word here is 'all'.  In the first algorithm design,
// loop 2 did not always check all files in the file tree hash.
// Loop 2 wass designed to direct script execution back to loop one at
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




// End of file directory-navigation.php

?>
