<?php

declare(strict_types=1);

if (!function_exists('dd')) {
    /**
     * Dumps a variable's contents, wrapped in <pre> tags for readability, and halts execution.
     *
     * @param mixed $var The variable to inspect.
     * @return void
     */
    function dd($var)
    {
        echo '<pre style="
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            padding: 15px;
            white-space: pre-wrap;
            word-break: break-all;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
        ">';
        var_dump($var);
        echo '</pre>';
        die();
    }
}
