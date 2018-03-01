<?php

//----------------------------------------------------------------------
//
//  PROJECT:  local PHP library functions, content management focused
//
//  FILE:  directory-navigation.php
//
//  STANDING:  2018-02-12 THIS FILE UNDER DEVELOPMENT, NOT FULLY
//    WORKING AND NOT RELEASED FOR PRODUCTION USE! - TMH
//
//
//
//  TO-DO:
//
//  2018-01-24:
//    [p]  add symbolic link file type detection, and optional skipping
//         of symbolic links in build_tree() function,
//
//       ...symbolic link detection in place, option to hide symlinks
//         not yet implemented.
//
//  2018-02-15:
//    [ ]  add a search feature to provide search for files by name
//         or text patterns,
//
//  2018-02-16:
//    [ ]  investigate how a mapped file hierarchy can be stored in
//         one or a few files, to avoid frequent or unneeded repeat
//         file system accesses to display a given file tree,
//
//    [ ]  investigate how compress potentially long path names into
//         some kind of CRC or numeric hash value, for shorter
//         URLs and shorter keynames of keys to PHP ordered maps,
//
//    [ ]  ensure that following variables,
//
//    $site = "https://neelanurseries.com";
//    $path_from_doc_root = "sandbox";
//    $script_name = $options["script_name"];
//
//         are assigned values in the most sensible and scalable
//         way possible.  First two of three are hard-code assigned
//         as of 2018 February 16 . . . TMH
//
//    [ ]  device good way to manage HTTP get values which need
//         appear in script's self calling URLs . . .
//
//  2018-02-22:
//    [ ]  review all PHP local library calls to functions which
//         return arrays and assure that '=&' is used to obtain a
//         a bound reference to the original data, where appropriate,
//         and not to assign a copy of the data, which may lose
//         synchronization with the original data set,
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
//
//  REFERENCES:
//
//    *  http://php.net/manual/en/function.reset.php . . . set internal pointer of array to its first element
//
//    *  http://php.net/manual/en/function.array-keys.php
//
//    *  http://php.net/manual/en/language.references.return.php
//
//    *  http://php.net/manual/en/types.comparisons.php#types.comparisions-loose
//
//    *  https://commons.wikimedia.org/wiki/File:ASCII-Table-wide.svg
//
//
//
//  AUTHORS AND CONTRIBUTORS:
//
//    Ted Havelka        ted@cs.pdx.edu        (TMH)
//
//



//----------------------------------------------------------------------
// - SECTION - PHP include directives
//----------------------------------------------------------------------

    require_once '/opt/nn/lib/php/defines-nn.php';

    require_once '/opt/nn/lib/php/diagnostics-nn.php';

    require_once '/opt/nn/lib/php/file-and-directory-routines.php';

    require_once '/opt/nn/lib/php/text-manipulation.php';

    require_once '/opt/nn/lib/php/page-building-routines.php';


    require_once('./lib/phpThumb/phpthumb.class.php');



//----------------------------------------------------------------------
// - SECTION - PHP script constants
//----------------------------------------------------------------------

// 2018-01-23 - added to improve source code readability in function
// tree_browser().
// NOTE:  these shorter defined names or labels will collide if defined
// elsewhere in this local PHP library.  - TMH

    define("FILE_NAME",              KEY_NAME__DIRECTORY_NAVIGATION__FILE_NAME);
    define("FILE_STATUS",            KEY_NAME__DIRECTORY_NAVIGATION__FILE_STATUS);
    define("FILE_TYPE",              KEY_NAME__DIRECTORY_NAVIGATION__FILE_TYPE);
    define("FILE_PATH_IN_BASE_DIR",  KEY_NAME__DIRECTORY_NAVIGATION__FILE_PATH_IN_BASE_DIR);
    define("FILE_DEPTH_IN_BASE_DIR", KEY_NAME__DIRECTORY_NAVIGATION__FILE_DEPTH_IN_BASE_DIR);
    define("FILE_COUNT",             KEY_NAME__DIRECTORY_NAVIGATION__COUNT_OF_REGULAR_FILES);

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

    define ("LIMIT_TO_100", 100);
    define ("LIMIT__BUILD_TREE__LOOP_2_ITERATION", 300);



//----------------------------------------------------------------------
// - SECTION - PHP functions
//----------------------------------------------------------------------

function &file_tree_view_mode_urls($rname, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to make and send to web browser links to this code's
//   calling script, a link for each supported file tree view mode.
//   File tree view modes include:
//
//  EXPECTS:  to work propery this function at minimum needs to know
//   the site for which it is generating mark-up and content, the
//   path from the web document root to the script which calls this
//   function, and the name of that script.  In summary this function
//   expects,
//
//     *  resolvable web URL,
//     *  path from server's web document root to calling script
//     *  name of script calling this function
//
//   Added help to this function to know also the base directory, or
//   "most parent" directory of the file hierarchy that calling code
//   is requesting to show as part of a web page, and the current
//   working directory in that file hierarchy to which a given user
//   has navigated so far . . .
//
//     KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_CWD_ABBR
//     KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_DIRECTORIES_AND_FILE_COUNTS_ABBR
//     KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_DIRECTORIES_TO_DEPTH_N_ABBR
//     KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_GALLERY_ABBR
//
//
//  RETURNS:  an array of URLs with HTML mark-up to create links
//    on a typical web page.
//
//
//
//----------------------------------------------------------------------

// VAR BEGIN

    $site = "https://neelanurseries.com";
    $path_from_doc_root = "sandbox";
    $script_name = $options["script_name"];
    $url = "";

// variables to hold 'GET' method values appended to each URL:
    $basedir = "";
    $cwd = "";
    $view_mode = "";

    $array_of_view_modes = null;
    $array_of_urls = null;
    $count_of_urls = 0;

// diagnostics:

    $dflag_dev     = DIAGNOSTICS_ON;
    $dflag_warning = DIAGNOSTICS_ON;
    $dflag_var_basedir = DIAGNOSTICS_OFF;
    $dflag_view_modes  = DIAGNOSTICS_OFF;

    $rname = "file_tree_view_mode_urls";

// VAR END


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - obtain base directory and current working directory from
//          PHP session variable:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR, $_GET) )
    {
        show_diag($rname, "obtaining base directory via HTTP 'get' method . . .", $dflag_var_basedir);
        $basedir = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $_SESSION) )
    {
        show_diag($rname, "obtaining base directory via PHP session variable . . .", $dflag_var_basedir);
        $basedir = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $options) )
    {
        show_diag($rname, "obtaining base directory via passed options variable . . .", $dflag_var_basedir);
        $basedir = $options[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    else
    {
//        show_diag($rname, "obtaining base directory passed hash of files in current working directory . . .", $dflag_var_basedir);
//        $first_file_tree_hash_entry = reset($files_in_cwd);
//        $basedir = $first_file_tree_hash_entry[FILE_PATH_IN_BASE_DIR];
        show_diag($rname, "- WARNING - unable to determine base directory of files to show", $dflag_warning);
        show_diag($rname, "  +  via get method, PHP session var or calling code options!", $dflag_warning);
//        show_diag($rname, "  +  returning early to calling code . . .", $dflag_warning);
//        return;
        show_diag($rname, "  +  FOR DEBUGGING SETTING \$basedir TO \"images/ken-bastow\" . . .", $dflag_warning);
        $basedir = "images/ken-bastow";
    }


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR, $_GET) )
    {
        $cwd = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $_SESSION) )
    {
        $cwd = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $options) )
    {
        $cwd = $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else
    {
        show_diag($rname, "- WARNING - unable to determine current working directory from", $dflag_warning);
        show_diag($rname, "  +  via get method, PHP session var or calling code options!", $dflag_warning);
//        show_diag($rname, "  +  returning early to calling code . . .", $dflag_warning);
//        return;
        show_diag($rname, "  +  FOR DEBUGGING SETTING \$cwd TO \"images/ken-bastow\" . . .", $dflag_warning);
        $cwd = "images/ken-bastow";
    }


// NEED TO SANITY CHECK THIS EXPECTED PASSED VALUE, BEFORE
//  +  MAKING FOLLOWING ASSIGNMENT:
    $array_of_view_modes = $options[ARRAY_NAME__ARRAY_OF_VIEW_MODES];

    $array_of_urls = array();


// DIAG:

    if ( $dflag_view_modes )
    {
        echo "<i>Array of view modes holds:<br />\n";
        echo "<pre>\n";
        print_r($array_of_view_modes);
        echo "</pre>\n";
        echo "</i>\n";
    }



//
// - STEP - build URL to file tree presenting script, with view mode:
//  PROBABLY we'll use a foreach construct to iterate over the
//  file tree view modes which we have implemented. . .

    foreach ( $array_of_view_modes as $key => $view_mode )
    {
        if ( $dflag_view_modes )
        {
            show_diag($rname, "building URL and appending view mode '$view_mode' as GET parameter . . .",
              $dflag_view_modes);
        }

//
// NOTE:  ampersand '&' character has ASCII value 0x26, but this makes
//  the ampersand literal in a web URL which breaks the 'get method' of
//  passing variables to the web server or web browser.  So we'll stick
//  with the '&' notation in this code to construct a URL, in spite of
//  Firefox 52.2.0's page source view marking these in red . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $url = "$site/$path_from_doc_root/$script_name?"
          . KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR . "=$basedir&"
//          . KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR . "=$basedir%26"
          . KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR . "=$cwd&"
//          . KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR . "=$cwd%26"
          . KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR
          . "=$view_mode";

          array_push($array_of_urls, $url);
    }


//    show_diag($rname, "URL list built, actual display moving to another routine . . .", $dflag_dev);

    return $array_of_urls;


} // end function file_tree_view_mode_urls()





