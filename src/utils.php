<?php

namespace App;

/**
 * @param $string
 * @param $startString
 *
 * @return bool
 */
function stringStartsWith($string, $startString): bool
{
    return (strpos($string, $startString) === 0);
}