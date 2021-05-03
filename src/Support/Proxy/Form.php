<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Closure;
use Pollen\Form\AddonDriverInterface;
use Pollen\Form\ButtonDriverInterface;
use Pollen\Form\FormBuilderInterface;
use Pollen\Form\FormFieldDriverInterface;
use Pollen\Form\FormInterface;
use Pollen\Form\FormManagerInterface;

/**
 * @method static FormInterface[]|array all()
 * @method static FormBuilderInterface buildForm(string|array|FormInterface $definition)
 * @method static FormInterface get(string $alias)
 * @method static AddonDriverInterface getAddonDriver(string $alias)
 * @method static ButtonDriverInterface getButtonDriver(string $alias)
 * @method static FormInterface|null getCurrentForm()
 * @method static FormFieldDriverInterface getFormFieldDriver(string $alias)
 * @method static int getFormIndex(FormInterface $form)
 * @method static FormManagerInterface registerAddonDriver(string $alias, string|array|AddonDriverInterface $addonDriverDefinition, ?Closure $registerCallback = null)
 * @method static FormManagerInterface registerButtonDriver(string $alias, string|array|ButtonDriverInterface $buttonDriverDefinition, ?Closure $registerCallback = null)
 * @method static FormManagerInterface registerFormFieldDriver(string $alias, string|array|FormFieldDriverInterface $fieldDriverDefinition, ?Closure $registerCallback = null)
 * @method static FormManagerInterface registerForm(string $alias, string|array|FormInterface $formDefinition):
 * @method static FormManagerInterface resetCurrentForm()
 * @method static FormManagerInterface setCurrentForm(FormInterface $form)
 */
class Form extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return FormManagerInterface
     */
    public static function getInstance(): FormManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return FormManagerInterface::class;
    }
}