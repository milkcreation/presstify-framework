<?php declare(strict_types=1);

namespace tiFy\Form;

use LogicException;
use tiFy\Contracts\Form\AddonFactory;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Metabox\MetaboxDriver;

abstract class AddonMetaboxDriver extends MetaboxDriver
{
    /**
     * Instance de l'addon de formulaire associé.
     * @var AddonFactory|null
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
     * @return AddonFactory
     */
    public function addon(): AddonFactory
    {
        if ($this->addon instanceof AddonFactory) {
            return $this->addon;
        }

        throw new LogicException(
            __('Aucune instance d\'addon de formulaire n\'est associé à la boîte de saisie.', 'tify')
        );
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
     * @param AddonFactory $addon
     *
     * @return $this
     */
    public function setAddon(AddonFactory $addon): self
    {
        $this->addon = $addon;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return "{$this->form()->viewer()->getDirectory()}/addon/{$this->addon()->name()}/metabox";
    }
}