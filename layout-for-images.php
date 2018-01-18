<?php
//======================================================================
//
//  PROJECT:  PHP library routines at Neela, de Ted
//
//  FILE:  layout-for-images.php
//
//  STARTED:  2017-11-03
//
//
//  DESCRIPTION:  this file part of local PHP library, and contains
//   routines to generate HTML and CSS based layout for images.
//
//
//
//
//  REFERENCES:
//
//    * REF *  https://stackoverflow.com/questions/11261192/modulus-operator-to-run-1st-and-then-every-3rd-item
//
//    * REF *  https://help.github.com/articles/adding-a-remote/
//
//    * REF *  https://git-scm.com/docs/git-config
//
//    * REF *  https://orga.cat/posts/most-useful-git-commands
//
//    * REF *  http:
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

    define("DEFAULT_INDENT_1_VALUE", 40); // <-- image row indents measured in pixels
    define("DEFAULT_INDENT_2_VALUE", 80); // <-- image row indents measured in pixels



//----------------------------------------------------------------------
// - SECTION - diagnostics and development
//----------------------------------------------------------------------



//----------------------------------------------------------------------
// - SECTION - routines for image layout and presentation
//----------------------------------------------------------------------

/*
************************************************************************
- 2017-11-07 - TWO BELOW FUNCTIONS MOVED TO FILE layout-for-documents.php



function open_document_section_with_margin_block_elements($caller, $options)
{
//    echo "<div class=\"container\">
    echo "<div style=\"display:flex; min-height:30px; max-height:70px\">
";

//    echo "   <div class=\"column-for-main-content-margin\">
    echo "   <div class=\"column-for-main-content-margin-with-border\">
      &nbsp;
   </div>


   <div class=\"column-middle-with-border\">
";

//   <div class=\"column-middle-with-relative-height\">

}



function close_document_section_with_margin_block_elements($caller, $options)
{

    echo "   </div><!-- Closing tag for middle column, primary content div element -->


";
//    echo "   <div class=\"column-for-main-content-margin\">
    echo "   <div class=\"column-for-main-content-margin-with-border\">
      &nbsp;
   </div>
</div>
";

}

- 2017-11-07 - TWO ABOVE FUNCTIONS MOVED TO FILE layout-for-documents.php
************************************************************************
*/




function open_row_of_images($caller, $options)
{
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    $background_color = "#c0c0c0";

// diagnostics:

    $rname = "open_row_of_images";


    if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_BACKGROUND, $options) )
    {
        $background_color = $options[KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_BACKGROUND];
    }


    echo "
<div style=\"min-height:100px; min-width:100px; overflow:auto; border:none; background:$background_color\">
   <div style=\"float:left; min-height:100px; min-width:100px; max-width:1000px; border:none\">\n\n";

}



function close_row_of_images($caller, $row_attributes)
{

//    echo "   {image row closing statements}<br />\n";

    echo "  </div>
</div>\n\n\n";

}



function indent_image_row($caller, $indent_by_n_pixels)
{
    echo "      <div style=\"float:left; width:{$indent_by_n_pixels}px; border:none.\"> &nbsp; <!-- &nbsp; - - block element for image row indent -->
      </div>\n";
}




