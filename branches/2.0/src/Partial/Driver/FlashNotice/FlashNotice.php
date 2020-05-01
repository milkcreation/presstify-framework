<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\FlashNotice;

use tiFy\Contracts\Partial\FlashNotice as FlashNoticeContract;
use tiFy\Partial\PartialDriver;
use tiFy\Support\Proxy\Session;

class FlashNotice extends PartialDriver implements FlashNoticeContract
{
    /**
     * @inheritDoc
     */
    public function add(string $message, string $type = 'error', array $attrs = []): FlashNoticeContract
    {
        Session::flash([$type => compact('attrs','message')]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        $this->set('types', ['error', 'info', 'success', 'warning']);

        foreach ($this->get('types', []) as $type) {
            $this->set($type, Session::flash($type, []));
        }
    }
}