<?php

namespace tiFy\Components\PostTypeField\Subtitle;

use tiFy\PostType\PostTypeFieldItemController;
use tiFy\Metadata\Post as PostMetadata;

class Subtitle extends PostTypeFieldItemController
{
    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        /** @var PostMetadata $postMetadata */
        $postMetadata = $this->app->appServiceGet(PostMetadata::class);
        $postMetadata->register($this->app->getName(), '_subtitle', true);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'context' => 'edit_form_after_title',
            'name'    => '_subtitle',
            'value'   => '',
            'attrs'   => [
                'placeholder' => __('Sous-titre', 'tify'),
            ],
        ];
    }

    /**
     * Affichage.
     *
     * @param \WP_Post $post Objet Post Wordpress
     *
     * @return string
     */
    public function edit_form_after_title($post)
    {
        ?>
        <input type="text" class="widefat" name="tify_meta_post[_subtitle]"
               value="<?php echo wp_unslash(get_post_meta($post->ID, '_subtitle', true)); ?>"
               placeholder="<?php echo $this->Args['placeholder']; ?>"
               style="margin-top:10px; margin-bottom:20px; background-color: #fff; font-size: 1.4em; height: 1.7em; line-height: 100%; margin: 10 0 15px; outline: 0 none; padding: 3px 8px; width: 100%;"/>
        <?php
    }
}