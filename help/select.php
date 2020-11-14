<?php

function buildSelect($dataRow)
{
    $html = "<select name='test' id='test'> ";
    foreach ($dataRow as $value) {
        $html .= "<option value='$value'>$value</option> ";
    }
    $html .= "</select >";
    return $html;
}

