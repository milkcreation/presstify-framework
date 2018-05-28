<?php

namespace tiFy\TabMetabox\Controller;

use Illuminate\Support\Arr;
use tiFy\Apps\AppTrait;
use tiFy\TabMetabox\Controller\TabBoxItemController;
use tiFy\TabMetabox\Controller\TabNodeItemController;
use tiFy\Partial\Partial;

class DisplayController
{
    use AppTrait;

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($attrs = [])
    {
        $this->attributes = $attrs;

        // Déclaration des événements de déclenchement
        switch($this->getScreenOption('object_type')) :
            case 'post_type' :
                if ($this->getScreen()->id === 'page') :
                    $this->appAddAction('edit_page_form', 'render');
                else :
                    $this->appAddAction('edit_form_advanced', 'render');
                endif;
                break;
            case 'options' :
                \add_settings_section('navtab', null, [$this, 'render'], $this->getScreenOption('object_name'));
                break;
            case 'taxonomy' :
                $this->appAddAction($this->getScreenOption('object_name') . '_edit_form', 'render', 10, 2);
                break;
            case 'user' :
                $this->appAddAction('show_user_profile', 'render');
                $this->appAddAction('edit_user_profile', 'render');
                break;
        endswitch;

        $this->appAddAction('admin_enqueue_scripts');
    }

    /**
     * Mise en file des scripts de l'interface d'administration
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        \wp_enqueue_style(
            'tiFyTabMetabox',
            $this->appAsset('/TabMetabox/css/styles.css'),
            ['tiFyPartial-Navtabs'],
            150216
        );
        \wp_enqueue_script(
            'tiFyTabMetabox',
            $this->appAsset('/TabMetabox/js/scripts.js'),
            ['tiFyPartial-Navtabs'],
            151019,
            true
        );
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attributs à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération de la classe de rappel de la boite à onglets.
     *
     * @return TabBoxItemController
     */
    public function getBox()
    {
        return $this->get('box');
    }

    /**
     * Récupération de l'identifiant d'accroche de la page d'affichage
     *
     * @return string
     */
    public function getHookname()
    {
        return $this->get('hookname');
    }

    /**
     * Récupération des classes de rappel des greffons.
     *
     * @return TabNodeItemController[]
     */
    public function getNodes()
    {
        return $this->get('nodes');
    }

    /**
     * Récupération de l'objet écran courant
     *
     * @return \WP_Screen
     */
    public function getScreen()
    {
        return $this->get('screen');
    }

    /**
     * Récupération d'une option l'objet écran courant WP_Screen.
     *
     * @param string $key Clé d'indexe de l'option à définir. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getScreenOption($key, $default = null)
    {
        return Arr::get($this->get('screen')->get_option('tfyTabMetabox'), $key, $default);
    }

    /**
     * Traitement de la liste des onglets de la boîte de saisie.
     *
     * @return array
     */
    protected function parseNodes()
    {
        $nodes = [];

        /* @todo
            $key_datas = ['name' => $item['name'], '_screen_id' => $this->screen->id];
            $key = base64_encode(serialize($key_datas));
            $current = ($this->current === $item['name']) ? true : false;

               data-key=\"{$key}\"
         */

        /** @var  TabNodeItemController $node */
        foreach($this->getNodes() as $id => $node) :
            $nodes[] = [
                'name'      => $node->getName(),
                'title'     => $node->get('title'),
                'parent'    => $node->get('parent'),
                'content'   => $node->get('content'),
                'args'      => array_merge(func_get_args(), [$node->get('args', [])]),
                'position'  => $node->get('position'),
                // @todo 'current'   => (get_user_meta(get_current_user_id(), 'navtab' . get_current_screen()->id, true) === $node->getName())
            ];
        endforeach;

        return $nodes;
    }

    /**
     * Affichage de la boîte à onglets.
     *
     * @return string
     */
    public function render()
    {
        $args = func_num_args() ? func_get_args() : [];

        $output = "";
        $output .= "<div id=\"tiFyTaboox-Container--" . $this->get('box')->getName() . "\" class=\"tiFyTaboox-Container\">";

        $output .= "\t<div class=\"hndle tiFyTaboox-ContainerHeader\">";

        $title = $this->get('box')->get('title');
        $output .= "\t\t<h3 class=\"hndle\"><span>" .
            (is_callable($title) ? call_user_func_array($title, $args) : $title) .
            "</span></h3>";

        $output .= "\t</div>";

        $output .= "\t<div id=\"tiFyTaboox-Wrapper--" . $this->get('box')->getName() . "\" class=\"tiFyTaboox-Wrapper\">";
        $output .= "\t\t<div class=\"tiFyTaboox-WrapperBack\"></div>";
        $output .= "\t\t<div class=\"tiFyTaboox-WrapperContent\">";

        $output .= Partial::Navtabs(['nodes' => call_user_func_array([$this, 'parseNodes'], $args)]);

        $output .= "\t\t</div>";
        $output .= "\t</div>";
        $output .= "</div>";

        echo $output;
    }
}