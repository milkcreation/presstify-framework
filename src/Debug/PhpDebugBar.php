<?php declare(strict_types=1);

namespace tiFy\Debug;

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar as BasePhpDebugBar;
use DebugBar\DebugBarException;
use tiFy\Contracts\Debug\DebugDriver;
use tiFy\Support\Proxy\Url;
use tiFy\tiFy;

class PhpDebugBar extends BasePhpDebugBar
{
    /**
     * Instance du gestionnaire de deboguage
     * @var DebugDriver
     */
    protected $debugDriver;

    /**
     * @param DebugDriver $debugDriver
     */
    public function __construct(DebugDriver $debugDriver)
    {
        $this->debugDriver = $debugDriver;

        try {
            $this->addCollector(new PhpInfoCollector());
            $this->addCollector(new MessagesCollector());
            $this->addCollector(new ConfigCollector(config()->all()));
            $this->addCollector(new RequestDataCollector());
            $this->addCollector(new TimeDataCollector(tiFy::instance()->getStartTime()));
            $this->addCollector(new MemoryCollector());
            $this->addCollector(new ExceptionsCollector());
        } catch (DebugBarException $e) {
            unset($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptRenderer($baseUrl = null, $basePath = null)
    {
        return parent::getJavascriptRenderer(
            Url::root('/vendor/maximebf/debugbar/src/DebugBar/Resources')->path(), $basePath
        );
    }
}
