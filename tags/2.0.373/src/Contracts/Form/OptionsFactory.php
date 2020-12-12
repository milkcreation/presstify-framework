<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface OptionsFactory
{
    /**
     * Chargement.
     *
     * @return OptionsFactory
     */
    public function boot(): OptionsFactory;
}