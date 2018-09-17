<?php

namespace tiFy\Metabox;

use Illuminate\Support\Arr;
use tiFy\Contracts\Metabox\MetaboxContentInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

abstract class AbstractMetaboxContentController extends AbstractParametersBag implements MetaboxContentInterface
{
    /**
     * Instance de l'écran d'affichage.
     * @var WpScreenInterface
     */
    protected $screen;

    /**
     * Instance du moteur de gabarits d'affichage.
     * @return ViewsInterface
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param WpScreenInterface $screen Ecran d'affichage.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __construct(WpScreenInterface $screen, $args = [])
    {
        $this->screen = $screen;

        parent::__construct($args);

        add_action(
            'current_screen',
            function ($wp_current_screen) {
                if ($wp_current_screen->id === $this->screen->getHookname()) :
                    $this->load($wp_current_screen);
                endif;
            }
        );

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }

    /**
     * Récupération de l'affichage depuis l'instance.
     *
     * @return string
     */
    public function __invoke()
    {
        return call_user_func_array([$this, 'display'], func_get_args());
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
    public function getObjectName()
    {
        return $this->screen->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return $this->screen->getObjectType();
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