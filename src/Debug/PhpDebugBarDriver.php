<?php declare(strict_types=1);

namespace tiFy\Debug;

class PhpDebugBarDriver extends DebugDriver
{
    /**
     * {@inheritDoc}
     *
     * @return PhpDebugBar|null
     */
    public function adapter(): ?object
    {
        if (is_null($this->adapter)) {
            $this->adapter = (new PhpDebugBar($this))->getJavascriptRenderer();
        }
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getHead(): string
    {
        return $this->adapter()->renderHead();
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->adapter()->render();
    }
}
