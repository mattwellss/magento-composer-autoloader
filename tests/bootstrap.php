<?php
namespace testfuncs;

function create_structure()
{
    if (!defined('BP')) {
        define('BP', __DIR__);
    }

    // Set recursive = true for the first app/code pool
    mkdir(BP . '/app/code/local', 0777, true);
    mkdir(BP . '/app/code/community');
    mkdir(BP . '/app/code/core');
    mkdir(BP . '/lib');
    mkdir(BP . '/includes');
}

function destroy_structure()
{
    rmdir(BP . '/app/code/local');
    rmdir(BP . '/app/code/community');
    rmdir(BP . '/app/code/core');
    rmdir(BP . '/app/code');
    rmdir(BP . '/app');
    rmdir(BP . '/lib');
    unlink(BP . '/includes/optimized_map.php');
    rmdir(BP . '/includes');
}
