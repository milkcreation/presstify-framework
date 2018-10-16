<?php

namespace tiFy\Options\Metabox\Slideshow;

use Illuminate\Support\Arr;
use tiFy\Metabox\AbstractMetaboxDisplayOptionsController;

class Slideshow extends AbstractMetaboxDisplayOptionsController
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *  @var string $name Nom de qualification d'enregistrement.
     *  @var boolean|array $suggest Liste de selection de contenu.
     *  @var boolean $custom Activation de l'ajout de vignettes personnalisées.
     *  @var integer $max Nombre maximum de vignette.
     *  @var array $editors Liste des interfaces d'édition des vignettes actives.
     *  @var string $driver Moteur d'affichage. slick par défaut.
     *  @var array $options Liste des options d'affichage.
     * }
     */
    protected $attributes = [
        'name'        => 'tify_taboox_slideshow',
        'suggest'     => true,
        'duplicate'   => false,
        'custom'      => true,
        'max'         => -1,
        'editors'     => ['image', 'title', 'link', 'caption'],
        'driver'      => 'slick',
        'options'     => [],
        'ajax_action' => 'metabox_options_slideshow'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action('wp_ajax_' . $this->get('ajax_action'), [$this, 'wp_ajax']);
    }

    /**
     * {@inheritdoc}
     */
    public function content($args = null, $null1 = null, $null2 = null)
    {
        $custom = true;
        $name = $this->get('name');
        $suggest = true;

        $values = get_option($this->get('name'));

        $items = Arr::get($values, 'slide', []);
        array_walk(
            $items,
            function (&$attrs, $index) {
                $attrs = $this->parseItem($index, $attrs);
            }
        );
        $options = Arr::get($values, 'options', []);

        return $this->viewer('content', compact('custom', 'items', 'name', 'options', 'suggest'));
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function header($args = null, $null1 = null, $null2 = null)
    {
        return $this->item->getTitle() ?: __('Diaporama', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                wp_register_script(
                    'tinyMCE-editor',
                    includes_url('js/tinymce') . '/tinymce.min.js',
                    [],
                    '4.1.4',
                    true
                );
                wp_register_script(
                    'jQuery-tinyMCE',
                    '//cdnjs.cloudflare.com/ajax/libs/tinymce/4.1.4/jquery.tinymce.min.js',
                    ['jquery', 'tinyMCE-editor'],
                    true
                );

                field('datetime-js')->enqueue_scripts();
                field('media-image')->enqueue_scripts();
                field('select-js')->enqueue_scripts();

                wp_enqueue_style(
                    'MetaboxOptionsSlideshow',
                    assets()->url('options/metabox/slideshow/css/styles.css'),
                    [],
                    181015
                );
                wp_enqueue_script(
                    'MetaboxOptionsSlideshow',
                    assets()->url('options/metabox/slideshow/js/scripts.js'),
                    [
                        'jQuery-tinyMCE',
                        'jquery-ui-sortable'
                    ],
                    181015,
                    true
                );
                wp_localize_script(
                    'MetaboxOptionsSlideshow',
                    'tiFyTabooxOptionSlideshowAdmin',
                    [
                        'l10nMax' => __('Nombre maximum de vignettes atteint', 'tify')
                    ]
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'options',
            array_merge(
                [
                    // Résolution du slideshow
                    'ratio'       => '16:9',
                    // Taille des images
                    'size'        => 'full',
                    // Navigation suivant/précédent
                    'nav'         => true,
                    // Vignette de navigation
                    'tab'         => true,
                    // Barre de progression
                    'progressbar' => false
                ],
                $this->get('options', [])
            )
        );
    }

    /**
     * Traitement des attributs d'un élément.
     *
     * @param mixed $index Indice de qualification de l'élément.
     * @param array $attrs Liste des attributs de configuration
     *
     * @return array
     */
    public function parseItem($index = null, $attrs = [])
    {
        if (!$index) :
            $index = uniqid();
        endif;

        $attrs = array_merge(
            [
                'post_id'       => 0,
                'attachment_id' => 0,
                'clickable'     => 0,
                'planning'      => [
                    'from'  => 0,
                    'start' => '',
                    'to'    => 0,
                    'end'   => '',
                ]
            ],
            $attrs
        );
        $attrs['name'] = "{$this->get('name')}[slide][{$index}]";
        $attrs['editors'] = $this->get('editors');

        $attrs['title'] = isset($attrs['title'])
            ? $attrs['title']
            : ($attrs['post_id']
                ? get_the_title($attrs['post_id'])
                : ''
            );

        $attrs['caption'] = isset($attrs['caption'])
            ? $attrs['caption']
            : ($attrs['post_id']
                ? apply_filters('the_excerpt', get_post_field('post_excerpt', $attrs['post_id']))
                : ''
            );

        $attrs['url'] = isset($attrs['url'])
            ? $attrs['url']
            : ($attrs['post_id']
                ? get_permalink($attrs['post_id'])
                : ''
            );

        return $attrs;
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        return [$this->get('name')];
    }

    /**
     * Action de récupération Ajax
     */
    public function wp_ajax()
    {
        $args = [
            'post_id'   => $_POST['post_id'],
            'title'     => get_the_title($_POST['post_id']),
            'caption'   => apply_filters('the_excerpt', get_post_field('post_excerpt', $_POST['post_id'])),
            'clickable' => $_POST['post_id'] ? 1 : 0,
            'order'     => $_POST['order']
        ];

        global $tify_events;
        if (($tify_events instanceof \tiFy_Events) && in_array(get_post_type($_POST['post_id']),
                $tify_events->get_post_types()) && ($range = tify_events_get_range($_POST['post_id']))) {
            $args['planning'] = [
                'from'  => 1,
                'start' => $range->start_datetime,
                'to'    => 1,
                'end'   => $range->end_datetime,
            ];
        }

        echo $this->item_render($args);
        exit;
    }
}