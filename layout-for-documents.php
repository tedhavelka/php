<?php
//======================================================================
//
//  PROJECT:  PHP library routines at Neela
//
//  FILE:  layout-for-documents.php
//
//  STARTED:  2017-11-03
//
//
//  DESCRIPTION:  this file part of local PHP library, and contains
//   routines to generate HTML and CSS based layout for documents and
//   textual information.  With its focus on page layout the routines
//   in this file generate mostly mark-up for HTML5 / CSS type block
//   elements, typically <div> elements.  Routines in this file are
//   sometimes designed to accept document filenames and or arrays
//   of shorter text strings to put into generated layout mark-up at
//   the correct places . . .
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

//    require_once '/opt/nn/lib/php/file-and-directory-routines.php';




//----------------------------------------------------------------------
// - SECTION - PHP file-scoped constants
//----------------------------------------------------------------------




//----------------------------------------------------------------------
// - SECTION - diagnostics and development
//----------------------------------------------------------------------

function &document_section_count($caller, $option)
{

    static $count_document_sections_opened = 0;


    if ($option === "increment" )
    {
        ++$count_document_sections_opened;
    }


    return $count_document_sections_opened;
}




//----------------------------------------------------------------------
// - SECTION - routines for image layout and presentation
//----------------------------------------------------------------------

function block_element_for_document_section_margin($caller, $options) // 2017-11-07 - NOT YET IMPLEMENTED - TMH
{
//----------------------------------------------------------------------
//----------------------------------------------------------------------

// VAR BEGIN

    $mark_for_margin = "&nbsp;";

// diagnostics:

    $rname = "block_element_for_document_section_margin";

// VAR END



// For browser-based layout development, check if calling code asks
// us to show a visible mark or identifying string in the document
// section left hand margin:

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__SHOW_MARK, $options) )
    {
        $mark_for_margin = $options[KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__SHOW_MARK];
    }

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__ALIGN_MARK, $options) )
    {
        if ( $options[KEY_NAME__DOC_LAYOUT__CONTENT_MARGIN__ALIGN_MARK] === KEY_VALUE__TEXT_ELEMENT__ALIGN_CENTER )
        $mark_for_margin = "<center>" . $mark_for_margin . "</center>";
    }

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__MARGIN_WIDTH, $options) )
    {
        $attr_width = "width:" . $options[KEY_NAME__DOC_LAYOUT__MARGIN_WIDTH] . ";";
    }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - generate mark-up . . .
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//    echo "   <div style=\"float:left; width:15%; border:none\">
    echo "   <div style=\"float:left; $attr_width border:none\">
      ${mark_for_margin}
   </div>

";

} // end function block_element_for_document_section_margin()




function open_document_section_with_margin_block_elements($caller, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to send HTML mark-up which opens a web document section
//    composed of, right now three fixed block elements, all left-
//    floated so they're in a row like this:
//
//
//   [ block element to clear prior HTML position attributes ]
//   |                                                       |
//   | [ margin ] [      main       ] [ margin ]             |
//   | | block  | |      block      | | block  |             |
//   | |        | |                 | |        |             |
//   | |   15%  | |    70% width    | |  15%   |             |
//   | [        ] [                 ] [        ]             |
//   [                                                       ]
//
//
//    Note:  block elements here are expressed by this PHP code as
//    HTML div tag pairs.
//
//
//  OPTIONS SUPPORTED:
//     *  block element border style
//     *  block element width
//
//    Note: To pass these options see file 'defines-nn.php'.
//
//
//  OPTIONS TO BE SUPPORTED . . .
//     *  enable/disable margin-creating blocks left and right,
//     *  block element background style
//
//
//  RETURNS:  nothing
//
//
//----------------------------------------------------------------------


// VAR BEGIN

// default mark is non-breakable space, not visible but must be
// something for at least some types of CSS block elements to be
// rendered with their specified height and width values:

//    $block_element_border = "1px dotted white";
    $block_element_border = "none";
    $block_element_name = "DEFAULT BLOCK ELEMENT NAME";

    $min_height = "";
    $attr_min_height = "";          // has the form "min-height:400px"

    $document_section_width = 100;  // measured in percent

    $width = "";
    $width_as_number = 0;
    $attr_width = "";               // has the form "width:10%;"

    $width_margin = "";
    $width_margin_as_number = 0;
  
    $padding_top = "";
    $padding_bottom = "";


    $style_attributes = "";


// diagnostics:

    $lbuf = "";
    $dflag_dev = DIAGNOSTICS_ON;
    $dflag_center_width_calc = DIAGNOSTICS_OFF;

    $rname = "open_document_section_with_margin_block_elements";

// VAR END


// Start building list of HTML attributes in CSS format:

// style=\"float:left; $attr_width $attr_min_height $attr_border $padding_top $padding_bottom\"
    $style_attributes = "float:left;";


    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_BORDER_STYLE, $options) )
    {
        $block_element_border = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_BORDER_STYLE];
        if ( strlen($block_element_border) > 0 )
        {
            $attr_border = "border:$block_element_border;";
            $style_attributes = "$style_attributes $attr_border";
        }
    }

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_VERTICAL_HEIGHT_IN_PX, $options) )
    {
        $min_height = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_VERTICAL_HEIGHT_IN_PX];
