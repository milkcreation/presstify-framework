<?php declare(strict_types=1);

namespace tiFy\PostType;

use InvalidArgumentException;
use tiFy\Contracts\PostType\PostTypeFactory as PostTypeFactoryContract;
use tiFy\Contracts\PostType\PostTypeManager as PostTypeManagerContract;
use tiFy\Contracts\PostType\PostTypePostMeta;
use tiFy\Support\Manager;

class PostTypeManager extends Manager implements PostTypeManagerContract
{
    /**
     * @inheritdoc
     */
    public function get($name): ?PostTypeFactoryContract
    {
        return parent::get($name);
    }

    /**
     * @inheritdoc
     */
    public function post_meta(): PostTypePostMeta
    {
        return $this->resolve('post-meta');
    }

    /**
     * @inheritdoc
     */
    public function register($name, ...$args): PostTypeManagerContract
    {
        return $this->set([$name => $args[0] ?? []]);
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $alias)
    {
        return $this->container->get("post-type.{$alias}");
    }

    /**
     * @inheritdoc
     */
    public function walk(&$item, $key = null): void
    {
        if (!$item instanceof PostTypeFactoryContract) {
            $item = new PostTypeFactory($key, $item);
        }
        $item->setManager($this)->boot();
    }
}