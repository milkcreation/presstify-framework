<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Form\AddonDriver;
use tiFy\Contracts\Form\ButtonDriver;
use tiFy\Contracts\Form\FieldDriver;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FormManager;

/**
 * @method static FormFactory[]|array all()
 * @method static FormFactory|null current($formDefinition = null)
 * @method static FormFactory|null get(string $alias)
 * @method static AddonDriver|null getAddonDriver(string $alias)
 * @method static ButtonDriver|null getButtonDriver(string $alias)
 * @method static FieldDriver|null getFieldDriver(string $alias)
 * @method static FormManager register(string $alias, FormFactory|array $formDefinition = [])
 * @method static FormManager registerAddonDriver(string $alias, AddonDriver|array $addonDefinition = [])
 * @method static FormManager registerButtonDriver(string $alias, ButtonDriver|array $buttonDefinition = [])
 * @method static FormManager registerFieldDriver(string $alias, FieldDriver|array $fieldDefinition = [])
 * @method static FormManager setAddonDriver(string $alias, AddonDriver $driver)
 * @method static FormManager setButtonDriver(string $alias, ButtonDriver $driver)
 * @method static FormManager setConfig(array $attrs)
 * @method static FormManager setFieldDriver(string $alias, FieldDriver $driver)
 * @method static FormFactory setForm(string $alias, FormFactory $factory)
 */
class Form extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|FormManager
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return FormManager::class;
    }
}