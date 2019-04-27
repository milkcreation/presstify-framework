<?php declare(strict_types=1);

namespace tiFy\Validation;

use tiFy\Contracts\Validation\Validator as ValidatorContract;

class Validator implements ValidatorContract
{
    /**
     * @inheritdoc
     */
    public function exactLength($value, int $length = 0): bool
    {
        return strlen($value) === $length;
    }

    /**
     * @inheritdoc
     */
    public function hasSpecialChars($value): bool
    {
        return !!preg_match('/^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[\W_]).*$/', $value);
    }

    /**
     * @inheritdoc
     */
    public function hasMaj($value): bool
    {
        return !!preg_match('/^.*(?=.*[A-Z]).*$/', $value);
    }

    /**
     * @inheritdoc
     */
    public function isAlpha($value): bool
    {
        return !!preg_match('/^[[:alpha:]]*$/', $value);
    }

    /**
     * @inheritdoc
     */
    public function isAlphaNum($value): bool
    {
        return !!preg_match('/^[[:alnum:]]*$/', $value);
    }

    /**
     * @inheritdoc
     */
    public function isDate($value, string $format = 'd/m/Y'): bool
    {
        switch ($format) {
            default :
                $regex = '^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$';
                break;
        }
        return !!preg_match('/' . $regex . '/', $value);
    }

    /**
     * @inheritdoc
     */
    public function isDifferent($a, $b): bool
    {
        return !$this->isEqual($a, $b);
    }

    /**
     * @inheritdoc
     */
    public function isEmail($value): bool
    {
        return !!preg_match('/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/', $value);
    }

    /**
     * @inheritdoc
     */
    public function isEmpty($value): bool
    {
        return empty($value);
    }

    /**
     * @inheritdoc
     */
    public function isEqual($a, $b): bool
    {
        return $a === $b;
    }

    /**
     * @inheritdoc
     */
    public function isInteger($value): bool
    {
        return !!preg_match('/^[[:digit:]]*$/', $value);
    }

    /**
     * @inheritdoc
     */
    public function isUrl($value): bool
    {
        return !!preg_match('@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $value);
    }

    /**
     * @inheritdoc
     */
    public function maxLength($value, int $max = 0): bool
    {
        return strlen($value) <= $max;
    }

    /**
     * @inheritdoc
     */
    public function minLength($value, int $min = 0): bool
    {
        return strlen($value) >= $min;
    }

    /**
     * @inheritdoc
     */
    public function notEmpty($value): bool
    {
        return !$this->isEmpty($value);
    }

    /**
     * @inheritdoc
     */
    public function validPassword($value, array $args = []): bool
    {
        $args = array_merge([
            'digit'   => 1,
            'letter'  => 1,
            'maj'     => 1,
            'max'     => 16,
            'min'     => 8,
            'special' => 0
        ], $args);

        /**
         * @var int $digit
         * @var int $letter
         * @var int $maj
         * @var int $max
         * @var int $min
         * @var int $special
         */
        extract($args);

        if ($min && (strlen($value) < (int)$min)) {
            return false;
        } elseif ($max && (strlen($value) > (int)$max)) {
            return false;
        }

        $regex = "";
        if ($digit) {
            $regex .= "(?=(?:.*\d){" . (int)$digit . ",})";
        }
        if ($letter) {
            $regex .= "(?=(?:.*[a-z]){" . (int)$letter . ",})";
        }
        if ($maj) {
            $regex .= "(?=(?:.*[A-Z]){" . (int)$maj . ",})";
        }
        if ($special) {
            $regex .= "(?=(?:.*[!@#$%^&*()\[\]\-_=+{};:,<.>]){" . (int)$special . ",})";
        }
        return !!preg_match('/' . $regex . '/', $value);
    }

    /**
     * @inheritdoc
     */
    public function regex($value, string $regex): bool
    {
        return !!preg_match('#' . $regex . '#', $value);
    }
}