function present_file_tree_view_mode_links($caller, $options)
{
//----------------------------------------------------------------------
//
//----------------------------------------------------------------------

// VAR BEGIN

    $array_of_urls = null;

    $count_of_urls = 0;

// when available we use this variable to hold current view mode and highlight it:
    $current_view_mode = "";

// This one needs explanation and code factoring work:
    $array_of_view_modes = null;

    $list_layout = KEY_VALUE__LAYOUT__VERTICAL; 
    $list_justification = KEY_VALUE__JUSTIFICATION__RIGHT; 
    $list_alignment = KEY_VALUE__ALIGNMENT__TOP; 
    $mark_up_between_items = "mark-up between items";


// diagnostics:

    $dflag_announce = DIAGNOSTICS_OFF;
    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_current_view_mode = DIAGNOSTICS_OFF;

    $rname = "present_file_tree_view_mode_links";

// VAR END


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - set some options to lay out several links in a document
//  section (1), as a vertical list, right-justified . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// // here in key value 'layout' affects block element attributes:
//     $options[KEY_NAME__LAYOUT__SECTION_OF_DOCUMENT] = KEY_VALUE__LAYOUT__HORIZONTAL;
// // here in key value 'layout' affects mark-up between text items of list:
//     $options[KEY_NAME__LAYOUT__LIST_OF_TEXT_ITEMS] = KEY_VALUE__LAYOUT__VERTICAL;
//     $options[KEY_NAME__JUSTIFICATION__LIST_OF_TEXT_ITEMS] = KEY_VALUE__JUSTIFICATION__RIGHT;
//     $options[KEY_NAME__ALIGNMENT__LIST_OF_TEXT_ITEMS] = KEY_VALUE__ALIGNMENT__TOP;

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR, $_GET) )
    {
        $current_view_mode = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE, $options) )
    {
        $current_view_mode = $options[KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE];
    }
    else
    {
        $current_view_mode = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_CWD_ABBR;
    }



    show_diag($rname, "starting,", $dflag_announce);


// 2018-02-19 - Somehow we need to make this function knowledgable of
// the link text which goes with each link . . .

    $array_of_view_modes = array();
    $array_of_view_modes[0] = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_CWD_ABBR;
    $array_of_view_modes[1] = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_DIRECTORIES_AND_FILE_COUNTS_ABBR;
    $array_of_view_modes[2] = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_DIRECTORIES_TO_DEPTH_N_ABBR;
    $array_of_view_modes[3] = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_GALLERY_ABBR;

    $options[ARRAY_NAME__ARRAY_OF_VIEW_MODES] = $array_of_view_modes;


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - obtain list of URLs . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $array_of_urls =& file_tree_view_mode_urls($caller, $options);


// - STEP - in fixed, unconditional manner open a horizontal document section:
    open_document_section_with_margin_block_elements($rname, $options);


    echo "<!-- This paragraph element gives us text alignment within present block element -->\n<p style=\"text-align:right\">\n";



// - STEP - generate mark-up for layout of list:
//  *  http://php.net/manual/en/types.comparisons.php#types.comparisions-loose

    switch ($list_layout) {
        case KEY_VALUE__LAYOUT__HORIZONTAL:
            $mark_up_between_items = "&nbsp; &nbsp; : &nbsp; &nbsp;";
            break;
        case KEY_VALUE__LAYOUT__VERTICAL:
            $mark_up_between_items = "<br />\n";
            break;
        default:
            $mark_up_between_items = "&nbsp; &nbsp; : &nbsp; &nbsp;";
    }



    $count_of_urls = count($array_of_urls);

//    show_diag($rname, "obtained array with $count_of_urls URLs to show,", $dflag_dev);
    echo "File viewing modes:<br />\n";

    if ( $count_of_urls > 0 )
    {
        foreach ( $array_of_urls as $key => $url )
        {
// 2018-02-19 NOTE:  link text not yet accounted for correctly here!
//            $link_text = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_CWD;
            $link_text = preg_replace('/-/', ' ', $array_of_view_modes[$key]);

            show_diag($rname, "comparing array of view modes entry '$array_of_view_modes[$key]' with current view mode '$current_view_mode' . . .", $dflag_current_view_mode);
            if ( $array_of_view_modes[$key] === $current_view_mode )
            {
                $link = "<a href=\"$url\"><b>" . $link_text . " *</b></a>";
            }
            else
            {
                $link = "<a href=\"$url\">" . $link_text . " <font color=\"lightgrey\">*</font></a>";
            }

//            echo "$line_to_browser<br />\n";
            if ( $key == 0 )
            {
                $line_to_browser = $link;
            }
            elseif ( $key < ($count_of_urls - 0))
            {
                $line_to_browser = $line_to_browser . $mark_up_between_items . $link;
            }
            else
            {
                $line_to_browser = $line_to_browser . $link;
            }
        }

        echo "$line_to_browser<br />\n";
    }


    echo "</p>\n";


// - STEP - in fixed, unconditional manner complete and close horizontal document section for caller's list:
    close_document_section_with_margin_block_elements($rname, $options);

    show_diag($rname, "done, returning . . .", $dflag_announce);

} // end function present_file_tree_view_mode_links()





function show_select_attributes_of_file_tree_hash_entries($rname, $file_hierarchy) // - FOR DEVELOPMENT -
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





function nn_tree_browser_entry($caller)
{
//
// 2018-02-15 - NEED to add a few key+value pairs to the array built here,
//   see call to this function in function named &build_tree():
//

    $tree_browser_hash_element = array(
      KEY_NAME__DIRECTORY_NAVIGATION__FILE_NAME => KEY_VALUE__DEFAULT_FILENAME,
      KEY_NAME__DIRECTORY_NAVIGATION__FILE_STATUS => KEY_VALUE__FILE_STATUS__NOT_CHECKED,
      KEY_NAME__DIRECTORY_NAVIGATION__FILE_TYPE => KEY_VALUE__FILE_TYPE__IS_FILE,

      KEY_NAME__DIRECTORY_NAVIGATION__FILE_PATH_IN_BASE_DIR => "",
      KEY_NAME__DIRECTORY_NAVIGATION__FILE_DEPTH_IN_BASE_DIR => 0,
      KEY_NAME__DIRECTORY_NAVIGATION__COUNT_OF_REGULAR_FILES => 0,
      KEY_NAME__DIRECTORY_NAVIGATION__PARENT_HASH_ENTRY => -1
    );

    return $tree_browser_hash_element;

}





function &build_tree($caller, $base_directory, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to map a file tree hierarchy to a PHP hash, also known as
//   an "ordered map", for parsing, presentation of files and possible
//   searching . . .
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


// VAR BEGIN

//    $show_usage = false;

    $handle = NULL;                      // . . . file handle to directory to search for navigation menu item files,

    $current_dir_has_files_to_process = 'true';

    $files_noted = 0;

    $file_tree_hash_entry = 0;  // pointer to hash entry, may be duplicative of variable \$files_noted - TMH

    $file_limit_not_reached = 'true';

    $files_in_current_dir = array();


// "file tree" hash related:
    $key = 0;

    $key_to_present_directory = 0;       // . . . key used to store count of files in containing directory hash entry

    $index_to_latest_not_checked = 0;    // . . . used by loop 2 in algorithm version 1, see notes on routine implementation

    $index_to_earliest_not_checked = 0;  // . . . used by loop 2 in algorithm version 2


// 2018-02-09 - poorly named given name of PHP define:
    $file = KEY_VALUE__DIRECTORY_NAVIGATION__DEFAULT_FILENAME;

    $file_type = KEY_VALUE__FILE_TYPE__IS_FILE;

// Used to hold starting directory and all subdirectories as routine maps caller's file tree:
    $current_path = ""; // ".";   // . . . this var was named 'file_path_in_base_dir' - TMH

    $file_depth_in_base_dir = 0;



// PHP "file tree" hash to return to calling code:
    $navigable_tree = array();


// Summary and reportable info not distilled in the hash itself:
    $count_of_regular_files = 0;

    $count_of_directories = 0;



// diagnostics and formatting:

    $dmsg = "";  // . . . local diagnostics message string for development and debugging,

    $term = "<br />\n";

    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_dev      = DIAGNOSTICS_ON;
    $dflag_format   = DIAGNOSTICS_ON;
    $dflag_verbose  = DIAGNOSTICS_OFF;
    $dflag_warning  = DIAGNOSTICS_ON;
    $dflag_minimal  = DIAGNOSTICS_OFF;
    $dflag_summary  = DIAGNOSTICS_ON;

    $dflag_open_dir    = DIAGNOSTICS_ON;
    $dflag_note_file   = DIAGNOSTICS_ON;
    $dflag_check_file  = DIAGNOSTICS_ON;
    $dflag_files_limit = DIAGNOSTICS_ON;
    $dflag_files_count = DIAGNOSTICS_ON;

    $dflag_file_count_per_directory = DIAGNOSTICS_ON;
    $dflag_base_directory_file_list = DIAGNOSTICS_ON;
    $dflag_loop_1                   = DIAGNOSTICS_ON;
    $dflag_loop_2                   = DIAGNOSTICS_ON;

    $dflag_file_limit_reached       = DIAGNOSTICS_ON;
    $dflag_noting_first             = DIAGNOSTICS_ON;
    $dflag_filenames_by_pattern     = DIAGNOSTICS_ON;
    $dflag_file_depth               = DIAGNOSTICS_ON;

//    $rname = "tree_browser";
    $rname = "build_tree";

// VAR END


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    if ( 1 )
    {
    show_diag($rname, "turning off most diagnostics . . .", $dflag_minimal);
//        $dflag_announce = DIAGNOSTICS_OFF;    // . . . diagnostics flag for development-related run time comments,
    $dflag_dev      = DIAGNOSTICS_OFF;
    $dflag_format   = DIAGNOSTICS_OFF;
    $dflag_verbose  = DIAGNOSTICS_OFF;
    $dflag_warning  = DIAGNOSTICS_OFF;
    $dflag_minimal  = DIAGNOSTICS_OFF;
    $dflag_summary  = DIAGNOSTICS_OFF;

    $dflag_open_dir    = DIAGNOSTICS_OFF;
    $dflag_note_file   = DIAGNOSTICS_OFF;
    $dflag_check_file  = DIAGNOSTICS_OFF;
    $dflag_files_limit = DIAGNOSTICS_OFF;
    $dflag_files_count = DIAGNOSTICS_OFF;

    $dflag_file_count_per_directory = DIAGNOSTICS_OFF;
    $dflag_base_directory_file_list = DIAGNOSTICS_OFF;
    $dflag_loop_1                   = DIAGNOSTICS_OFF;
    $dflag_loop_2                   = DIAGNOSTICS_OFF;

//    $dflag_file_limit_reached       = DIAGNOSTICS_OFF;
    $dflag_noting_first             = DIAGNOSTICS_OFF;
    $dflag_filenames_by_pattern     = DIAGNOSTICS_OFF;
    $dflag_file_depth               = DIAGNOSTICS_OFF;
    }

    show_diag($rname, "- 2018-01-23 - ROUTINE IMPLEMENTATION UNDERWAY -", $dflag_dev);
    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "called by '$caller' with base directory '$base_directory',", $dflag_dev);



