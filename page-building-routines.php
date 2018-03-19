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



/*
function open_body($caller)
{
// 2017-10-19 - added by Ted
    echo "<body>\n";
}
*/



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


// Version watermark added 2018-03-14:
    if ( 1 )
    {
        echo "   <!-- 2018-03-14 WED, second latest git commit 058e597 -->\n";
    }


    echo "</head>\n\n\n";
//    echo "</head>\n" . $BLANK_LINES_BETWEEN_HTML_PAGE_SECTIONS;

}




function nn_build_footer($caller, $date_last_updated, $options)
{

//----------------------------------------------------------------------
//
//    * REF *  http://learnlayout.com/position.html
//
//
// TO-DO 2017-08-06 SUN -- implement a meaningful or at least better
//  reference to 'date web page updated' in the footer message below . . .
//
//----------------------------------------------------------------------


// - 2017-11-14 NOTE - new local PHP library wide standard has $options
//  declared as an array, whose key-value pairs are able to pass
//  arbitrarily few and many options.  So this PHP string comparison
//  is now depricated, and a newer version of this function follows
//  further or elsewhere in this source file:

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




function nn_build_footer_v2($caller, $options)
{
//----------------------------------------------------------------------
// STARTED:  2017-11-10 FRI
//
// NOTES:  uses PHP array of options, see defines-nn.php for supported
//   web page footer options
//----------------------------------------------------------------------


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// VAR BEGIN

    $block_element_positioning = KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_STATIC;

    $text_alignment = KEY_VALUE__TEXT_ELEMENT__ALIGN_CENTER;

// This attribute not yet updated from passed options:
      $text_size_as_percentage = "80%";

    $text_element_style = KEY_VALUE__TEXT_ELEMENT__STYLE_NORMAL;

    $background_style = "none";

    $line_1 = "";
    $line_2 = "";
    $line_3 = "";

    $contact_name = "";
    $contact_email = "";
    $page_last_updated = "";

    $contact_line = "";

    $term = "<br />\n";


// diagnostics:

    $rname = "nn_build_footer_v2";

// VAR END
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - handle supported options
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__BLOCK_ELEMENT_POSITIONING, $options) )
        { $block_element_positioning = $options[KEY_NAME__FOOTER_ATTRIBUTES__BLOCK_ELEMENT_POSITIONING]; }

    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__TEXT_ALIGNMENT, $options) )
        { $text_alignment = $options[KEY_NAME__FOOTER_ATTRIBUTES__TEXT_ALIGNMENT]; }

    if ( array_key_exists(KEY_NAME__TEXT_ELEMENT__STYLE, $options) )
        { $text_element_style = $options[KEY_NAME__TEXT_ELEMENT__STYLE]; }


    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__LINE_1, $options) )
        { $line_1 = $options[KEY_NAME__FOOTER_ATTRIBUTES__LINE_1]; }

    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__LINE_2, $options) )
        { $line_2 = $options[KEY_NAME__FOOTER_ATTRIBUTES__LINE_2]; }

    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__LINE_3, $options) )
        { $line_3 = $options[KEY_NAME__FOOTER_ATTRIBUTES__LINE_3]; }


    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__BACKGROUND_STYLE, $options) )
        { $background_style = $options[KEY_NAME__FOOTER_ATTRIBUTES__BACKGROUND_STYLE]; }


    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__CONTACT_NAME, $options) )
        { $contact_name = $options[KEY_NAME__FOOTER_ATTRIBUTES__CONTACT_NAME]; }

    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__CONTACT_EMAIL, $options) )
        { $contact_email = $options[KEY_NAME__FOOTER_ATTRIBUTES__CONTACT_EMAIL]; }

    if ( array_key_exists(KEY_NAME__FOOTER_ATTRIBUTES__PAGE_LAST_UPDATED, $options) )
        { $page_last_updated = $options[KEY_NAME__FOOTER_ATTRIBUTES__PAGE_LAST_UPDATED]; }



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - for CSS fixed type positioning of block level elements,
//          append a couple of needed CSS attributes, 'bottom' and 'left',
//          see http://learnlayout.com/position.html for information
//          about CSS position attribute and values.
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//  bottom: 0; left: 0;

    if ( $block_element_positioning === KEY_VALUE__BLOCK_ELEMENT_ATTRIBUTES__POSITIONING_FIXED )
    {
        $block_element_positioning = $block_element_positioning . "; bottom:0; left:0";
    }


