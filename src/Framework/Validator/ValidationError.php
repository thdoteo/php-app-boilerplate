<?php

namespace Framework\Validator;

class ValidationError
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var array
     */
    private $params;

    private $messages = [
        'required' => 'The field %s is required.',
        'empty' => 'The field %s cannot be empty.',
        'slug' => 'The field %s must be a slug.',
        'minLength' => 'The field %s must be greater than %d.',
        'maxLength' => 'The field %s must be less than %d.',
        'betweenLength' => 'The field %s must be between %d and %d.',
        'datetime' => 'The field %s must be a valid date (%s).'
    ];

    /**
     * ValidationError constructor.
     * @param string $key
     * @param string $rule
     * @param array $params
     */
    public function __construct(string $key, string $rule, array $params = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->params = $params;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->params);
        return (string)call_user_func_array('sprintf', $params);
    }
}