// 2018-20-26 - added this Monday:
    $base_directory = preg_replace('/\/+$/', '', $base_directory);

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
    else
    {
        if ( 0 )
        {
            show_diag($rname, "noting base directory '$base_directory' in file tree hash at entry $file_tree_hash_entry . . .",
              $dflag_noting_first);

            $navigable_tree[$file_tree_hash_entry] =& nn_tree_browser_entry($rname);

            $navigable_tree[$file_tree_hash_entry][FILE_NAME] = basename($base_directory);
//        $navigable_tree[$file_tree_hash_entry][FILE_STATUS] = KEY_VALUE__DIRECTORY_NAVIGATION__DEFAULT_FILE_STATUS;
            $navigable_tree[$file_tree_hash_entry][FILE_STATUS] = KEY_VALUE__FILE_STATUS__CHECKED;
            $navigable_tree[$file_tree_hash_entry][FILE_TYPE] = KEY_VALUE__FILE_TYPE__IS_DIRECTORY;

 // was "." but creates "./dir_name" which list_of_filenames_by_pattern() cannot open - TMH
            if ( dirname($base_directory) === "." )
                { $navigable_tree[$file_tree_hash_entry][FILE_PATH_IN_BASE_DIR] = ""; }
            else
                { $navigable_tree[$file_tree_hash_entry][FILE_PATH_IN_BASE_DIR] = dirname($base_directory); }

//        $navigable_tree[$file_tree_hash_entry][FILE_PATH_IN_BASE_DIR] = realpath($base_directory); 
            $navigable_tree[$file_tree_hash_entry][FILE_DEPTH_IN_BASE_DIR] = 0;
            $navigable_tree[$file_tree_hash_entry][FILE_COUNT] = 0;

            ++$files_noted;
            $file_tree_hash_entry = $files_noted;  // <-- update file tree hash pointer at end of loop 1, needed here or at top of loop 1 :/

            show_diag($rname, "hash entry pointer now holds $file_tree_hash_entry.  Continuing,",
              $dflag_noting_first);
        }
        else
        {
            show_diag($rname, "NOTE:  Not storing base directory '$base_directory' in file tree hash.",
              $dflag_noting_first);
        }

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

    show_diag($rname, "calling for list of files in base directory '$base_directory' . . .",
      $dflag_open_dir);
    $files_in_current_dir =& list_of_filenames_by_pattern($rname, $base_directory, "/(.*)/");
    sort( $files_in_current_dir );
//    array_push($files_in_current_dir, "---MARKER---");

    if ( $dflag_dev )
    {
//        echo "In loop 1, from base directory got file list:<br />\n";
        echo "Above loop 1, from base directory got file list:<br />\n";
        echo "<pre>\n";
        print_r($files_in_current_dir);
        echo "</pre>\n";
    }


// main WHILE-loop set up:

    $current_dir_has_files_to_process = 'true';

    $index_to_latest_not_checked = 0;    // . . . loop set up, this value may already by assigned in var block top of routine

//    $files_noted = 0;

    if ( $files_noted < FILE_LIMIT_OF_TREE_BROWSER )
        { $file_limit_not_reached = 'true'; }
    else
    {
        show_diag($rname, "- LIMIT REACHED - detecting that we reached file limit " . FILE_LIMIT_OF_TREE_BROWSER
          . "!", $dflag_file_limit_reached);
        $file_limit_not_reached = 'false';
    }

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

        show_diag($rname, "- TOP OF MAIN LOOP - Noting file types in path '$current_path':", $dflag_loop_1);

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

            show_diag($rname, "assigning to variable \$current_path the string concatenation '$current_path' '/' '$file' . . .",
              $dflag_note_file);
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

            show_diag($rname, "figuring current file's depth in base directory from path '$current_path' . . .",
              $dflag_file_depth);
            $file_depth_in_base_dir = substr_count($current_path, "/");
            ++$file_depth_in_base_dir;

            $_SESSION[KEY_NAME__FILE_DEPTH_IN_BASE_DIR] = $file_depth_in_base_dir;

            if ( $file_depth_in_base_dir > $_SESSION[KEY_NAME__FILE_DEPTH_GREATEST_VALUE] )
            {
                $_SESSION[KEY_NAME__FILE_DEPTH_GREATEST_VALUE] = $file_depth_in_base_dir;
            }


//            $file_tree_hash_entry = $files_noted;  // <-- update file tree hash pointer at end of loop 1

            $navigable_tree[$file_tree_hash_entry] = nn_tree_browser_entry($rname);
// PHP Notice:  Only variables should be assigned by reference in /var/www/neelanurseries.com/public_html/lib/php/directory-navigation.php on line 1086
//            $navigable_tree[$file_tree_hash_entry] =& nn_tree_browser_entry($rname);

            $navigable_tree[$file_tree_hash_entry][FILE_NAME] = $file;
            $navigable_tree[$file_tree_hash_entry][FILE_STATUS] = KEY_VALUE__DIRECTORY_NAVIGATION__DEFAULT_FILE_STATUS;
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
show_diag($rname, "- zztop - above loop 2 setting \$current_path_and_file from file hash tree data, to '$current_path_and_file',", $dflag_dev);
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

        while ( ($hash_pointer_loop_2 < $file_tree_hash_entry) && ($loop_2_iteration < LIMIT__BUILD_TREE__LOOP_2_ITERATION) )
        {
            ++$loop_2_iteration;

            show_diag($rname, "- TOP OF LOOP 2 - iteration $loop_2_iteration, file_tree_hash_entry = $file_tree_hash_entry, hash_pointer_loop_2 = $hash_pointer_loop_2,", $dflag_check_file);
            show_diag($rname, "checking file '$current_path_and_file',", $dflag_check_file);


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
                        show_diag($rname, "at hash entry $hash_pointer_loop_2 looking at first un-checked directory '"
                          . $noted_file[FILE_NAME] . "',", $dflag_check_file);
                        show_diag($rname, "note:  this will be next directory whose contents loop 1 maps.",
                          $dflag_check_file);

                        $unchecked_directory_found = 'true';
                        $navigable_tree[$hash_pointer_loop_2][FILE_STATUS] = FILE_CHECKED;

// - NOTE - Here set the path from which loop 1 will next read files:
                        if ( strlen($noted_file[FILE_PATH_IN_BASE_DIR]) > 0 )
                        {
                            $current_path = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];
                        }
                        else
                        {
                            $current_path = $noted_file[FILE_NAME];
                        }
show_diag($rname, "- ZZTop - in loop 2 setting \$current_path from file hash tree data, to '$current_path',", $dflag_dev);

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
            $noted_file = $navigable_tree[$hash_pointer_loop_2];  // 2018-02-14 - undefined offset warnings this line - TMH
            if ( strlen($noted_file[FILE_PATH_IN_BASE_DIR]) > 0 )
            {
                $current_path_and_file = $noted_file[FILE_PATH_IN_BASE_DIR] . "/" . $noted_file[FILE_NAME];
            }
            else
            {
                $current_path_and_file = $noted_file[FILE_NAME];
            }

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
              $dflag_filenames_by_pattern);
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


    show_diag($rname, "returning . . .", $dflag_dev);

    return $navigable_tree;

} // end function build_tree()




function present_files($caller, $file_hierarchy, $options)  // older function, Ted not sure of correctness of this function
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





function &visible_path_depth($caller, $callers_path, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to determine and to return the number of visible path
//    elements (directories) in a path, when some of the initial
//    elements may be requested hidden by calling code.
//
//  EXPECTS:
//    *  calling code identifier (string),
//    *  current working directory or path,
//    *  options hash which holds the number of initial path elements to hide
//
//  RETURNS:
//    *  integet count of visible path elements
//
//----------------------------------------------------------------------


// VAR BEGIN

    $path_elements = null;  // assigned an array type value from PHP explode() function,

    $path_element = "";

    $path_element_count = 0;

    $path_depth = 0;

    $hide_first_n_path_elements = 1;


// diagnostics:

    $dflag_announce          = DIAGNOSTICS_OFF;
    $dflag_dev               = DIAGNOSTICS_OFF;
    $dflag_hide_path_element = DIAGNOSTICS_OFF;
    $dflag_path_element      = DIAGNOSTICS_OFF;

    $rname = "visible_path_depth";

// VAR END


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $options) )
    {
        $hide_first_n_path_elements = $options[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $_SESSION) )
    {
        $hide_first_n_path_elements = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS];
    }


