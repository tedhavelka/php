<?php

//======================================================================
//
//  FILE:  site-navigation-routines.php
//
//  DESCRIPTION:  PHP library of Ted's, to hold routines related to
//   building site navigation menus, based in part on filenames and on
//   site meta-data stored in files . . .   - TMH
//
//  STARTED:  2017-08-31 THU
//
//
//  REFERENCES:
//
//    * REF *  http://php.net/manual/en/function.array-search.php
//
//    * REF *  http://php.net/manual/en/function.unset.php
//
//    * REF *  http://php.net/manual/en/function.preg-replace.php
//
//    * REF *  http://php.net/manual/en/language.types.string.php
//
//    * REF *  https://www.ibm.com/developerworks/library/os-php-readfiles/index.html -- 2017-09-28
//
//    * REF *  https://www.tutorialspoint.com/php/php_regular_expression.htm -- 2017-09-28
//
//    * REF *  http://php.net/manual/en/control-structures.break.php
//
//    * REF *  http://php.net/manual/en/function.get-defined-constants.php
//
//
//
//
//======================================================================





//----------------------------------------------------------------------
// - SECTION - PHP include directives
//----------------------------------------------------------------------

    require_once '/opt/nn/lib/php/defines-nn.php';

    require_once '/opt/nn/lib/php/diagnostics-nn.php';

    require_once '/opt/nn/lib/php/file-and-directory-routines.php';




//----------------------------------------------------------------------
// - SECTION - PHP file-scoped constants
//----------------------------------------------------------------------

    define("KEY_NAME_FOR_URL", "url");
    define("KEY_NAME_FOR_LINK_TEXT", "link_text");
    define("KEY_NAME_FOR_LINK_STATUS", "link_status");

    define("POSTFIX__LINK_DISABLED", "--disabled");
    define("POSTFIX__LINK_POINTS_TO_CWD", "--in-current-directory");
    define("POSTFIX__HIDE_NAV_LINK_WHEN_IN_CURRENT_DIRECTORY", "--hide-link-in-current-directory");

    define("LIMIT_FOR_DETAILED_DIAGS", 20);



//----------------------------------------------------------------------
// - SECTION - diagnostics and development
//----------------------------------------------------------------------

function nn_show_array__version_local($caller, $array_reference, $options)
{

    $rname = "nn_show_array__version_local";

    $count_of_elements_in_array = count($array_reference);

    if ( $count_of_elements_in_array > 0 )
    {
        foreach ($array_reference as $key => $item)
        {
            echo "$key => '$item'<br />\n";
        }

    }

}




//----------------------------------------------------------------------
// - SECTION - routines to construct navigation menus
//----------------------------------------------------------------------

/*
function ___place_holder___
*/


function nav_menu_layout__opening_lines($caller, $options)
{

    $mark_margins_with = "";

    if ( array_key_exists(KEY_NAME__SITE_NAVIGATION__MARK_MARGINS_WITH, $options) )
        { $mark_margins_with = $options[KEY_NAME__SITE_NAVIGATION__MARK_MARGINS_WITH]; }

    echo "<!-- BEGIN LAYOUT for Neela Nurseries top menu, navigation bar -->
<div class=\"container\">
  <div class=\"menu-bar-top\">
    <div class=\"menu-block-element-as-margin\">
";

    if ( strlen($mark_margins_with) > 0 )
    {
//      <font color=\"#ffffff\"> . . . </font>
        echo "      <font color=\"#ffffff\">$mark_margins_with</font>\n";
    }
    else
    {
        echo "&nbsp;\n";
    }

echo "    </div>\n\n";

}




function nav_menu_layout__closing_lines($caller, $options)
{

    $mark_margins_with = "";

    if ( array_key_exists(KEY_NAME__SITE_NAVIGATION__MARK_MARGINS_WITH, $options) )
        { $mark_margins_with = $options[KEY_NAME__SITE_NAVIGATION__MARK_MARGINS_WITH]; }

    echo "    <div class=\"menu-block-element-as-margin\">\n";

    if ( strlen($mark_margins_with) > 0 )
    {
//        echo "      <font color=\"#ffffff\"> . . . </font>\n";
        echo "      <font color=\"#ffffff\">$mark_margins_with</font>\n";
    }
    else
    {
        echo "&nbsp;\n";
    }

    echo "
    </div>
  </div><!-- close of div element of type 'menu-bar-top' for menu bar 1 -->
</div><!-- close of div element of type 'container' -->
<!-- END LAYOUT for Neela Nurseries top menu, navigation bar -->\n\n\n";

}





// zzz

