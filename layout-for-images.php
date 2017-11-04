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
    echo "   {image row opening statements}<br />\n";
}


function close_row_of_images($caller, $row_attributes)
{

    echo "   {image row closing statements}<br />\n";
}


function build_layout_for_image_and_caption($caller, $image_file, $options_for_layout)
{
    echo "      $image_file<br />\n";
}



function present_image_set($caller, $directory_of_images, $explanatory_text_file, $options)
{
//----------------------------------------------------------------------
//
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $list_of_images = array();

    $attributes_for_row_of_images = array();

    $options_to_form_caption = array();

    $image_in_current_row = 0;

    $new_row_after_n_images = 10;  // an arbitrary default number of images to show, if caller sends no value


// diagnostics:

    $dflag_announce = DIAGNOSTICS_ON;
    $dflag_summary  = DIAGNOSTICS_ON;
    $dflag_verbose  = DIAGNOSTICS_ON;
    $dflag_development = DIAGNOSTICS_ON;

    $rname = "present_image_set";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    show_diag($rname, "starting,", $dflag_announce);
    show_diag($rname, "--- NOTE THIS ROUTINE IN PROGRESS! ---", $dflag_development);

    if ( $dflag_summary )
    {
        show_diag($rname, "called by '$caller',", $dflag_verbose);
        show_diag($rname, "with image directory '$directory_of_images',", $dflag_verbose);
        show_diag($rname, "with explanatory text file '$explanatory_text_file',", $dflag_verbose);
        show_diag($rname, "and with options:", $dflag_verbose);
        echo "<pre>\n";
        print_r($options);
        echo "</pre>\n";
    }



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
    $list_of_images = list_of_filenames_by_pattern($rname, $directory_of_images, "/(.*).jpg/");

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
            build_layout_for_image_and_caption($rname, $image_file, $options_to_form_caption);
            ++$images_in_current_row;
echo "there are now $images_in_current_row images in current row, \$new_row_after_n_images set to $new_row_after_n_images,<br />\n";
        }


    }

    close_row_of_images($rname, $attributes_for_row_of_images);





    show_diag($rname, "returning to calling code . . .", $dflag_announce);

//    echo "zztop";

}





?>
