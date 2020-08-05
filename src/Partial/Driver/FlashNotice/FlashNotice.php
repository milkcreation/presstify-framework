<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\FlashNotice;

use tiFy\Contracts\Partial\{PartialDriver as BaseDriverContract, FlashNotice as FlashNoticeContract};
use tiFy\Partial\PartialDriver as BaseDriver;
use tiFy\Support\Proxy\Session;

class FlashNotice extends BaseDriver implements FlashNoticeContract
{
    /**
     * @inheritDoc
     */
    public function add(string $message, string $type = 'error', array $attrs = []): FlashNoticeContract
    {
        $key = ($namespace = $this->get('namespace')) ? "{$namespace}-{$type}" : $type;

        Session::flash([$key => compact('attrs', 'message', 'type')]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'namespace' => '',
            'types'     => ['error', 'info', 'success', 'warning'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $attrs = []): FlashNoticeContract
    {
        return $this->add($message, 'error', $attrs);
    }

    /**
     * @inheritDoc
     */
    public function info(string $message, array $attrs = []): FlashNoticeContract
    {
        return $this->add($message, 'info', $attrs);
    }

    /**
     * @inheritDoc
     */
    public function parse(): BaseDriverContract
    {
        parent::parse();

        if ($namespace = $this->get('namespace')) {
            $types = $this->get('types');
            foreach ($types as &$type) {
                $type = "{$namespace}-{$type}";
            }
            $this->set(compact('types'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        foreach ($this->get('types') as $type) {
            if (!$this->has($type)) {
                $this->set($type, Session::flash($type));
            }
        }

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function success(string $message, array $attrs = []): FlashNoticeContract
    {
        return $this->add($message, 'success', $attrs);
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $attrs = []): FlashNoticeContract
    {
        return $this->add($message, 'warning', $attrs);
    }
}