function &navigation_items_via_filenames(
  $caller,
  $include_path,
  $include_pattern,
  $exclude_path,
  $exclude_pattern,
  $options)
{
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//
//  PURPOSE:  to build a PHP array (an ordered map) of a given site's
//   pages, and or external URLs, to be presented in a navigation menu.
//   The aim of this routine is to provide a dynamic response via PHP
//   coding, to the filenames present in a given directory of a site's
//   content.
//
//  EXPECTS:
//    *  calling code name or identifer,
//    *  file system path to directory to search for navigation markers,
//    *  pattern to match for items to include in nav list,
//    *  file system path to directory to search for 'exclude item' markers,
//    *  pattern to match for items to exclude in nav list,
//    *  additional options ( 2017-08-31 not yet implemented )
//
//  RETURNS:
//    *  an array of navigation item names
//
//
//  NOTES ON IMPLEMENTATION:
//
//   On Unix and Unix-like systems, filenames can identify among other
//   entities,
//
//     *  regular files
//     *  directories
//     *  symbolic links
//
//   Via thoughtful, strategic file naming, each of these file system
//   elements can be named to act as markers or indicators of particular
//   site content to enable or disable.
//
//
//
//   While site navigation can be
//   expressed and managed using data or meta-data in text files or
//   even a database, these ways are more complicated and more likely
//   part of a much larger content management framework implementation.
//   These PHP routines are intended to be smaller and limited in scope,
//   just enough to get some repetitive site mark-up generation
//   automated and off the ground.

//
//
//  GENERATES HTML FOR WEB BROWSERS:  no
//
//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// array to hold navigable site items, and to return to calling code:
    $navigation_menu_items = array();

// local array for temporary storage of items which may appear in array of navigable items, but not be planned for publication yet on the give site:
    $items_to_exclude = array();

// local string parsing array, holds results of PHP preg_match() routine:
    $matches = array();

// . . .
    $marker_infix = "--nn--";

// . . .
    $item_name_only = "";


// mark-up string for diagnsotics to web browsers:
    $term = "<br />\n";

// routine name or function name:
    $rname = "navigation_items_via_filenames";


    
// DEV - show parameters passed to us from calling code:

/*
    echo "2017-08-31 - implementation underway,<br />\n";

    echo "called by '$caller'," . $term;
    echo "caller sends path set to '$include_path'," . $term;
    echo "pattern of items to include holds '$include_pattern'," . $term;
    echo "caller sends path set to '$exclude_path'," . $term;
    echo "pattern of items to exclude holds '$exclude_pattern'," . $term;
    echo "and options string holding '$options'." . $term . $term;
*/

// END DEV



// - STEP - search caller's file system path for navigable site items:

//    $navigation_menu_items =& list_of_filenames_by_pattern($rname, $include_path, $include_pattern);
    $navigation_menu_items =& list_of_filenames_sorted_by_same_marker($rname, $include_path, $marker_infix, 2);


// - STEP - search caller's path for items explicitly marked to exclude from list of navigables:

    $items_to_exclude =& list_of_filenames_by_pattern($rname, $exclude_path, $exclude_pattern);


// - STEP - remove the include pattern, a prefix or infix, from all found navigation menu items:

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  The following text clean-up not needed when calling function
//  list_of_filenames_sorted_by_same_marker() - TMH
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( 0 )
    {
        foreach ($navigation_menu_items as $key => $value)
        {
            echo "$rname:  - DEV - while removing prefix characters from found menu item, working with \$key = '$key',$term";  
            preg_match('@(.*--nn--)(.*)@', $value, $matches);  // . . . call preg_match() with pattern, string_to_search, array_of_matches_found
            echo "$rname:  - DEV - \$matches[0] holds '". $matches[0] ."',$term";  
            echo "$rname:  - DEV - \$matches[1] holds '". $matches[1] ."',$term";  
            echo "$rname:  - DEV - \$matches[2] holds '". $matches[2] ."',$term";  
            $navigation_menu_items[$key] = $matches[2];
        }
    }


// - STEP - search in caller's specified path for items to exclude from navigation list:

    foreach ($items_to_exclude as $key => $value)
    {
        preg_match('@(.*--nn--)(.*)(.*--exclude-from-nav$)@', $value, $matches);
        $items_to_exclude[$key] = $matches[2];
    }

//    echo "- DEV - array of navigation menu items to exclude holds:" . $term;
//    nn_show_array($rname, $items_to_exclude, "--no-options");
//    echo $term;




//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  TO-DO:  yet need to delete from array of navigation menu items,
//    all items parsed and noted in array of item to exclude from
//    list of navigation points at given page of given site - TMH
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//

// - STEP - for each navigation item, point, named point, delete that in nav menu list array:

    foreach ($items_to_exclude as $key => $value)
    {
// echo "- DEV - looking for navigation menu item to exclude, item by name of '$value' . . .$term";

        $key_to_navigation_item = array_search($value, $navigation_menu_items);

        if ( $key_to_navigation_item !== FALSE )
        {
// echo "- DEV - in menu items array, found $key_to_navigation_item => " . $navigation_menu_items[$key_to_navigation_item] . $term;

            unset($navigation_menu_items[$key_to_navigation_item]);
        }
    }

//    echo $term . $term;



//   .
//   .
//   .



    return $navigation_menu_items;

} // end function &navigation_items_via_filenames





// zzz

function nn_horizontal_navigation_menu($caller, $base_url, $include_path, $exclude_path, $options)
{
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//
//  PURPOSE:  to request a list of navigation menu items from code
//   which can read filenames in directories of a given web site, then
//   to present these navigation menu items in one of the manners,
//
//     *  hyper reference to another page, file or site
//     *  plain text name of item, greyed out
//     *  item hidden, not shown
//
//
//  OPTIONS SUPPORTED BY THIS PHP FUNCTION:
//
//     *  '--show-not-ready'
//
//
//  GENERATES HTML FOR WEB BROWSERS:  yes
//
//
//  NOTES ON IMPLEMENTATION:  this routine gets a hash, a PHP ordered
//   map of what are effecitively directory names, from another function
//   which reads and parses file names and directory names and builds
//   that hash.  Most of the detailed work this routine carries out is
//   to generate HTML mark-up language and hyperlinks, creating a
//   menu which typically gets sent to a web browser as part of a site
//   page.
//
//   Ted here noting that this routine has some limitations and is
//   really a first step and a developmental step onlly, not very useful
//   for production use.  See routine nn_menu_building_hybrid_fashion()
//   to view next steps in navigation menu building efforts of Ted's.
//
//
//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// VARIABLES

    $menu_items = array();

    $last_item = 0;

    $menu_item = "DEFAULT_MENU_ITEM";

    $menu_item_minus_postfix = "DEFAULT_MENU_ITEM_MINUS_POSTFIX";


// local text string for use during development and diagnostics:
    $term = "<br />\n";

    $rname = "nn_horizontal_navigation_menu";

// END VARIABLES



// DEV - show parameters passed to us from calling code:

/*
    echo "2017-08-31 - implementation underway,<br />\n";

    echo "called by '$caller'," . $term;
    echo "caller sends include path set to '$include_path'," . $term;
    echo "and exclude path set to '$exclude_path'," . $term;
    echo "and options string holding '$options'." . $term . $term;
*/

// END DEV


// Parameters in following call - caller, path_to_search, pattern_include, pattern_exclude, options . . .

    $menu_items =& navigation_items_via_filenames(
      $rname,
      $include_path, "/.*-nn-*/",
      $exclude_path,  "/.*--exclude-from-nav*/",
      "--no-options"
    );



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - IN FUNCTION - process menu items per caller's options, before
//    generating navigation menu:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// - STEP - send navigation menu mark-up opening lines:

    nav_menu_layout__opening_lines($rname, $options);

    $last_item = count($menu_items);

// Actually we may only want a navigation menu sorted alphabetically some of the time - TMH:
    if ( 0 )
    {
        sort($menu_items);
    }


    foreach ( $menu_items as $key => $menu_item )
    {

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - Look for menu items marked 'not ready' . . .
//
//  ( preg_match parameters are pattern, target_string, array_of_pattern_matches )
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// echo "$rname:  DEV - looking at menu item '$
        preg_match('/.*--not-ready/', $menu_item, $matches);

        if ( $matches )
        {
            if ( $options === "--show-not-ready" )
            {
                $menu_item_minus_postfix = preg_replace('/--not-ready/', '', $menu_items[$key]);
                echo "<div class=\"menu-item\"> <font color=\"#7f7f7f\">" . $menu_item_minus_postfix . "</font> </div>\n\n";
            }
            else
            {
                // show nothing
            }
        }
        else
        {
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - ELSE to above STEP - handle ready, active navigation menu items . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            preg_match('/.*--in-current-directory/', $menu_item, $matches);

            if ( $matches )
            {
                $menu_item_minus_postfix = preg_replace('/--in-current-directory/', '', $menu_item);
                echo "<div class=\"menu-item\"> <a href=\"$base_url\">$menu_item_minus_postfix</a> </div>\n\n";
            }
            else
            {
//                echo "<div class=\"menu-item\"> <a href=\"$base_url/" . $menu_items[$key] . "\">" . $menu_items[$key] . "</a> </div>\n\n";
                echo "<div class=\"menu-item\"> <a href=\"$base_url/$menu_item\">$menu_item</a> </div>\n\n";
            }
        }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// place a visual separator between navigation menu items, looks best
// when menu is laid out as a horizontal menu:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        if ( $key < ($last_item - 1) )
        {
            echo "<div class=\"menu-item-text-separator\"> : </div>\n\n";
        }
    }

    nav_menu_layout__closing_lines($rname, $options);

    echo "done.$term$term$term";


} // end function nn_horizontal_navigation_menu()





