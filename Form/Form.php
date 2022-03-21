<?php

namespace ojpro\phpmvc\Form;

use ojpro\phpmvc\Model;

class Form
{
    public static function begin($action, $method, $classes)
    {
        echo sprintf("<form action='%s' method='%s' class='%s'>", $action, $method, $classes);
        return new Form();
    }
    public static function end()
    {
        echo "</form>";
    }
    public function input(Model $model, $attribute, $fieldType = 'text')
    {
        return new Input($model, $attribute, $fieldType);
    }
    public function textArea(Model $model, $attribute)
    {
        return new TextArea($model, $attribute);
    }
}