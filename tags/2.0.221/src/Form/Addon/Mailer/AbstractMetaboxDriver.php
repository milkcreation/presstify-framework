<?php declare(strict_types=1);

namespace tiFy\Form\Addon\Mailer;

use tiFy\Contracts\{Form\FormFactory, Form\AddonController, Metabox\MetaboxDriver as MetaboxDriverContract};
use tiFy\Form\Factory\ResolverTrait;
use tiFy\Metabox\{MetaboxDriver, MetaboxView};

abstract class AbstractMetaboxDriver extends MetaboxDriver
{
    use ResolverTrait;

    /**
     * Instance de l'addon associé.
     * @var AddonController|null
     */
    protected $addon;

    /**
     * Liste des noms d'enregistement des options.
     * @var array
     */
    protected $optionNames = [];

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $this->optionNames = $this->addon->get('option_names', []);

        return $this;
    }

    /**
     * Définition de l'addon associé.
     *
     * @param AddonController $addon
     *
     * @return $this
     */
    public function setAddon(AddonController $addon): self
    {
        $this->addon = $addon;

        return $this;
    }

    /**
     * Définition du formulaire associé.
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

    /**
     * @inheritDoc
     */
    public function viewer(?string $view = null, array $data = [])
    {
        if (!$this->viewer) {
            $defaultDir = $this->form->viewer()->getDirectory() . '/addon/mailer/admin';
            $fallbackDir = $this->get('viewer.override_dir') ?: $defaultDir;

            $this->viewer = view()
                ->setDirectory($defaultDir)
                ->setOverrideDir($fallbackDir)
                ->setController(MetaboxView::class)
                ->set('metabox', $this);
        }

        if (func_num_args() === 0) {
            return $this->viewer;
        }

        return $this->viewer->make("_override::{$view}", $data);
    }
}