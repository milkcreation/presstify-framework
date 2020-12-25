<?php declare(strict_types=1);

namespace tiFy\Form\Concerns;

use tiFy\Contracts\Form\AddonDriver as AddonDriverContract;
use tiFy\Contracts\Form\AddonsFactory as AddonsFactoryContract;
use tiFy\Contracts\Form\ButtonDriver as ButtonDriverContract;
use tiFy\Contracts\Form\ButtonsFactory as ButtonsFactoryContract;
use tiFy\Contracts\Form\EventsFactory as EventsFactoryContract;
use tiFy\Contracts\Form\FieldDriver as FieldDriverContract;
use tiFy\Contracts\Form\FieldsFactory as FieldsFactoryContract;
use tiFy\Contracts\Form\FieldGroupDriver as FieldGroupDriverContract;
use tiFy\Contracts\Form\FieldGroupsFactory as FieldGroupsFactoryContract;
use tiFy\Contracts\Form\HandleFactory as HandleFactoryContract;
use tiFy\Contracts\Form\OptionsFactory as OptionsFactoryContract;
use tiFy\Contracts\Form\SessionFactory as SessionFactoryContract;
use tiFy\Contracts\Form\ValidateFactory as ValidateFactoryContract;

trait FactoryBagTrait
{
    /**
     * Instance du gestionnaire d'addons.
     * @var AddonsFactoryContract|null
     */
    private $addons;

    /**
     * Instance du gestionnaire de boutons.
     * @var ButtonsFactoryContract|null
     */
    private $buttons;

    /**
     * Instance du gestionnaire d'évenenements.
     * @var EventsFactoryContract|null
     */
    private $events;

    /**
     * Instance du gestionnaire de groupes de champs.
     * @var FieldsFactoryContract|null
     */
    private $fields;

    /**
     * Instance du gestionnaire de groupes de champs.
     * @var FieldGroupsFactoryContract|null
     */
    private $groups;

    /**
     * Instance du gestionnaire de traitement de la requête.
     * @var HandleFactoryContract|null
     */
    private $handle;

    /**
     * Instance du gestionnaire des options.
     * @var OptionsFactoryContract|null
     */
    private $options;

    /**
     * Instance du gestionnaire de session.
     * @var SessionFactoryContract|null
     */
    private $session;

    /**
     * Instance du gestionnaire de validation.
     * @var ValidateFactoryContract|null
     */
    private $validate;

    /**
     * Récupération d'un pilote d'addon selon son alias.
     *
     * @param string $alias
     *
     * @return AddonDriverContract|null
     */
    public function addon(string $alias): ?AddonDriverContract
    {
        return $this->addons()->get($alias);
    }

    /**
     * Récupération du gestionnaire d'addons.
     *
     * @return AddonsFactoryContract|AddonDriverContract[]
     */
    public function addons(): AddonsFactoryContract
    {
        return $this->addons;
    }

    /**
     * Récupération d'un pilote d'addon selon son alias.
     *
     * @param string $alias
     *
     * @return ButtonDriverContract|null
     */
    public function button(string $alias): ?ButtonDriverContract
    {
        return $this->buttons()->get($alias);
    }

    /**
     * Récupération du gestionnaire de boutons.
     *
     * @return ButtonsFactoryContract|ButtonDriverContract[]
     */
    public function buttons(): ButtonsFactoryContract
    {
        return $this->buttons;
    }

    /**
     * Déclenchement d'un événement.
     *
     * @param string $alias Nom de qualification.
     * @param array $args Liste des arguments passé à l'événement
     *
     * @return void
     */
    public function event(string $alias, array $args = []): void
    {
        $this->events()->trigger($alias, $args);
    }

    /**
     * Récupération du gestionnaire d'événenements.
     *
     * @return EventsFactoryContract
     */
    public function events(): EventsFactoryContract
    {
        return $this->events;
    }

