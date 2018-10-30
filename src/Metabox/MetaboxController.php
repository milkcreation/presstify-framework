<?php

namespace tiFy\Metabox;

use Illuminate\Support\Arr;
use tiFy\Contracts\Metabox\MetaboxController as MetaboxControllerContract;
use tiFy\Contracts\Metabox\MetaboxFactory;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

abstract class MetaboxController extends AbstractParametersBag implements MetaboxControllerContract
{
    /**
     * Instance de l'élément.
     * @var MetaboxFactory
     */
    protected $item;

    /**
     * Instance du moteur de gabarits d'affichage.
     * @return ViewsInterface
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param MetaboxFactory $item Instance de l'élément.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __construct(MetaboxFactory $item, $args = [])
    {
        $this->item = $item;

        parent::__construct($args);

        add_action(
            'current_screen',
            function ($wp_current_screen) {
                if ($wp_current_screen->id === $this->item->getScreen()->getHookname()) :
                    $this->load($wp_current_screen);
                endif;
            }
        );

        $this->boot();
    }

    /**
     * Récupération de l'affichage depuis l'instance.
     *
     * @return string
     */
    public function __invoke()
    {
        return call_user_func_array([$this, 'content'], func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function content($var1 = null, $var2 = null, $var3 = null)
    {
        return __('Aucun contenu à afficher', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->item->getScreen()->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return $this->item->getScreen()->getObjectType();
    }

    /**
     * {@inheritdoc}
     */
    public function header($var1 = null, $var2 = null, $var3 = null)
    {
        return $this->item->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $cinfo = class_info($this);
            $default_dir = $cinfo->getDirname() . '/views';
            $this->viewer = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(MetaboxView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname())
                )
                ->set('metabox', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}