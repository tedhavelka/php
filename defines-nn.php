<?php

//----------------------------------------------------------------------
//
//  PROJECT:  PHP library routines at Neela, de Ted
//
//  FILE:  defines-nn.php
//
//  STARTED:  2017-10-02 MON
//
//
//  DESCRIPTION:  This file named defines-nn.php to hold constants used
//   across local PHP library project files.  These library files
//   started by Ted Havelka to aid in the layout and content management
//   of Neela Nurseries web site and other web sites.  - TMH
//
//
//  NOTES ON DEVELOPMENT PHASES:
//   Just a quick note on PHP defined labels naming schemes chosen by
//   project contributor Ted.  Naming schemes include,
//
//     (1)  LOCAL_PHP_LIBRARY_OPTION__
//     (2)  KEY_NAME__ | KEY_VALUE__
//     (3)  KEY_NAME__[RELATED_TASKS]__[ELEMENT_NAME]__[ATTRIBUTE]
//
//   The first PHP defined label name only high-lighted that the given
//   define is part of this local PHP library project.  This highlight
//   may be useful if this file, as of 2017-11-07 named defines-nn.php
//   is included in part of another PHP project.  But the fixed part of
//   naming scheme 1 is long and only useful for that one
//   distination.
//
//   Naming scheme 2 highlights the syntactic purpose of related
//   defined constants, indicating when they are key names in ordered
//   maps, and when they hold enumeration-like values in ordered maps
//   of this local PHP library.  These label names are shorter, but
//   don't tie together problem-and-solution related defines.
//
//   Naming scheme 3 is again a long name scheme and is hierarchical.
//   This naming scheme keeps the name part showing a little of PHP
//   syntax where the defines are used in this code base.  Defines are
//   grouped then by task, or problem-and-solution, for example two
//   tasks are "image layout" and "navigation menus".  Where there
//   exists a mark-up or content element which the PHP define helps
//   process that element's name appears next, and lastly when there
//   is an attribute for the element such as filename, border, width
//   etc that appears in the fourth part of naming scheme 3.
//
//   As of 2017-11-07 Tuesday, all three define label name schemes
//   are yet present in this file.  Ted to return and clean up this
//   situation as time permits . . .
//
//
//
//  REFERENCES:
//
//    * REF *  http:
//
//
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

    define("LOCAL_PHP_LIBRARY_OPTION__SHOW_PARAMETERS", "show_parameters"); // 2017-11-07 - NOT YET IMPLEMENTED - TMH



//----------------------------------------------------------------------
// --- OPTIONS FOR FORMATTING NAVIGATION MENUS ---
//----------------------------------------------------------------------

// **  **  **  **  **
// 2017-11-11 - Old PHP library hash (ordered map) key names to remove from library code:

    define("LOCAL_PHP_LIBRARY_OPTION__SITE_MENU_DISABLED_LINK_COLOR", "site_menu_disabled_link_color");
    define("KEY_NAME__SITE_MENU_DISABLED_LINK_COLOR", "site_menu_disabled_link_color"); 

// **  **  **  **  **

    define("KEY_NAME__SITE_NAVIGATION__SITE_MENU_DISABLED_LINK_COLOR", "site_menu_disabled_link_color"); 
    define("KEY_NAME__SITE_NAVIGATION__CURRENT_PAGE_IN_SITE_MENU", "current_page_in_site_menu");
    define("KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS", "site_navigation_diagnostics");





//----------------------------------------------------------------------
// --- OPTIONS FOR LAYOUT AND FORMAT OF WEB PAGE, DOCUMENT SECTIONS ---
//----------------------------------------------------------------------

    define("KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__SHOW_MARK", "content_margin_block_element_mark_with");
    define("KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__ALIGN_MARK", "content_margin_block_element_mark_alignment");
    define("KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__BORDER_STYLE", "content_margin_block_element_border_style");
    define("KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME", "block_element_name");

