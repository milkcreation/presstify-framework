<?php

namespace tiFy\TabMetabox;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use tiFy\Apps\AppController;
use tiFy\TabMetabox\Controller\DisplayController;
use tiFy\TabMetabox\Controller\TabContentControllerInterface;
use tiFy\TabMetabox\Controller\TabBoxItemController;
use tiFy\TabMetabox\Controller\TabNodeItemController;

final class TabMetabox extends AppController
{
    /**
     * Liste des boites à onglets déclarées.
     * @var TabBoxItemController[]
     */
    protected $boxes = [];

    /**
     * Liste des greffons déclarés
     * @var TabNodeItemController
     */
    protected $nodes = [];

    /**
     * Liste des identifiants d'accroche déclarés.
     * @var string[]
     */
    protected $hooknames = [];

    /**
     * Liste des alias des identifiants d'accroche.
     * @var array
     */
    protected $hooknameAlias = [];

    /**
     * Liste des options porté par l'objet WP_Screen.
     * @var array
     */
    protected $screenOptions = [];

    /**
     * Identifiant d'accroche de la page d'affichage courante.
     * @var string
     */
    protected $currentHookname = '';

    /**
     * Classe de rappel du controleur d'affichage.
     * @var DisplayController
     */
    protected $display;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appServiceAdd(TabBoxItemController::class);
        $this->appServiceAdd(TabNodeItemController::class);

