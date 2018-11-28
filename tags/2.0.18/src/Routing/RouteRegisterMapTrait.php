<?php

namespace tiFy\Routing;

use League\Route\Strategy\StrategyInterface;

trait RouteRegisterMapTrait
{
    /**
     * {@inheritdoc}
     */
    public function register($name, $attrs = [])
    {
        $attrs = array_merge(
            [
                'method' => 'GET',
                'path'   => '/',
                'cb'     => ''
            ],
            $attrs
        );
        extract($attrs);

        $scheme   = $scheme ?? request()->getScheme();
        $host     = $host ?? request()->getHost();
        $strategy = $strategy ?? null;

        $route = $this
            ->map($method, $path, $cb)
            ->setName($name)
            ->setScheme($scheme)
            ->setHost($host);

        if (is_string($strategy)) :
            try {
                $strategy = $this->getContainer()->get("router.strategy.{$strategy}");
            } catch (\Exception $e) {
                $strategy = null;
            }
        endif;

        if ($strategy instanceof StrategyInterface) :
            $strategy->setContainer($this->getContainer());

            $route->setStrategy($strategy);
        endif;

        return $route;
    }
}