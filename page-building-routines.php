<?php
//======================================================================
//
//  PROJECT:  PHP library routines at Neela, de Ted
//
//  FILE:  page-building-routines.php
//
//  STARTED:  2017 July
//
//
//  DESCRIPTION:  routines, many with so far fixed output, to support
//   production of repetitive and boiler plate mark-up, for HTML
//   pages.  As of 2017 just a starting point.  Author Ted hopeful that
//   much or some of functionality he would put here will already be
//   covered better by parts of larger, developed web frameworks such
//   as Drupal . . .    - TMH
//
//
//  HOW TO USE:
//
//
//
//  NOTES ON IMPLEMENTATION:
//
//
//  REFERENCES:
//
//
//  AUTHORS AND CONTRIBUTORS:
//
//     * Ted Havelka,  <ted@cs.pdx.edu>,  TMH
//
//
//
//
//======================================================================





/*
function --
function -- SECTION -- HTML page basic sections --
function --
*/

function send_opening_lines($caller)
{
    echo "<!doctype html>\n";
    echo "<html>\n";
}



function send_html_document_opening_lines($caller)
{
// 2017-08-30 - added by Ted, to make function name more specific and
//  clear.  Does same thing as function named 'send_opening_lines',
//  which Ted hereby depracating.

    echo "<!doctype html>\n";
    echo "<html>\n";
}




function send_closing_lines($caller)
{
    echo "</html>\n";
}




function send_html_document_closing_lines($caller)
{
// 2017-08-30 - added by Ted, to make function name more specific and
//  clear.  Does same thing as function named 'send_closing_lines',
//  which Ted hereby depracating.

    echo "</html>\n";
}



function open_body($caller)
{
// 2017-10-19 - added by Ted
    echo "<body>\n";
}



function close_body($caller)
{
// 2017-10-19 - added by Ted
    echo "</body>\n";
}





function nn_build_header($caller, $title, $css_files)
{
    echo "<head>\n";

    if ( $title )
    {
        echo "   <title>$title</title>\n";
    }
    else
    {
        echo "<i>DEV NOTE - no page title sent to 'build header' routine,</i><br />\n";
    }


// TO-DO 2017-08-06 - implement while-loop or similar construct to iterate over all
//  CSS file array elements:

if ( 0 )
{
    if ( $css_files[0] )
    {
        echo "   <link rel=\"stylesheet\" href=\"$css_files[0]\">\n";
    }
    else
    {
        echo "<i>DEV NOTE - no style sheet sent to 'build header' routine,</i><br />\n";
    }
}

    foreach ($css_files as $index => $value)
    {
        echo "   <link rel=\"stylesheet\" href=\"$value\">\n";
    }



    echo "</head>\n\n\n";
//    echo "</head>\n" . $BLANK_LINES_BETWEEN_HTML_PAGE_SECTIONS;

}




function nn_build_footer($caller, $date_last_updated, $options)
{

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//
//    * REF *  http://learnlayout.com/position.html
//
//
// TO-DO 2017-08-06 SUN -- implement a meaningful or at least better
//  reference to 'date web page updated' in the footer message below . . .
//
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( $options === "--div-position-fixed" )
    {
        echo "<div class=\"footer-custom\">";
    }
    else
    {
        echo "<div style=\"clear:left; width:100%; padding-top:2em; padding-bottom:2em; background:none\">";
    }


    echo "
<center>
<font size=\"2\">
<i>
Page last updated $date_last_updated by <a href=\"mailto:ariliriswebmaster@gmail.com?subject=Neela%20Nurseries%20website\">Ted Havelka</a>
</i>
</font>
</center>
</div>\n
";

}



function send_html_body_open($caller)
{
    echo "<body>\n";
}




function send_html_body_close($caller)
{
    echo "</body>\n";
}





/*
function --
function -- SECTION -- early routines with fixed output --
function --
*/

function nn_build_navigation__top_of_gallery_page__fixed_output($script_name)
{

$not_ready_color = "<font color=\"#7f7f7f\">";
$font_close_tag = "</font>";

echo "<!-- BEGIN LAYOUT for Neela Nurseries top menu, navigation bar -->
<div class=\"container\">
  <div class=\"menu-bar-top\">
    <div class=\"menu-block-element-as-margin\">
      <font color=\"#ffffff\"> . . . </font>
    </div>

    <div class=\"menu-item\"> <a href=\"https://neelanurseries.com\">home</a> </div>

    <div class=\"menu-item-text-separator\"> : </div>


    <div class=\"menu-item\"> " . $not_ready_color . "plants" . $font_close_tag . "</div>

    <div class=\"menu-item-text-separator\"> : </div>


    <div class=\"menu-item\"> " . $not_ready_color . "seeds" . $font_close_tag . "</div>

    <div class=\"menu-item-text-separator\"> : </div>


    <div class=\"menu-item\"> " . $not_ready_color . "articles" . $font_close_tag . "</div>

    <div class=\"menu-item-text-separator\"> : </div>


    <div class=\"menu-item\"> " . $not_ready_color . "resources" . $font_close_tag . "</div>

    <div class=\"menu-item-text-separator\"> : </div>


    <div class=\"menu-item\"> <a href=\"https://neelanurseries.com/gallery\">gallery</a> </div>

    <div class=\"menu-block-element-as-margin\">
      <font color=\"#ffffff\"> . . . </font>
    </div>
  </div><!-- close of div element of type 'menu-bar-top' for menu bar 1 -->
</div><!-- close of div element of type 'container' -->
<!-- END LAYOUT for Neela Nurseries top menu, navigation bar -->\n\n\n";

}




