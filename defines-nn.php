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

// --- OPTIONS FOR ROUTINE DEVELOPMENT ---

    define("LOCAL_PHP_LIBRARY_OPTION__SHOW_PARAMETERS", "show_parameters");


// --- OPTIONS FOR IMAGE GALLERY FORMATTING ---

    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_ROW_HEIGHT_IN_PIXELS",   "image_row_height_in_px");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_ROW_WIDTH_IN_PIXELS",    "image_row_width_in_px");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_HEIGHT",   "image_block_element_height");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_WIDTH",    "image_block_element_width");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_SIZE",                   "image_size");
    define("LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF", "new_row_after_image_count_of");

    define("KEY_NAME__NEW_ROW_AFTER_IMAGE_COUNT_OF", LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF);


// 2017-11-05:
    define("KEY_NAME__IMAGE_DIR", "directory_of_images");
// 2017-11-06:
    define("KEY_NAME__EXPLANATORY_TEXT_FILE_FOR_IMAGES",       "explanatory_text_for_images");
    define("KEY_NAME__UNITS_OF_MEASUREMENT",                   "units_of_measurement");

    define("KEY_NAME__IMAGE_ROW_HEIGHT",                       "image_row_height");
    define("KEY_NAME__IMAGE_ROW_WIDTH",                        "image_row_width");
    define("KEY_NAME__IMAGE_ROW_BACKGROUND",                   "image_row_background");
    define("KEY_NAME__IMAGE_ROW_INDENT_STYLE",                 "image_row_indent_style");
    define("KEY_NAME__IMAGES_SHOWN_PER_ROW",                   "images_shown_per_row");

    define("KEY_NAME__IMAGE_AND_CAPTION_BLOCK_ELEMENT_HEIGHT", "image_and_caption_element_height");
    define("KEY_NAME__IMAGE_AND_CAPTION_BLOCK_ELEMENT_WIDTH",  "image_and_caption_element_width");

    define("KEY_NAME__IMAGE_HEIGHT",                           "image_height");
    define("KEY_NAME__IMAGE_WIDTH",                            "image_width");
//    define("KEY_NAME__", "");



// --- ENUMERATION-LIKE VALUES FOR GALLERY AND IMAGE PRESENTATION FORMATTING:

    define("KEY_VALUE__UNITS_IN_EMS", "em");
    define("KEY_VALUE__UNITS_IN_PIXELS", "px");

    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__NONE", "none");
    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__ALTERNATE", "alternate");
    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__STAGGERED_1", "staggered_style_1");
    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__SAWTOOTH", "sawtooth");

    define("KEY_NAME__INDENT_1_IN_PIXELS",                     "indent_1_in_pixels");
    define("KEY_NAME__INDENT_2_IN_PIXELS",                     "indent_2_in_pixels");
    define("KEY_NAME__INDENT_3_IN_PIXELS",                     "indent_3_in_pixels");
    define("KEY_NAME__INDENT_4_IN_PIXELS",                     "indent_4_in_pixels");



// --- OPTIONS FOR FORMATTING NAVIGATION MENUS ---

    define("LOCAL_PHP_LIBRARY_OPTION__SITE_MENU_DISABLED_LINK_COLOR", "site_menu_disabled_link_color");
    define("KEY_NAME__SITE_MENU_DISABLED_LINK_COLOR", LOCAL_PHP_LIBRARY_OPTION__SITE_MENU_DISABLED_LINK_COLOR); 





?>