function handle_image_row_indents($caller, $count_of_image_rows, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to handle one or more styles of indents for block element
//    based rows of images on a web page.
//
//  EXPECTS:
//    *  calling code identifying string
//    *  count of rows presented plus row in progress
//    *  image row formatting options which include indent style
//
//  RETURNS:
//    *  nothing, but sends HTML5 mark-up to browser or standard out
//
//
//  NOTES:  See ./lib/php/defines-nn.php for supported row indent 
//    styles.
//
//----------------------------------------------------------------------



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $indent_style = "none";

    $indent_1_in_pixels = DEFAULT_INDENT_1_VALUE;

// diagnostics:

    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_summary  = DIAGNOSTICS_ON;
    $dflag_verbose  = DIAGNOSTICS_OFF;
    $dflag_development = DIAGNOSTICS_ON;

    $rname = "handle_image_row_indents";

// VAR END 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    show_diag($rname, "called by '$caller' with row count of $count_of_image_rows,", $dflag_verbose);
    show_diag($rname, "\$options[" . KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_INDENT_STYLE . "] => $options[KEY_NAME__IMAGE_ROW_INDENT_STYLE],", $dflag_verbose);

//    if (array_key_exists(
    if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_INDENT_STYLE, $options) )
    {
        $indent_style = $options[KEY_NAME__IMAGE_LAYOUT__IMAGE_ROW_INDENT_STYLE];

        if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__INDENT_1_IN_PIXELS, $options) )
            { $indent_1_in_pixels = $options[KEY_NAME__IMAGE_LAYOUT__INDENT_1_IN_PIXELS]; }

        if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__INDENT_2_IN_PIXELS, $options) )
            { $indent_2_in_pixels = $options[KEY_NAME__IMAGE_LAYOUT__INDENT_2_IN_PIXELS]; }

    }


    switch ($indent_style)
    {
        case KEY_VALUE__IMAGE_ROW_INDENT_STYLE__NONE:
            break;

        case KEY_VALUE__IMAGE_ROW_INDENT_STYLE__ALTERNATE:
            if ( ( $count_of_image_rows % 2 ) == 0 )
            {
//                ...
                indent_image_row($rname, $indent_1_in_pixels);
            }
            break;

        case KEY_VALUE__IMAGE_ROW_INDENT_STYLE__STAGGERED_1:
            break;

        case KEY_VALUE__IMAGE_ROW_INDENT_STYLE__SAWTOOTH:
            break;
    }

} // end function handle_image_row_indents()





function &images_and_captions_list_from_database($caller, $name_of_image_group, $options) // NOT YET IMPLEMENTED
{
    echo "- 2017-11-06 - ROUTINE '&images_and_captions_list_from_database' NOT YET IMPLEMENTED." . $term;
}



function &build_caption_list_from_file($caller, $captions_text_file, $options) // NOT YET IMPLEMENTED
{
    echo "- 2017-11-06 - ROUTINE '&build_caption_list_from_file' NOT YET IMPLEMENTED." . $term;
}




function &get_caption($caller, $image_filename, $options)
{

    $caption_source = "default caption";
    $regex = "";
    $matches = array();
    $caption = "";

//diagnostics:
    $rname = "get_caption";


    if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__SOURCE_OF_IMAGE_CAPTIONS, $options) )
    {
        $caption_source = $options[KEY_NAME__IMAGE_LAYOUT__SOURCE_OF_IMAGE_CAPTIONS];
    }


    switch ($caption_source)
    {
        case KEY_VALUE__CAPTIONS_FROM_IMAGE_FILENAMES:
            if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__PARSE_CAPTIONS_FROM_IMAGE_NAMES_VIA_REGEX, $options) )
            {
               $regex = $options[KEY_NAME__IMAGE_LAYOUT__PARSE_CAPTIONS_FROM_IMAGE_NAMES_VIA_REGEX];
               preg_match($regex, $image_filename, $matches);
               if ( $matches )
               {
                   if ( $matches[3] )
                   {
                       $caption = $matches[3];
// echo "<pre>matches for caption:";
// print_r($matches);
// echo "</pre>";
                       $caption = preg_replace('/-/',' ', $caption);
                   }
               }
            }
            break;

        case KEY_VALUE__CAPTIONS_FROM_FLAT_TEXT_FILE:
// 2017-11-06 - CAPTIONS FROM FLAT TEXT FILE NOT YET IMPLEMENTED
            break;

        case KEY_VALUE__CAPTIONS_FROM_DATABASE:
// 2017-11-06 - CAPTIONS FROM DATABASE NOT YET IMPLEMENTED
            break;
    }


    return $caption;


}





