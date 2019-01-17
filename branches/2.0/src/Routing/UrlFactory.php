<?php declare(strict_types=1);

namespace tiFy\Routing;

use League\Uri\Components\Query;
use League\Uri\Http;
use League\Uri\Modifiers\AppendSegment;
use League\Uri\Modifiers\AppendQuery;
use League\Uri\Modifiers\RemoveQueryParams;
use League\Uri\UriInterface;
use tiFy\Contracts\Routing\UrlFactory as UrlFactoryContract;

class UrlFactory implements UrlFactoryContract
{
    /**
     * Instance du controleur d'url.
     * @var UriInterface
     */
    protected $url;

    /**
     * CONSTRUCTEUR
     *
     * @param string|UriInterface Url à traiter.
     *
     * @return void
     */
    public function __construct($url)
    {
        if (!$url instanceof UriInterface) :
            $url = Http::createFromString($url);
        endif;

        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->get();
    }

    /**
     * {@inheritdoc}
     */
    public function appendSegment($segment)
    {
        $this->url = (new AppendSegment($segment))->process($this->url);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSegments($segment)
    {
        if (preg_match("#{$segment}#", $this->url->getPath(), $matches)) :
            $this->url = $this->url->withPath(preg_replace("#{$matches[0]}#", '', $this->url->getPath()));
        endif;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return (string) $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getDecode()
    {
        return urldecode((string) $this->url);
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $args)
    {
        $this->url = (new AppendQuery((string) Query::createFromPairs($args)))->process($this->url);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function without(array $args)
    {
        $this->url = (new RemoveQueryParams($args))->process($this->url);

        return $this;
    }
}