<?php

declare(strict_types=1);

namespace tiFy\Support\Concerns;

use InvalidArgumentException;
use tiFy\Support\MessagesBag;

trait MessagesBagTrait
{
    /**
     * Instance du gestionnaire des messages.
     * @var MessagesBag|null
     */
    protected $messagesBag;

    /**
     * Définition|Récupération|Instance des intitulés.
     *
     * @param string|null $message
     * @param string|int $level
     * @param mixed $datas
     *
     * @return string|array|mixed|MessagesBag
     *
     * @throws InvalidArgumentException
     */
    public function messages(?string $message = null, $level = 'error', array $datas = [])
    {
        if (!$this->messagesBag instanceof MessagesBag) {
            $this->messagesBag = new MessagesBag();
        }

        if (is_null($message)) {
            return $this->messagesBag;
        } else {
            return $this->messagesBag->add($level, $message, $datas);
        }
    }
}