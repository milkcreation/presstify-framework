<?php declare(strict_types=1);

namespace tiFy\Routing;

use League\Uri\Components\Query;
use League\Uri\Http;
use League\Uri\Modifiers\AppendQuery;
use League\Uri\Modifiers\RemoveQueryParams;
use function League\Uri\uri_to_rfc3986;
use function League\Uri\uri_to_rfc3987;
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
    public function format($format = 'RFC3986')
    {
        switch(strtoupper($format)) :
            default:
            case 'RFC3986' :
                $this->url = uri_to_rfc3986($this->url);
                break;
            case 'RFC3987' :
                $this->url = uri_to_rfc3987($this->url);
                break;
        endswitch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->url;
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