<?php

/**
 * Increments the patch number and writes it to stdout
 */
if (preg_match('/\d+\.\d+\.\d+/', @$argv[1])) {
    list($major, $minor, $patch) = explode('.', $argv[1]);
    $patch++;
    echo "$major.$minor.$patch";
}