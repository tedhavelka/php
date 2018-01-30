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

    define("FILE_CHECKED", KEY_VALUE__FILE_STATUS__CHECKED);
    define("FILE_NOT_CHECKED", KEY_VALUE__FILE_STATUS__NOT_CHECKED);

//    define("FILE_LIMIT_OF_TREE_BROWSER", 4096);
    define("FILE_LIMIT_OF_TREE_BROWSER", 2047);

// Note:  best place PHP defines outside of functions, as functions
//  called more than once by one and same calling script lead to PHP
//  interpreter / engine notice of defined value already defined on
//  line x . . .   - TMH





//----------------------------------------------------------------------
// - SECTION - PHP functions
//----------------------------------------------------------------------

function show_file_hierarchy_paths($rname, $file_hierarchy)
{

    $i = 0;

    $file_entry_count = count($file_hierarchy);

    $rname = "show_file_hierarchy_paths";



    show_diag($rname, "received hash with $file_entry_count file entries,", DIAGNOSTICS_ON);
    show_diag($rname, "showing full paths and files:", DIAGNOSTICS_ON);

    echo "<pre>\n";

    foreach ( $file_hierarchy as $key => $value )
    {
        if ( is_array($value))
        {
            show_diag($rname, $value[FILE_PATH_IN_BASE_DIR], DIAGNOSTICS__MESSAGE_ONLY);
            echo "item $i)" . $value[FILE_PATH_IN_BASE_DIR] . "/" . $value[FILE_NAME] . "\n";
        }
        else
        {
            print_r($value);
        }

        ++$i;
    }

    echo "</pre>\n";

    show_diag($rname, "done.", DIAGNOSTICS_ON);
}




