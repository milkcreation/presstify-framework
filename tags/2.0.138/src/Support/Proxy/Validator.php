<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

/**
 * @method static bool exactLength($value, int $length = 0)
 * @method static bool hasSpecialChars($value)
 * @method static bool hasMaj($value)
 * @method static bool isAlpha($value)
 * @method static bool isAlphaNum($value)
 * @method static bool isDate($value, string $format = 'd/m/Y')
 * @method static bool isEmail($value)
 * @method static bool isEmpty($value)
 * @method static bool isEqual($a, $b)
 * @method static bool isInArray($value, array $values)
 * @method static bool isInteger($value)
 * @method static bool isUrl($value)
 * @method static bool maxLength($value, int $max = 0)
 * @method static bool minLength($value, int $min = 0)
 * @method static bool notEmpty($value)
 * @method static bool notInArray($value, array $values)
 * @method static bool validPassword($value, array $args = [])
 * @method static bool regex($value, string $regex)
 */
class Validator extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'validator';
    }
}