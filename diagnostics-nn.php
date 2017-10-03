<?php

//======================================================================
//
//  FILE:  diagnostics-nn.php
//
//======================================================================



// - 2017-10-02 MON - QUESTION:  Permissible in PHP to have like-named functions with differing parameter lists?  ANSWER:  No - TMH


function show_diag_default_formatting($caller, $message)
{
    show_diag($caller, $message, 0);
}




function show_diag($caller, $message, $options)
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

    if ( $options )
    {
        echo "$caller: &nbsp;$message<br />\n";
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