// function tree_browser($caller, $base_directory, $options)
function &build_tree($caller, $base_directory, $options)
{

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

//    $show_usage = false;

    $handle = NULL;                      // . . . file handle to directory to search for navigation menu item files,

    $current_dir_has_files_to_process = 'true';

    $files_noted = 0;

    $file_limit_not_reached = 'true';

    $files_in_current_dir = array();

    $key = 0;

    $file = KEY_VALUE__SITE_NAVIGATION__TREE_BROWSER__DEFAULT_FILENAME;

    $file_type = KEY_VALUE__SITE_NAVIGATION__TREE_BROWSER__DEFAULT_FILE_TYPE;

    $file_path_in_base_dir = ".";

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

//    $rname = "tree_browser";
    $rname = "build_tree";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    if ( 1 )
    {
    $dflag_announce = DIAGNOSTICS_OFF;    // . . . diagnostics flag for development-related run time comments,
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
    }

    show_diag($rname, "- 2018-01-23 - ROUTINE IMPLEMENTATION UNDERWAY -", $dflag_dev);
    show_diag($rname, "starting,", $dflag_dev);
    show_diag($rname, "called by '$caller' with base directory '$base_directory',", $dflag_dev);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - sanity checking base directory from caller:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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
        echo "From base directory got file list:<br />\n";
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

//    $file_path_in_base_dir = ".";
    $file_path_in_base_dir = $base_directory;


// Primary file-processing loop:

    while (( $current_dir_has_files_to_process == 'true' ) && ( $file_limit_not_reached == 'true' ))
    {
//        show_diag($rname, "flag \$current_dir_has_files_to_process:", $dflag_dev);
//        var_dump($current_dir_has_files_to_process);
//        var_dump($file_limit_not_reached);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - add files from latest checked directory in tree to present:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "Loop 1 - Noting file types in '$file_path_in_base_dir' relative to basedir:",
          $dflag_note_file);

        foreach ( $files_in_current_dir as $key => $file )
        {

// NEED TO CHANGE NEXT TEN OR SO CODE LINES SO THERE'S ONLY ONE
// CONTINUE TO OCCUR TO HANDLE '.' and '..' INSTANCES WE WANT TO
// EXCLUDE:

//            if (( $file == "." ) || ( 0 == preg_match($file, '@(.*)(\/)(\.$)@') ))
/*
            if ( $file == "." )
            {
                show_diag($rname, "skipping relative directory '$file' . . .", $dflag_note_file);
                continue;
            }
*/

//            $file_depth_in_base_dir = substr_count($file_path_in_base_dir, "/");
//            if ( $file_depth_in_base_dir == 0 ) 
//            {
                if (( $file == "." ) || ( $file == ".." ))
//                if ( $file == ".." )
                {
                    show_diag($rname, "skipping relative directory '$file' . . .", $dflag_note_file);
                    continue;
                }
//            }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - Check file types . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $file_type = KEY_VALUE__FILE_TYPE__IS_NOT_DETERMINED;

            $path_to_latest_file = $file_path_in_base_dir . "/" . $file;

//            show_diag($rname, "checking file type of '$file' . . .", $dflag_note_file);
            show_diag($rname, "checking file type of '$path_to_latest_file' . . .", $dflag_note_file);

            if ( is_dir($path_to_latest_file) )
            {
                show_diag($rname, "- zz1 - noting directory '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_DIRECTORY;
                ++$count_of_directories;
            }

            if ( is_file($path_to_latest_file) )
            {
                show_diag($rname, "- zz2 - noting file '$file',", $dflag_note_file);
                $file_type = KEY_VALUE__FILE_TYPE__IS_FILE;
                ++$count_of_regular_files;
            }

            if ( !(file_exists($path_to_latest_file)) )
            {
                show_diag($rname, "- zz3 - noting file '$file' does not exist,", $dflag_note_file);
                $file_type = "non-existent";
            }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - figure file depth in base directory, used to determine
//          whether to provide link to parent directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $file_depth_in_base_dir = substr_count($file_path_in_base_dir, "/");

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
            $navigable_tree[$key_name][FILE_PATH_IN_BASE_DIR] = $file_path_in_base_dir;
            $navigable_tree[$key_name][FILE_DEPTH_IN_BASE_DIR] = $file_depth_in_base_dir;

            ++$files_noted;

        } // end of loop 1 to note files in current directory

        if ( $dflag_format )
            { echo $term; }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - review most recently added files, advancing to next
//          unchecked directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "Loop 2 - Checking noted files for directories to map,",
          $dflag_check_file);
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

        while ( ($noted_file[FILE_STATUS] == FILE_NOT_CHECKED) && 
                ($current_dir_has_files_to_process == 'true') )
        {
            show_diag($rname, "checking file '" . $path_to_latest_file . "',", $dflag_check_file);
            $navigable_tree[$index_to_latest_not_checked][FILE_STATUS] = FILE_CHECKED;
            show_diag($rname, "marked file '" . $path_to_latest_file . "' as '" . $noted_file[FILE_STATUS] . ",",
              $dflag_check_file);
            ++$index_to_latest_not_checked;


            if ( $noted_file[FILE_TYPE] == "directory" )
            {
                show_diag($rname, "file " . $noted_file[FILE_NAME] . " is a directory!  breaking out of file review loop . . .",
                  $dflag_check_file);
                $file_path_in_base_dir = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];
                break;
            }

            if ( $noted_file[FILE_TYPE] == "file" )
            {
                show_diag($rname, "'" . $file_path_in_base_dir . "' is a regular file,",
                  $dflag_check_file);
            }

            if ( $noted_file[FILE_TYPE] == "non-existent" )
            {
                show_diag($rname, "'" . $file_path_in_base_dir . "' is non-existent,",
                  $dflag_check_file);
            }
            else
            {
                show_diag($rname, "'" . $file_path_in_base_dir . "' is neither directory nor regular file,",
                  $dflag_check_file);
            }

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

        } // end of loop 2 to check noted files



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - if latest noted file is directory, read its contents:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        show_diag($rname, "after file-review loop hash element pointer set to $index_to_latest_not_checked,",
          $dflag_check_file);

        $path_to_latest_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];

        if ( is_dir($path_to_latest_file) )
        {
            show_diag($rname, "calling for list of files in '$path_to_latest_file' . . .", $dflag_open_dir);
            $files_in_current_dir =& list_of_filenames_by_pattern($rname, $path_to_latest_file, "/(.*)/");
            sort($files_in_current_dir);
//            array_push($files_in_current_dir, "---MARKER---");
        }
        else
        {
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

        show_diag($rname, "so far have noted $files_noted files and FILE_LIMIT_OF_TREE_BROWSER holds " .
          FILE_LIMIT_OF_TREE_BROWSER . ",", $dflag_files_count);



// NEED TO ADJUST THIS TEST:

//        $current_dir_has_files_to_process = 'false';

    } // end WHILE loop to iterate over files in base directory to present in tree view



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

    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_get = DIAGNOSTICS_ON;
    $dflag_session_var = DIAGNOSTICS_ON;

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

    }



} // end function present_files()





