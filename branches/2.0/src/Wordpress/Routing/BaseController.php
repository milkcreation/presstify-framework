<?php declare(strict_types=1);

namespace tiFy\Wordpress\Routing;

use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use tiFy\Contracts\Http\Response as ResponseContract;
use tiFy\Http\Response;
use tiFy\Routing\BaseController as ParentBaseController;
use tiFy\Support\Proxy\{Request, Storage};
use tiFy\Support\Str;

class BaseController extends ParentBaseController
{
    /**
     * Image de remplacement.
     * @var string|null
     */
    protected $imagePlaceholder;

    /**
     * Cartographie des méthodes de récupération des gabarits d'affichage
     * @see ./wp-includes/template-loader.php
     * @var array
     */
    protected $tagTemplates = [
        'is_embed'             => 'get_embed_template',
        'is_404'               => 'get_404_template',
        'is_search'            => 'get_search_template',
        'is_front_page'        => 'get_front_page_template',
        'is_home'              => 'get_home_template',
        'is_privacy_policy'    => 'get_privacy_policy_template',
        'is_post_type_archive' => 'get_post_type_archive_template',
        'is_tax'               => 'get_taxonomy_template',
        'is_attachment'        => 'get_attachment_template',
        'is_single'            => 'get_single_template',
        'is_page'              => 'get_page_template',
        'is_singular'          => 'get_singular_template',
        'is_category'          => 'get_category_template',
        'is_tag'               => 'get_tag_template',
        'is_author'            => 'get_author_template',
        'is_date'              => 'get_date_template',
        'is_archive'           => 'get_archive_template',
    ];

    /**
     * Traitement de la requête HTTP.
     *
     * @param string $path
     *
     * @return ResponseContract|SfResponse
     */
    public function handle($path)
    {
        /*if (config('routing.remove_trailing_slash', true)) {
            if (($path != '/') && (substr($path, -1) == '/') && (Request::isMethod('get'))) {
                return $this->redirect(Request::getBaseUrl() . '/' . rtrim($path, '/'));
            }
        }*/

        if ($this->imagePlaceholder && preg_match('/^wp-content\/uploads\//', $path)) {
            return $this->handleUpload(...func_get_args());
        } else {
            foreach (array_keys($this->tagTemplates) as $tag) {
                if (call_user_func($tag)) {
                    if ($response = $this->handleTag($tag, ...func_get_args())) {
                        return $response;
                    }
                }
            }
        }

        return $this->response(__('Impossible de charger le gabarit d\'affichage', 'theme'), 404);
    }

    /**
     * Traitement de la requête HTTP.
     *
     * @param string $tag Indicateur de contexte d'affichage Wordpress.
     * @param array ...$args Liste des arguments dynamiques de requête HTTP
     *
     * @return ResponseContract|null
     */
    public function handleTag(string $tag, ...$args): ?ResponseContract
    {
        $method = Str::camel($tag);
        $view = preg_replace('/^is_/', '', (string)$tag);

        if (method_exists($this, $method)) {
            return $this->{$method}(...$args);
        } elseif (file_exists(get_template_directory() . "/views/app/{$view}/index.php")) {
            return $this->view("app::{$view}/index", $this->all());
        } else {
            if ($template = call_user_func($this->tagTemplates[$tag])) {
                if ('attachment' === $tag) {
                    remove_filter('the_content', 'prepend_attachment');
                }
            } else {
                $template = get_index_template();
            }

            if ($template = apply_filters('template_include', $template)) {
                $template = preg_replace(
                    '#' . preg_quote(get_template_directory(), DIRECTORY_SEPARATOR) . '#', '', $template
                );

                return $this->view(pathinfo($template, PATHINFO_FILENAME), $this->all());
            }

            return null;
        }
    }

    /**
     * Traitement de la requête HTTP de.
     *
     * @param mixed ...$args Liste des arguments dynamiques de requête HTTP
     *
     * @return mixed
     */
    public function handleUpload(...$args)
    {
        $path = $args[0];

        $storage = Storage::local(WP_CONTENT_DIR . '/uploads');

        if (!$storage->has($path)) {
            try {
                $response = new BinaryFileResponse($this->imagePlaceholder);
                $name = basename($this->imagePlaceholder);

                $disposition = $response->headers->makeDisposition('inline', $name, Str::ascii($name));

                if ($mimeType = mime_content_type($this->imagePlaceholder)) {
                    $headers['Content-Type'] = $mimeType;
                }

                if ($length = filesize($this->imagePlaceholder)) {
                    $headers['Content-Length'] = $length;
                }

                $response->headers->replace($headers + ['Content-Disposition' => $disposition]);

                return $response;
            } catch (Exception $e) {
                return $this->response($e->getMessage(), 404);
            }
        } else {
            try {
                return $storage->response($path)->send();
            } catch (Exception $e) {
                return $this->response($e->getMessage(), 404);
            }
        }
    }

    /**
     * Définition de l'image de remplacement.
     *
     * @param string $image
     *
     * @return $this
     */
    public function setImagePlaceholder(string $image): self
    {
        if (file_exists($image)) {
            $this->imagePlaceholder = $image;
        }

        return $this;
    }
}