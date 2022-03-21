<?php

namespace ojpro\phpmvc\Form;

class TextArea extends BaseField
{
    public function renderInput()
    {
        return sprintf(
            "<textarea name='%s' class='form-control %s'>%s</textarea>",
            $this->fieldType,
            $this->attribute,
            $this->model->hasError($this->attribute) ? 'is-invalid': '',
            $this->model->{$this->attribute},
        );
    }
}