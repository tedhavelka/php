<?php

//======================================================================
//
//  FILE:  diagnostics-nn.php
//
//
//  REFERENCES:
//
//    * REF *  http://php.net/manual/en/language.operators.bitwise.php
//
//======================================================================







// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  - SECTION - PHP defined constants
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    define("DIAGNOSTICS_OFF", 0);
    define("DIAGNOSTICS_ON", 1);
    define("DIAGNOSTICS__ROUTINE_NAME_AND_MESSAGE", 2);
    define("DIAGNOSTICS__MESSAGE_ONLY", 4);
    define("DIAGNOSTICS__WARNING_STYLE", 8);
    define("DIAGNOSTICS__ERROR_STYLE", 16);

    define("DEFAULT_DIAGNOSTIC_MESSAGE", (DIAGNOSTICS_ON | DIAGNOSTICS__ROUTINE_NAME_AND_MESSAGE));
    define("MESSAGE_ONLY", (DIAGNOSTICS_ON | DIAGNOSTICS__MESSAGE_ONLY));





// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//  - SECTION - function definitions
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// - 2017-10-02 MON - QUESTION:  Permissible in PHP to have like-named functions with differing parameter lists?  ANSWER:  No - TMH

function show_diag_default_formatting($caller, $message)
{
    show_diag($caller, $message, 0);
}




function show_diag($caller, $message, $bit_wise_message_option)
{
//----------------------------------------------------------------------
//
//  2017-09-25 MON - added by Ted . . .
//
//  EXPECTS:
//     *  calling code identifying string,
//     *  message to send to this or given PHP script's standard out fd,
//     *  integer type option to specify caller's desired message format,
//
//  RETURNS:
//     *  nothing
//
//  NOTES ON IMPLEMENTATION:  In this case, unlike some other local
//    PHP routines, the \$options parameter passed to this show_diag()
//    routine are treated as integer values and expected to be
//    integers.  See file `defines-nn.php' or similar for names of
//    PHP constants which this routine treats as different kinds of
//    requested output formats.  - TMH
//
//----------------------------------------------------------------------


// Note:  parameter options must be non-zero for calling code's message
//  to show:

    if ( ( $bit_wise_message_option == DEFAULT_DIAGNOSTIC_MESSAGE ) || ( $bit_wise_message_option == DIAGNOSTICS_ON ) )
    {
        echo "$caller ($bit_wise_message_option): &nbsp;$message<br />\n";
    }
    else if ( $bit_wise_message_option == MESSAGE_ONLY )
    {
        echo $message;
    }
    else
    {
        // do nothing
    }

}




function warn($caller, $message)
{
    echo "<font color=\"maroon\">\n";
    echo "$caller: &nbsp;$message<br />\n";
    echo "</font>\n";
}




function nn_show_array($caller, $array_reference, $options)
{
//----------------------------------------------------------------------
//
//  NOTE:  $options not yet implemented in this local PHP diagnostics
//    routine . . .   - TMH
//
//----------------------------------------------------------------------


    $rname = "nn_show_array";


    $count_of_elements_in_array = count($array_reference);

    if ( $count_of_elements_in_array > 0 )
    {
        foreach ($array_reference as $key => $item)
        {
            echo "$key => '$item'<br />\n";
        }

    }

} // end function nn_show_array()





?>
