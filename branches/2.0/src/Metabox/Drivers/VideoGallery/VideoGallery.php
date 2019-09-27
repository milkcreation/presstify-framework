<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\VideoGallery;

use tiFy\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\{Request, Router};

class VideoGallery extends MetaboxDriver
{
    /**
     * Indice de l'intance courante.
     * @var integer
     */
    static $instance = 0;

    /**
     * Url de traitement Xhr.
     * @var string Url de traitement
     */
    protected $url = '';

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        static::$instance++;
        $this->setUrl();
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        //$this->set('items', post_type()->post_meta()->get($post->ID, $this->get('name')) ? : []);

        return parent::content();
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'name' => '_tify_taboox_video_gallery',
            'max'  => -1,
            'title' => __('Galerie de vidéos', 'tify')
        ]);
    }

    /**
     * Affichage d'un élément
     *
     * @param int $id Identifiant de qualification de l'élément.
     * @param array $attrs Attributs de configuration de l'élément.
     * @param string string $name Nom d'enregistrement de l'élément.
     *
     * @return string
     */
    public function displayItem($id, $attrs, $name)
    {
        $attrs = array_merge([
            'poster' => '',
            'src' => ''
        ], $attrs);

        $attrs['poster_src'] =
            ($attrs['poster'] && ($image = wp_get_attachment_image_src($attrs['poster'], 'thumbnail')))
                ? $image[0]
                : '';
        $attrs['name'] = $name;
        $attrs['id'] = $id;

        return $this->viewer('item', $attrs);
    }

    /**
     * Récupération de l'url de traitement Xhr.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Définition de l'url de traitement Xhr.
     *
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null): self
    {
        $this->url = is_null($url)
            ? Router::xhr(md5('MetaboxVideoGallery--' . static::$instance), [$this, 'xhrResponse'])->getUrl()
            : $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load($current_screen)
    {
        add_action('admin_enqueue_scripts', function () {
            @wp_enqueue_media();

            wp_enqueue_style(
                'PostTypeMetaboxVideoGallery',
                asset()->url('post-type/metabox/video-gallery/css/styles.css'),
                ['tiFyAdmin'],
                180724
            );

            wp_enqueue_script(
                'PostTypeMetaboxVideoGallery',
                asset()->url('post-type/metabox/video-gallery/js/scripts.js'),
                ['jquery', 'jquery-ui-sortable', 'tiFyAdmin'],
                180724,
                true
            );

            wp_localize_script(
                'PostTypeMetaboxVideoGallery',
                'tify_taboox_video_gallery',
                [
                    'maxAttempt' => __('Nombre maximum de vidéos dans la galerie atteint', 'tify'),
                ]
            );
        });
    }

    /**
     * Récupération d'un élément via Ajax
     *
     * @param array ...$args Liste des arguments de requête passés dans l'url.
     *
     * @return array
     */
    public function xhrResponse(...$args): array
    {
        if ($name = Request::input('name')) {
            return [
                'success' => true,
                'data'    => $this->displayItem(uniqid(), [], request()->post('name'))
            ];
        } else {
            return [
                'success' => false,
                'data'    => __('Impossible de récupérer le contenu associé', 'tify'),
            ];
        }

    }
}