//    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "working with \$callers_path set to '$callers_path',", $dflag_dev);
    show_diag($rname, "hiding first $hide_first_n_path_elements path elements,", $dflag_dev);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - get elements of path from base dir to current working dir:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $path_elements = explode("/", $callers_path, LIMIT_TO_100);

    $path_element_count = count($path_elements);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP -
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    show_diag($rname, "- BEGIN FIGURING OF VISIBLE PATH DEPTH -", $dflag_dev);

    if ( ($path_element_count > 0) && ($path_element_count > $hide_first_n_path_elements) )
    {
        $path_depth = 0;

        foreach ( $path_elements as $key => $path_element )
        {

show_diag($rname, "looking at path element '$path_element',", $dflag_path_element);

//            if ( $hide_first_n_path_elements > 0 )
            if ( $hide_first_n_path_elements >= 0 )
            {
                show_diag($rname, "hiding path element '$path_element',", $dflag_hide_path_element);
                --$hide_first_n_path_elements;
            }
            else
            {
                ++$path_depth;
                show_diag($rname, "visible path depth at present is $path_depth . . .", $dflag_dev);
            }

        } // end FOREACH construct to iterate over elements of path from base dir to cwd

    } // end IF-block to determine whether there are path elements above files in cwd to show


    show_diag($rname, "- END FIGURING OF VISIBLE PATH DEPTH -", $dflag_dev);

    return $path_depth;

} // end function visible_path_depth()




function &url_of_file_tree_intermediate_path($caller, $callers_path, $options)
{
//----------------------------------------------------------------------
// NOTE:  this function also returns a fixed indent as the first part
//   of the link text and URL line returned to calling code . . . - TMH
//
//----------------------------------------------------------------------


// VAR BEGIN

// These three variables assigned values from options hash which caller
// sets up . . .

    $site = "";

    $path_from_doc_root = "";

    $basedir = "";

//    $cwd . . . not the same as calling code's path, \$cwd is the end user's latest selected directory or file.

    $view_mode = "";


    $path_elements = null;  // assigned an array type value from PHP explode() function,

    $path_element = "";

    $path_element_count = 0;

    $path_depth = 0;

    $hide_first_n_path_elements = 1;


    $path_intermediate = "";
    $indent;
    $url;
    $file_type_note;


// diagnostics:

    $dflag_dev = DIAGNOSTICS_OFF;
    $dflag_hide_path_element = DIAGNOSTICS_OFF;
    $dflag_at_document_root  = DIAGNOSTICS_ON;
    $dflag_var_basedir       = DIAGNOSTICS_OFF;

    $dflag_warning = DIAGNOSTICS_ON;

    $rname = "url_of_file_tree_intermediate_path";

// VAR END



    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__SITE_URL, $options) )
    {
        $site = $options[KEY_NAME__DIRECTORY_NAVIGATION__SITE_URL];
    }
    else
    {
        show_diag($rname, "- WARNING - no site URL defined in passed options hash!", $dflag_warning);
        show_diag($rname, "Need site Uniform Resource Locator (URL) to build complete URL to page of this site,", $dflag_warning);
        show_diag($rname, "returning early to calling code . . .", $dflag_warning);
        return;
    }


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__PATH_FROM_DOC_ROOT, $options) )
    {
        $path_from_doc_root = $options[KEY_NAME__DIRECTORY_NAVIGATION__PATH_FROM_DOC_ROOT];
    }
    else
    {
        show_diag($rname, "- INFO - present script at document root,", $dflag_at_document_root);
    }


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__SCRIPT_NAME, $options) )
    {
        $script_name = $options[KEY_NAME__DIRECTORY_NAVIGATION__SCRIPT_NAME];
    }
    else
    {
        show_diag($rname, "- WARNING - no parent script name found in passed options hash!", $dflag_warning);
        show_diag($rname, "Need script name to build complete URL to page of this site,", $dflag_warning);
        show_diag($rname, "returning early to calling code . . .", $dflag_warning);
        return;
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - obtain base directory, current working directory, first via
//          HTTP get method, then via PHP session variable . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR, $_GET) )
    {
        show_diag($rname, "obtaining base directory via HTTP 'get' method . . .", $dflag_var_basedir);
        $basedir = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $_SESSION) )
    {
        show_diag($rname, "obtaining base directory via PHP session variable . . .", $dflag_var_basedir);
        $basedir = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $options) )
    {
        show_diag($rname, "obtaining base directory via passed options variable . . .", $dflag_var_basedir);
        $basedir = $options[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    else
    {
//        show_diag($rname, "obtaining base directory passed hash of files in current working directory . . .", $dflag_var_basedir);
//        $first_file_tree_hash_entry = reset($files_in_cwd);
//        $basedir = $first_file_tree_hash_entry[FILE_PATH_IN_BASE_DIR];
        show_diag($rname, "- WARNING - unable to determine base directory of files to show", $dflag_warning);
        show_diag($rname, "  +  via get method, PHP session var or calling code options!", $dflag_warning);
        show_diag($rname, "  +  returning early to calling code . . .", $dflag_warning);
        return;
    }


// Preserve view mode as most recently captured through HTTP get method:

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR, $_GET) )
    {
        $view_mode = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR];
    }


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $options) )
    {
        $hide_first_n_path_elements = $options[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $_SESSION) )
    {
        $hide_first_n_path_elements = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS];
    }



//    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "starting,", $dflag_dev);
    show_diag($rname, "working with \$callers_path set to '$callers_path',", $dflag_dev);
    show_diag($rname, "hiding first $hide_first_n_path_elements path elements,", $dflag_dev);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - get elements of path from base dir to current working dir:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $path_elements = explode("/", $callers_path, LIMIT_TO_100);

    $path_element_count = count($path_elements);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - show directories from base dir to current working dir:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// NOTE:  hidden path elements won't appear on the page but will
//  necessarily appear in the URLs of shown path elements and files
//  of the current working directory:

    show_diag($rname, "- BEGIN INTERMEDIATE PATHS MARK-UP GENERATION -", $dflag_dev);

    if ( ($path_element_count > 0) && ($path_element_count > $hide_first_n_path_elements) )
    {
        $path_depth = 0;

        foreach ( $path_elements as $key => $path_element )
        {
            if ( strlen($path_intermediate) == 0 )
                { $path_intermediate = $path_element; }
            else
                { $path_intermediate = $path_intermediate . "/" . $path_element; }


            if ( $hide_first_n_path_elements > 0 )
            {
                show_diag($rname, "hiding path element '$path_element',", $dflag_hide_path_element);
                --$hide_first_n_path_elements;
            }
            else
            {
//                show_diag($rname, "building indent string and path depth at present is $path_depth . . .",
//                  $dflag_indent_string);
//                $indent =& nbsp_based_indent($rname, $path_depth, 0);

                $url = "$site/$path_from_doc_root/$script_name?";

                if ( strlen($view_mode) > 0 )
                {
                    $url = $url . KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR . "=$view_mode";
                }

//                $url = "$site/$path_from_doc_root/$script_name?"
                $url = "$url&"
                  . KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR . "=$basedir&" 
                  . KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR . "=$path_intermediate";



                $file_type_note = "(directory)";

                $link_text = $path_element;

// 2018-02-22 - indent string building factored into separate routine:
//                $line_to_browser = "$indent <a href=\"$url\">" . $link_text . "</a>";
                $line_to_browser = "<a href=\"$url\">" . $link_text . "</a>";

// 2018-02-22 - echo statement left in calling routine while $line_to_broswer
//  +  construction factored into this routine:
//                echo "$line_to_browser<br />\n";

                ++$path_depth;
            }

        } // end FOREACH construct to iterate over elements of path from base dir to cwd

    } // end IF-block to determine whether there are path elements above files in cwd to show


    show_diag($rname, "- END INTERMEDIATE PATHS MARK-UP GENERATION -", $dflag_dev);

//    return $line_to_browser;
    return $url;

} // end function url_of_file_tree_intermediate_path()




