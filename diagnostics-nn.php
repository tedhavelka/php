<?php



function show_diag($caller, $message, $options)
{
// 2017-09-25 MON - added by Ted:

    echo "$caller: &nbsp;$message<br />\n";

}




function warn($caller, $message)
{
    echo "<font color=\"maroon\">\n";
    echo "$caller: &nbsp;$message<br />\n";
    echo "</font>\n";
}




function nn_show_array($caller, $array_reference, $options)
{

    $rname = "nn_show_array";

    $count_of_elements_in_array = count($array_reference);

    if ( $count_of_elements_in_array > 0 )
    {
        foreach ($array_reference as $key => $item)
        {
            echo "$key => '$item'<br />\n";
        }

    }

}





?>
