<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field\Fields\FileJs;

use tiFy\Contracts\Field\FieldFactory as BaseFieldFactoryContract;
use tiFy\Field\Fields\FileJs\FileJs as BaseFileJs;
use tiFy\Support\Proxy\Router;
use tiFy\Wordpress\Contracts\Field\FieldFactory as FieldFactoryContract;

class FileJs extends BaseFileJs implements FieldFactoryContract
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'dirname'  => WP_CONTENT_DIR . '/uploads',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setUrl(?string $url = null): BaseFieldFactoryContract
    {
        if (is_null($url)) {
            $prefix = '/';
            if (is_multisite()) {
                $prefix = get_blog_details()->path !== '/'
                    ? rtrim(preg_replace('#^' . url()->rewriteBase() . '#', '', get_blog_details()->path), '/')
                    : '/';
            }
            $path = $prefix . '/' . md5($this->getAlias());

            $this->url = Router::xhr($path, [$this, 'xhrResponse'])->getUrl();
        } else {
            $this->url =  $url;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enqueue(): FieldFactoryContract
    {
        return $this;
    }
}