// 2017-11-08 - TO BE IMPLEMENTED, KEYS POINTING TO PAGE-LOAD-WISE RUNNING COUNTS OF SPECIFIC ROUTINE CALLS:
    define("KEY_NAME__DOC_LAYOUT__DEV__COUNT_SECTIONS_OPENED", "count_of_document_sections_opened");
    define("KEY_NAME__DOC_LAYOUT__DEV__COUNT_SECTIONS_CLOSED", "count_of_document_sections_closed");

    define("KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_BORDER_STYLE", "block_element_border_style");
    define("KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_VERTICAL_HEIGHT_IN_PX", "block_element_minimum_height_in_pixels");
    define("KEY_NAME__DOC_LAYOUT__SEND_HORIZONTAL_BREAK_OF_WIDTH", "horizontal_break_of_width");



//----------------------------------------------------------------------
// --- OPTIONS FOR IMAGE GALLERY FORMATTING ---
//----------------------------------------------------------------------

// *** * *** * ***
// BEGIN Old style names from label naming convention 1:

    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_ROW_HEIGHT_IN_PIXELS",   "image_row_height_in_px");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_ROW_WIDTH_IN_PIXELS",    "image_row_width_in_px");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_HEIGHT",   "image_block_element_height");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_WIDTH",    "image_block_element_width");
    define("LOCAL_PHP_LIBRARY_OPTION__IMAGE_SIZE",                   "image_size");
    define("LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF", "new_row_after_image_count_of");

// END Old style names from label naming convention 1:
// *** * *** * ***

    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_HEIGHT_IN_PIXELS",             "image_row_height_in_px");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_WIDTH_IN_PIXELS",              "image_row_width_in_px");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_BLOCK_ELEMENT_HEIGHT",             "image_block_element_height");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_BLOCK_ELEMENT_WIDTH",              "image_block_element_width");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_SIZE",                             "image_size");
    define("KEY_NAME__IMAGE_LAYOUT__NEW_ROW_AFTER_IMAGE_COUNT_OF",           "new_row_after_image_count_of");


// 2017-11-05:
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_DIR",                              "directory_of_images");
// 2017-11-06:
    define("KEY_NAME__IMAGE_LAYOUT__EXPLANATORY_TEXT_FILE_FOR_IMAGES",       "explanatory_text_for_images"); // NOT YET IMPLEMENTED - TMH
    define("KEY_NAME__IMAGE_LAYOUT__UNITS_OF_MEASUREMENT",                   "units_of_measurement");

    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_HEIGHT",                       "image_row_height");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_WIDTH",                        "image_row_width");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_BACKGROUND",                   "image_row_background");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGES_SHOWN_PER_ROW",                   "images_shown_per_row");

    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_INDENT_STYLE",                 "image_row_indent_style");
    define("KEY_NAME__IMAGE_LAYOUT__INDENT_1_IN_PIXELS",                     "indent_1_in_pixels");
    define("KEY_NAME__IMAGE_LAYOUT__INDENT_2_IN_PIXELS",                     "indent_2_in_pixels");
    define("KEY_NAME__IMAGE_LAYOUT__INDENT_3_IN_PIXELS",                     "indent_3_in_pixels");
    define("KEY_NAME__IMAGE_LAYOUT__INDENT_4_IN_PIXELS",                     "indent_4_in_pixels");

// 2017-11-08 - TO BE IMPLEMENTED:
    define("KEY_NAME__IMAGE_LAYOUT__VERTICAL_SPACE_BETWEEN_IMAGE_ROWS",      "vertical_space_between_images_rows_in_px");

    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_AND_CAPTION_BLOCK_ELEMENT_HEIGHT", "image_and_caption_element_height");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_AND_CAPTION_BLOCK_ELEMENT_WIDTH",  "image_and_caption_element_width");

    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_HEIGHT",                           "image_height");
    define("KEY_NAME__IMAGE_LAYOUT__IMAGE_WIDTH",                            "image_width");

    define("KEY_NAME__IMAGE_LAYOUT__SOURCE_OF_IMAGE_CAPTIONS",               "source_of_image_captions");
    define("KEY_NAME__IMAGE_LAYOUT__PARSE_CAPTIONS_FROM_IMAGE_NAMES_VIA_REGEX", "parse_captions_from_image_names_via_regex");
    define("KEY_NAME__IMAGE_LAYOUT__TEXT_FILE_HOLDING_IMAGE_CAPTIONS",       "text_file_holding_image_captions");
    define("KEY_NAME__IMAGE_LAYOUT__CAPTIONS_FROM_DATABASE",                 "get_captions_from_database");



