<?php

namespace ojpro\phpmvc\Form;

use ojpro\phpmvc\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;
    public ?string $fieldType;
    public function __construct(Model $model, string $attribute, string $fieldType = null)
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->fieldType = $fieldType;
    }
    public function __toString()
    {
        return sprintf(
            "
        <div class='mb-3'>
    <label>%s</label>
            %s
        <div class='invalid-feedback'>
        <p>
            %s
        </p>
        </div>
    </div>
",
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute) ?? ''
        );
    }

    abstract public function renderInput();
}