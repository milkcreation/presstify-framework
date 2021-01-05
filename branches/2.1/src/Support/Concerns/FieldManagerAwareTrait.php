<?php

declare(strict_types=1);

namespace tiFy\Support\Concerns;

use Exception;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Field\Field;

trait FieldManagerAwareTrait
{
    /**
     * Instance du gestionnaire de champs.
     * @var FieldContract
     */
    private $fieldManager;

    /**
     * Instance du gestionnaire de champs.
     *
     * @return FieldContract
     */
    public function fieldManager(): FieldContract
    {
        if ($this->fieldManager === null) {
            if ($this instanceof ContainerAwareTrait && $this->containerHas(FieldContract::class)) {
                $this->fieldManager = $this->containerGet(FieldContract::class);
            } else {
                try {
                    $this->fieldManager = Field::instance();
                } catch(Exception $e) {
                    $this->fieldManager = new Field();
                }
            }
        }

        return $this->fieldManager;
    }

    /**
     * Définition du gestionnaire de champs.
     *
     * @param FieldContract $fieldManager
     *
     * @return static
     */
    public function setFieldManager(FieldContract $fieldManager): self
    {
        $this->fieldManager = $fieldManager;

        return $this;
    }
}