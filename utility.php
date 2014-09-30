<?php

function genFilelist($root, $ext = '.php')
{
    if (!is_dir($root)) {
        return array($root);
    }

    $flist = array();
    $stack = array($root);
    while ($stack) {
        $path = array_shift($stack);
        @$items = scandir($path);
        if (!is_array($items)) {
            continue;
        }

        foreach ($items as $item) {
            if ($item[0] == '.') {
                continue;
            }

            $item = $path.'/'.$item;
            if (is_dir($item)) {
                $stack[] = $item;
            } elseif (substr($item, -4) == $ext) {
                $flist[] = $item;
            }
        }
    }

    return $flist;
}