        $this->appAddAction('wp_loaded', null, 0);
        $this->appAddAction('current_screen');
    }

    /**
     * A l'issue du chargement complet de Wordpress.
     *
     * @return void
     */
    public function wp_loaded()
    {
        foreach ($this->appConfig() as $type => $items) :
            switch ($type) :
                case 'box' :
                    foreach ($items as $hookname => $attrs) :
                        $this->registerBox($hookname, $attrs);
                    endforeach;
                    break;
                case 'nodes' :
                    foreach ($items as $hookname => $nodes) :
                        foreach($nodes as $attrs) :
                            $this->registerNode($hookname, $attrs);
                        endforeach;
                    endforeach;
                    break;
            endswitch;
        endforeach;

        do_action('tify_tabmetabox_register', $this);
    }

    /**
     * Chargement de l'écran courant de l'interface d'administration de Wordpress.
     *
     * @param \WP_Screen $current_screen Classe de rappel du controleur d'écran courant.
     *
     * @return void
     */
    public function current_screen($current_screen)
    {
        // Bypass
        if (!$this->currentHookname = $this->getHookname($current_screen->id)) :
            return;
        endif;

        if (!$nodes = $this->getNodeCollection($this->currentHookname)) :
            return;
        endif;

        if (!$box = $this->getBox($this->currentHookname)) :
            $box = $this->registerBox($this->currentHookname);
        endif;

        $current_screen->add_option('tfyTabMetabox', Arr::get($this->screenOptions, $this->currentHookname, []));

        /** @var TabNodeItemController $node */
        foreach($nodes as $node) :
            $node->load($current_screen);
        endforeach;

        $this->display =  new DisplayController([
            'screen'       => $current_screen,
            'hookname'     => $this->currentHookname,
            'box'          => $box,
            'nodes'        => $nodes
        ]);
        $this->appServiceShare(DisplayController::class, $this->display);
    }

    /**
     * Récupération de la liste des aliases associés à un identifiant d'accroche.
     *
     * @param string $hookname Nom de qualification de l'identifiant d'accroche de la page d'administration associée.
     *
     * @return array
     */
    public function getAliases($hookname)
    {
        $aliases = array_keys($this->hooknameAlias, $hookname);
        $aliases[] = $hookname;

        return $aliases;
    }

    /**
     * Récupération de la boîte à onglet associée à un identifiant d'accroche
     *
     * @param string $hookname Nom de qualification de l'identifiant d'accroche de la page d'administration associée.
     *
     * @return null|TabBoxItemController
     */
    public function getBox($hookname)
    {
        $aliases = $this->getAliases($this->currentHookname);

        foreach($aliases as $alias) :
            if (isset($this->boxes[$alias])) :
                return $this->boxes[$alias];
            endif;
        endforeach;
    }

    /**
     * Récupération d'un identifiant de d'accroche de page d'administation dans la liste des éléments déclarés.
     *
     * @param string $name Nom de qualification ou alias de l'identifiant d'accroche à récupérer.
     *
     * @return string
     */
    public function getHookname($name)
    {
        if (in_array($name, $this->hooknames)) :
            return $name;
        elseif ($hookname = Arr::get($this->hooknameAlias, $name)) :
            return $hookname;
        endif;

        return '';
    }

    /**
     * Récupération de la liste des éléments associé à une page d'administration.
     *
     * @param string $hookname Nom de qualification de l'identifiant d'accroche de la page d'administration associée.
     *
     * @return array
     */
    public function getNodeCollection($hookname)
    {
        $aliases = $this->getAliases($hookname);

        $collection = new Collection($this->nodes);

        return $collection->filter(function ($value, $key) use ($aliases) {
            return in_array($key, $aliases);
        })->collapse()->all();
    }

    /**
     * Traitement des arguments issus du nom de qualification des boîtes à onglets et sections de boites.
     *
     * @param string $alias Nom de qualification de la page d'administration d'affichage. alias|hookname.
     *
     * @return array
     */
    public function parseAliasArgs($alias)
    {
        if(preg_match('#(.*)@(options|post_type|taxonomy|user)#', $alias, $matches)) :
            switch($matches[2]) :
                case 'post_type' :
                    $hookname = $matches[1];
                    break;
                case 'taxonomy' :
                    $hookname = 'edit-' . $matches[1];
                    break;
                case 'options' :
                    $hookname = 'settings_page_' . $matches[1];
                    break;
                default :
                    return [];
                    break;
            endswitch;

            $this->hooknameAlias[$alias] = $hookname;

            $object_name = $matches[1];
            $object_type = $matches[2];
        else :
            $hookname = $alias;

            if(preg_match('#^settings_page_(.*)#', $hookname, $matches)) :
                $object_name = $matches[1];
                $object_type = 'options';
            elseif(preg_match('#^edit-(.*)#', $hookname, $matches) && taxonomy_exists($matches[1])) :
                $object_name = $matches[1];
                $object_type = 'taxonomy';
            elseif(post_type_exists($hookname)) :
                $object_name = $hookname;
                $object_type = 'post_type';
            else :
                return [];
            endif;
        endif;

        if(!in_array($hookname, $this->hooknames)) :
            array_push($this->hooknames, $hookname);
        endif;

        if(! Arr::has($this->screenOptions, "{$hookname}.object_name")) :
            Arr::set($this->screenOptions, "{$hookname}.object_name", $object_name);
        endif;

        if(! Arr::has($this->screenOptions, "{$hookname}.object_type")) :
            Arr::set($this->screenOptions, "{$hookname}.object_type", $object_type);
        endif;

        return [$object_name, $object_type];
    }

    /**
     * Déclaration de boîte à onglets
     *
     * @param string $alias Nom de qualification de la page d'administration d'affichage. alias|hookname.
     * @param string $attrs {
     *      Attributs de configuration de la boîte à onglets.
     *
     *      @var string $name Nom de qualification. optionnel, généré automatiquement.
     *
     * }
     *
     * @return null|TabBoxItemController
     */
    public function registerBox($alias, $attrs = [])
    {
        if ($args = $this->parseAliasArgs($alias)) :
            array_push($args, $alias, $attrs);

            return $this->boxes[$alias] = $this->appServiceGet(
                TabBoxItemController::class,
                $args
            );
        endif;
    }
    
    /**
     * Déclaration d'une section de boîte à onglets.
     * 
     * @param string $alias Nom de qualification de la page d'administration d'affichage. alias|hookname.
     * @param array $attrs {
     *      Attributs de configuration du greffon
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
     * @return null|TabNodeItemController
     */
    public function registerNode($alias, $attrs = [])
    {
        if ($args = $this->parseAliasArgs($alias)) :
            array_push($args, $alias, $attrs);

            return $this->nodes[$alias][] = $this->appServiceGet(
                TabNodeItemController::class,
                $args
            );
        endif;
    }
}