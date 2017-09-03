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
//    * REF *  http://php.net/manual/en/function.array-search.php
//
//    * REF *  http://php.net/manual/en/function.unset.php
//
//    * REF *  http://php.net/manual/en/function.preg-replace.php
//
//    * REF *  http://php.net/manual/en/language.types.string.php
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





function &navigation_items_via_filenames__dev_version(
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
    $rname = "navigation_items_via_filenames__dev_version";


    
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

} // end function &navigation_items_via_filenames__dev_version





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

    if ( 0 ) // <-- this text clean-up not needed when calling function list_of_filenames_sorted_by_same_marker() - TMH
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





function nn_horizontal_navigation_menu__dev_version($caller, $base_url, $include_path, $exclude_path, $options)
{
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//
//  GENERATES HTML FOR WEB BROWSERS:  yes
//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $menu_items = array();

    $last_item = 0;

// local text string for use during development and diagnostics:
    $term = "<br />\n";

    $rname = "nn_horizontal_navigation_menu__dev_version";



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

// Actually we may only want a navigation menu sorted alphabetically some of the time - TMH:
//    sort($menu_items);

    foreach ($menu_items as $key => $value)
    {
        echo "<div class=\"menu-item\"> <a href=\"$base_url/" . $menu_items[$key] . "\">" . $menu_items[$key] . "</a> </div>\n\n";

        if ( $key < ($last_item - 1) )
        {
            echo "<div class=\"menu-item-text-separator\"> : </div>\n\n";
        }
    }

    nav_menu_layout__closing_lines($rname);

    echo "done.$term$term$term";


} // end function nn_horizontal_navigation_menu__dev_version()





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
//
//
//  GENERATES HTML FOR WEB BROWSERS:  yes
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

    nav_menu_layout__opening_lines($rname);

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

    nav_menu_layout__closing_lines($rname);

    echo "done.$term$term$term";


} // end function nn_horizontal_navigation_menu()





?>
