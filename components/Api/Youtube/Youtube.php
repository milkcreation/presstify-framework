<?php
/**
 * @see https://github.com/madcoda/php-youtube-api
 */
namespace tiFy\Components\Api\Youtube;

class Youtube extends \Madcoda\Youtube\Youtube
{
    /**
     * Instance de la classe
     * @var tiFy\Components\Api\Youtube\Youtube
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
     * Vérification de correspondance d'url
     *
     * @param string $url
     *
     * @return string
     */
    public static function isUrl($url)
    {
        return preg_match('#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $url);
    }

    /**
     * Récupération de miniature
     *
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
     * @see https://developers.google.com/youtube/player_parameters?hl=fr#Parameters
     *
     * @param string $url Url de la video.
     * @param array $params Liste des paramètres.
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
            $info = $this->getVideoInfo($id);

            preg_match('#<iframe.*width=\"([\d]+)\"\s+height=\"([\d]+)\".*><\/iframe>#', $info->player->embedHtml, $matches);
            $ratio = round(($matches[2]/$matches[1])*100, 2);

            ob_start();
            ?>
            <div style="position:relative;width:100%;height:0;padding-bottom:<?php echo $ratio; ?>%;">
                <iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" width="<?php echo $matches[1]; ?>" height="<?php echo $matches[2]; ?>" src="//www.youtube.com/embed/<?php echo $id; ?>?<?php echo http_build_query($params);?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
            <?php
            return ob_get_clean();
        } catch (\Exception $e) {
            return;
        }
    }
}