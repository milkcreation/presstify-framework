<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use LogicException;
use tiFy\Contracts\Form\EventsFactory as EventsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;

class EventsFactory implements EventsFactoryContract
{
    use FormAwareTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * @inheritDoc
     */
    public function boot(): EventsFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $events = (array)$this->form()->params('events', []);

            foreach ($events as $name => $event) {
                if (is_callable($event)) {
                    $listener = $event;
                    $priority = 10;
                } elseif (isset($event['call']) && is_callable($event['call'])) {
                    $listener = $event['call'];
                    $priority = $event['priority'] ?? 10;
                } else {
                    continue;
                }
                $this->listen($name, $listener, $priority);
            }

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listen($name, $listener, $priority = 0): EventsFactoryContract
    {
        events()->listen("form.factory.events.{$this->form()->getAlias()}.{$name}", $listener, $priority);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trigger($name, $args = []): void
    {
        $name = "form.factory.events.{$this->form()->getAlias()}.{$name}";

        events()->trigger($name, $args);
    }
}