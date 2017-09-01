<?php

//======================================================================
//
//  FILE:  site_navigation_routines.php
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
//    *  ___
//
//
//
//======================================================================





//----------------------------------------------------------------------
// - SECTION - PHP include directives
//----------------------------------------------------------------------

    require_once '/opt/nn/lib/php/file-and-directory-routines.php';




//----------------------------------------------------------------------
// - SECTION - diagnostics and development
//----------------------------------------------------------------------

function nn_show_array($caller, $array_reference, $options)
{

    $rname = "nn_show_array";

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

function nav_menu_layout__opening_lines($caller) {

    echo "<!-- BEGIN LAYOUT for Neela Nurseries top menu, navigation bar -->
<div class=\"container\">
  <div class=\"menu-bar-top\">
    <div class=\"menu-block-element-as-margin\">
      <font color=\"#ffffff\"> . . . </font>
    </div>\n\n";

}




function nav_menu_layout__closing_lines($caller)
{
    echo "    <div class=\"menu-block-element-as-margin\">
      <font color=\"#ffffff\"> . . . </font>
    </div>
  </div><!-- close of div element of type 'menu-bar-top' for menu bar 1 -->
</div><!-- close of div element of type 'container' -->
<!-- END LAYOUT for Neela Nurseries top menu, navigation bar -->\n\n\n";

}





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
    $item_name_only = "";


// mark-up string for diagnsotics to web browsers:
    $term = "<br />\n";

// routine name or function name:
    $rname = "navigation_items_via_filenames";


    
// DEV - show parameters passed to us from calling code:

    echo "2017-08-31 - implementation underway,<br />\n";

    echo "called by '$caller'," . $term;
    echo "caller sends path set to '$include_path'," . $term;
    echo "pattern of items to include holds '$include_pattern'," . $term;
    echo "caller sends path set to '$exclude_path'," . $term;
    echo "pattern of items to exclude holds '$exclude_pattern'," . $term;
    echo "and options string holding '$options'." . $term . $term;

// END DEV



// - STEP - search caller's file system path for navigable site items:

    echo "STEP - reading files in '$include_path' and searching for navigation includes . . .$term";
    $navigation_menu_items =& list_of_filenames_by_pattern($rname, $include_path, $include_pattern);
    nn_show_array($rname, $navigation_menu_items, "--no-options");
    echo $term;



// - STEP - search caller's path for items explicitly marked to exclude from list of navigables:

    echo "STEP - reading files in '$exclude_path' and searching for navigation excludes . . .$term";
    $items_to_exclude =& list_of_filenames_by_pattern($rname, $exclude_path, $exclude_pattern);
    nn_show_array($rname, $items_to_exclude, "--no-options");
    echo $term;



// - STEP - remove the include pattern, a prefix or infix, from all found navigation menu items:

    echo "STEP - cleaning up navigation menu item names:$term";

    foreach ($navigation_menu_items as $key => $value)
    {
        preg_match('@(.*--nn--)(.*)@', $value, $matches);  // . . . call preg_match() with pattern, string_to_search, array_of_matches_found
        echo "- DEV - looking at nav' menu item '$value',$term";
        echo "- DEV - matches[2] holds '$matches[2]',$term";
        $navigation_menu_items[$key] = $matches[2];
    }
    echo $term;

    nn_show_array($rname, $navigation_menu_items, "--no-options");
    echo $term;



    foreach ($items_to_exclude as $key => $value)
    {
        preg_match('@(.*--nn--)(.*)(.*--exclude-from-nav$)@', $value, $matches);  // . . . call preg_match() with pattern, string_to_search, array_of_matches_found
        $items_to_exclude[$key] = $matches[2];
    }
    echo $term;

    nn_show_array($rname, $items_to_exclude, "--no-options");
    echo $term;



    return $navigation_menu_items;

}




function nn_horizontal_navigation_menu($caller, $base_url, $include_path, $exclude_path, $options)
{

    $menu_items = array();

    $last_item = 0;

// local text string for use during development and diagnostics:
    $term = "<br />\n";

    $rname = "nn_horizontal_navigation_menu";



// DEV - show parameters passed to us from calling code:

//    echo "2017-08-31 - this function not yet implemnented.<br />\n";
    echo "2017-08-31 - implementation underway,<br />\n";

    echo "called by '$caller'," . $term;
    echo "caller sends include path set to '$include_path'," . $term;
    echo "and exclude path set to '$exclude_path'," . $term;
    echo "and options string holding '$options'." . $term . $term;

// END DEV


// Parameters in following call - caller, path_to_search, pattern_include, pattern_exclude, options . . .

    $menu_items =& navigation_items_via_filenames(
      $rname,
      $include_path, "/.*-nn-*/",
      $exclude_path,  "/.*--exclude-from-nav*/",
      "--no-options"
    );

    echo "$rname:  ok got back list of navigation items as follows:$term$term";

    nn_show_array($rname, $menu_items, "--no-options");
    echo $term;


// - STEP - send navigation menu mark-up opening lines:

    nav_menu_layout__opening_lines($rname);

    $last_item = count($menu_items);

    foreach ($menu_items as $key => $value)
    {
        echo "<div class=\"menu-item\"> <a href=\"$base_url/$menu_items[$key]\">$menu_items[$key]</a> </div>\n\n";
        if ( $key < ($last_item - 1) )
        {
            echo "<div class=\"menu-item-text-separator\"> : </div>\n\n";
        }
    }


    nav_menu_layout__closing_lines($rname);

    echo "done.$term$term$term";

}



?>