// treat min_height value as a string, to capture both PX and percent values of block element height:
        if ( strlen($min_height) > 0 )
        {
            $attr_min_height = "min-height:$min_height;";  // will be of the form 'min-height:5em', 'min-height:50%' or 'min-height:500px'
            $style_attributes = "$style_attributes $attr_min_height";
        }
    }

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME, $options) )
    {
        $block_element_name = $options[KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME];
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - figure out the width value to apply to the middle block element of given document section:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__DOCUMENT_SECTION_WIDTH_IN_PERCENT, $options) )
    {
        $document_section_width = $options[KEY_NAME__DOC_LAYOUT__DOCUMENT_SECTION_WIDTH_IN_PERCENT];
        $document_section_width = preg_replace('/[^0-9]/', '', $document_section_width);
    }


    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_WIDTH, $options) )
    {
        $width = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_WIDTH];
        $attr_width = "width:$width;";
    }
    elseif (
             ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_WIDTH, $options) ) &&
             ( array_key_exists(KEY_NAME__DOC_LAYOUT__MARGIN_WIDTH, $options) )
           )
    {
        $width = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_WIDTH];
        $width_margin = $options[KEY_NAME__DOC_LAYOUT__MARGIN_WIDTH]; 
        $lbuf = "caller sends margin width of $margin_width and content column width of $width,";
        show_diag($rname, $lbuf, $dflag_dev);
    }
    elseif (
             ( !(array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_WIDTH, $options)) ) &&
             ( array_key_exists(KEY_NAME__DOC_LAYOUT__MARGIN_WIDTH, $options) )
           )
    {
        $width_margin = $options[KEY_NAME__DOC_LAYOUT__MARGIN_WIDTH]; 
        $lbuf = "caller sends only margin width, set to $width_margin,";
        show_diag($rname, $lbuf, $dflag_center_width_calc);
        $width_margin_as_number = preg_replace('/[^0-9]/', '', $width_margin);
        $lbuf = "calculating margin width times two as " . ($width_margin_as_number * 2);
        show_diag($rname, $lbuf, $dflag_center_width_calc);

//        $width_as_number = (100 - ($width_margin_as_number * 2));
        $width_as_number = ($document_section_width - ($width_margin_as_number * 2));
        $attr_width = "width:$width_as_number%;";
    }

    if ( strlen($attr_width) > 0 )
    {
        $style_attributes = "$style_attributes $attr_width";
    }



// - 2018-03-19 MON - added:
    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_PADDING_TOP, $options) )
    {
        $padding_top = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_PADDING_TOP];
        $style_attributes = "$style_attributes padding-top:$padding_top;";
    }

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_PADDING_BOTTOM, $options) )
    {
        $padding_bottom = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_PADDING_BOTTOM];
        $style_attributes = "$style_attributes padding-bottom:$padding_bottom;";
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - create div element to clear prior HTML position attributes:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// 2018-03-19 NEED TO REVIEW THIS DIV ELEMENT, WHOSE CLOSING TAG APPEARS TO BE
// +  HANDLED IN ANOTHER ROUTINE;  MESSY, HARD TO FOLLOW! . . .

    echo "<!-- document section tag to open -->
<div style=\"clear:left; $attr_border background:none\"><!-- div to clear previous block element position attributes -->
";

    $option[KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME] = "block element for document section margin left";


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - create div element for left margin in web page section:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    block_element_for_document_section_margin($rname, $options);


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - create div element for center column content:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//    echo "   <div style=\"float:left; $attr_width $attr_min_height $attr_border\"><!-- document section, middle column -->
//    echo "   <div style=\"float:left; $attr_width $attr_min_height $attr_border $padding_top $padding_bottom\"><!-- document section, middle column -->
//echo "- zzz - style attributes holds '$style_attributes',<br />\n";
    if ( strlen($style_attributes) > 0 )
    {
// add the final CSS syntax to our completed attributes string:
        $style_attributes = "style=\"$style_attributes\"";
        echo "   <div $style_attributes><!-- document section, middle column -->\n";
    }
    else
    {
        echo "   <div><!-- document section, middle column -->\n";
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - DEVELOPMENT STEP - increment a number which shows how many times
//   this function, an "open doc section" function, has been called.
//   Note this can safely be commented out or skipped without changing
//   the given web page or web document's layout:
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    document_section_count($rname, "increment");

} // end function open_document_section_with_margin_block_elements()




