<?php

function _e(string $str): string
{
    return htmlentities($str);
}

function _err(?array $model, string $entity, $print = 'is-invalid'): void
{
    if (isset($model['errors'][$entity])) {
        echo $print;
    }
}

function _val(?array $model, string $entity): void
{
    if (isset($model['content'][$entity])) {
        echo 'value="'.$model['content'][$entity].'"';
    }
}
