<?php

if ((isset($_GET['profile']) || isset($_COOKIE['xhprof'])) && extension_loaded('xhprof')) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    register_shutdown_function(function () {
        $xhprof_data = xhprof_disable();

        $run_id = uniqid();
        $output_dir = '/var/log/xhprof'; // make sure this exists and is writable
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0777, true);
        }
        file_put_contents("$output_dir/$run_id.xhprof.xhprof", serialize($xhprof_data));
    });
}