function build_layout_for_image_and_caption($caller, $image_file, $caption, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to construct the HTML5 and CSS mark up needed to present
//    one image and when present its optinal caption.
//
//  EXPECTS:
//    *  image filename
//    *  image directory
//    *  optional caption for image
//    +  IMAGE_AND_CAPTION_BLOCK_ELEMENT_HEIGHT
//    +  IMAGE_AND_CAPTION_BLOCK_ELEMENT_WIDTH
//    +  image height
//    +  image width
//    +
//
//  RETURNS:
//
//
//
//  OPTIONS SUPPORTED:
//
//     units_of_measurement . . . [ px | em ]
//  . . . but are there places where we want only to apply px units?
//     image_height
//     image_width
//
//
//
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $image_width = 134;

    $image_dir = $options[KEY_NAME__IMAGE_LAYOUT__IMAGE_DIR];  // <-- NEED TO CHECK FOR NON-ZERO DIRNAME LENGTH HERE - TMH


// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    $rname = "build_layout_for_image_and_caption";



// DEV - single echo statement at point of initial developement:
//    echo "      $image_file<br />\n";


    echo "
      <div style=\"float:left; border:none\">
         <div style=\"display:flex; width:${image_width}px; margin:auto; border:none\">
            <div style=\"min-width:100px; max-width:${image_width}px; min-height:100px; max-height:100px; padding-top:10px; padding-bottom:10px; border:none\">\n";

//              <img src="./images/icons/A52-TrendArrow-Blue-FourDirections.svg" alt="blue arrows, four directions" width="100">
    echo "               <img src=\"${image_dir}/${image_file}\" alt=\"blue arrows, four directions\" width=\"${image_width}\">";

    echo "
            </div>
         </div>
         <div style=\"float:left; text-align:center; width:" . ($image_width + 20) . "px; padding-top:10px; padding-bottom:10px; border:none\">
            <div class=\"image-caption\">
$caption 
            </div>
         </div>
      </div>\n";


} // end function build_layout_for_image_and_caption()




function present_image_set($caller, $image_directory, $explanatory_text_file, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to present explanatory text, images, and associated
//   captions in a web page, building layout via HTML5 and CSS.
//
//  EXPECTS:
//    *  calling code identifying string
//    *  directory holding image files or image list in text file form
//    *  full path to explanatory notes of and for images
//    *  PHP array (ordered map) of various formatting options
//
//  Formatting options include:
//      +  width of image row
//      +  height of image row
//      +  background of image row, color or style passed here as string
//      +  images shown per row before wrapping to next row
//      +  image row indent style
//      +  source of captions
//      +
//      +
//
//    2017-11-06 - Contributor Ted likely to add some image padding and
//    caption padding options.
//
//
//  Supported indentation styles:
//
//     0  . . . no indent
//
//     1  . . . alternating indent, zero pixels and n pixels wide
//
//
//      no indent    alternate     staggered 1    sawtooth
//
//       | *           | *           | *           | *
//       | *           |   *         |  *          |  *
//       | *           | *           | *           |   *
//       | *           |   *         |   *         | *
//       | *           | *           | *           |  *
//       | *           |   *         |  *          |   *
//       | *           | *           | *           | *
//       | *           |   *         |   *         |  *
//
//
//  NOTES ON IMPLEMENTATION:  options to format a group of images
//   and their explanatory text and captions are passed to this routine
//   as an array, technically a PHP ordered map.  This same array of
//   options is passed on to called routines from this routine, rather
//   than creating smaller more specific arrays of options for those
//   more specialized routines.
//
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

// array of image files which is populated locally:
    $list_of_images = array();

// running count of images in present row, for wrapping to next row:
    $image_in_current_row = 0;

// an arbitrary default number of images to show, if caller sends no value
    $new_row_after_n_images = 10;

// local variable for pass/fail tests and similar:
    $result = 0;


// diagnostics:

    $dflag_announce = DIAGNOSTICS_OFF;
    $dflag_summary  = DIAGNOSTICS_OFF;
    $dflag_verbose  = DIAGNOSTICS_OFF;
    $dflag_development = DIAGNOSTICS_OFF;

    $dflag_show_image_list    = DIAGNOSTICS_OFF;
    $dflag_image_count_in_row = DIAGNOSTICS_OFF;

    $diagnostics_requests = array();

    $rname = "present_image_set";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "--- NOTE THIS ROUTINE IN PROGRESS! ---", $dflag_development);

    if ( $dflag_summary )
    {
        show_diag($rname, "called by '$caller',", $dflag_verbose);
        show_diag($rname, "with image directory '$image_directory',", $dflag_verbose);
        show_diag($rname, "with explanatory text file '$explanatory_text_file',", $dflag_verbose);
        show_diag($rname, "and with options:", $dflag_verbose);
        echo "<pre>\n";
        print_r($options);
        echo "</pre>\n";
    }



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - grab options from caller:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//    if ( array_key_exists(LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF, $options) )
    if ( array_key_exists(KEY_NAME__IMAGE_LAYOUT__IMAGES_SHOWN_PER_ROW, $options) )
        { $new_row_after_n_images = $options[KEY_NAME__IMAGE_LAYOUT__IMAGES_SHOWN_PER_ROW]; }

    if ( array_key_exists(KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS_DETAILED, $options) )
        { $detailed_diags = $options[KEY_NAME__SITE_NAVIGATION__DIAGNOSTICS_DETAILED]; }


    $split_pattern = hash_of_diagnostics_elements_split_on($rname);
    show_diag($rname, "calling for detailed diagnostics hash, elements split on $split_pattern . . .", DIAGNOSTICS_ON);
    $limit = 20;
