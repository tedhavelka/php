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




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

function nn_build_footer($caller, $date_last_updated)
{

//
// TO-DO 2017-08-06 SUN -- implement a meaningful or at least better
//  reference to 'date web page updated' in the footer message below . . .
//

    echo "
<div class=\"footer-custom\">
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



?>
