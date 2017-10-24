<?php
/**
 * Creates views from files.
 *
 * @param string $location  location of a template
 * @param array  $data      array of variables to extract
 * @return void
 */
function view($location, $data = [])
{
    ob_start();
    extract($data);
    include __DIR__ . '/../views/' . $location;
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
}

/**
 * Returns the current configuration values.
 *
 * @return array
 */
function config($file = 'config.php')
{
    static $config;
    if (!$config) {
        $config = include __DIR__ . '/../' . $file;
    }
    return $config;
}
