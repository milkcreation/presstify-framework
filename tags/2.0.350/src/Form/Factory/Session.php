<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use Exception, BadMethodCallException;
use tiFy\Contracts\Form\{FactorySession, FormFactory};
use tiFy\Contracts\Session\Store;
use tiFy\Support\Proxy\{Crypt, Session as SessionProxy};

/**
 * @mixin \tiFy\Session\Store
 */
class Session implements FactorySession
{
    use ResolverTrait;

    /**
     * Instance du gestionnaire de session associé.
     * @var Store
     */
    private $store;

    /**
     * Jeton d'identification.
     * @var string|null
     */
    protected $token;

    /**
     * CONSTRUCTEUR.
     *
     * @param FormFactory $form
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;
    }

    /**
     * @inheritDoc
     */
    public function __call(string $name, array $arguments)
    {
        try {
            return $this->store()->$name(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(
                    __('La méthode de session [%s] n\'est pas disponible.', 'tify'), $name)
            );
        }
    }

    /**
     * Génération du jeton de qualification.
     *
     * @return void
     */
    protected function generateToken(): void
    {
        $this->token = Crypt::encrypt(json_encode(['name' => $this->form()->name(), 'id' => uniqid()]));
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        if (is_null($this->token)) {
            $this->generateToken();
        }

        return $this->token;
    }

    /**
     * Récupération de l'instance du gestionnaire de session associé.
     *
     * @return Store
     */
    public function store(): Store
    {
        if (is_null($this->store)) {
            $this->store = SessionProxy::registerStore("form.{$this->form()->name()}");
        }

        return $this->store;
    }
}