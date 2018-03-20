<?php


function &url_to_calling_script($caller, $options)
{

    $url = "";

    if ( array_key_exists(SCRIPT_NAME, $_SERVER) )
    {
        $url = $_SERVER[SCRIPT_NAME];
    }
    else
    {
    }

    return $url;

}



?>
