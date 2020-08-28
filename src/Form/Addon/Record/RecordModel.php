<?php declare(strict_types=1);

namespace tiFy\Form\Addon\Record;

use tiFy\Database\{Concerns\WithMetaTrait, Model as BaseModel};

class RecordModel extends BaseModel
{
    use WithMetaTrait;

    /**
     * Nom de classe du modèle de la table de métadonnées associé.
     * @var string
     */
    protected $metaModel = RecordMetaModel::class;

    /**
     * Clé Primaire.
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Nom de qualification de la table associée.
     * @var string
     */
    protected $table = 'tify_forms_record';
}