/*
function ___place_holder___
*/


function &nn_nav_menu_entry($caller)
{

//    $nav_menu_entry = array("url" => "DEFAULT URL", "link_text" => "DEFAULT LINK NAME", "link_status" => "disabled");
    $nav_menu_entry = array(
      KEY_NAME_FOR_URL => "DEFAULT URL",
      KEY_NAME_FOR_LINK_TEXT => "DEFAULT LINK TEXT",
      KEY_NAME_FOR_LINK_STATUS => "disabled"
    );

    return $nav_menu_entry;

}




function present_menu_from_hash_of_hashes($caller, $hash_reference, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to read a specifcally structured PHP hash of hashes, and
//    present the values of this hash table as an HTML formatted menu
//    of URLs and plain text place holders in menu.  As of 2017-11-03
//    this routine supports enabled and disabled menu items.  Enabled
//    menu items are formatted as HTML links, while disabled menu
//    items are formatted as plain text.  Menu items may also be
//    qualified as 'hidden', and in this case they're not laid out in
//    the menu at all.
//
//
//  SUPPORTED OPTIONS:
//
//    SITE_MENU_DISABLED_LINK_COLOR
//
//       If array $options contains a key with the name
//       SITE_MENU_DISABLED_LINK_COLOR, this color is applied to
//       disable menu item links.  The value of this attribute is
//       treated as a string.  It's expected and not checked to be
//       either a valid HTML color name, or a red-green-blue triplet
//       of the form #rrggbb.
//
//
//
//
//  NOTES ON IMPLEMENTATION:
//    This routine part of 'build_menu_hybrid_fashion' PHP library
//    effort.  This routine is designed to take a hash of hashes with
//    this following form:
//
//
//    $hash_reference
//         |
//        001 => $hash_reference
//         |            |
//         |           url => "given URL"
//         |           link_text => "link text presented as hyperlink"
//         |           link_status => "[ enabled | disabled | hidden ]"
//         |
//        002 => $hash_reference
//         |            |
//         |           url => "given URL"
//         |           link_text => "link text presented as hyperlink"
//         |           link_status => "[ enabled | disabled | hidden ]"
//         |
//        003 => $hash_reference
//         |            |
//         |            .
//         |            .
//         |            .
//         |
//        nnn
//
//
//  Most hash key names in this project are defined PHP constants,
//  as of 2017-11-03 set in local PHP library file 'defines-nn.php'.
//
//
//  REFERENCES:
//
//    * REF *  http://php.net/manual/en/function.is-array.php
//
//
//
//
//
//----------------------------------------------------------------------



    $key = "";          // . . . used to traverse passed hash reference,

    $value = NULL;      // . . . holds given value from passed hash reference,

    $url = "";
    $link_text = "";
    $link_status = "";  // . . . elements used to build one menu item,

    $last_item = 0;     // . . . hold count of assumed valud menu items in caller's hash table,

    $site_menu_disabled_link_color = "#707070";

// diagnostics:

    $rname = "present_menu_from_hash_of_hashes";


    show_diag($rname, "starting,", 0);
    show_diag($rname, "called by '$caller',", 0);


//
// TO-DO:  add foreach construct to check for presence of one valid
//   child hash in the caller's passed hash reference:
//
//    * REF *  http://php.net/manual/en/control-structures.break.php
//



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - SECTION - send menu in mark-up form to browser or standard out
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $last_item = count($hash_reference);   // NOTE this assignment assumes all keys point to complete valid menu item entries in caller's hash.

    nav_menu_layout__opening_lines($rname, $options);


    foreach ($hash_reference as $key => $value)
    {
        if (is_array($value))
        {
// Here we want to check for the existence of three specifically named
// hash keys, and only present a menu item when the keys exists and
// when the key pointing to navigation item status holds the value
// "eneabled" . . .

/*
            if ( isset($hash_reference[$key][KEY_NAME_FOR_URL] ) )
                { $url = $hash_reference[$key][KEY_NAME_FOR_URL]; }

            if ( isset($hash_reference[$key][KEY_NAME_FOR_LINK_TEXT] ) )
                { $link_text = $hash_reference[$key][KEY_NAME_FOR_LINK_TEXT]; }
*/

            if ( isset($hash_reference[$key][KEY_NAME_FOR_LINK_STATUS] ) )
                { $link_status = $hash_reference[$key][KEY_NAME_FOR_LINK_STATUS]; }


// TO-DO 2017-10-03 - place the hard-coded strings `enabled' and `disabled' into PHP constants,
//  called out at the top of this routine or somewhere which makes it easy to see what
//  constant values are used in the scope of this routine - TMH

            if ( 0 == strncmp("enabled", $link_status, LENGTH__KEY_NAME) )
            {
                if ( isset($hash_reference[$key][KEY_NAME_FOR_URL] ) )
                    { $url = $hash_reference[$key][KEY_NAME_FOR_URL]; }

                if ( isset($hash_reference[$key][KEY_NAME_FOR_LINK_TEXT] ) )
                    { $link_text = $hash_reference[$key][KEY_NAME_FOR_LINK_TEXT]; }

                echo "<div class=\"menu-item\"> <a href=\"$url\">" . $link_text . "</a> </div>\n\n";

                if ( $key < ($last_item - 1) )
                {
                    echo "<div class=\"menu-item-text-separator\"> : </div>\n\n";
                }
            }
            elseif ( 0 == strncmp("disabled", $link_status, LENGTH__KEY_NAME) )
            {
                if ( isset($hash_reference[$key][KEY_NAME_FOR_LINK_TEXT] ) )
                    { $link_text = $hash_reference[$key][KEY_NAME_FOR_LINK_TEXT]; }

//                echo "<div class=\"menu-item\">$link_text</div>\n\n";
                echo "<div class=\"menu-item\"><font color=$site_menu_disabled_link_color>$link_text</font></div>\n\n";

                if ( $key < ($last_item - 1) )
                {
                    echo "<div class=\"menu-item-text-separator\"> : </div>\n\n";
                }
            }
        }
        else
        {
// Do nothing, or print warning that top-level hash entry not itself a hash reference,
        }
    }


    nav_menu_layout__closing_lines($rname, $options);



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

} // end function present_menu_from_hash_of_hashes()




