<?php

/**
 * @see https://github.com/madcoda/php-youtube-api
 * @see https://github.com/oscarotero/Embed
 * @see https://oscarotero.com/embed3/demo/index.php
 */
namespace tiFy\Api\Youtube;

use Illuminate\Support\Arr;
use Embed\Embed;
use tiFy\Apps\AppTrait;

class Youtube extends \Madcoda\Youtube\Youtube
{
    use AppTrait;

    /**
     * Instance de la classe
     * @var tiFy\Api\Youtube\Youtube
     */
    static $Inst        = null;

    /**
     * CONSTRUCTEUR
     *
     * @param array $param
     * @param $sslPath
     *
     * @return void
     */
    public function __construct($params = [], $sslPath = null)
    {
        parent::__construct($params, $sslPath);
    }
    
    /**
     * CONTROLEURS
     */
    /**
     * Initialisation
     *
     * @param array $attrs
     *
     * @return void
     */
    public static function create($attrs = [])
    {
        return self::$Inst = new static($attrs, is_ssl());
    }
    
    /**
     * Vérification de correspondance d'url.
     *
     * @param string $url Url de la vidéo.
     *
     * @return string
     */
    public static function isUrl($url)
    {
        return preg_match('#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $url);
    }

    /**
     * Récupération de miniature.
     *
     * @param string $url Url de la vidéo.
     * @param string $formats Format de l'image, par ordre de préférence.
     *
     * @return array
     */
    public static function getThumbnailSrc($url, $formats = ['maxres','standard', 'height', 'medium', 'default'])
    {
        if (!$inst = self::$Inst) :
            return;
        endif;

        // Vérification de l'url de la vidéo
        if (! self::isUrl($url)) :
            return new \WP_Error('tFyComponentsApiYtInvalidSrc', __('Url YouTube invalide', 'tify'));
        endif;

        // Récupération de l'ID de la vidéo
        if (! $ytid = self::parseVIdFromURL($url)) :
            return new \WP_Error('tFyComponentsApiYtParseVIdFailed', __('Récupération de l\ID de la vidéo depuis l\'url en échec', 'tify'));
        endif;

        // Récupération de infos de la vidéo
        if (! $infos = $inst->getVideoInfo($ytid)) :
            return new \WP_Error('tFyComponentsApiYtGetVideoInfos', __('Impossible de récupérer les informations de la vidéo', 'tify'));
        endif;

        // Récupération de la liste des miniatures
        if (empty($infos->snippet->thumbnails)) :
            return new \WP_Error('tFyComponentsApiYtAnyThumbnailAvailable', __('Aucune miniature disponible', 'tify'));
        endif;

        foreach ($formats as $format) :
            if(empty($infos->snippet->thumbnails->{$format})) :
                continue;
            endif;
            $attrs = $infos->snippet->thumbnails->{$format};
            if(empty($attrs->url) || empty($attrs->width) || empty($attrs->height) ) :
                continue;
            endif;

            $src = array_values((array) $attrs);
            $src[] = $format;

            return $src;
        endforeach;
    }

    /**
     * Récupération du code d'intégration d'une vidéo.
     *
     * @param string $url Url de la video.
     * @param array $params {
     *      Liste des paramètres.
     *      @see https://developers.google.com/youtube/player_parameters?hl=fr#Parameters
     * }
     *
     * @return string|void
     */
    public function getVideoEmbed($url, $params = [])
    {
        try{
            $id = self::parseVIdFromURL($url);
        } catch (\Exception $e) {
            return;
        }

        try{
            $info = Embed::create($url);

            if (Arr::get($params, 'loop')) :
                Arr::set($params, 'playlist', $id);
            endif;

            $height = $info->getHeight();
            $ratio = $info->getAspectRatio();
            $src = esc_url("//www.youtube.com/embed/{$id}". ($params ? '?' . http_build_query($params) : ''));
            $width = $info->getWidth();

            return $this->appTemplateRender('iframe', compact('height', 'ratio', 'src', 'width'));
        } catch (\Exception $e) {
            return;
        }
    }
}