//    $diagnostics_requests =& hash_of_diagnostics_requested($rname, "diag1,diag2,diag3", $limit);
    $diagnostics_requests =& hash_of_diagnostics_requested($rname, $detailed_diags, $limit);

    show_diag($rname, "for 2018-01-18 development requesting specific diagnostics:", DIAGNOSTICS_ON);
    echo "<pre>
";
    print_r($diagnostics_requests);
    echo "</pre>\n";

    $result = check_for_request_in_hash($rname, $diagnostics_requests, "zzz");
    show_diag($rname, "check for request \"zzz\" returns $result,", DIAGNOSTICS_ON);

    $result = check_for_request_in_hash($rname, $diagnostics_requests, "none set");
    show_diag($rname, "check for request \"none set\" returns $result,", DIAGNOSTICS_ON);





// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - build list of image files:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    show_diag($rname, "building list of image files . . .", $dflag_verbose);
    $list_of_images = list_of_filenames_by_pattern($rname, $image_directory, "/(.*).jpg/");

    if ( $dflag_show_image_list )
    {
        echo "<pre>\n";
        sort($list_of_images);
        print_r($list_of_images);
        echo "</pre>\n";
    }



// - STEP - set up to begin a row:

    {
        $row_count = 0;
        $images_in_current_row = 0; 

        ++$row_count;
        echo "<!-- image row $row_count begin -->";
        open_row_of_images($rname, $options);

        foreach ($list_of_images as $key => $image_file )
        {

// STEP - when needed, wrap to the next row:

            if ( $images_in_current_row >= $new_row_after_n_images )
            {
                close_row_of_images($rname, $options);

                ++$row_count;
                $images_in_current_row = 0;


// 2017-11-08 - Adding space between rows . . .
echo "<div style=\"min-height:10px; overflow:auto; border:none; background:none\"><center> &nbsp; </center>
</div>
";

                echo "<!-- image row $row_count begin -->";
                open_row_of_images($rname, $options);
                handle_image_row_indents($rname, $row_count, $options);
            }

// STEP - obtain caption for present image:

            $caption = get_caption($rname, $image_file, $options);


// STEP - build and send layout, image and caption to browser:

            build_layout_for_image_and_caption($rname, $image_file, $caption, $options);
            ++$images_in_current_row;

// echo "there are now $images_in_current_row images in current row, \$new_row_after_n_images set to $new_row_after_n_images,<br />\n";

            show_diag($rname, "there are now $images_in_current_row images in current row, \$new_row_after_n_images set to $new_row_after_n_images,", $dflag_image_count_in_row);
        }


    }

    close_row_of_images($rname, $options);





    show_diag($rname, "returning to calling code . . .", $dflag_announce);

//    echo "zztop";

} // end function present_image_set()





?>