function nn_menu_building_hybrid_fashion($caller, $path_to_search, $filename_infix, $filename_postfix, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to construct site menus from symbolic links and or
//    text files with a particular three-line format.
//
//  EXPECTS:
//    *  calling code identifying string,
//    *  full path to directory containing symlinks and text files for navigation menu, 
//    *  filename infix pattern for initial filtering of files to treat as possible menu item sources,
//    *  filename postfix pattern used for . . .
//
//
//   Code of this routine acts on matching filenames in three ways,
//   generally one way per file, depending on the following conditions:
//
//      
//      *  symlinks which point to directories in the working directory
//
//      *  text files which name the working directory or another directory
//
//      *  text files which name an external URL
//
//
//
//
//  [working_directory]
//     |
//     |
//     +  01--infix--site-page-1 --> ./site-page-1  . . . a directory
//     +  02--infix--site-page-2 --> ./site-page-2  . . . another directory
//     +  03--infix--site-page-3 --> ./site-page-3  . . . another directory
//     |
//     +  04--infix--site-page-4.txt     . . . a text file which contains "URL=..
//     |                                                                   LINK-TEXT=section index"
//     |
//     +  05--infix--site-page-5.txt     . . . a text file which contains "URL=../..
//     |                                                                   LINK-TEXT=site home"
//     |
//     +  06--infix--external-link.txt   . . . a text file which contains "URL=https://www.google.com
//     |                                                                   LINK-TEXT=Google search engine"
//     |
//
//
// NOTE: filenames this routine searches for are of the form,
//
//    (navigation item ordering number)(filename infix)(navigable location name)[(postfix for item attributes)]
//
// An example filename is,
//
//    05--nn--resources
//
// Assuming the site developer starts numbering files and symbolic links
// at the value 1, then this example file expresses a navigable
// directory which is fifth in a list of navigation menu items at least
// five items long.  The nav' item ordering number can start elsewhere,
// for example from zero.  The next example filename is of a text file
// which appears first in the navigation list,
//
//   00--nn--home.txt
//
// Rather than expressing a directory in the initiating script's current
// directory, the text file can specify an arbitrary URL, which includes
// external URLs which are akin to absolute paths, in the "." versus
// absolute path sense.
//
//
//  REFERENCES:
//
//    *  - REF - http://php.net/manual/en/language.types.null.php
//
//    *  - REF - http://php.net/manual/en/language.oop5.php
//
//
//
//  TO-DO:
//
//    [   ]  add usage message and sanity check on passed values,
//
//    [   ]  add finite bound on number of files to process,
//
//
//----------------------------------------------------------------------


//    define("PATTERN_TO_MATCH_NAV_ITEM_FILENAME", "@(.*)($filename_infix)(.*)@i");
    define("PATTERN_TO_MATCH_NAV_ITEM_FILENAME", "/(^\d{2})(--\w+--)(\w+)(.*)/i");

    define("PATTERN_TO_MATCH_NAV_ITEM_FILENAME_WITH_POSTFIX", "/(^\d{2})(--\w+--)(\w+)(--.*)/i");


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $show_usage = false;

    $handle = NULL;                // . . . file handle to directory to search for navigation menu item files,

    $nav_links = array();          // . . . PHP hash of hashes to hold navigation menu items and to return,

    $integer_key_value = 1;        // . . . integer value which gets zero-padded to build top-level hash key names for $nav_links,

    $key_name = "";                // . . . string to hold constructed hash key names,

    define("KEY_NAME_LENGTH", 3);  // . . . constant to define character width of hash key names,

    $pattern = "";                 // . . . locally built regex making use of calling code's filename infix pattern,


// for specific files found during this function's execution:

    $full_path_to_file;            // . . . constructed full path to given file matching caller's infix pattern,

    $result = false;               // . . . variable to hold test result beyond local block of conditional test,

    $matches = array();            // . . . array to hold results of calls to PHP preg_match() pattern matching function,

    $count_filenames_matching = 0;
    $count_filenames_not_matching = 0;


// . . . these four variables used to exclude current dir from navigation menu,

    $current_directory = "";
    $current_directory_dirname_only = "";
// NEED TO GET RID OF THESE KLUDGY VARIABLES:
//    $link_text_minus_disabled_postfix = "";
//    $link_text_minus_postfix = "";


// file handle used to read regular files, as opposed to symbolic links:

    $handle_to_file = NULL;        // . . . handle to given file which tests as type "regular file",

    $line = "";                    // . . . line of text from regular file whose name matches caller's infix pattern,

// 2018-01-16 - found undefined, Ted adding:
    $lines_read = 0;

    $intermediate_string = "";     // . . . intermediate string for development of text processing stuff,


// local flags set by presence of options passed by call in last parameter:
//  ( Note:  general options parsing not yet implemented, flags set manually here . . . TMH )

    $flag__hide_not_ready         = true;
    $flag__hide_disabled          = false;
    $flag__hide_current_directory = true;

// Used in near-end-of-loop test to hide certain navigation menu links:
    $postfix_to_hide_link = "";


// diagnostics and formatting:

    $dmsg = "";                          // . . . local diagnostics message string for development and debugging,

    $term = "<br />\n";

    $dflag_announce = DIAGNOSTICS_OFF;   // . . . diagnostics flag for development-related run time comments,
    $dflag_dev      = DIAGNOSTICS_OFF;   // . . . diagnostics flag for development-related run time comments,
    $dflag_verbose  = DIAGNOSTICS_OFF;    // . . . diagnostics flag for verbose messages during development,
    $dflag_warning  = DIAGNOSTICS_ON;    // . . . diagnostics flag to toggle warnings in this routine,

// Some flags for diagnostics at specific steps in routine development:
    $dflag_matching_patterns     = DIAGNOSTICS_OFF;
    $dflag_parse_filename        = DIAGNOSTICS_OFF;
    $dflag_infix                 = DIAGNOSTICS_OFF;
    $dflag_link_text             = DIAGNOSTICS_OFF;
    $dflag_key_exists            = DIAGNOSTICS_OFF;

    $dflag_filename_non_matching = DIAGNOSTICS_OFF;
    $dflag_parse_file_text       = DIAGNOSTICS_OFF;
    $dflag_show_nav_links_hash   = DIAGNOSTICS_OFF;
    $dflag_top_of_loop           = DIAGNOSTICS_OFF;

    $dflag_show_defines = DIAGNOSTICS_OFF;
    $dflag_show_matches = DIAGNOSTICS_OFF;

// diagnostics for code to hide current working directory from showing in navigation menu:
    $dflag_hide_cwd = DIAGNOSTICS_OFF;
    $dflag_hide_item_tests = DIAGNOSTICS_OFF;
    $dflag_mark_for_nav_menu_margin = DIAGNOSTICS_OFF;

    $loop_counter = 0;

    $rname = "nn_menu_building_hybrid_fashion";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// 2017-11-11 added . . .

    if ( array_key_exists(KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS, $options) )
    {
//        show_diag($rname, "caller passing us key holding site navigation diagnostics setting request,", DIAGNOSTICS_ON);
        if ( $options[KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS] == DIAGNOSTICS_ON )
        {
            show_diag($rname, "enabling some diagnostics,", DIAGNOSTICS_ON);
            $dflag_announce = DIAGNOSTICS_ON;
            $dflag_dev = DIAGNOSTICS_ON;
            $dflag_parse_filename = DIAGNOSTICS_ON;
            $dflag_show_nav_links_hash = DIAGNOSTICS_ON;
            $dflag_hide_cwd = DIAGNOSTICS_ON;
        }
    }


    if ( array_key_exists(KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS_DETAILED, $options) )
    {
        $one_up_diags = preg_split('/,/', $options[KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS_DETAILED], LIMIT_FOR_DETAILED_DIAGS);
//        echo "Calling code requests detailed diags:" . $term . "<pre>\n";
//        print_r($one_up_diags);
//        echo "</pre>\n\n";

        if ( 0 == strncmp($one_up_diags[1], "top of loop", LENGTH__TOKEN) )
            { $dflag_top_of_loop = DIAGNOSTICS_ON; }

        if ( 0 == strncmp($one_up_diags[2], "pattern matching", LENGTH__TOKEN) )
            { $dflag_matching_patterns = DIAGNOSTICS_ON; }

        if ( 0 == strncmp($one_up_diags[3], "parse filename", LENGTH__TOKEN) )
            { $dflag_parse_filename = DIAGNOSTICS_ON; }

        if ( 0 == strncmp($one_up_diags[4], "link text", LENGTH__TOKEN) )
            { $dflag_link_text = DIAGNOSTICS_ON; }

    }





    show_diag($rname, "starting,", $dflag_announce);

    show_diag($rname, "ROUTINE UNDER DEVELOPMENT<br />\n<br />", $dflag_dev);

    if ( $dflag_mark_for_nav_menu_margin )
    {
        $lbuf = "caller sends mark for nav menu margin of '" . 
          $options[KEY_NAME__SITE_NAVIGATION__MARK_MARGINS_WITH] . "'";
        show_diag($rname, $lbuf, $dflag_mark_for_nav_menu_margin);
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - sanity checks of passed values
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// $caller, $path_to_search, $filename_infix, $filename_postfix, $options)

    $show_usage = 0;
    if ( 0 == strlen($caller) ) { show_diag($rname, "WARNING - calling code sends no name, zero-length identifying string.", $dflag_warning); $show_usage = true; }
    if ( 0 == strlen($path_to_search) ) { show_diag($rname, "WARNING - calling code sends no path to read for filename.", $dflag_warning); $show_usage = true; }
    if ( 0 == strlen($filename_infix) ) { show_diag($rname, "WARNING - calling code sends no filename infix to apply to found filenames.", $dflag_warning); $show_usage = true; }

    if ( $show_usage )
    {
        show_diag($rname, " &nbsp;$rname:  parameters and use are . . .

    $rname(\$caller, \$path_to_search, \$filename_infix, \$filename_postfix, \$options)

 &nbsp;\$caller           . . . identifies calling code, usually by routine or code block name,
 &nbsp;\$path_to_search   . . . names path to search for files which represent navigable web page URLs,
 &nbsp;\$filename_infix   . . . text pattern which calling code expects in files which represent desired URL data,
 &nbsp;\$filename_postfix . . . text pattern to help exclude certain text files which are yet needed as site developement markers,
 &nbsp;\$options          . . . array of options whose key names are mostly defined in local library file defines-nn.php
", MESSAGE_ONLY);
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP -
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( $handle = opendir($path_to_search) )
    {
        show_diag($rname, "call to opendir() succeeded!", $dflag_dev);

//        $pattern = "@(.*)($filename_infix)(.*)@i";
        $pattern = PATTERN_TO_MATCH_NAV_ITEM_FILENAME;
        show_diag($rname, "- ZZZ - looking for filenames matching $pattern . . .", $dflag_matching_patterns);


// 2017-10-04 - these two assignments used to optionally exclude this
// script's directory from navigable menu items:

        $current_directory = getcwd();
        $current_directory_dirname_only = basename($current_directory);




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - iterate over files in the directory named to us by calling
//          code
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//  While there are filenames to read from the opened file system directory:

        while (false !== ($current_filename = readdir($handle)))
        {

            show_diag($rname, "- TOP OF LOOP -", $dflag_top_of_loop);

// 2018-01-16 - NEED TO ADD SANE BOUNDS CHECKING AND ENFORCEMENT ON
//  +  NUMBER OF LINES READ FROM FILE HERE:
            ++$lines_read;

            preg_match("$pattern", $current_filename, $matches);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - process files which represent navigation menu items
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Test to avoid PHP 'undefined offset' warning:

            if ( $matches )
            {
                show_diag($rname, "filename '" . $current_filename. "' matches pattern of a navigation menu item,", $dflag_dev);
                if ( $dflag_dev )
                    { nn_show_array($rname, $matches, "--no-options"); }


// Note matches array holds from parsed filename data 'menu item ordering number', 'filename infix', 'menu item name', ['attributes postfix']

                if ( $matches[0] )
                {

// blind, auto-incrementing way of naming nav' menu top level hash keys:
//                    $key_name = str_pad($integer_key_value, KEY_NAME_LENGTH, "0", STR_PAD_LEFT);
//                    ++$integer_key_value;


// filename based way of key naming:
                    $key_name = str_pad($matches[1], KEY_NAME_LENGTH, "0", STR_PAD_LEFT);

                    show_diag($rname, "looking at filename '$current_filename' from which script derives hash key name '$key_name',", $dflag_parse_filename);

                    show_diag($rname, "first filename parsing yields array of matches:", $dflag_parse_filename);
                    if ( $dflag_parse_filename )
                        { nn_show_array($rname, $matches, "--no-options"); }


// 2018-01-16 - NEED to move / assure these variables declared top of routine in variables section:

                    $full_filename = $matches[0];
                    $link_ordering_number = $matches[1];
                    $infix_as_found = $matches[2];
                    $link_text = $matches[3];
                    $postfix_as_found = $matches[4];

//                    show_diag($rname, "near top of loop, link text holds '$link_text'", $dflag_link_text);
                    show_diag($rname, "Summary of nav item filename parsing, near top of processing loop:", $dflag_link_text);
                    show_diag($rname, "------------------------------------------------------------------", $dflag_link_text);
                    show_diag($rname, "  full filename:  $full_filename", $dflag_link_text);
                    show_diag($rname, "  link ordering number:  $link_ordering_number", $dflag_link_text);
                    show_diag($rname, "  filename infix found:  $infix_as_found", $dflag_link_text);
                    show_diag($rname, "  link text:  $link_text", $dflag_link_text);
                    show_diag($rname, "  postfix found:  $postfix_as_found", $dflag_link_text);
                    show_diag($rname, "------------------------------------------------------------------", $dflag_link_text);
//                    show_diag($rname, "", $dflag_link_text);



// 2017-11-11 - optionally omit present link to current directory from navigation menu items:

/*
                    if ( ($key_name === $current_directory_dirname_only ) && ( $flag__hide_current_directory == true ) )
                    {
                        break;
                    }
*/

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - filter and skip filenames whose infix pattern differs from
//          caller's infix:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                    $result = strncmp($filename_infix, $infix_as_found, LENGTH__TOKEN );
                    if ( 0 != strncmp($filename_infix, $infix_as_found, LENGTH__TOKEN ) )
                    {
                        show_diag($rname, "filename infix not requested by caller, skipping file $current_filename . . .",
                          $dflag_infix);
                        show_diag($rname, "strncmp() of '$infix_as_found' and '$filename_infix' returns $result.",
                          $dflag_infix);
                        continue;
                    }
                    else
                    {
                        show_diag($rname, "strncmp() says string '$infix_as_found' matches '$filename_infix'.",
                          $dflag_infix);
                    }


                    if ( array_key_exists($key_name, $nav_links) )
                    {
                        show_diag($rname, "navigation links array key '$key_name' already exists!",
                          $dflag_key_exists);
                        show_diag($rname, "leaving this key intact and may update its data in next lines of code . . .",
                          $dflag_key_exists);
                    }
                    else
                    {
// this function call gives us a PHP ordered map with keys 'url', 'link_text', 'link_status':

                        $nav_links[$key_name] =& nn_nav_menu_entry($rname); 
                    }

//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - SUMMARY THUS FAR - we now have a navigation list key name which
//   corresponds to the present nav list filename being parsed.
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - 2018-01-17 WED -
//  The '===' PHP operator means compare whether two values
//  are equal and of the same data type.  This is a more strict
//  comparison than PHP's ==, which does "type juggling" to and can
//  return true when values are defined as equal in the context of PHP
//  but are of different data types.  For more information see,
//
//    *  http://php.net/manual/en/language.operators.comparison.php
//
// - 2017-09-28 -
//  Reference http://php.net/manual/en/language.types.boolean.php
//  implies that boolean values are tested using the '==' operator,
//  and yet when we tried that yesterday both file-type tests returned
//  just ahead of this comment block returned true.  Seems a file in
//  Unix and Linux context may be a symbolic link, but a symlink is
//  not a regular file.  Better revisit PHP's documentation on functions
//  is_link() and is_file() . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                    $full_path_to_file = "$path_to_search/$current_filename";
                    $result = 0;  // QUESTION what is variable 'result' used for? - TMH


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - process symbolic links to add item to navigation menu:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                    if ( is_link($full_path_to_file) )
                    {
// This menu building routine treats symlinks as pointers to directories
// of the given web site.  Here build the relative within-site URL
// and store that plus link text to show for this URL's hyperlink:

                        $nav_links[$key_name][KEY_NAME_FOR_URL] = "$path_to_search/$link_text";
                        $nav_links[$key_name][KEY_NAME_FOR_LINK_TEXT] = "$link_text";


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// TO-DO 2017-10-03 - in following PHP IF-ELSEIF-ELSE place the '--not-ready'
//   postfix pattern in a PHP constant:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                        if ( ( $flag__hide_not_ready ) and ( preg_match("/(.)--not-ready$/", $current_filename) ) )
                        {
                            $nav_links[$key_name][KEY_NAME_FOR_LINK_STATUS] = "hidden";
                        }

// URLs indicated disabled will appear as text only, not hyperlinks:

                        elseif ( preg_match("/(.)--disabled$/", $current_filename) )
                        {
                            $result = 1;
                            $nav_links[$key_name][KEY_NAME_FOR_LINK_STATUS] = "disabled";
                        }
                        else
                        {
                            $result = 1;
                            $nav_links[$key_name][KEY_NAME_FOR_LINK_STATUS] = "enabled";
                        }


// Handle symbolic links whose names parse and give navigation link text
// equal to the name of the current working directory:

                        if ( 0 == strncmp($link_text, $current_directory_dirname_only, LENGTH__TOKEN) )
                        {
                            $nav_links[$key_name][KEY_NAME_FOR_LINK_STATUS] = "hidden";
                        }




                    } // end IF-block which catches and handles symbolic links found in caller's path

//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - 2018-01-17 WED -
//
//  So we have a challenge:  we want to hide a link to the same page
//  on which we're building a site navigation menu, because we're
//  already at that page.
//
//  When links are relative within the web site to directories of
//  the website other than document root, our routine can compare the
//  parsed part of a symblic link name which we call "link text"
//  against the current working directory.  When these match this
//  routine hides such links, so we don't have a page point to itself.
//  
//  But because symlinks conventiently point to subdirectories, there
//  is no symlink to point to the site's home page, it's document root.
//  And yet from more specific site pages which are under the document'
//  root we want to have those pages' nav menus include links back to
//  the site's home page.
//
//  What information does this script have at hand, to determine when
//  to hide an item which symlinks or text files indicate we want to
//  appear in a navigation menu?  This script has the data:
//
//
//   *  $path_to_search
//   *  $filename_infix
//   *  $filename_postfix
//
//
//  When this routine looks at a file in a particular path, that file
//  can be in the current directory "." or in another directory
//  relative to ".".  If for example the nav item filename has postfix
//  "--hide-in-current-directory" and this routine receives "." as
//  its path to search, the routine then can set "hidden" this
//  navigation menu item.  When the search path is some other relative
//  path, the same nav menu item then is not marked hidden.
//
//  Our solution then is to use a specific postfix, and have this
//  routine check its present search path, in order to hide menu
//  navigation items which represent the page for which we're
//  constructing a navigation menu.
//
//
//  In the case where search path points to directory outside of
//  current working directory, we can compare link text parsed from
//  a filename of a symbolic link to the name of the current directory . . .
//



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - process regular files to add item to navigation menu:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//    * REF *  https://www.ibm.com/developerworks/library/os-php-readfiles/index.html

                    if ( is_file($full_path_to_file) )
                    {
                        $result = 1;
                        show_diag($rname, "file contents, if any, shown line by line in green:",
                          $dflag_parse_file_text);

                        $line_count = 0;
                        $handle_to_file = fopen($full_path_to_file, "r");
                        while ( !feof($handle_to_file))
                        {
                            $line = fgets($handle_to_file);
                            ++$line_count;
                            show_diag($rname, "<font color=\"green\">$line</font>$term",
                              $dflag_parse_file_text);

                            preg_match("/(^URL=)(.*)/", $line, $matches);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// DIAG START
if ( $dflag_parse_file_text )
{
                            if ( $matches )
                            {
                                show_diag($rname, "after matching line from file to \"/(^URL=)(.*)/\", \$matches holds:",
                                  $dflag_parse_file_text);
                                nn_show_array($rname, $matches, "--no-options");
                            }
                            else
                            {
                                show_diag($rname, "line holding '$line' does not match regex /(^URL=)(.*)/.",
                                  $dflag_parse_file_text);
                            }
}
// DIAG END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                            if ( isset($matches[2]) ) { $nav_links[$key_name][KEY_NAME_FOR_URL] = $matches[2]; }



                            preg_match("@(LINK_TEXT=)(.*)@", $line, $matches);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// DIAG START
if ( $dflag_parse_file_text )
{
                            if ( $matches )
                            {
                                show_diag($rname, "after matching line from file to \"@(LINK_TEXT=)(.*)@\", \$matches holds:",
                                  $dflag_parse_file_text);
                                nn_show_array($rname, $matches, "--no-options");
                            }
                            else
                            {
                                show_diag($rname, "line holding '$line' does not match regex @(LINK_TEXT=)(.*)@.",
                                  $dflag_parse_file_text);
                            }
}
// DIAG END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


//
// 2017-10-02 MON - Ted observes PHP notice of undefined offset from
//  following line . . . ok PHP isset() fixes that notice, now applying
//  same fix to like test on line 1048 this file . . . TMH

//                            if ( $matches[2] ) { $nav_links[$key_name]["link_text"] = $matches[2]; }

                            if ( isset($matches[2]) )
                            {
                                $link_text = $matches[2];
                                $nav_links[$key_name][KEY_NAME_FOR_LINK_TEXT] = $link_text;
                                $link_text_minus_disabled_postfix = preg_replace('/--disabled$/', '', $link_text);
                                $link_text_minus_postfix = preg_replace('/--disabled$/', '', $link_text);
                            }

                            preg_match("@(LINK_STATUS=)(.*)@", $line, $matches);
                            if ( isset($matches[2]) ) { $nav_links[$key_name][KEY_NAME_FOR_LINK_STATUS] = $matches[2]; }

                        } // end WHILE loop, while navigation menu file has lines of text to process,

                        fclose($handle_to_file);


// 2017-11-11 added:
// BUT IF THE REGULAR FILE IS EMPTY, ITS NAME MAY BE THE MARKER WE NEED TO ACT ON
// SO HERE DETECT WHEN A FILE IS EMPTY:

                        if ( $lines_read == 0 )
                        {
                            show_diag($rname, "file '$full_path_to_file' appears to be an empty file,", $dflag_dev);
                        }

                    } // end IF-block testing whether file is of type regular file,





                    show_diag($rname, "flag to hide current directory from navigable menu holds '$flag__hide_current_directory',",
                      $dflag_hide_cwd);
                    show_diag($rname, "2018-10-17 - this flag presently set to 'true' locally in this routine,",
                      $dflag_hide_cwd);


// gather here the navigation item postfix to look for in present test:
                    $postfix_to_hide_link = POSTFIX__HIDE_NAV_LINK_WHEN_IN_CURRENT_DIRECTORY;

                    if ( ( $flag__hide_current_directory ) && 
                         ( 0 == strncmp($path_to_search, ".", LENGTH__TOKEN) ) &&
                         ( 0 == strncmp($postfix_as_found, $postfix_to_hide_link, LENGTH__TOKEN) ) )
                    {
                        show_diag($rname, "setting status of navigation menu item '" . $nav_links[$key_name][KEY_NAME_FOR_LINK_TEXT] . "' to 'hidden' . . .", $dflag_hide_cwd);
                        $nav_links[$key_name][KEY_NAME_FOR_LINK_STATUS] = "hidden";
                    }
                    else
                    {
                        $result = strncmp($path_to_search, ".", LENGTH__TOKEN);
                        show_diag($rname, "comparing path to search '$path_to_search' and '.' strncmp() returns $result,",
                          $dflag_hide_item_tests);
                        $result = strncmp($postfix_as_found, $postfix_to_hide_link, LENGTH__TOKEN);
                        show_diag($rname, "comparing as-found postfix '$postfix_as_found' and '$postfix_to_hide_link' strncmp() returns $result,",
                          $dflag_hide_item_tests);
                    }




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - 2017-10-02 - 
// - STEP - replace spaces with HTML non-breakable spaces . . . 
//  $pattern, $replacement, $string, [$replacement_limit, $replacements_made]:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                    $string = $nav_links[$key_name][KEY_NAME_FOR_LINK_TEXT];
                    $count = 0;
                    $intermediate_string = preg_replace('/\s/', '&nbsp;', $string, NN_MAXIMUM_PREG_REPLACEMENTS, $count);
                    $nav_links[$key_name][KEY_NAME_FOR_LINK_TEXT] = $intermediate_string;


// 2018-01-17 - ANSWER, variable $result used for diagnostics summary here:

                    if ( $result )
                    {
                        show_diag($rname, "For latest matched filename,", $dflag_parse_file_text);
                        show_diag($rname, "showing array to hold attributes of newly added nav' menu link,", $dflag_parse_file_text);
                        show_diag($rname, "\$nav_links[" . $key_name . "] =>", $dflag_parse_file_text);

                        if ( $dflag_parse_file_text )
                        {
                            echo "<font color=\"#3358ff\">";
                            nn_show_array($rname, $nav_links[$key_name], "--no-options");
                            echo "</font>";
                        }

                        show_diag($rname, "---<br />\n", ($dflag_parse_file_text | DIAGNOSTICS__MESSAGE_ONLY));
                    }

                } // end IF-block testing whether there is a first match, a $matches[0],

            } // end IF-block testing whether there are any matches to filename infix pattern from caller,

            else
            {
                show_diag($rname, "filename doesn't match that of file to treat as nav menu item.", $dflag_filename_non_matching);
                ++$count_filenames_not_matching;
//                echo $term;
            }

            ++$loop_counter;

        } // end WHILE-block iterating over files in caller-specified directory,

        closedir($handle);

    } // end IF-block testing file handle assignment of value from opendir(),


    show_diag($rname, "- DEVELOPMENT SUMMARY -", 0);
    show_diag($rname, "found $count_filenames_not_matching filenames not matching caller's infix pattern,", 0);




// ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! !
// NEED 2017-10-02 - need to sanity check that $nav_links is an array,
//  and has at least two elements:
// ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! ! !

// 2018-01-17 - QUESTION:  why must $nav_links hold at least two elements?  - TMH

    if ( is_array($nav_links) )
    {
        show_diag($rname, "\$nav_links is an array (a PHP hash),", $dflag_dev);
        if ( 1 < count($nav_links) ) { ksort($nav_links); }
    }


// At this point we have a hash of hashes which holds an effective list
// of navigable links.  Following code will be designed to write these
// links with horizontal (could be vertical or otherwise) CSS layout
// and HTML tags for layout and formatting . . .

//    present_menu_from_hash_of_hashes($rname, $nav_links, "--no-options");
    present_menu_from_hash_of_hashes($rname, $nav_links, $options);


    show_diag($rname, "Just presented navigation menu per built hash, PHP print_r() shows that hash contains:",
      $dflag_show_nav_links_hash);

    if ( $dflag_show_nav_links_hash )
    {
        echo "<pre>\n";
        print_r($nav_links);
        echo "</pre>\n";
    }


    show_diag($rname, "done.", $dflag_announce);


} // end function nn_menu_building_hybrid_fashion()





/*
function ___place_holder___
*/




?>