// <a href="mailto:someone@example.com">Send email</a>

    if ( ( strlen($contact_email) > 0 ) && ( strlen($contact_name) > 0 ) )
        { $contact_line = "<a href=\"mailto:$contact_email\">$contact_name</a>"; }

    elseif ( strlen($contact_email) > 0 ) 
        { $contact_line = "<a href=\"mailto:$contact_email\">$contact_email</a>"; }

    elseif ( strlen($contact_name) > 0 ) 
        { $contact_line = $contact_name; }


    if ((( strlen($contact_email) > 0 ) || ( strlen($contact_name) > 0 ) ) && ( strlen($page_last_updated) > 0 ) )
    {
        $contact_line = "Page last update $page_last_updated by $contact_line";
    }
    else
    {
        $contact_line = "Contact webmaster $contact_line";
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - send HTML5 mark-up to web browser
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    {
        echo "<!-- document footer open -->\n";

        echo "<div style=\"clear:left; position:$block_element_positioning; width:100%; text-align:$text_alignment; font-size:$text_size_as_percentage; font-style:$text_element_style; padding-top:2em; padding-bottom:2em; background:$background_style\">
";

//    if ( strlen($contact_line) > 0 ) { echo $contact_line . $term; }

    if ( strlen($line_1) > 0 ) { echo $line_1 . $term; }
    if ( strlen($line_2) > 0 ) { echo $line_2 . $term; }
    if ( strlen($line_3) > 0 ) { echo $line_3 . $term; }

    if ( strlen($contact_line) > 0 ) { echo $contact_line . $term; }

        echo"</div>
<!-- document footer close -->\n\n";
    }

} // end fucntion nn_build_footer_v2()




/*
function send_html_body_open($caller)
{
    echo "<body>\n";
}
*/





function send_html_body_open($caller, $options)
{

    $web_page_body_background = "";
    $style = "";
    $html = "";

    $rname = "send_html_body_open";


// - STEP - query passed options hash for style and other document body attributes:

    if (array_key_exists(KEY_NAME__ATTRIBUTES_BLOCK__BACKGROUND, $options) )
    {
        $web_page_body_background = $options[KEY_NAME__ATTRIBUTES_BLOCK__BACKGROUND];
    }


// - STEP - build web document opening body statement:

    if ( strlen($web_page_body_background) > 0 )
    {
        $style = "style=\"" . $options[KEY_NAME__ATTRIBUTES_BLOCK__BACKGROUND] . "\"";

        $html = "<body $style>\n";
    }
    else
    {
        $html = "<body>\n";
    }

    echo $html;

} // end function send_html_body_open()







function send_html_body_close($caller)
{
    echo "</body>\n";
}





/*
function --
function -- SECTION -- functions to build small strings
function --
*/

function &nbsp_based_indent($caller, $depth, $options)
{

//    $mark_up = "&nbsp;";
//    $mark_up = "*&nbsp;";
    $mark_up = "<font color=\"lightgrey\">*</font>&nbsp;";

    $indent;

    $rname = "nbsp_based_indent";


//    if ( $depth > 1 )
    if ( $depth > 0 )
    {
//        $indent = $mark_up . str_repeat(" $mark_up", ($depth - 1));
//        $indent = $mark_up . str_repeat("*$mark_up", ($depth - 1));
        $indent = str_repeat($mark_up, $depth);
    }
//    elseif ( $depth == 1 )
//    {
//        $indent = $mark_up;
//    }
    else
    {
        $indent = "";
    }

    return $indent;

}


?>
