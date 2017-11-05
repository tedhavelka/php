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
//    * REF *  http:
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



//----------------------------------------------------------------------
// - SECTION - diagnostics and development
//----------------------------------------------------------------------



//----------------------------------------------------------------------
// - SECTION - routines for image layout and presentation
//----------------------------------------------------------------------

function open_row_of_images($caller, $row_attributes)
{

//    echo "   {image row opening statements}<br />\n";

    echo "
<div style=\"min-height:100px; min-width:100px; overflow:auto; border:none; background:#c0c0c0\">
   <div style=\"float:left; min-height:100px; min-width:100px; max-width:1000px; border:none\">\n";

//    echo "
//      <div style=\"float:left; border:none\">
//         <div style=\"display:flex; width:134px; margin:auto; border:none\">\n";

}



function close_row_of_images($caller, $row_attributes)
{

//    echo "   {image row closing statements}<br />\n";

    echo "  </div>
</div>\n";

}



function indent_image_row($caller, $indent_by_n_pixels)
{
    echo "      <div style=\"float:left; width:{$index_by_n_pixels}px; border:none.\"> &nbsp;
      </div>";
}




function build_layout_for_image_and_caption($caller, $image_file, $caption, $options)
{
//
//
//  PURPOSE:
//
//
//  EXPECTS:
//
//
//  RETURNS:
//
//
//  OPTIONS SUPPORTED:
//
//     units_of_measurement . . . [ px | em ]
//     image_width
//
//


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $image_width = 134;

    $image_dir = $options[KEY_NAME__IMAGE_DIR];  // <-- NEED TO CHECK FOR NON-ZERO DIRNAME LENGTH HERE - TMH


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
    echo "                <img src=\"${image_dir}/${image_file}\" alt=\"blue arrows, four directions\" width=\"${image_width}\">";

    echo "
            </div>
         </div>
         <div style=\"float:left; text-align:center; width:" . ($image_width + 20) . "px; padding-top:10px; padding-bottom:10px; border:none\">
$caption 
         </div>
      </div>\n\n";


}




function present_image_set($caller, $image_directory, $explanatory_text_file, $options)
{
//----------------------------------------------------------------------
//
//
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
//
//
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $list_of_images = array();

    $attributes_for_row_of_images = array();

    $options_for_images_and_captions = array();

    $image_in_current_row = 0;

    $new_row_after_n_images = 10;  // an arbitrary default number of images to show, if caller sends no value


// diagnostics:

    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_summary  = DIAGNOSTICS_ON;
    $dflag_verbose  = DIAGNOSTICS_ON;
    $dflag_development = DIAGNOSTICS_ON;

    $dflag_image_count_in_row = DIAGNOSTICS_OFF;

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


    $options_for_images_and_captions[KEY_NAME__IMAGE_DIR] = $image_directory;


// - STEP - grab options from caller:

    if ( array_key_exists(LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF, $options) )
    {
        $new_row_after_n_images = $options[LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF];
    }
    else
    {
        show_diag($rname, "couldn't find option '" . LOCAL_PHP_LIBRARY_OPTION__NEW_ROW_AFTER_IMAGE_COUNT_OF . "' in caller's array of sent options,", $dflag_verbose);
    }



// - STEP - build list of image files:

    show_diag($rname, "building list of image files . . .", $dflag_verbose);
    $list_of_images = list_of_filenames_by_pattern($rname, $image_directory, "/(.*).jpg/");

    if ( 1 )
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
        open_row_of_images($rname, $attributes_for_row_of_images);
        ++$row_count;

        foreach ($list_of_images as $key => $image_file )
        {
            if ( $images_in_current_row >= $new_row_after_n_images )
            {
                close_row_of_images($rname, $attributes_for_row_of_images);
                open_row_of_images($rname, $attributes_for_row_of_images);
                ++$row_count;
                $images_in_current_row = 0;
            }
            build_layout_for_image_and_caption($rname, $image_file, "caption", $options_for_images_and_captions);
            ++$images_in_current_row;

// echo "there are now $images_in_current_row images in current row, \$new_row_after_n_images set to $new_row_after_n_images,<br />\n";

            show_diag($rname, "there are now $images_in_current_row images in current row, \$new_row_after_n_images set to $new_row_after_n_images,", $dflag_image_count_in_row);
        }


    }

    close_row_of_images($rname, $attributes_for_row_of_images);





    show_diag($rname, "returning to calling code . . .", $dflag_announce);

//    echo "zztop";

} // end function present_image_set()





?>
