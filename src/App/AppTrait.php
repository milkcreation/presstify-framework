<?php

namespace tiFy\App;

trait AppTrait
{
    /**
     * {@inheritdoc}
     */
    public function appAddAction($tag, $method = '', $priority = 10, $accepted_args = 1)
    {
        return $this->appAddFilter($tag, $method, $priority, $accepted_args);
    }

    /**
     * {@inheritdoc}
     */
    public function appAddFilter($tag, $method = '', $priority = 10, $accepted_args = 1)
    {
        if (!$method) :
            $method = $tag;
        endif;

        if (is_string($method) && !preg_match('#::#', $method)) :
            if ((new \ReflectionMethod($this, $method))->isStatic()) :
                $classname = get_class($this);
            else :
                $classname = $this;
            endif;

            $method = [$classname, $method];
        endif;

        if (!is_callable($method)) :
            return false;
        endif;

        return \add_filter($tag, $method, $priority, $accepted_args);
    }
}