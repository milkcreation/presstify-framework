<?php declare(strict_types=1);

namespace tiFy\Form\Concerns;

use tiFy\Contracts\Form\FormFactory;

trait FormAwareTrait
{
    /**
     * Instance du formulaire associé.
     * @var FormFactory|null
     */
    protected $form;

    /**
     * Récupération de l'instance du formulaire associé.
     *
     * @return FormFactory
     */
    public function form(): FormFactory
    {
        return $this->form;
    }

    /**
     * Définition de l'addon associé.
     *
     * @param FormFactory $form
     *
     * @return $this
     */
    public function setForm(FormFactory $form): self
    {
        $this->form = $form;

        return $this;
    }
}