function &link_to_first_non_hidden_path_elements($caller, $options)
{
//----------------------------------------------------------------------
//  PURPOSE:  To construct a hyperlink to the first non-hidden
//   directory of a given file tree.  Calling code specifies a path
//   with one or more directories, e.g. a non-trivial and valid path.
//   Calling code also sets number of leading path elements to hide
//   from the web document's presented text, the text of the given
//   hyperlink.  Hidden path elements are present in the given URL
//   this routine constructs.
//
//  EXPECTS:
//   *  valid and non-trivial path to web-server accessible file tree
//   *  count of leading path elements to hide
//
//  RETURNS:
//   *  a hyperlink with URL, link text and HTML5 mark-up
//
//
//  2018-02-26 NOTE:  This routine does not answer the task of building
//   two or more hyperlinks to account for two or more nested
//   directories in series, each of which contain one file, that is
//   one directory.  This task needs be implemented to handle multiple
//   symlinks which library-calling code (e.g. web page designer) wants
//   shown.
//
//   Contributor Ted not worrying about this today, as may be rare
//   case where people viewing a file tree want to see leading
//   directories which each contain only one file.
//
//
//----------------------------------------------------------------------

// VAR BEGIN

    $url = "";
    $link_text = "";
    $link = ""; // a link entails a URL, link text and HTML mark-up

    $site = "";
    $script_name = "";
    $path_from_doc_root = "";
    $view_mode = "";
    $base_dir = "";
    $cwd = "";
    $hide_first_n_path_elements = 0;


    $dflag_dev = DIAGNOSTICS_OFF;

    $rname = "link_to_first_non_hidden_path_elements";

// VAR END



// - STEP - set variables with data from local $options hash:


// NOTE:  NEED TO SANITY CHECK FOLLOWING VARIABLES' VALUES AFTER
//   +  ASSIGNEMENT TO BE SURE THEY ARE NON-ZERO LENGTH:

    $site = $options[KEY_NAME__DIRECTORY_NAVIGATION__SITE_URL_ABBR];
    $script_name = $options[KEY_NAME__DIRECTORY_NAVIGATION__SCRIPT_NAME_ABBR];
    $path_from_doc_root = $options[KEY_NAME__DIRECTORY_NAVIGATION__PATH_FROM_DOC_ROOT];
    $view_mode = $options[KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE];
    $base_dir = $options[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    $cwd = $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD];


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $options) )
        { $hide_first_n_path_elements = $options[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS]; }
    else
        { $hide_first_n_path_elements = 0; }



    if ( $hide_first_n_path_elements > 0 )
    {
        $path_elements = explode("/", $cwd, LIMIT_TO_100);

// 2018-02-23 - Note, hiding path elements nearest the relative root
// of the file tree's base directory works in the case where the
// first n path elements are a series of nested directories, one in the
// next.
//
// As before we want to construct a URL which has the full, "web
// document root" oriented path but whose hyperlink text has only the
// non-hidden path elements . . .

//

        $url = $site . "/" . $path_from_doc_root . "/" . $script_name
          . "?" . KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR . "=$base_dir"
          . "&" . KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR . "=$view_mode"
          . "&" . KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR . "=$cwd";

        show_diag($rname, "built URL which holds '$url',", $dflag_dev);


// Build link text:

        $path_element_count = count($path_elements);
        show_diag($rname, "finding '$path_element_count' path elements in '$cwd',", $dflag_dev);
        show_diag($rname, "asked to hide '$hide_first_n_path_elements' path elements,", $dflag_dev);
        show_diag($rname, "showing all path elements:", $dflag_dev);
        if ( $dflag_dev)
        {
            echo "<pre>\n";
            print_r($path_elements);
            echo "</pre>\n";
        }


        if ( $path_element_count > 0 )
        {
            foreach ( $path_elements as $key => $path_element )
            {
                if ( $hide_first_n_path_elements > 0 )
                {
                    --$hide_first_n_path_elements;
                }
                else
                {
                    if ( $key >= ($path_element_count - 1))
                        { $link_text = $link_text . $path_element; }
                    else
                        { $link_text = $link_text . "$path_element/"; }
                }
            }
        }

    } // end IF-block to test for hidden path elements
    else
    {
        show_diag($rname, "not asked to hide any leading path elements!", $dflag_dev);
        $link_text = $cwd;
    }

    show_diag($rname, "built link text '$link_text',", $dflag_dev);


    $link = "<a href=\"$url\">$link_text</a><br />\n";

    show_diag($rname, "returning link '$link' . . .", $dflag_dev);
    return $link;

} // end function link_to_first_non_hidden_path_elements





function &hash_of_files_in_cwd($caller, $file_tree_hierarchy, $options)
{

    $files_in_cwd = array();

    $key = 0;

    $entry = null;

// diagnostics:
    $dflag_warning = DIAGNOSTICS_ON;
    $dflag_dev     = DIAGNOSTICS_ON;

    $rname = "hash_of_files_in_cwd";


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $options) )
    {
        $cwd = $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else
    {
        show_diag($rname, "- WARNING - no current working directory value found in \$options hash!",
          $dflag_warning);
        show_diag($rname, "  +  returning early to calling code . . .",
          $dflag_warning);
        return;
    }



    foreach ( $file_tree_hierarchy as $key => $entry )
    {
        if ( $cwd === $entry[FILE_PATH_IN_BASE_DIR] )
        {
//            show_diag($rname, "adding file tree hash entry $key to hash of files in \$cwd,",
//              $dflag_dev);
            $files_in_cwd[$key] = $entry;
        }
    }


    return $files_in_cwd;

} // end function hash_of_files_in_cwd()




function present_images_as_thumbnails($caller, $hash_of_symlinks, $cwd, $options)
{
//----------------------------------------------------------------------
// 2018-03-01 - THIS ROUTINE NOT COMPLETE, CALLS ANOTHER ROUTINE TO
//   clean up filenames, but this may not be sufficient to address
//   filename needs of James Heinrich's phpThumb code . . .  - TMH
//
//----------------------------------------------------------------------

// VAR BEGIN

    $phpThumb = null;
    $thumbnail_width = 50;
    $thumbnail_height = 50;

    $key = 0;
    $hash_entry = null;
//    $safe_filename = "";

    $link_to_thumbnail = "";
    $link_to_image = "";

// diagnostics:
    $lbuf = "";
    $dflag_announce  = DIAGNOSTICS_ON;
    $dflag_dev       = DIAGNOSTICS_ON;
    $dflag_php_thumb = DIAGNOSTICS_OFF;
    $rname = "present_images_as_thumbnails";

// VAR END



    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "working with \$cwd set to '$cwd',", $dflag_dev);

    {
        $phpThumb = new phpThumb();
        $thumbnail_width = 50;
        $thumbnail_height = 50;

        foreach ( $hash_of_symlinks as $key => $entry )
        {

            $lbuf = "setting phpThumb source data to '" . $cwd."/".$entry[KEY_NAME__SYMLINK_NAME] . "' . . .";
            show_diag($rname, $lbuf, $dflag_php_thumb);
            $phpThumb->setSourceData(file_get_contents($cwd."/".$entry[KEY_NAME__SYMLINK_NAME]));

// See James Heinrich's phpThumb/docs/phpthumb.readme.txt file for
// details on settable parameters, many expressed by single letters,
// such as thumbnail width 'w' and height 'h':

//            $phpThumb->setParameter('w', $thumbnail_width);
            $phpThumb->setParameter('h', $thumbnail_height);

// NEED - to replace hard coded thumbnail filename prefix with PHP defined constant:
            $filename_for_thumbnail = preg_replace('/z-tn--/', 'thumbnail--', $entry[KEY_NAME__SYMLINK_NAME]);
// Note:  variable $output_filename gets assigned a relative path which our local phpThumb object uses in its context, 
//  +  a context which may differ in environment variables like $PWD from the environment of Neela Nurseries PHP
//  +  library code:
            $output_filename = "./thumbnails/$filename_for_thumbnail";

            if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
                if ($phpThumb->RenderToFile($output_filename)) {
//                    echo 'Successfully rendered to "'.$output_filename.'"';
// echo "<br />\n";
                } else {
                    echo 'Failed:<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
                }
                $phpThumb->purgeTempFiles();
            } else {
                echo 'Failed:<pre>'.$phpThumb->fatalerror."\n\n".implode("\n\n", $phpThumb->debugmessages).'</pre>';
            }

            $phpThumb->resetObject();
        }

    } // end local scope, 


// Thumbnails have now been created,

//    echo "<br /> <br />\n";

    {
        foreach ( $hash_of_symlinks as $key => $entry )
        {
            $filename_for_thumbnail = preg_replace('/z-tn--/', 'thumbnail--', $entry[KEY_NAME__SYMLINK_NAME]);
            $path_to_thumbnail = "./lib/phpThumb/thumbnails/$filename_for_thumbnail";

            $link_to_thumbnail = "<img border=\"1\" src=\"$path_to_thumbnail\" width=\"*\" alt=\"image thumbnail at hash entry $key\">\n";
 

//            echo $link_to_thumbnail;

            $link_to_image = "<a href=\"$cwd/".$entry[KEY_NAME__SYMLINK_NAME]."\">$link_to_thumbnail</a>";

            echo $link_to_image;
        }
    }

//    echo "<br /> <br />\n";
    echo "<br />\n";

    show_diag($rname, "done.", $dflag_announce);

} // end function present_images_as_thumbnails()








