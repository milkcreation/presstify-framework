<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Form\FormManager;

/**
 * @method static FormManager all()
 */
class Form extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'form';
    }
}