<?php declare(strict_types=1);

namespace tiFy\Support;

class MessagesBag
{
    /**
     * Instance des données associées aux messages.
     * @var ParamsBag
     */
    protected $datas;

    /**
     * Instance des messages.
     * @var ParamsBag
     */
    protected $messages;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->datas = new ParamsBag();
        $this->messages = new ParamsBag();
    }

    /**
     * @inheritDoc
     */
    public function add(string $type, string $message = '', ?array $datas = null, ?string $key = null): ?string
    {
        $key = $key ? : Str::random();
        $this->messages->set("{$type}.{$key}", $message);
        if ($datas) {
            $this->datas->set("{$type}.{$key}", $datas);
        }

        return $key;
    }

    /**
     * @inheritDoc
     */
    public function all(?string $type = null): array
    {
        return is_null($type) ? $this->messages->all() : $this->get($type, []);
    }

    /**
     * @inheritDoc
     */
    public function clear(string $type = null): self
    {
        $this->messages->forget($type ? $type : $this->messages->keys());
        $this->datas->forget($type ? $type : $this->datas->keys());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(string $type = null): int
    {
        $count = 0;
        if (is_null($type)) {
           foreach($this->messages->keys() as $key) {
               $count += count($this->messages->get($key));
           }
           return $count;
        } else {
            return count($this->messages->get($type));
        }
    }

    /**
     * @inheritDoc
     */
    public function datas(string $type, $key = null, $default = null)
    {
        if (!$this->has($type)) {
            return $default;
        } elseif (is_null($key)) {
            return $this->messages->get($type, []);
        } else {
            return $this->messages->get("{$type}.{$key}", $default);
        }
    }

    /**
     * @inheritDoc
     */
    public function error(string $message = '', ?array $datas = null, ?string $key = null): ?string
    {
        return $this->add('error', $message, $datas, $key);
    }

    /**
     * @inheritDoc
     */
    public function get(string $type, $key = null, string $default = '')
    {
        if (!$this->has($type)) {
            return $default;
        } elseif (is_null($key)) {
            return $this->messages->get($type, []);
        } else {
            return $this->messages->get("{$type}.{$key}", $default);
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $type)
    {
        return in_array($type, $this->messages->keys());
    }

    /**
     * @inheritDoc
     */
    public function info(string $message = '', ?array $datas = null, ?string $key = null): ?string
    {
        return $this->add('info', $message, $datas, $key);
    }

    /**
     * @inheritDoc
     */
    public function success(string $message = '', ?array $datas = null, ?string $key = null): ?string
    {
        return $this->add('success', $message, $datas, $key);
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message = '', ?array $datas = null, ?string $key = null): ?string
    {
        return $this->add('warning', $message, $datas, $key);
    }
}
