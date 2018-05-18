<?php

namespace tiFy\TabMetabox\Controller;

use tiFy\TabMetabox\Controller\TabContentControllerInterface;

class TabNodeItemController extends AbstractTabItemController
{
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
    protected function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->attributes = array_merge(
            [
                'name'     => md5("node-{$this->alias}-" . $this->getIndex()),
                'cap'      => 'manage_options',
                'title'    => '',
                'content'  => '',
                'args'     => [],
                'parent'   => '',
                'show'     => true,
                'position' => 0
            ],
            $this->attributes
        );

        if (!$this->get('title')) :
            $this->set('title', $this->get('name'));
        endif;

        $content = $this->get('content', '');

        if (is_string($content) && class_exists($content)) :
            $content = call_user_func_array("{$content}::create", [$this->object_name, $this->object_type]);
        endif;

        $this->set('content', $content);
    }

    /**
     * Pré-Chargement de la page d'administration courante de Wordpress. Déclaration de l'écran courant.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($current_screen)
    {
        $content = $this->get('content');

        if ($content instanceof TabContentControllerInterface) :
            $content->_load($current_screen);
        endif;
    }
}