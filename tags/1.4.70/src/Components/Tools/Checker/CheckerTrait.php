<?php

namespace tiFy\Components\Tools\Checker;

use tiFy\Kernel\Tools;

/**
 * Trait CheckerTrait
 * @package tiFy\Components\Tools\Checker
 *
 * @method bool checkerIsEmpty(mixed $value)
 * @method bool checkerIsInteger(mixed $value)
 * @method bool checkerIsAlpha(mixed $value)
 * @method bool checkerIsAlphaNum(mixed $value)
 * @method bool checkerIsEmail(mixed $value)
 * @method bool checkerIsUrl(mixed $value)
 * @method bool checkerIsDate(mixed $value, string $format = 'd/m/Y')
 * @method bool checkerRegex(mixed $value, string $regex)
 * @method bool checkerMaxLength(mixed $value, int $max = 0)
 * @method bool checkerMinLength(mixed $value, int $min = 0)
 * @method bool checkerExactLength(mixed $value, int $length = 0)
 * @method bool checkerHasSpecialChars(mixed $value)
 * @method bool checkerHasMaj(mixed $value)
 * @method bool checkerIsValidPassword(mixed $value, array $args = [])
 * @method bool checkerIsEqual(mixed $a, mixed $b)
 * @method bool checkerIsDifferent(mixed $a, mixed $b)
 */
trait CheckerTrait
{
    public function __call($name, $arguments)
    {
        if (preg_match('#^checker(.*)#', $name, $matches)) :
            $method = lcfirst($matches[1]);
            if (method_exists(Tools::Checker(), $method)) :
                return call_user_func_array([Tools::Checker(), $method], $arguments);
            endif;
        endif;
    }
}