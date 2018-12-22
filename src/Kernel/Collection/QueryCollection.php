<?php

namespace tiFy\Kernel\Collection;

use tiFy\Contracts\Kernel\QueryCollection as QueryCollectionContract;

class QueryCollection extends Collection implements QueryCollectionContract
{
    /**
     * Nombre d'éléments trouvés.
     * @var int
     */
    protected $founds = 0;

    /**
     * {@inheritdoc}
     */
    public function getFounds()
    {
        return $this->founds ? : $this->count();
    }

    /**
     * {@inheritdoc}
     */
    public function query($args)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setFounds($founds)
    {
        $this->founds = (int)$founds;

        return $this;
    }
}