<?php

namespace tiFy\PostType\Metabox\Fileshare;

use tiFy\Metabox\MetaboxWpPostController;
use tiFy\PostType\Metadata\Post as PostMeta;

class Fileshare extends MetaboxWpPostController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name'     => '_tify_taboox_fileshare',
            'filetype' => '', // video || application/pdf || video/flv, video/mp4,
            'max'      => -1,
            // Gestion des métadonnées en mode single
            'single'   => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function content($post = null, $args = null, $null = null)
    {
        $metadatas = $this->get('single')
            ? get_post_meta($post->ID, $this->get('name'), true)
            : app(PostMeta::class)->get($post->ID, $this->get('name'));

        ob_start();
        if ($this->get('max', -1) !== 1) :
            ?>
            <div id="fileshare-postbox">
                <?php
                echo field(
                    'hidden',
                    [
                        'name'  => '_taboox_fileshare_names[]',
                        'value' => esc_attr($this->get('name')),
                    ]
                );
                ?>

                <ul id="fileshare-<?php echo sanitize_title($this->get('name')); ?>-list" class="fileshare-list">
                    <?php if (!empty($metadatas)) : ?>
                        <?php foreach ((array)$metadatas as $meta_id => $meta_value) : ?>
                            <li>
                                <span class="icon">
                                    <?php echo \wp_get_attachment_image($meta_value, [46, 60], true); ?>
                                </span>

                                <span class="title">
                                    <?php echo \get_the_title($meta_value); ?>
                                </span>

                                <span class="mime">
                                    <?php echo \get_post_mime_type($meta_value); ?>
                                </span>

                                <a href="#" class="remove tify_button_remove"></a>

                                <?php
                                echo field(
                                    'hidden',
                                    [
                                        'name'  => $this->get('single', false)
                                            ? $this->get('name') . '[]'
                                            : "{$this->get('name')}[{$meta_id}]",
                                        'value' => esc_attr($meta_value),
                                    ]
                                );
                                ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <a href="#" class="add-fileshare button-secondary"
                    <?php
                    if ($filetype = $this->get('filetype')) :
                        echo "data-type=\"{$filetype}\"";
                    endif;
                    ?>
                   data-item_name="<?php echo $this->get('name'); ?>"
                   data-target="#fileshare-<?php echo sanitize_title($this->get('name')); ?>-list"
                   data-max="<?php echo $this->get('max', -1); ?>"
                   data-uploader_title="<?php _e('Sélectionner les fichiers à associer', 'tify'); ?>"
                >
                    <span class="dashicons dashicons-media-text" style="vertical-align:middle;"></span>&nbsp;
                    <?php echo _n(__('Ajouter le fichier', 'tify'), __('Ajouter des fichiers', 'tify'),
                        (($this->get('max', -1) === 1) ? 1 : 2), 'tify'); ?>
                </a>
            </div>
        <?php
        else :
            $name = $this->get('single')
                ? $this->get('name') . '[]'
                : "{$this->get('name')}[" . ($metadatas ? key($metadatas) : '') . "]";

            echo field(
                'media-file',
                [
                    'name'     => $name,
                    'filetype' => $this->get('filetype'),
                    'value'    => $metadatas ? current($metadatas) : 0,
                ]
            );
        endif;

        return ob_get_clean();
    }

    /**
     * {@inheritdoc}
     */
    public function header($post = null, $args = null, $null = null)
    {
        return $this->item->getTitle() ? : __('Partage de fichiers', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                if ($this->get('max', -1) !== 1) :
                    \wp_enqueue_style(
                        'MetaboxPostTypeFileshare',
                        assets()->url('post-type/metabox/fileshare/css/styles.css'),
                        [],
                        151216
                    );
                    \wp_enqueue_media();
                    \wp_enqueue_script(
                        'MetaboxPostTypeFileshare',
                        assets()->url('post-type/metabox/fileshare/js/scripts.js'),
                        ['jquery', 'jquery-ui-sortable'],
                        151216,
                        true
                    );
                else :
                    field('media-file')->enqueue_scripts();
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [
            $this->get('name')        => $this->get('single', false),
            '_taboox_fileshare_names' => true,
        ];
    }
}