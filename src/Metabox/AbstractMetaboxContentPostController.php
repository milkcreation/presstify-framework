<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxContentPostInterface;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\PostType\Metadata\Post as MetadataPost;

abstract class AbstractMetaboxContentPostController
    extends AbstractMetaboxContentController
    implements MetaboxContentPostInterface
{
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
        parent::__construct($screen, $args);

        /** @var MetadataPost $postMetadata */
        $postMetadata = app(MetadataPost::class);
        foreach ($this->metadatas() as $meta => $single) :
            if (is_numeric($meta)) :
                $meta = (string) $single;
                $single = true;
            endif;

            $postMetadata->register($object_name, $meta, $single);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {
        return $this->viewer('display', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function getPostType()
    {
        return $this->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [];
    }
}