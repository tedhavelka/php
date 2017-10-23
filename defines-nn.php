<?php

//----------------------------------------------------------------------
//  Started 2017-10-02 MON . . .
//
//  DESCRIPTION:  This file named defines-nn.php to hold constants used
//    across local PHP library project files.  These library files
//   started by Ted Havelka to aid in the layout and content management
//   of Neela Nurseries web site and other web sites.  - TMH
//
//----------------------------------------------------------------------


// First use in file site-navigation-routines.php, for calls to PHP strncmp():
    define("LENGTH__KEY_NAME", 256);

// 2017-10-04 added for use in site-navigation-routines.php:
    define("LENGTH__FILE_NAME", 1024);

// 2017-10-03 added for use in site-navigation-routines.php:
    define("NN_MAXIMUM_PREG_REPLACEMENTS", 1024);



// 2017-10-20 - one place to define keynames used for local library routine supported options:

    define("LOCAL_PHP_LIBRARY_OPTION__SHOW_PARAMETERS", "show_parameters");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_ROW_HEIGHT_IN_PIXELS", "image_row_height_in_px");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_ROW_WIDTH_IN_PIXELS", "image_row_width_in_px");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_HEIGHT", "image_block_element_height");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_WIDTH", "image_block_element_width");

    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_SIZE", "image_size");
    define("LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF", "new_row_after_image_count_of");



?>
