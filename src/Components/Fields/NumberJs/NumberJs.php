<?php

/**
 * @name NumberJs
 * @desc Champ de selection de valeur numérique JS
 * @package presstiFy
 * @namespace tiFy\Components\Fields\NumberJs
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Fields\NumberJs;

use tiFy\Field\AbstractFieldController;

class NumberJs extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $container_id Id HTML du conteneur du champ.
     *      @var string $container_class Classe HTML du conteneur du champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var int $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array $data-options {
     *          Liste des options du contrôleur ajax.
     *          @see http://api.jqueryui.com/spinner/
     *      }
     * }
     */
    protected $attributes = [
        'before'          => '',
        'after'           => '',
        'container_id'    => '',
        'container_class' => '',
        'attrs'           => [],
        'name'            => '',
        'value'           => 0,
        'data-options'    => []
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyFieldNumberJs',
            $this->appAbsUrl() . '/assets/NumberJs/css/styles.css',
            ['dashicons'],
            171019
        );
        \wp_register_script(
            'tiFyFieldNumberJs',
            $this->appAbsUrl() . '/assets/NumberJs/js/scripts.css',
            ['jquery-ui-spinner'],
            171019,
            true
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyFieldNumberJs');
        \wp_enqueue_script('tiFyFieldNumberJs');
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $this->attributes['container_id'] = 'tiFyField-numberJsContainer--' . $this->getIndex();

        parent::parse($attrs);

        if (!isset($this->attributes['container_class'])) :
            $this->attributes['container_class'] = 'tiFyField-numberJsContainer ' . $this->attributes['container_class'];
        else :
            $this->attributes['container_class'] = 'tiFyField-numberJsContainer';
        endif;

        if (!isset($this->attributes['attrs']['id'])) :
            $this->attributes['attrs']['id'] = 'tiFyField-numberJs--' . $this->getIndex();
        endif;
        $this->attributes['attrs']['type'] = 'text';
        $this->attributes['attrs']['data-options'] = array_merge(
            [
                'icons' => [
                    'down' => 'dashicons dashicons-arrow-down-alt2',
                    'up'   => 'dashicons dashicons-arrow-up-alt2',
                ]
            ],
            $this->attributes['data-options']
        );
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        ob_start();
?><?php $this->before(); ?>
    <div id="<?php echo $this->get('container_id'); ?>" class="<?php echo $this->get('container_class'); ?>">
        <input <?php echo $this->attrs(); ?> />
    </div>
<?php $this->after(); ?><?php

        return ob_get_clean();
    }
}