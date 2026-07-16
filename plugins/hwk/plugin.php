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
    addCSS ('css/hwk_style.css');
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
