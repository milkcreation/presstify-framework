<?php

namespace tiFy\PageHook;

use tiFy\App\AppController;
use tiFy\Metabox\Metabox;
use tiFy\Options\Options;
use tiFy\PageHook\Admin\PageHookAdminOptions;

class PageHook extends AppController
{
    /**
     * Liste des éléments déclarés.
     * @var PageHookItemInterface[]
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    public function appBoot()
    {
        if ($config = $this->appConfig()) :
            foreach ($config as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;
        endif;

        add_action(
            'init',
            function () {
                if (!$this->items) :
                    return;
                endif;

                /** @var Metabox $metabox */
                $metabox = app(Metabox::class);
                $metabox->add(
                    'tify_options@options',
                    [
                        'name'      => 'tiFyPageHook-optionsNode',
                        'title'     => __('Pages d\'accroche', 'tify'),
                        'content'   => PageHookAdminOptions::class
                    ]
                );
            }
        );

        do_action('tify_page_hook_register', $this);
    }

    /**
     * Récupération de la listes des classes de rappel des pages d'accroche déclarées.
     *
     * @return PageHookItemInterface[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Récupération de la classe de rappel d'une page d'accroche déclarée.
     *
     * @param string $name Nom de qualification.
     *
     * @return null|PageHookItemInterface
     */
    public function get($name)
    {
        if (isset($this->items[$name])) :
            return $this->items[$name];
        endif;
    }

    /**
     * Vérifie si une page d'affichage correspond à la page d'accroche.
     *
     * @param string $name Nom de qualification.
     * @param null|int|\WP_Post Page d'affichage courante|Identifiant de qualification|Objet post Wordpress à vérifier.
     *
     * @return bool
     */
    public function is($name, $post = null)
    {
        if (!$item = $this->get($name)) :
            return false;
        endif;

        return $item->isCurrent($post);
    }

    /**
     * Récupération de l'identifiant de qualification de la page d'accroche associée.
     *
     * @param string $name Nom de qualification.
     * @param int $default Valeur de retour par défaut.
     *
     * @return int
     */
    public function getId($name, $default = 0)
    {
        if (!$item = $this->get($name)) :
            return $default;
        endif;

        return $item->getId();
    }

    /**
     * Récupération du permalien de la page d'accroche associée.
     *
     * @param string $name Nom de qualification.
     *
     * @return string
     */
    public function getPermalink($name)
    {
        if (!$item = $this->get($name)) :
            return '';
        endif;

        return $item->getPermalink();
    }

    /**
     * Déclaration d'une page d'accroche.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return PageHookItemInterface
     */
    public function register($name, $attrs = [])
    {
        return $this->items[$name] = new PageHookItemController($name, $attrs, $this);
    }
}