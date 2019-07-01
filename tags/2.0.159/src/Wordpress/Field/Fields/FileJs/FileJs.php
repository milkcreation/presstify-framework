<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field\Fields\FileJs;

use tiFy\Field\Fields\FileJs\FileJs as BaseFileJs;
use tiFy\Wordpress\Contracts\Field\FieldFactory as FieldFactoryContract;

class FileJs extends BaseFileJs implements FieldFactoryContract
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $prefix = '/';
        if (is_multisite()) {
            $prefix = get_blog_details()->path !== '/'
                ? rtrim(preg_replace('#^' . url()->rewriteBase() . '#', '', get_blog_details()->path), '/')
                : '/';
        }

        $this->url = $prefix . '/' . md5($this->getId());

        $this->prepareRoute();
    }

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
    public function enqueue(): FieldFactoryContract
    {
        return $this;
    }
}