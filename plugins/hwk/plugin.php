<?php

function plugin_hwk_info ()
{
    return array
    (
        'name' => 'hwk',
        'longname' => "Hawk's look and feel",
        'version' => '1.0',
        'home_url' => 'https://github.com/tinihawk'
    );
}

function plugin_hwk_init()
{
    echo <<<CSS
    <style>
        .portlet, td > table.cooltable {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        /* border-radius: 8px; */
        border: 0px !important;
        margin-bottom: 1em;
        }

        /*
        #foldertab li a {
            border-radius: 5px 5px 0px 0px; 
        }
        */

        td.pcleft, td.pcright, .pagebar {
            padding: 0.5em 0.5em 0 0.5em;
        }

        td.pcleft {
            max-width: 75%;
        }
    </style>
    CSS;
}

function plugin_hwk_install()
{
    return True;
}

function plugin_hwk_uninstall()
{
    return True;
}

function plugin_hwk_upgrade()
{
    return True;
}


?>