function close_document_section_with_margin_block_elements($caller, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to send CSS and HTML mark up which expresses the closing
//   block element tags of a web page section.  
//
//  SUPPORTED OPTIONS:
//
//  METRICS RETURNED:
//
//
//  NOTES ON IMPLEMENTATION:
//
//
//----------------------------------------------------------------------



// diagnostics:

    $rname = "close_document_section_with_margin_block_elements";

//   echo "   </div><!-- document section middle column, tag to close -->
    echo "   </div>

";

    $option[KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME] = "block element for document section margin right";

    block_element_for_document_section_margin($rname, $options);

    echo "</div>
<!-- document section tag to close -->


";

}





function document_section_for_vertical_spacing($caller, $options)
{
//----------------------------------------------------------------------
//  PURPOSE:  to generate an HTML5 block element which expresses,
//   causes browser to render vertical space in the parent block
//   element or web page document.
//----------------------------------------------------------------------


    $border_style = "1px dotted white";  // arbitrary visible border style for debugging

    $mark_for_block_element = "&nbsp;";

    $horizontal_break_width = "100";     // default width of one hundred percent for horizontal breaks,

    $vertical_spacing_in_pixels = 10;    // arbitrary non-zero value for visibility when options not set,

// diagnostics:

    $rname = "document_section_for_vertical_spacing";


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - handle supported options
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_BORDER_STYLE, $options) )
    {
        $border_style = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_BORDER_STYLE];
    }

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_VERTICAL_HEIGHT_IN_PX, $options) )
    {
        $vertical_spacing_in_pixels = $options[KEY_NAME__DOC_LAYOUT__BLOCK_ELEMENT_VERTICAL_HEIGHT_IN_PX];
    }

// Note:  support here for HTML horizontal rule element

    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__SEND_HORIZONTAL_BREAK_OF_WIDTH, $options) )
    {
        $horizontal_break_width = $options[KEY_NAME__DOC_LAYOUT__SEND_HORIZONTAL_BREAK_OF_WIDTH];
        $mark_for_block_element = "<hr style=\"width:${horizontal_break_width}%\">";
    }


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - STEP - send HTML5 mark-up to web browser
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    echo "
   <div style=\"float:left; width:100%; min-height:${vertical_spacing_in_pixels}px; border:${border_style}\"><!-- block element for vertical spacing -->
      ${mark_for_block_element}
   </div>
";


} // end function document_section_for_vertical_spacing()




function layout_small_list_of_text_items($caller, $items, $options)
{
//----------------------------------------------------------------------
//
//  PURPOSE:  to create HTML mark-up for small lists of text items,
//    such as hyperlinks in their own HTML block element (div element).
//   with support for horizontal and vertical type layout, and
//   justification left, center and right . . .
//
//
//  EXPECTS:
//
//     *  list of items to show (a PHP array, an ordered map),
//     *  a flag to indicate whether caller needs items shown as hyperlinks,
//     *  an optional list of URL or hyperlink text strings,
//     *  a layout style:  horizontal or vertical
//     *  a justification style:  left, center, right
//     *  a vertical justification style:  top, center, bottom
//
//----------------------------------------------------------------------


    $rname = "layout_small_list_of_text_items";




    $count_of_items = count($array_of_urls);

    if ( $count_of_items > 0 )
    {
        foreach ( $array_of_urls as $key => $url )
        {
// 2018-02-19 NOTE:  link text not yet accounted for correctly here!
//            $link_text = KEY_VALUE__DIRECTORY_NAVIGATION__VIEW_FILES_IN_CWD;
            $link_text = preg_replace('/-/', ' ', $array_of_view_modes[$key]);
            $link = "<a href=\"$url\">" . $link_text . "</a>";

//            echo "$line_to_browser<br />\n";
            if ( $key == 0 )
            {
                $line_to_browser = $link;
            }
            elseif ( $key < ($count_of_items - 0))
            {
                $line_to_browser = $line_to_browser . "&nbsp; &nbsp; : &nbsp; &nbsp;" . $link;
            }
            else
            {
                $line_to_browser = $line_to_browser . $link;
            }
        }

        echo "$line_to_browser<br />\n";
    }

} // end function layout_small_list_of_text_items()




?>