function present_path_elements_and_files_of_cwd($caller, $files_in_cwd, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to present files of a file tree hierarchy which are in
//   an indicated current working directory of that hierarchy.
//
//   This is one of multiple file presentation modes of nn local PHP
//   library source file named 'directory-navigation.php'.
//
//----------------------------------------------------------------------

// VAR BEGIN

// variables this function expects from external sources, e.g. PHP
// session var or HTTP get method, or other:
    $site = "https://neelanurseries.com";
    $path_from_doc_root = "sandbox";
    $script_name = $options["script_name"];
    $basedir = "";
    $cwd = "";
    $hide_first_n_path_elements = 1;

// variable to build correct URLs for directories from base dir to cwd:
    $path_elements = null;
    $path_element_count = 0;
    $path_intermediate = "";

// local string and integer variables used to build URL to file:
    $path = "";
    $filename = "";
    $file_count = 0;

// additional local strings used to build URL, these need to be defineds
// and passed to this function in a sensible library way, where calling
// code knows about and sets these appropriately.  Right now just
// hard-coded values for a couple of these:                        - TMH
    $url = "";

    $link_text = "";
    $line_to_browser = "";

    $flag__show_file_type = 'true';
    $file_type_note = "";

// variable used to reach first file hierarchy hash entry, as we
// don't know the key names of the passed hash, in this function's
// scope:
    $first_file_tree_hash_entry = null;

// variable used to indent successive directory elements in cwd's path:
    $path_depth = 0;

// variable to hold mark-up:
    $indent = "";


// diagnostics:

    $lbuf = "";

    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_dev      = DIAGNOSTICS_ON;
    $dflag_warning  = DIAGNOSTICS_ON;

    $dflag_first_hash_entry       = DIAGNOSTICS_OFF;
    $dflag_path_elements          = DIAGNOSTICS_ON;

    $dflag_var_basedir            = DIAGNOSTICS_OFF;
    $dflag_var_cwd                = DIAGNOSTICS_OFF;
    $dflag_var_hide_path_elements = DIAGNOSTICS_OFF;
    $dflag_indent_string          = DIAGNOSTICS_OFF;

    $dflag_hide_path_element      = DIAGNOSTICS_OFF;
    $dflag_intermediate_path_mark_up = DIAGNOSTICS_OFF;

    $rname = "present_path_elements_and_files_of_cwd";

// VAR END


    if ( 1 )
    {
    $dflag_announce = DIAGNOSTICS_OFF;
    $dflag_dev      = DIAGNOSTICS_OFF;
    $dflag_warning  = DIAGNOSTICS_OFF;

    $dflag_first_hash_entry       = DIAGNOSTICS_OFF;
    $dflag_path_elements          = DIAGNOSTICS_OFF;

    $dflag_var_basedir            = DIAGNOSTICS_OFF;
    $dflag_var_cwd                = DIAGNOSTICS_OFF;
    $dflag_var_hide_path_elements = DIAGNOSTICS_OFF;
    $dflag_indent_string          = DIAGNOSTICS_OFF;

    $dflag_hide_path_element      = DIAGNOSTICS_OFF;
    $dflag_intermediate_path_mark_up = DIAGNOSTICS_OFF;
    }

    show_diag($rname, "starting,", $dflag_announce);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - obtain base directory, current working directory, first via
//          HTTP get method, then via PHP session variable . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR, $_GET) )
    {
        show_diag($rname, "obtaining base directory via HTTP 'get' method . . .", $dflag_var_basedir);
        $basedir = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $_SESSION) )
    {
        show_diag($rname, "obtaining base directory via PHP session variable . . .", $dflag_var_basedir);
        $basedir = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $options) )
    {
        show_diag($rname, "obtaining base directory via passed options variable . . .", $dflag_var_basedir);
        $basedir = $options[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    else
    {
//        show_diag($rname, "obtaining base directory passed hash of files in current working directory . . .", $dflag_var_basedir);
//        $first_file_tree_hash_entry = reset($files_in_cwd);
//        $basedir = $first_file_tree_hash_entry[FILE_PATH_IN_BASE_DIR];
        show_diag($rname, "- WARNING - unable to determine base directory of files to show", $dflag_warning);
        show_diag($rname, "  +  via get method, PHP session var or calling code options!", $dflag_warning);
        show_diag($rname, "  +  returning early to calling code . . .", $dflag_warning);
        return;
    }



    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR, $_GET) )
    {
        $cwd = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $_SESSION) )
    {
        $cwd = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $options) )
    {
        $cwd = $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else
    {
        show_diag($rname, "- WARNING - unable to determine current working directory", $dflag_warning);
        show_diag($rname, "  +  via get method, PHP session var or calling code options!", $dflag_warning);
        show_diag($rname, "  +  returning early to calling code . . .", $dflag_warning);
        return;
    }


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $options) )
    {
        $hide_first_n_path_elements = $options[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS];
    }
    else
    {
        $hide_first_n_path_elements = 0;
    }


    show_diag($rname, "\$basedir = '$basedir'", $dflag_var_basedir);
    show_diag($rname, "\$cwd = '$cwd'", $dflag_var_cwd);
    show_diag($rname, "'hide first n path elements' var set to $hide_first_n_path_elements,", $dflag_var_hide_path_elements);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - get elements of path from base dir to current working dir:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//    $path_elements = explode("/", $first_file_tree_hash_entry[FILE_PATH_IN_BASE_DIR], LIMIT_TO_100);
    $path_elements = explode("/", $cwd, LIMIT_TO_100);

    $path_element_count = count($path_elements);

    show_diag($rname, "current working directory has $path_element_count elements,", $dflag_path_elements);
    show_diag($rname, "we're asked to hide first $hide_first_n_path_elements elements of these," , $dflag_path_elements);
    show_diag($rname, "here are the path elements of \$cwd:" , $dflag_path_elements);
    if ( $dflag_path_elements )
    {
        foreach ( $path_elements as $key => $path_element )
        {
            echo "&nbsp; &nbsp;$path_element<br />\n";
        }
    }

    if ( $dflag_path_elements )
    {
        echo "<br />\n"; // <-- for diagnostics readability
    }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - show directories from base dir to current working dir:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// NOTE:  hidden path elements won't appear on the page but will
//  necessarily appear in the URLs of shown path elements and files
//  of the current working directory:

    show_diag($rname, "- BEGIN INTERMEDIATE PATHS MARK-UP GENERATION -", $dflag_intermediate_path_mark_up);

    if ( ($path_element_count > 0) && ($path_element_count > $hide_first_n_path_elements) )
    {
        $path_depth = 0;

        foreach ( $path_elements as $key => $path_element )
        {
            if ( strlen($path_intermediate) == 0 )
                { $path_intermediate = $path_element; }
            else
                { $path_intermediate = $path_intermediate . "/" . $path_element; }


            if ( $hide_first_n_path_elements > 0 )
            {
                show_diag($rname, "hiding path element '$path_element',", $dflag_hide_path_element);
                --$hide_first_n_path_elements;
            }
            else
            {
                show_diag($rname, "building indent string and path depth at present is $path_depth . . .",
                  $dflag_indent_string);

                $indent =& nbsp_based_indent($rname, $path_depth, 0);
                $url = "$site/$path_from_doc_root/$script_name?"
                  . KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR . "=$basedir&" 
                  . KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR . "=$path_intermediate";

                $file_type_note = "(directory)";

                $link_text = $path_element;
                $line_to_browser = "$indent <a href=\"$url\">" . $link_text . "</a>";
                echo "$line_to_browser<br />\n";

//                $path_intermediate = $path_intermediate . "/" . $path_element;
                ++$path_depth;
            }

        } // end FOREACH construct to iterate over elements of path from base dir to cwd

    } // end IF-block to determine whether there are path elements above files in cwd to show

//    ++$path_depth;

    show_diag($rname, "- END INTERMEDIATE PATHS MARK-UP GENERATION -", $dflag_intermediate_path_mark_up);

    if ( $dflag_intermediate_path_mark_up )
    {
        echo "<br />\n"; // <-- for diagnostics readability
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - show files in current working directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( count($files_in_cwd) == 0 )
    {
        echo "<i>current directory contains no files!</i><br >\n";
    }


    {
        foreach ( $files_in_cwd as $key => $file_entry )
        {
            $path = $file_entry[FILE_PATH_IN_BASE_DIR];
//            $filename = $file_entry[FILE_NAME];
            $filename = url_safe_filename($rname, $file_entry[FILE_NAME], $_SESSION);
            $file_count = $file_entry[FILE_COUNT];

            if ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            {
//                $url = "$site/$path_from_doc_root/$script_name?base_dir=$basedir&cwd=$path/$name";
//                $url = "$site/$path_from_doc_root/$script_name?base_dir=$path/$filename";
                $url = "$site/$path_from_doc_root/$script_name?"
                  . KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR . "=$basedir&" 
                  . KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR . "=$path/$filename";
                $file_type_note = "(directory)";
            }

            elseif ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_FILE )
            {
                $url = "$site/$path_from_doc_root/$path/$filename";
                $file_type_note = "(file)";
            }

            elseif ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_SYMBOLIC_LINK )
            {
                $url = "$site/$path_from_doc_root/$path/$filename";
                $file_type_note = "(symlink)";
            }

            else
            {
                $url = "$site/$path_from_doc_root/$path/$filename";
                $file_type_note = "(unrecognized file type)";
            }



            $indent =& nbsp_based_indent($rname, $path_depth, 0);
            $link_text = $filename;
            $line_to_browser = "$indent<a href=\"$url\">" . $link_text . "</a>";

            if ( $flag__show_file_type == 'true' )
            {
                $line_to_browser = "$line_to_browser $file_type_note";
            }

            $line_to_browser = "$line_to_browser<br />\n";

            echo $line_to_browser;

        } // end FOREACH construct to iterate over files in caller's file tree hash

    } // end local scope

    echo "<br />\n"; // <-- for diagnostics readability

    show_diag($rname, "returning . . .", $dflag_dev);


} // end function present_path_elements_and_files_of_cwd()




