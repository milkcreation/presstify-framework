<?php declare(strict_types=1);

namespace tiFy\Form\Concerns;

use LogicException;
use tiFy\Contracts\Form\AddonDriver;
use tiFy\Contracts\Form\FormFactory;

trait AddonAwareTrait
{
    /**
     * Instance de l'addon de formulaire associé.
     * @var AddonDriver|null
     */
    protected $addon;

    /**
     * Instance du formulaire associé.
     * @var FormFactory|null
     */
    protected $form;

    /**
     * Récupération de l'instance de l'addon associé.
     *
     * @return AddonDriver
     */
    public function addon(): AddonDriver
    {
        if ($this->addon instanceof AddonDriver) {
            return $this->addon;
        }

        throw new LogicException('Unavailable related addon');
    }

    /**
     * Récupération de l'instance du formulaire associé.
     *
     * @return FormFactory
     */
    public function form(): FormFactory
    {
        return $this->addon()->form();
    }

    /**
     * Définition de l'addon associé.
     *
     * @param AddonDriver $addon
     *
     * @return $this
     */
    public function setAddon(AddonDriver $addon): self
    {
        $this->addon = $addon;

        return $this;
    }
}