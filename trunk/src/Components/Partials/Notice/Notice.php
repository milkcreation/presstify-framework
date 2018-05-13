<?php

namespace tiFy\Components\Partials\Notice;

use tiFy\Partial\AbstractPartialController;

class Notice extends AbstractPartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $id Identifiant de qualification du controleur d'affichage.
     *      @var string $container_id ID HTML du conteneur de l'élément.
     *      @var string $container_class Classes HTML du conteneur de l'élément.
     *      @var string $text Texte de notification. défaut 'Lorem ipsum dolor site amet'.
     *      @var string $dismissible Bouton de masquage de la notification.
     *      @var string $type Type de notification info|warning|success|error. défaut info.
     * }
     */
    protected $attributes = [
        'container_id'    => '',
        'container_class' => '',
        'text'            => 'Lorem ipsum dolor site amet',
        'dismissible'     => false,
        'type'            => 'info'
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
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
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartial-notice');
        \wp_enqueue_script('tiFyPartial-notice');
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
        $this->attributes['container_id'] = 'tiFyPartial-notice--' . $this->getIndex();

        parent::parse($attrs);

        $class = "tiFyPartial-notice tiFyPartial-notice--" . $this->getId() . " tiFyPartial-notice--" . strtolower($this->attributes['type']);
        $this->attributes['container_class'] = $this->attributes['container_class']
            ? $class . " " . $this->attributes['container_class']
            : $class;

        if ($this->attributes['dismissible'] !== false) :
            $this->attributes['dismissible'] = is_bool($attrs['dismissible'])
                ? '&times;'
                : (string)$this->attributes['dismissible'];
        endif;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        return $this->appTemplateRender('notice', $this->compact());
    }
}