function present_directories_with_file_counts($rname, $file_hierarchy, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to present files from a file hierarchy, filtering for 
//   and showing only directory type files followed by their respective
//   counts of regular files with each immediate directory.
//
//   Note there is additional behavior this function planned to carry
//   out:  when a user navigates to a directory containing only regular
//   files, that directory's file content is listed.  That is to say
//   that when the current working directory contains only files those
//   files are also shown by their names, though they are not of type
//   directory.
//
//
//  NOTES ON IMPLEMENTATION:  this routine present all files of given
//   file tree which are themselves directories.  In this action this
//   routine always shows the same number of items so long as the
//   file tree remains unchanged, that is nothing added nor deleted.
//   With each navigating click or user selection of a directory,
//   however, this routine tracks and highlights that selected
//   directory, which we dub the "current working directory" to match
//   the same notion in modern and long-time operating systems with
//   file systems.
//
//   A further behavior planned for this routine is to show in
//   thumbnail format or tiny icons the regular files within the
//   user's current working directory . . .
//
//
//----------------------------------------------------------------------

// VAR BEGIN

    $file_entry = null;

//
// NEED 2018-02-23 FRI - Need to take care with \$cwd which as of this
//  morning has a different meaning and use in this function than
//  elsewhere in this file.  \$cwd normally holds session state by
//  getting passed in PHP session var and or via HTTP 'get' method:
//

    $cwd = "";        // assigned from PHP session variable or $_GET variable,

    $current_path = "";  // assigned value from data member of a file tree hash entry for each file presented here,

    $path_depth = 0;  // figured by another routine which splits $current_path into path elements,

    $indent = "";  // holds string built by another routine which uses value of path depth,


    $non_hidden_path_elements = "";  // specific link to handle leading path elements which contain only one file each,

    $url = "";              // Uniform Resource Locator, generally a web address,

    $file_count_note = "";  // part of link text,

    $link_text = "";        // text which appears on web document for given URL,

    $link = "";             // combined URL, link text and mark-up for formatting,


    $files_in_cwd = null;

    $hash_of_symlinks = null;  // holds list of symlink plus filename pairs, for use with phpThumb calls,


    $phpThumb = null;
    $thumbnail_width = 50;
    $filename_for_thumbnail = "";
    $output_filename = "";     // used as parameter to local phpThumb object,
    $path_to_thumbnail = "";   // points to file which to which $output_filename ultimately refers, but relative path differs,
    $link_to_thumbnail = "";


// diagnostics:

    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_dev      = DIAGNOSTICS_OFF;
    $dflag_options  = DIAGNOSTICS_OFF;
    $dflag_warning  = DIAGNOSTICS_OFF;

    $dflag_source_of_cwd      = DIAGNOSTICS_OFF;
    $dflag_visible_path_depth = DIAGNOSTICS_OFF;
    $dflag_indent_string      = DIAGNOSTICS_OFF;
    $dflag_show_hash_files_in_cwd = DIAGNOSTICS_OFF;
    $dflag_symlink_names      = DIAGNOSTICS_ON;
    $dflag_php_thumb          = DIAGNOSTICS_OFF;

    $rname = "present_directories_with_file_counts";

// VAR END



// Look for current working directory in a couple of places:

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR, $_GET) )
    {
        show_diag($rname, "obtaining current working directory via HTTP get method . . .", $dflag_source_of_cwd);
        $cwd = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR];
    }
    else if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $_SESSION) )
    {
        show_diag($rname, "obtaining current working directory via PHP session variable . . .", $dflag_source_of_cwd);
        $cwd = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $options) )
    {
        show_diag($rname, "obtaining current working directory via options from caller . . .!", $dflag_source_of_cwd);
        $cwd = $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else    
    {
        show_diag($rname, "- WARNING - couldn't find current working directory via HTTP get method,", $dflag_warning);
        show_diag($rname, "  +  or other means.  Falling back to current working directory from first", $dflag_warning);
        show_diag($rname, "  +  entry in file tree hash:", $dflag_warning);
        $cwd = $file_hierarchy[0][FILE_PATH_IN_BASE_DIR];
    }



    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS, $options) )
    {
        $hide_first_n_path_elements = $options[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS];
    }
    else
    {
        $hide_first_n_path_elements = 0;
    }



    show_diag($rname, "starting,", $dflag_announce);

    show_diag($rname, "- 2018-02-22 - ROUTINE IMPLEMENTATION UNDERWAY -", $dflag_dev);
    show_diag($rname, "received \$options hash which holds:", $dflag_options);
    if ( $dflag_options )
    {
        echo "<pre>\n";
        print_r($options);
        echo "</pre>\n";
    }
//    show_diag($rname, "-", $dflag_dev);



//----------------------------------------------------------------------
// - STEP - show initial not-hidden file tree path elements
//----------------------------------------------------------------------

// Note - library-calling code normally sets number of leading path
//  +  elements to hide:
//    $options[KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FIRST_N_PATH_ELEMENTS] = 1;
    $non_hidden_path_elements =& link_to_first_non_hidden_path_elements($rname, $options);

    echo $non_hidden_path_elements;


//----------------------------------------------------------------------
// - STEP - show files of file tree which are directories
//----------------------------------------------------------------------

    {
        foreach ( $file_hierarchy as $key => $file_entry )
        {
            if ( $file_entry[FILE_TYPE] == KEY_VALUE__FILE_TYPE__IS_DIRECTORY )
            {

                $current_path = $file_entry[FILE_PATH_IN_BASE_DIR] . "/" . $file_entry[FILE_NAME];


// - STEP - build indent based on file depth in present file tree:

                $path_depth =& visible_path_depth($rname, $current_path, $options);
                show_diag($rname, "called for and got back visible path depth of $path_depth,", $dflag_visible_path_depth);

                $indent =& nbsp_based_indent($rname, $path_depth, 0);
                show_diag($rname, "and indent string '$indent',", $dflag_indent_string);


// - STEP - build URL:

                $url = &url_of_file_tree_intermediate_path($caller, $current_path, $options);


// - STEP - build link text:

                $link_text = $file_entry[FILE_NAME];

                if ( $file_entry[FILE_COUNT] > 0 )
                {
//                    echo $file_entry[FILE_NAME] . " (" . $file_entry[FILE_COUNT] . ")<br >\n";
                    $file_count_note = "(" . $file_entry[FILE_COUNT] . ")<br >\n";
                }
                elseif ( $file_entry[FILE_COUNT] == 0 )
                {
//                    echo $file_entry[FILE_NAME] . " . . .<br >\n";
                    $file_count_note = ". . .<br >\n";
                }
                else
                {
//                    echo $file_entry[FILE_NAME] . " (<font color=\"red\">" . $file_entry[FILE_COUNT] . "</font>) d'oh wonky file count value, should not be negative!<br >\n";
                    $file_count_note = "(<font color=\"red\">" . $file_entry[FILE_COUNT] . "</font>) d'oh wonky file count value, should not be negative!<br >\n";
                }

                $link_text = "$link_text $file_count_note";


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Note:  PHP implements loose comparison operator '==' and strict
//  comparison operation '==='.  For more details and useful truth
//  tables see PHP on-line document at:
//
//    *  http://php.net/manual/en/types.comparisons.php#types.comparisions-loose
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                if ( $current_path === $cwd )
                {
                    $url = "<b><a href=\"$url\">$link_text</a></b>";
                }
                else
                {
                    $url = "<a href=\"$url\">$link_text</a>";
                }


                if ( strlen($indent) > 0 )
                {
                    $link = "$indent $url";
                }
                else
                {
                    $link = $url;
                }


                echo $link;


//
// - STEP - show files in current working directory:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                if ( $current_path === $cwd )
                {
                    $files_in_cwd =& hash_of_files_in_cwd($rname, $file_hierarchy, $options);

                    show_diag($rname, "unsorted files in current working directory include:",
                      $dflag_show_hash_files_in_cwd);
                    if ( $dflag_show_hash_files_in_cwd )
                    {
                        echo "<pre>\n";
//            print_r($files_in_cwd);
                        foreach ( $files_in_cwd as $key => $entry )
                        {
                            echo "[$key] => '" . $entry[FILE_NAME] . "'\n";
                        }
                        echo "</pre>\n";
                    } // end local scope


//                    present_images_as_thumbnails($rname, $files_in_cwd, $options);


                    $hash_of_symlinks = create_symlinks_with_safe_names($rname, $cwd, $options);

                    if ( $dflag_symlink_names )
                    {
                        show_diag($rname, "got back hash containing in part these symbolic links:",
                          $dflag_symlink_names);
                        echo "<pre>\n";
                        foreach ( $hash_of_symlinks as $key => $entry )
                        {
                            echo "$key => '" . $entry[KEY_NAME__FILENAME] . " --> " . $entry[KEY_NAME__SYMLINK_NAME] . "'<br />\n";
                        }
                        echo "</pre>\n";
                    }


//
// - STEP - show thumbnails of image files in current working directory
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// NEED:  to make this into its own routine . . .

                    present_images_as_thumbnails($rname, $hash_of_symlinks, $cwd, $options);


// Thumbnails have now been created,

//                    echo "<br /> <br />\n";

if (0)
{
                    foreach ( $hash_of_symlinks as $key => $entry )
                    {
                        $filename_for_thumbnail = preg_replace('/z-tn--/', 'thumbnail--', $entry[KEY_NAME__SYMLINK_NAME]);
                        $path_to_thumbnail = "./lib/phpThumb/thumbnails/$filename_for_thumbnail";

                        $link_to_thumbnail = "<img border=\"1\" src=\"$path_to_thumbnail\" width=\"*\" alt=\"image thumbnail at hash entry $key\">\n";
 
                        echo $link_to_thumbnail;
                    }

                    echo "<br /> <br />\n";
}


                } // end IF-statement $current_path equals $cwd

            } // end IF-statement to test whether present file is a directory

        } // end FOREACH construct to iterate over entries of file tree hierarchy

    } // end local scope


    show_diag($rname, "returning . . .", $dflag_announce);

} // end function present_directories_with_file_counts()





