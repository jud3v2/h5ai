<?php

if(!function_exists('create_file')) {
    function create_file(string $file_name, string $content): void
    {
        $file = fopen($file_name, 'w');
        fwrite($file, $content);
        fclose($file);
    }
}

if(!function_exists('create_dir')) {
    function create_dir(string $dir_name): void
    {
        mkdir($dir_name);
    }
}

if(!function_exists('create_file_in_dir')) {
    function create_file_in_dir(string $dir_name, string $file_name, string $content): void
    {
        create_dir($dir_name);
        create_file($dir_name . '/' . $file_name, $content);
    }
}