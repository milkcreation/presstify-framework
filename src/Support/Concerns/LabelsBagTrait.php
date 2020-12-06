<?php declare(strict_types=1);

namespace tiFy\Support\Concerns;

use InvalidArgumentException;
use tiFy\Support\LabelsBag;

trait LabelsBagTrait
{
    /**
     * Instance du gestionnaire d'intitulés.
     * @var LabelsBag|null
     */
    protected $labelsBag;

    /**
     * Liste des intitulés par défaut.
     *
     * @return array
     */
    public function defaultLabels(): array
    {
        return [];
    }

    /**
     * Définition|Récupération|Instance des intitulés.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|array|mixed|LabelsBag
     *
     * @throws InvalidArgumentException
     */
    public function labels($key = null, $default = '')
    {
        if (!$this->labelsBag instanceof LabelsBag) {
            $this->labelsBag = LabelsBag::createFromAttrs($this->defaultLabels());
        }

        if (is_null($key)) {
            return $this->labelsBag;
        } elseif (is_string($key)) {
            return $this->labelsBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->labelsBag->set($key);
        } else {
            throw new InvalidArgumentException('Invalid LabelsBag passed method arguments');
        }
    }

    /**
     * Traitement de la liste des intitulés.
     *
     * @return static
     */
    public function parseLabels(): self
    {
        return $this;
    }

    /**
     * Définition de la liste des intitulés.
     *
     * @param array $labels
     *
     * @return static
     */
    public function setLabels(array $labels): self
    {
        $this->labels($labels);

        return $this;
    }
}