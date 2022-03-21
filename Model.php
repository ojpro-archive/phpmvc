<?php

namespace ojpro\phpmvc;

use ojpro\phpmvc\Application;

abstract class Model
{
    public array $errors = [];
    protected const RULE_REQUIRED = "required";
    protected const RULE_EMAIL = "email";
    protected const RULE_MIN = "min";
    protected const RULE_MAX = "max";
    protected const RULE_MATCH = "match";
    protected const RULE_UNIQUE = "unique";

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
    public function rules(): array
    {
        return [];
    }
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MAX && !strlen($value) > $rule['max']) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !==  $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ??  $attribute;
                    $tabName = $className::tableName();
                    $query = "SELECT * FROM $tabName WHERE $uniqueAttr LIKE '$value'";
                    $statement = Application::$app->db->pdo->prepare($query);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addError($attribute, self::RULE_UNIQUE, ['field'=>$this->getLabel($attribute)]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    /**
     * Add to errors
     *
     * @return  void
     */
    public function addError($attribute, string $value, $params = [])
    {
        $message = $this->getMessages()[$value] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function addErrorMessage($attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    public function getMessages()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'This {field} is already exists'
        ];
    }

    public function labels():array
    {
        return [];
    }
    public function getLabel($label)
    {
        return $this->labels()[$label] ?? $label;
    }
    /**
     * Get the value of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function hasError(string $attribute)
    {
        return $this->errors[$attribute]  ?? false;
    }
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
    abstract public function attributes():array;
}