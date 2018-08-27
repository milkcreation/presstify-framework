<?php

namespace tiFy\TabMetabox;

use tiFy\TabMetabox\Controller\ContentController;
use tiFy\Metadata\Post as PostMetadata;

class ContentPostTypeController extends ContentController
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($object_name, $object_type, $args = [])
    {
        parent::__construct($object_name, $object_type, $args);

        /** @var PostMetadata $postMetadata */
        $postMetadata = $this->appServiceGet(PostMetadata::class);

        foreach ($this->metadatas() as $meta => $single) :
            if (is_numeric($meta)) :
                $meta = (string) $single;
                $single = true;
            endif;

            $postMetadata->register($object_name, $meta, $single);
        endforeach;
    }

    /**
     * Affichage.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($post, $args = [])
    {
        return $this->appTemplateRender('display', $this->all());
    }

    /**
     * Récupération du type de post de l'environnement d'affichage de la page d'administration.
     *
     * @return string post|page|{custom_post_type}
     */
    public function getPostType()
    {
        return $this->getObjectName();
    }

    /**
     * Listes des metadonnées à enregistrer.
     *
     * @return array
     */
    public function metadatas()
    {
        return [];
    }
}