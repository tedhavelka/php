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

//    require_once '/opt/nn/lib/php/file-and-directory-routines.php';




//----------------------------------------------------------------------
// - SECTION - PHP file-scoped constants
//----------------------------------------------------------------------




//----------------------------------------------------------------------
// - SECTION - diagnostics and development
//----------------------------------------------------------------------




//----------------------------------------------------------------------
// - SECTION - routines for image layout and presentation
//----------------------------------------------------------------------

function block_element_for_document_section_margin($caller, $options) // 2017-11-07 - NOT YET IMPLEMENTED - TMH
{

    $mark_for_margin = "&nbsp;";

// diagnostics:

    $rname = "block_element_for_document_section_margin";


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


    echo "   <div style=\"float:left; width:15%; border:none\">
      ${mark_for_margin}
   </div>
";

} // end function block_element_for_document_section_margin()




function open_document_section_with_margin_block_elements($caller, $options)
{

// default mark is non-breakable space, not visible but must be
// something for at least some types of CSS block elements to be
// rendered with their specified height and width values:

    $block_element_border = "1px dotted white";

    $block_element_name = "DEFAULT BLOCK ELEMENT NAME";

// diagnostics:

    $rname = "open_document_section_with_margin_block_elements";


    if ( array_key_exists(KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME, $options) )
    {
        $block_element_name = $options[KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME];
    }


//    echo "<div class=\"container\">
//    echo "<div style=\"display:flex; min-height:30px; max-height:70px\">
//    echo "<div style=\"display:flex; min-height:30px\">
//    echo "<div style=\"float:left\">
//    echo "<div style=\"clear:left; border:$block_element_border; background:none\"><!-- document section tag to open -->
    echo "<div style=\"clear:left; border:$block_element_border; background:none\"><!-- document section tag to open -->
";

    $option[KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME] = "block element for document section margin left";

    block_element_for_document_section_margin($rname, $options);

    echo "   <div style=\"float:left; width:70%; border:none\"><!-- document section, middle column -->
";

}




function close_document_section_with_margin_block_elements($caller, $options)
{

    echo "   </div><!-- Closing tag for middle column, main content block element -->


";

    $option[KEY_NAME__DOC_LAYOUT__CONTENT_COLUMN__BLOCK_ELEMENT_NAME] = "block element for document section margin right";

    block_element_for_document_section_margin($rname, $options);

    echo "</div><!-- document section tag to close -->



";

}



?>
