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

    require_once './file_and_directory_routines.php';




//----------------------------------------------------------------------
// - SECTION - PHP library routines, developed at Neela Nurseries
//----------------------------------------------------------------------

function &navigation_items_via_filenames($caller, $path, $pattern_items_to_include, $pattern_items_to_exclude, $options)
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


    $rname = "navigation_items_via_filenames";


    


}




function nn_horizontal_navigation_menu($caller, $path, $options)
{
    echo "2017-08-31 - this function not yet implemnented.<br />\n";
}



?>
