<?php

namespace ojpro\phpmvc\Form;

class Input extends BaseField
{
    public function renderInput()
    {
        return sprintf(
            "<input type='%s' name='%s' class='form-control %s' value='%s'>",
            $this->fieldType,
            $this->attribute,
            $this->model->hasError($this->attribute) ? 'is-invalid': '',
            $this->model->{$this->attribute},
        );
    }
}