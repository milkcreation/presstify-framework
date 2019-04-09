<?php

namespace tiFy\Kernel\Validation;

use tiFy\Contracts\Kernel\Validator as ValidatorContract;

class Validator implements ValidatorContract
{
    /**
     * {@inheritdoc}
     */
    public function exactLength($value, $length = 0)
    {
        return (strlen($value) === $length);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSpecialChars($value)
    {
        return preg_match('/^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[\W_]).*$/', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function hasMaj($value)
    {
        return preg_match('/^.*(?=.*[A-Z]).*$/', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isAlpha($value)
    {
        return preg_match('/^[[:alpha:]]*$/', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isAlphaNum($value)
    {
        return preg_match(preg_match('/^[[:alnum:]]*$/', $value));
    }

    /**
     * {@inheritdoc}
     */
    public function isDate($value, $format = 'd/m/Y')
    {
        switch ($format) :
            default :
                $regex = '^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$';
                break;
        endswitch;

        if (!preg_match('/' . $regex . '/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isDifferent($a, $b)
    {
        return !$this->isEqual($a, $b);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmail($value)
    {
        return preg_match('/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty($value)
    {
        return empty($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isEqual($a, $b)
    {
        return ($a === $b);
    }

    /**
     * {@inheritdoc}
     */
    public function isInteger($value)
    {
        return preg_match('/^[[:digit:]]*$/', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isUrl($value)
    {
        return preg_match('@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function maxLength($value, $max = 0)
    {
        return (strlen($value) <= $max);
    }

    /**
     * {@inheritdoc}
     */
    public function minLength($value, $min = 0)
    {
        return (strlen($value) >= $min);
    }

    /**
     * {@inheritdoc}
     */
    public function notEmpty($value)
    {
        return !$this->isEmpty($value);
    }

    /**
     * {@inheritdoc}
     */
    public function validPassword($value, $args = [])
    {
        $args = array_merge([
            'digit'        => 1,
            'letter'       => 1,
            'maj'          => 1,
            'special_char' => 0,
            'min'          => 8,
            'max'          => 16,
        ], $args);

        extract($args);

        if ($min && (strlen($value) < (int)$min)) :
            return false;
        endif;
        if ($max && (strlen($value) > (int)$max)) :
            return false;
        endif;

        $regex = "";
        if ($digit) :
            $regex .= "(?=(?:.*\d){" . (int)$digit . ",})";
        endif;
        if ($letter) :
            $regex .= "(?=(?:.*[a-z]){" . (int)$letter . ",})";
        endif;
        if ($maj) :
            $regex .= "(?=(?:.*[A-Z]){" . (int)$maj . ",})";
        endif;
        if ($special_char) :
            $regex .= "(?=(?:.*[!@#$%^&*()\[\]\-_=+{};:,<.>]){" . (int)$special_char . ",})";
        endif;

        if (preg_match('/' . $regex . '/', $value)) :
            return true;
        endif;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function regex($value, $regex)
    {
        return preg_match('#' . $regex . '#', $value);
    }
}