// function present_files_conventional_view($caller, $file_hierarchy, $options)  // <-- name change 2018-02-22 THU - TMH
function present_files_in_selected_view($caller, $file_hierarchy, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to present the files in the passed file hierarchy hash,
//    in one of multiple ways.  File presentation ways include:
//
//    *  present files in current working directory (1)
//    *  present all directories and files fully expanded tree format
//    *  present files to a path element depth of n
//
//
//  EXPECTS:
//    *  a base directory for building correct URLs to path elements
//       in the current working directory,
//    *  a current working directory with which to filter files to
//       present and omit noted files which are in other directories,
//    *  a number of path elements to hide from the base directory
//       and further into the branches of the file tree to present
//
//
//
//  RETURNS:
//
//
//  PRODUCES:  HTML hyperlinks to directories and or files, to  be formatted 
//    per page visitor's selection of a given directory or file,
//   and possibly per some file and image gallery display options
//   provided on the web page.  All this formatting yet underway as
//   of 2018-02-13 Tuesday . . .  - TMH
//
//
//  NOTES ON IMPLEMENTATION:
//   The default "conventional" file tree view which this function
//   produces shows all files in the page visitor's current working
//   directory.  This PHP routine has a notion of a base directory at
//   the "top" of the file tree being presented, a current working
//   directory to which the user has navigated thus far, and a number
//   of path elements to omit from the presented tree view of files
//   and their containing directories, up to the base directory.
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

// optionally hide directories which are empty:
    $hide_empty_dirs = 0;

// NEED REVIEW, POSSIBLY DEPRECATE:  variable here to optionally hide files, this now handled by view mode routines
    $hide_files = 0;

// 2018-02-13 - added:
    $files_in_cwd = array();

    $url = "";
    $site = "https://neelanurseries.com";
    $path_from_doc_root = "sandbox";
    $script_name = $options["script_name"];

//    $base_directory = "";  // NOTE THERE IS A VARIABLE NAMED 'basedir' IN THIS FUNCION - TMH
    $cwd = "";
    $view_mode = "";


// flag to toggle development message:
    $flag__show_file_type = 'false';

// local string to hold development note regarding type of present or given file:
    $file_type_note = "";


// diagnostics:

    $lbuf = "";

    $indent="&nbsp; &nbsp; &nbsp;";

    $dflag_announce   = DIAGNOSTICS_ON;
    $dflag_dev        = DIAGNOSTICS_OFF;
    $dflag_warning    = DIAGNOSTICS_ON;
    $dflag_legend     = DIAGNOSTICS_OFF;
    $dflag_summary    = DIAGNOSTICS_ON;

    $dflag_empty_dirs     = DIAGNOSTICS_ON;
    $dflag_nested_loop_l1 = DIAGNOSTICS_ON;
    $dflag_nested_loop_l2 = DIAGNOSTICS_ON;

    $dflag_var_basedir    = DIAGNOSTICS_OFF;
    $dflag_source_of_cwd  = DIAGNOSTICS_OFF;
    $dflag_tracking_cwd   = DIAGNOSTICS_OFF;

    $rname = "present_files_in_selected_view";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



    show_diag($rname, "starting,", $dflag_announce);

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

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__HIDE_FILES, $_SESSION) )
    {
        $hide_files = 1;
    }


// NOTE:
// At this point when this function called, the hash $file_hierarchy
// has numeric keys starting at zero (0), yet we can't always assume
// numeric hash keys . . .  - TMH

//    $basedir = $file_hierarchy[0][FILE_PATH_IN_BASE_DIR];

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR, $_GET) )
    {
        show_diag($rname, "obtaining base directory via HTTP 'get' method . . .", $dflag_var_basedir);
        $basedir = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY_ABBR];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $_SESSION) )
    {
        show_diag($rname, "obtaining base directory via PHP session variable . . .", $dflag_var_basedir);
        $basedir = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    elseif ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY, $options) )
    {
        show_diag($rname, "obtaining base directory via passed options variable . . .", $dflag_var_basedir);
        $basedir = $options[KEY_NAME__DIRECTORY_NAVIGATION__BASE_DIRECTORY];
    }
    else
    {
//        show_diag($rname, "obtaining base directory passed hash of files in current working directory . . .", $dflag_var_basedir);
//        $first_file_tree_hash_entry = reset($files_in_cwd);
//        $basedir = $first_file_tree_hash_entry[FILE_PATH_IN_BASE_DIR];
        show_diag($rname, "- WARNING - unable to determine base directory of files to show", $dflag_warning);
        show_diag($rname, "  +  via get method, PHP session var or calling code options!", $dflag_warning);
        show_diag($rname, "  +  returning early to calling code . . .", $dflag_warning);
        return;
    }


// Look for current working directory in a couple of places:

    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR, $_GET) )
    {
        show_diag($rname, "obtaining current working directory via HTTP get method . . .", $dflag_source_of_cwd);
        $cwd = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__CWD_ABBR];
    }
    else if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $_SESSION) )
    {
        show_diag($rname, "obtaining current working directory via PHP session variable . . .", $dflag_source_of_cwd);
        $cwd = $_SESSION[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__CWD, $options) )
    {
        show_diag($rname, "obtaining current working directory via options from caller . . .!", $dflag_source_of_cwd);
        $cwd = $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD];
    }
    else    
    {
        show_diag($rname, "- WARNING - couldn't find current working directory via HTTP get method,", $dflag_warning);
        show_diag($rname, "  +  or other means.  Falling back to current working directory from first", $dflag_warning);
        show_diag($rname, "  +  entry in file tree hash:", $dflag_warning);
        $cwd = $file_hierarchy[0][FILE_PATH_IN_BASE_DIR];
    }


    if ( array_key_exists(KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR, $_GET) )
    {
        $view_mode = $_GET[KEY_NAME__DIRECTORY_NAVIGATION__FILE_TREE_VIEW_MODE_ABBR];
    }
    else
    {
        $view_mode = KEY_VALUE__DIRECTORY_NAVIGATION__DEFAULT_FILE_VIEW_MODE_ABBR;
    }


    show_diag($rname, "\$basedir set to '$basedir',", $dflag_dev);
    show_diag($rname, "\$cwd set to '$cwd',", $dflag_dev);
    show_diag($rname, "\$view_mode set to '$view_mode',", $dflag_dev);


// Copy current working directory to local $options hash, to provide
// to various file presenting formatters:

    $options[KEY_NAME__DIRECTORY_NAVIGATION__CWD] = $cwd;


    show_diag($rname, "enabling note about file type for development,", $dflag_dev);
    $flag__show_file_type = 'true';

    show_diag($rname, "about to show file hierarchy which has " . count($file_hierarchy) . " elements:", $dflag_dev);



    {

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  Loop 1 to gather files in current working directory, for sorting:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        foreach ( $file_hierarchy as $key => $file_entry )
        {
            show_diag($rname, "looking for files in '$cwd', path from base dir to present file is '" .
              $file_entry[FILE_PATH_IN_BASE_DIR] . "',", $dflag_tracking_cwd);
//            if ( basename($file_entry[FILE_PATH_IN_BASE_DIR]) == $cwd )
            if ( $file_entry[FILE_PATH_IN_BASE_DIR] == $cwd )
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


//
// - STEP - based on present view mode, select a routine to format and
//    to show files or subset of files from present file tree:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        switch ($view_mode)
        {
            case KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_CWD_ABBR:
                present_path_elements_and_files_of_cwd($rname, $files_in_cwd, $options);
                break;

            case KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_DIRECTORIES_AND_FILE_COUNTS_ABBR:
                present_directories_with_file_counts($rname, $file_hierarchy, $options);
                break;

            case KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_DIRECTORIES_TO_DEPTH_N_ABBR:
                show_diag($rname, "File browsing view mode 'directories to depth n' not yet implemented!", $dflag_warning);
                break;

            case KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_GALLERY_ABBR:
                show_diag($rname, "File browsing view mode 'files in gallery' not yet implemented!", $dflag_warning);
                break;

            default:
                present_path_elements_and_files_of_cwd($rname, $files_in_cwd, $options);
        }

    } // end local scope


    show_diag($rname, "returning . . .", $dflag_announce);

} // end function present_files_in_selected_view()





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
    $dflag_get = DIAGNOSTICS_OFF;
    $dflag_session_var = DIAGNOSTICS_OFF;
    $dflag_options     = DIAGNOSTICS_OFF;
    $dflag_announce_function_calls = DIAGNOSTICS_ON;
    $dflag_file_hash_tree_in_full  = DIAGNOSTICS_OFF;

    $rname = "present_tree_view";

// VAR END


    if ( 0 )
    {
        $dflag_dev = DIAGNOSTICS_OFF;
        $dflag_get = DIAGNOSTICS_OFF;

        $dflag_session_var = DIAGNOSTICS_OFF;
        $dflag_options     = DIAGNOSTICS_OFF;

        $dflag_announce_function_calls = DIAGNOSTICS_OFF;
        $dflag_file_hash_tree_in_full  = DIAGNOSTICS_OFF;  // end block to turn all diagnostics off
    }


    show_diag($rname, "starting,", $dflag_dev);

    show_diag($rname, "calling code '$caller' sends options:", $dflag_options);
    if ( $dflag_options )
    {
        echo "<pre>\n";
        print_r($options);
        echo "</pre>\n";
    }


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
    }

// 2018-01-28 - For development only:
//    show_select_attributes_of_file_tree_hash_entries($rname, $file_hierarchy);
//    echo $term . $term;

    show_diag($rname, "calling function to present files in user-selected view mode . . .", $dflag_dev);
    present_files_in_selected_view($caller, $file_hierarchy, $options);


    if ( $dflag_file_hash_tree_in_full )
    {
        show_diag($rname, "file tree hash in full:", $dflag_file_hash_tree_in_full);
        echo "<pre>\n";
        print_r($file_hierarchy);
        echo "</pre>\n";
    }


    show_diag($rname, "returning . . .", $dflag_dev);

} // end function present_tree_view()




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



// - 2018-02-15 - from function present_path_elements_and_files_of_cwd() . . .

//    show_diag($rname, "have following path to parse and show its elements:", $dflag_first_hash_entry);
//    $lbuf = "'" . $files_in_cwd[0][FILE_PATH_IN_BASE_DIR] . "'";
//    show_diag($rname, $lbuf , $dflag_first_hash_entry);
//
//    if ( $dflag_first_hash_entry )
//    {
//        show_diag($rname, "files in current working directory:" , $dflag_first_hash_entry);
//        echo "<pre>\n";
//        print_r($files_in_cwd);
//        echo "</pre>\n";
//    }



//        if ( array_key_exists(KEY_NAME__BASE_DIRECTORY, $_SESSION) )
//        {
//            $path = $_SESSION[KEY_NAME__BASE_DIRECTORY];
//        }
//        else
//        {
//            show_diag($rname, "- WARNING - unable to determine base directory of calling code's", $dflag_warning);
//            show_diag($rname, "- WARNING - file tree!  returning early . . .", $dflag_warning);
//            return;
//        }




// End of file directory-navigation.php

?>