//----------------------------------------------------------------------
// ---
//----------------------------------------------------------------------

    define("KEY_NAME__TEXT_ELEMENT__STYLE", "text_element_style");



//----------------------------------------------------------------------
// --- ENUMERATION-LIKE VALUES FOR GALLERY AND IMAGE PRESENTATION FORMATTING:
//----------------------------------------------------------------------

    define("KEY_VALUE__UNITS_IN_EMS", "em");
    define("KEY_VALUE__UNITS_IN_PIXELS", "px");

    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__NONE", "none");
    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__ALTERNATE", "alternate");
    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__STAGGERED_1", "staggered style 1");
    define("KEY_VALUE__IMAGE_ROW_INDENT_STYLE__SAWTOOTH", "sawtooth");

    define("KEY_VALUE__CAPTIONS_FROM_IMAGE_FILENAMES", "captions from image filenames");
    define("KEY_VALUE__CAPTIONS_FROM_FLAT_TEXT_FILE", "captions from flat text file");
    define("KEY_VALUE__CAPTIONS_FROM_DATABASE", "captions from database");


// --- VALUES FOR TEXT ELEMENT ALIGNMENT AND STYLES ---

    define("KEY_VALUE__TEXT_ELEMENT__ALIGN_LEFT", "left");
    define("KEY_VALUE__TEXT_ELEMENT__ALIGN_CENTER", "center");
    define("KEY_VALUE__TEXT_ELEMENT__ALIGN_RIGHT", "right");

    define("KEY_VALUE__TEXT_ELEMENT__STYLE_NORMAL", "normal");
    define("KEY_VALUE__TEXT_ELEMENT__STYLE_ITALIC", "italic");
    define("KEY_VALUE__TEXT_ELEMENT__STYLE_BOLD", "bold");
    define("KEY_VALUE__TEXT_ELEMENT__STYLE_OBLIQUE", "oblique");
    define("KEY_VALUE__TEXT_ELEMENT__STYLE_UNDERLINED", "underlined");
    define("KEY_VALUE__TEXT_ELEMENT__STYLE_STRIKE_THROUGH", "strike through");



// * REF * http://www.learnlayout.com/position.html
    define("KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_STATIC", "static");
    define("KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_RELATIVE", "relative");
    define("KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_FIXED", "fixed");
    define("KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_ABSOLUTE", "absolute");

//    define("KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_", "");



//----------------------------------------------------------------------
// --- OPTIONS FOR WEB PAGE FOOTERS ---
//----------------------------------------------------------------------

    define("KEY_NAME__FOOTER_ATTRIBUTES__BLOCK_ELEMENT_POSITIONING", "footer_block_element_positioning");
    define("KEY_NAME__FOOTER_ATTRIBUTES__TEXT_ALIGNMENT", "footer_block_element_text_alignment");
    define("KEY_NAME__FOOTER_ATTRIBUTES__TEXT_SIZE_AS_PERCENTAGE", "footer_block_element_text_size_as_percentage");
    define("KEY_NAME__FOOTER_ATTRIBUTES__LINE_1", "footer_line_1");
    define("KEY_NAME__FOOTER_ATTRIBUTES__LINE_2", "footer_line_2");
    define("KEY_NAME__FOOTER_ATTRIBUTES__LINE_3", "footer_line_3");



?>