function nn_main_content_formatting__opening_lines__fixed_output($script_name, $option)
//       nn_main_content_formatting__opening_lines__fixed_output
{

echo "<div class=\"container\">
<!-- This is the left hand column, typically for navigation or easily controlled margin -->
   <div class=\"column-for-main-content-margin\">
   </div>

";

    if ( strcmp($option, "--divs-with-borders") == 0 )
    {
        echo "   <div class=\"column-middle-with-border\">\n";
    }
    else
    {
        echo "   <div class=\"column-middle\">\n";
    }


}




function nn_main_content_formatting__closing_lines__fixed_ouput($script_name)
{

    echo "  </div>

  <div class=\"column-for-main-content-margin\">
  </div>
</div><!-- closing scope of div 'container' -->
";

}




/*
function --
function -- SECTION -- image layout routines --
*/

function parse_image_measures_and_layout_requests_v2($caller, $path_to_image, $caption, $options)
{

//    echo "Routine 'image_and_caption_with_row_oriented_layout' not yet implemented<br />\n";

//----------------------------------------------------------------------
//
//  PURPOSE:
//
//
//  SUPPORTED OPTIONS:
//    *  image block element height in pixels
//    *  image block element width in pixels
//
//
// 2017-10-20 FRI - Today Friday working to implement caller-specifiable
//  images sizes.  Looks in echo statement below which sends multiple
//  physical lines to browser, that an image's caption div element 
//  width is 10px less than width of div directly containing image.
//  Also looks like padding-left and padding-right are both 10px, so
//  QUESTION:  are padding-left and padding-right set by difference
//  between image width and caption div element width?
//
//  This question comes from need to determine how many sizes calling
//  code need provide.  If just image width and height, this would be
//  more simple.  But finer control may mean we support calling code's
//  spec'ing dimensions and alignment left, right, center horontally
//  and vertically of the captions of images in a given image list.
//
//  To get started with first draft caller-spec'd dimensions assume
//  this code needs only image wdith and height.  Parameter $options
//  could hold a parseable string of the form:
//
//
//  --image-size:width=200,height=150,units=px
//
//
//----------------------------------------------------------------------

    global $term;

    $options_parsed = array();


// development and diagnostics:

    static $times_called = 0;

    $dflag_summary         = DIAGNOSTICS_OFF;
    $dflag_options_parsing = DIAGNOSTICS_ON;
    $dflag_dev             = DIAGNOSTICS_OFF;

    $dflag_send_div_element = ($dflag_summary | $dflag_options_parsing | $dflag_dev);

    $rname = "parse_image_measures_and_layout_requests_v2";



    ++$times_called;

    if ( $dflag_send_div_element ) { echo "<div style=\"float:left\">\n"; }
    show_diag($rname, "called by $caller,$term caller sends:", $dflag_summary);
    show_diag($rname, "image = $path_to_image,", $dflag_summary);
    show_diag($rname, "caption = $caption,", $dflag_summary);
    show_diag($rname, "options = $options,", $dflag_summary);   // this statement written beofre $options implemented as PHP array,


    if ( array_key_exists($options, LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_WIDTH) )
    {
        $image_block_element_width = $options[LOCAL_PHP_LIBRARY_OPTION__IMAGE_BLOCK_ELEMENT_WIDTH];
        show_diag($rname, "setting image_block_element_width to $image_block_element_width,", $dflag_options_parsing);
        if ( $image_block_element_width < 10 )
        {
            $image_block_element_width = 10;
        }
    }


// Parsing for image size setting options . . .
    if ( array_key_exists($options, LOCAL_PHP_LIBRARY_OPTION__IMAGE_SIZE) )
    {
//        preg_match('@(--image-size)(:)(width=)([0-9]*),(height=)([0-9]*),(units=)([0-9]*)@', $options, $options_parsed);
        preg_match('@(--image-size)(:)(width=)([0-9]*),(height=)([0-9]*),(units=)([0-9]*)@', $options[LOCAL_PHP_LIBRARY_OPTION__IMAGE_SIZE], $options_parsed);
    }


    if ( $times_called < 2 )
    {
        show_diag($rname, "this routine called $times_called times,", $dflag_options_parsing);
        show_diag($rname, "parsed " . count($options_parsed) . " tokens from caller's option string,", $dflag_options_parsing);
        show_diag($rname, "option parsing give tokens:$term", $dflag_options_parsing);
        if ( $dflag_options_parsing )
        {
            echo "<pre>\n";
            print_r($options_parsed);
            echo "</pre>\n";
        }
    }


    if ( $dflag_send_div_element ) { echo "</div>\n"; }

    $image_width = ($image_block_element_width - 10);
//          <div style=\"display:flex; width:210px\"> <!-- create a layout element that is 210 pixels wide -->
//           |
    echo "
         <div style=\"float:left\"> <!-- floating div causes browser to lay out images starting left and going to right -->
            <div style=\"display:flex; width:${image_block_element_width}px\"> <!-- create a layout element that is ${image_block_element_width} pixels wide -->
               <div style=\"width:${image_width}px; height:150px; margin:auto\"> <!-- with margin set to auto, create 200 pixel wide element centered in parent element -->
                  <img src=\"$path_to_image\" width=\"$image_width\" align=\"left\">
               </div>
            </div>
            <div style=\"float:left; width:190px; padding-left:10px; padding-right:10px\">$caption
            </div>
         </div>

";

} // end function parse_image_measures_and_layout_requests_v2()






?>