    /**
     * Récupération d'un champs selon son alias.
     *
     * @param string $slug
     *
     * @return FieldDriverContract
     */
    public function field(string $slug): ?FieldDriverContract
    {
        return $this->fields()->get($slug);
    }

    /**
     * Récupération du gestionnaire de champs.
     *
     * @return FieldsFactoryContract|FieldDriverContract[]
     */
    public function fields(): FieldsFactoryContract
    {
        return $this->fields;
    }

    /**
     * Récupération du groupe selon son alias.
     *
     * @param string $alias
     *
     * @return FieldGroupDriverContract
     */
    public function group(string $alias): ?FieldGroupDriverContract
    {
        return $this->groups()->get($alias);
    }

    /**
     * Récupération du gestionnaire de groupes de champs.
     *
     * @return FieldGroupsFactoryContract|FieldGroupDriverContract[]
     */
    public function groups(): FieldGroupsFactoryContract
    {
        return $this->groups;
    }

    /**
     * Récupération du gestionnaire de traitment de la requête de soumission du formualire.
     *
     * @return HandleFactoryContract
     */
    public function handle(): HandleFactoryContract
    {
        return $this->handle;
    }

    /**
     * Récupération d'option.
     *
     * @param string $key Indice de l'option
     * @param mixed|null $default Valeur de retoru par défaut
     *
     * @return mixed
     */
    public function option(string $key, $default = null)
    {
        return $this->options->params($key, $default);
    }

    /**
     * Récupération du gestionnaire des options.
     *
     * @return OptionsFactoryContract
     */
    public function options(): OptionsFactoryContract
    {
        return $this->options;
    }

    /**
     * Récupération du gestionnaire de session.
     *
     * @return SessionFactoryContract
     */
    public function session(): SessionFactoryContract
    {
        return $this->session;
    }

    /**
     * Récupération du gestionnaire de validation de la requête de soumission du formulaire.
     *
     * @return ValidateFactoryContract
     */
    public function validate(): ValidateFactoryContract
    {
        return $this->validate;
    }

    /**
     * Définition du gestionnaire d'addons.
     *
     * @param AddonsFactoryContract $addons
     *
     * @return static
     */
    public function setAddonsFactory(AddonsFactoryContract $addons): self
    {
        $this->addons = $addons;

        return $this;
    }

    /**
     * Définition du gestionnaire de boutons.
     *
     * @param ButtonsFactoryContract $buttons
     *
     * @return static
     */
    public function setButtonsFactory(ButtonsFactoryContract $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * Définition du gestionnaire d'événements.
     *
     * @param EventsFactoryContract $events
     *
     * @return static
     */
    public function setEventsFactory(EventsFactoryContract $events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Définition du gestionnaire de champs.
     *
     * @param FieldsFactoryContract $fields
     *
     * @return static
     */
    public function setFieldsFactory(FieldsFactoryContract $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Définition du gestionnaire de groupe de champs.
     *
     * @param FieldGroupsFactoryContract $groups
     *
     * @return static
     */
    public function setGroupsFactory(FieldGroupsFactoryContract $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Définition du gestionnaire de traitement de la requête de soumission du formulaire.
     *
     * @param HandleFactoryContract $handle
     *
     * @return static
     */
    public function setHandleFactory(HandleFactoryContract $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Définition du gestionnaire des options de formulaire.
     *
     * @param OptionsFactoryContract $options
     *
     * @return static
     */
    public function setOptionsFactory(OptionsFactoryContract $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Définition du gestionnaire de session.
     *
     * @param SessionFactoryContract $session
     *
     * @return static
     */
    public function setSessionFactory(SessionFactoryContract $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Définition du gestionnaire de validation de la requêter de soumission du formulaire.
     *
     * @param ValidateFactoryContract $validate
     *
     * @return static
     */
    public function setValidateFactory(ValidateFactoryContract $validate): self
    {
        $this->validate = $validate;

        return $this;
    }
}