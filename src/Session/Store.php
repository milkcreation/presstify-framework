<?php declare(strict_types=1);

namespace tiFy\Session;

use Illuminate\Database\Query\Builder as DbBuilder;
use Illuminate\Database\Schema\Blueprint;
use tiFy\Contracts\Log\Logger;
use tiFy\Contracts\Session\Session;
use tiFy\Contracts\Session\Store as StoreContract;
use tiFy\Support\Arr;
use tiFy\Support\Proxy\Crypt;
use tiFy\Support\Proxy\Database;
use tiFy\Support\Proxy\Log;
use tiFy\Support\Proxy\Schema;
use tiFy\Support\Str;

class Store implements StoreContract
{
    /**
     * Liste des attributs de session.
     * @var array
     */
    protected $attributes = [];

    /**
     * Liste des attributs d'identification de stockage de session.
     * @var array
     */
    protected $credentials = [];

    /**
     * Listes des clés de qualification des attributs d'identification de stockage de session.
     * @var string[]
     */
    protected $credentialKeys = ['key', 'expiration', 'hash'];

    /**
     * Liste des attributs de session stockés.
     * @var array
     */
    private $data = [];

    /**
     * Clé d'indice de stockage de session.
     * @var string
     */
    protected $key = '';

    /**
     * Nom de qualification de la session.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du gestionnaire de sessions.
     * @var Session
     */
    protected $session;

    /**
     * Indicateur d'initialisation.
     * @var boolean
     */
    private $started = false;

    /**
     * Instance du gestionnaire de journalisation.
     * @var Logger|null
     */
    private $logger;

    /**
     * CONSTRUCTEUR
     *
     * @param Session $session Instance du gestionnaire de session.
     *
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function db(): DbBuilder
    {
        if (!Database::schema()->hasTable('tify_session')) {
            Database::addConnection(
                array_merge(
                    Database::getConnection()->getConfig(), ['strict' => false]
                ),
                'tify.session'
            );

            if (is_multisite()) {
                global $wpdb;

                Database::getConnection('tify.session')->setTablePrefix($wpdb->prefix);
            }

            $schema = Schema::connexion('tify.session');

            $schema->create('tify_session', function (Blueprint $table) {
                $table->unsignedInteger('session_id');
                $table->string('session_name', 255);
                $table->char('session_key', 32);
                $table->longText('session_value');
                $table->bigInteger('session_expiry', false, true);
                $table->primary(['session_key']);
                $table->index('session_id', 'session_id');
            });

            $schema->table('tify_session', function (Blueprint $table) {
                $table->bigInteger('session_id', true, true)->change();
            });
        }

        return Database::table('tify_session');
    }

    /**
     * @inheritDoc
     */
    public function destroy(): StoreContract
    {
        $this->session()->set($this->getName(), null);

        $this->db()->where([
            'session_key'  => $this->getKey(),
            'session_name' => $this->getName(),
        ])->delete();

        $this->flush();
        $this->credentials = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiration(): int
    {
        return time() + intval(60 * 60 * 48);
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        $this->attributes = [];
    }

    /**
     * @inheritDoc
     */
    public function forget($keys): void
    {
        Arr::forget($this->attributes, $keys);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials($keys = null): ?array
    {
        if (!$credentials = $this->credentials) {
            return null;
        }

        if (is_null($keys)) {
            return $credentials;
        } elseif (is_string($keys)) {
            $keys = [$keys];
        } elseif (!is_array($keys)) {
            return null;
        }

        $keys = array_intersect($keys, $this->credentialKeys);

        return count($keys) > 1 ? compact($keys) : ($credentials[reset($keys)] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function getHash(int $expire): string
    {
        $hash = $this->getKey() . '|' . $expire;

        return hash_hmac('md5', $hash, Crypt::encrypt($hash));
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * @inheritDoc
     */
    public function logger(): Logger
    {
        if (is_null($this->logger)) {
            $this->logger = Log::registerChannel('session');
        }

        return $this->logger;
    }

    /**
     * @inheritDoc
     */
    public function only(array $keys): array
    {
        return Arr::only($this->attributes, $keys);
    }

    /**
     * @inheritDoc
     */
    public function pull(string $key, $default = null)
    {
        return Arr::pull($this->attributes, $key, $default);
    }

    /**
     * Push a value onto a session array.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push(string $key, $value): void
    {
        $array = $this->get($key, []);

        $array[] = $value;

        $this->put($key, $array);
    }

    /**
     * @inheritDoc
     */
    public function put($key, $value = null): StoreContract
    {
        if (!is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $k => $v) {
            $this->putOne($k, $v);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function putOne(string $key, $value = null): StoreContract
    {
        if ($value !== $this->get($key)) {
            Arr::set($this->attributes, $key, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function read(): array
    {
        $data = $this->db()->where([
            'session_name' => $this->getName(),
            'session_key'  => $this->getKey(),
        ])->value('session_value');

        $data = is_string($data) ? Str::unserialize($data) : [];
        $data = is_array($data) ? $data : [];

        events()->trigger('session.read', [&$data]);

        return $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function save(): StoreContract
    {
        if ($this->credentials && ($this->data !== $this->attributes)) {
            $this->db()->updateOrInsert([
                'session_key'  => $this->getKey(),
                'session_name' => $this->getName(),
            ], [
                'session_value'  => Arr::serialize($this->all()),
                'session_expiry' => $this->expiration(),
            ]);

            $this->logger()->info('prepare', [
                'id'     => spl_object_hash($this),
                'key'    => $this->getKey(),
                'name'   => $this->getName(),
                'expiry' => $this->expiration(),
                'value'  => Arr::serialize($this->all()),
            ]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function session(): Session
    {
        return $this->session;
    }

    /**
     * @inheritDoc
     */
    public function setKey(?string $key = null): StoreContract
    {
        $this->key = is_null($key) ? Str::random(32) : $key;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(Logger $logger): StoreContract
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): StoreContract
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function start(): StoreContract
    {
        if (!$this->started) {
            register_shutdown_function([$this, 'save']);

            if ($credentials = $this->session()->get($this->getName())) {
                /**
                 * @var string|int $key
                 * @var int $expiration
                 * @var string $hash
                 */
                extract($credentials);

                $this->setKey($key);

                if (time() > $expiration) {
                    $expiration = $this->expiration();
                    $this->updateStoredExpiration($expiration);
                }

                $this->attributes = $this->read() ?: [];
            } else {
                $this->setKey();
                $expiration = $this->expiration();
            }

            $this->credentials = array_merge(compact('expiration'), [
                'hash' => $this->getHash($expiration),
                'key'  => $this->getKey(),
            ]);

            $this->session()->set($this->getName(), $this->credentials);

            $this->started = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function updateStoredExpiration(int $expiration): StoreContract
    {
        $this->db()->where([
            'session_key'  => $this->getKey(),
            'session_name' => $this->getName(),
        ])->update(['session_expiry' => $expiration]);

        return $this;
    }
}