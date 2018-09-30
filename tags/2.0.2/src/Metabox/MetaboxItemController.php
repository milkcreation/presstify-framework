<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxItemInterface;
use tiFy\Contracts\Metabox\MetaboxDisplayInterface;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;
use tiFy\Wp\WpScreen;

class MetaboxItemController extends AbstractParametersBag implements MetaboxItemInterface
{
    /**
     * Compteur d'indices de qualification.
     * @var int
     */
    protected static $_index = 0;

    /**
     * Indicateur d'activation.
     * @var boolean
     */
    protected $active = false;

    /**
     * Traitement des arguments de configuration
     *
     * @param array $attrs {
     *      Attributs de configuration
     *
     *      @var string $name Nom de qualification. optionnel, généré automatiquement.
     *      @var string|callable $title Titre du greffon.
     *      @var string|callable|TabContentControllerInterface $content Fonction ou méthode ou classe de rappel d'affichage du contenu de la section.
     *      @var mixed $args Liste des variables passées en argument dans les fonction d'affichage du titre, du contenu et dans l'objet.
     *      @var string $parent Identifiant de la section parente.
     *      @var string|callable@todo $cap Habilitation d'accès.
     *      @var bool|callable@todo $show Affichage/Masquage.
     *      @var int $position Ordre d'affichage du greffon.
     * }
     *
     * @return array
     */
    protected $attributes = [
        'name'     => '',
        'title'    => '',
        'content'  => '',
        'context'  => 'tab',
        'priority' => 'default',
        'position' => 0,
        'args'     => [],
        'cap'      => 'manage_options',
        'parent'   => '',
        'show'     => true
    ];

    /**
     * Indice de qualification.
     * @var int
     */
    protected $index = 0;

    /**
     * Instance de l'écran d'affichage associé.
     * @var WpScreenInterface
     */
    protected $screen;

    /**
     * CONSTRUCTEUR.
     *
     * @param null|string|\WP_Screen|WpScreenInterface $screen Qualification de la page d'affichage.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($screen = null, $attrs = [])
    {
        $this->index = self::$_index++;

        if ($screen instanceof WpScreenInterface) :
            $this->screen = $screen;
        else :
            add_action(
                'admin_init',
                function () use ($screen){
                    $this->screen = WpScreen::get($screen);

                    $content = $this->getContent();

                    if (is_string($content) && class_exists($content)) :
                        $resolved = new $content($this, $this->getArgs());

                        if ($resolved instanceof MetaboxDisplayInterface) :
                            $this->set('content', $resolved);
                        endif;
                    endif;
                },
                9999999
            );
        endif;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name' => md5("metabox-" . uniqid() . "-{$this->getIndex()}")
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getArgs()
    {
        return $this->get('args', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->get('content', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->get('context', 'advanced');
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader()
    {
        if ($this->getContent() instanceof MetaboxDisplayInterface) :
            return call_user_func_array([$this->getContent(), 'header'], func_get_args());
        else :
            return $this->getTitle();
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get('name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->get('parent', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->get('position', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title', '');
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function load(WpScreenInterface $current_screen)
    {
        if ($this->getScreen() && ($current_screen->getHookname() === $this->getScreen()->getHookname())) :
            $this->active = true;
        endif;
    }
}