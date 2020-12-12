<?php declare(strict_types=1);

namespace tiFy\Debug;

use tiFy\Contracts\Debug\Debug as DebugManager;
use tiFy\Contracts\Debug\DebugDriver as DebugDriverContract;

class DebugDriver implements DebugDriverContract
{
    /**
     * Instance du gestionnaire de deboguage
     * @var DebugManager
     */
    protected $debugManager;

    /**
     * Instance de l'adapateur du pilote associé.
     * @var object|null
     */
    protected $adapter;

    /**
     * @param DebugManager $debugManager
     * @param object|null $adapter
     */
    public function __construct(DebugManager $debugManager, ?object $adapter = null)
    {
        $this->debugManager = $debugManager;

        if (!is_null($adapter)) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * @inheritDoc
     */
    public function adapter(): ?object
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getFooter(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getHead(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(object $adapter): DebugDriverContract
    {
        $this->adapter = $adapter;

        return $this;
    }
}