function present_files_conventional_view($caller, $file_hierarchy, $options)
{

// VAR BEGIN

    $i = 0;

    $key = 0;
    $key_in_nested_foreach = 0;
    $file_entry = null;
    $file_entry_in_nested_foreach = null;
    $path = "";

// diagnostics:

    $dflag_dev     = DIAGNOSTICS_ON;
    $dflag_warning = DIAGNOSTICS_ON;

    $rname = "present_files_conventional_view";

// VAR END



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


    show_diag($rname, "showing dirs followed by files in each directory:", $dflag_dev);

    {
        foreach ( $file_hierarchy as $key => $file_entry )
        {
//            echo "(L1) first foreach looking at element $key:<br />\n";

            if ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            {
                $path = $file_entry[FILE_PATH_IN_BASE_DIR] . "/" . $file_entry[FILE_NAME];
//                echo "     current entry notes a directory,<br />\n";
//                echo "     current entry's path:  '$path'<br />\n";
//                echo "     current entry's dirname:  '" . $file_entry[FILE_NAME] . "'<br />\n";

                $file_count = 0;

                foreach ( $file_hierarchy as $key_in_nested_foreach => $file_entry_in_nested_foreach )
                {
//                    echo "nested foreach looking at element $key_in_nested_foreach with noted path '" .
//                      $file_entry_in_nested_foreach[FILE_PATH_IN_BASE_DIR] . "':<br />\n";
                    if ( $file_entry_in_nested_foreach[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_FILE )
                    {
//                        echo "(L2) nested foreach looking at at regular file '" . $file_entry_in_nested_foreach[FILE_NAME] . "',<br />\n";
//                        echo "     comparing:<br />\n";
//                        echo "<pre>'$path' with<br />\n";
//                        echo "'" . $file_entry_in_nested_foreach[FILE_PATH_IN_BASE_DIR] . "'</pre><br />\n";

                        if ( $file_entry_in_nested_foreach[FILE_PATH_IN_BASE_DIR] == $path )
                        {
//                            echo "- ZZZ - FOUND file in latest encountered directory!<br />\n";
                            echo "&nbsp; &nbsp; &nbsp; (T2) " . $file_entry_in_nested_foreach[FILE_NAME] . "<br />\n";
//                              . " noted in directory " . $file_entry_in_nested_foreach[FILE_PATH_IN_BASE_DIR] . "<br />\n";
                            ++$file_count;
                        }

                    } // end IF-block to test whether current file is a regular file

                } // end nested foreach construct, to show files in current directory

                echo "- YYY - $file_count files in directory <i>$path</i><br />\n";

            } // end IF-block to test whether current file is a directory

        } // end foreach construct, to iterate over files in calling code's file hierarchy

    }




    show_diag($rname, "returning . . .", $dflag_dev);

} // end function present_files_conventional_view()





function present_tree_view($caller, $base_directory, $options)
{

// VAR BEGIN


    $count_of_files_noted = 0;

    $term = "<br />\n";


// diagnostics:

    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_get = DIAGNOSTICS_ON;
    $dflag_session_var = DIAGNOSTICS_ON;

    $rname = "present_tree_view";

// VAR END


    if ( 1 )
    {
    $dflag_dev = DIAGNOSTICS_OFF;
    $dflag_get = DIAGNOSTICS_OFF;
    $dflag_session_var = DIAGNOSTICS_OFF;
    }


    show_diag($rname, "starting,", $dflag_dev);

    $file_hierarchy = array();

    show_diag($rname, "building tree of directories and files . . .", $dflag_dev);
    $file_hierarchy =& build_tree($caller, $base_directory, $options);

    $count_of_files_noted = count($file_hierarchy);
    show_diag($rname, "function build_tree() returns $count_of_files_noted files noted,", $dflag_dev);

    show_diag($rname, "calling stub function to present tree view . . .", $dflag_dev);
    present_files($caller, $file_hierarchy, $options);

// 2018-01-28 - For development only:
    show_file_hierarchy_paths($rname, $file_hierarchy);
    echo $term . $term;

// 2018-01-29 - For development only:
    present_files_conventional_view($caller, $file_hierarchy, $options);
    echo $term . $term;


    show_diag($rname, "returning to caller . . .", $dflag_dev);

}




// End of file directory-navigation.php

?>
