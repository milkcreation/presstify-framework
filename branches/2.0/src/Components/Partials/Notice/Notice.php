<?php

/**
 * @name Notice
 * @desc Controleur d'affichage de message de notification
 * @package presstiFy
 * @namespace tiFy\Components\Partials\Notice
 * @version 1.1
 * @subpackage Components
 * @since 1.2.593
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Partials\Notice;

use tiFy\Partial\AbstractFactory;

/**
 * @param array $attrs {
 *      Liste des attributs de configuration
 *
 *      @var string $id Identifiant de qualification du controleur d'affichage.
 *      @var string $container_id ID HTML du conteneur de l'élément.
 *      @var string $container_class Classes HTML du conteneur de l'élément.
 *      @var string $text Texte de notification. défaut 'Lorem ipsum dolor site amet'.
 *      @var string $dismissible Bouton de masquage de la notification.
 *      @var string $type Type de notification info|warning|success|error. défaut info.
 * }
 */
class Notice extends AbstractFactory
{
    /**

     * Initialisation globale
     *
     * @return void
     */
    final public function init()
    {
        // Déclaration des scripts
        \wp_register_style(
            'tiFyPartial-notice',
            $this->appAbsUrl() . '/assets/Notice/css/styles.css',
            [],
            180214
        );
        \wp_register_script(
            'tiFyPartial-notice',
            $this->appAbsUrl() . '/assets/Notice/js/scripts.js',
            ['jquery'],
            180214,
            true
        );
    }

    /**
     * Mise en file des scripts
     *
     * @return void
     */
    final public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartial-notice');
        \wp_enqueue_script('tiFyPartial-notice');
    }

    /**
     * Traitement des attributs de configuration
     *
     * @param array $attrs Liste des attributs de configuration
     *
     * @return array
     */
    final protected function parse($attrs = [])
    {
        $defaults = [
            'container_id'    => 'tiFyPartial-notice--' . $this->getIndex(),
            'container_class' => '',
            'text'            => 'Lorem ipsum dolor site amet',
            'dismissible'     => false,
            'type'            => 'info'
        ];
        $attrs = array_merge($defaults, $attrs);

        $class = "tiFyPartial-notice tiFyPartial-notice--" . $this->getId() . " tiFyPartial-notice--" . strtolower($attrs['type']);
        $attrs['container_class'] = $attrs['container_class']
            ? $class . " " . $attrs['container_class']
            : $class;

        if ($attrs['dismissible'] !== false) :
            $attrs['dismissible'] = is_bool($attrs['dismissible'])
                ? '&times;'
                : (string)$attrs['dismissible'];
        endif;

        return $attrs;
    }

    /**
     * Affichage
     *
     * @return string
     */
    final protected function display()
    {
        ob_start();
        self::tFyAppGetTemplatePart('notice', $this->getId(), $this->compact());

        return ob_get_clean();
    }
}