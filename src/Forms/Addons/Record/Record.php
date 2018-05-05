<?php

/**
 * USAGE
 * Configuration des champs
    Standard
    ----------------------------------------
    'fields'    => array(
        [...]
        array(
            [...]
             'addons'        => array(
                'record'        => array(
                    // Active l'affichage de la colonne pour ce champ, le label du champ de formulaire est utilisé comme intitulé de colonne
                    'column'         => true,
                    // Active l'affichage de l'aperçu en ligne pour ce champ, le label du champ de formulaire est utilisé comme intitulé
                    'preview'        => true
                )
            )
        )
        [...]
    )
    Avancée
    ----------------------------------------
    'fields'    => array(
        [...]
        array(
            [...]
             'addons'        => array(
                'record'        => array(
                    // Active l'affichage de la colonne pour ce champ
                    'column'         => 'intitulé personnalisé',
                    // Active l'affichage de l'aperçu en ligne pour ce champ
                    'preview'        => 'intitulé personnalisé'
                )
            )
        )
        [...]
    )
 */


namespace tiFy\Forms\Addons\Record;

use tiFy\Forms\Addons\AbstractAddonController;
use tiFy\Db\Db;
use tiFy\Forms\Addons\Record\ListTable;
use tiFy\Forms\Addons\Record\Export;
use tiFy\Forms\Form\Handle;
use tiFy\Media\Download;
use tiFy\Templates\Templates;

class Record extends AbstractAddonController
{
    /**
     * Liste des options du formulaire associé.
     * @var array
     */
    protected $formOptions = [
        'cb'     => ListTable::class,
        'export' => false
    ];

    /**
     * Liste des options par défaut des champs du formulaire associé.
     * @var array
     */
    protected $defaultFieldOptions = [
        'record'   => true,
        'export'   => false,
        'column'   => false,
        'preview'  => false,
        'editable' => false,
    ];

    /**
     * Indicateur d'existance d'une instance
     */
    protected static $hasInstance = false;

    /**
     * Liste des attributs de configuration des données en base.
     * @var array
     */
    protected static $dbAttrs = [
        'install'    => true,
        'name'       => 'tify_forms_record',
        'col_prefix' => 'record_',
        'columns'    => [
            'ID'      => [
                'type'           => 'BIGINT',
                'size'           => 20,
                'unsigned'       => true,
                'auto_increment' => true,
                'prefix'         => false,
            ],
            'form_id' => [
                'type'   => 'VARCHAR',
                'size'   => 255,
                'prefix' => false,
            ],
            'session' => [
                'type' => 'VARCHAR',
                'size' => 32,
            ],
            'status'  => [
                'type'    => 'VARCHAR',
                'size'    => 32,
                'default' => 'publish',
            ],
            'date'    => [
                'type'    => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ],
        ],
        'keys'       => ['form_id' => 'form_id'],
        'meta'       => true,
    ];

    /**
     * Indicateur d'activation de l'export.
     * @var bool
     */
    protected static $export = false;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->callbacks = [
            'handle_successfully' => [$this, 'cb_handle_successfully'],
        ];

        if (! self::$hasInstance) :
            self::$hasInstance = true;

            $this->appAddAction('tify_templates_register');
            $this->appAddAction('tify_db_register');
            $this->appAddAction('tify_media_download_register');
        endif;
    }

    /**
     * Initialisation de l'addon.
     *
     * @return void
     */
    public function boot()
    {
        if (! self::$export && $this->getFormAttr('export', false)) :
            self::$export = true;
        endif;
    }

    /**
     * Définition d'interface d'administration
     *
     * @return void
     */
    public function tify_templates_register()
    {
        Templates::register(
            'tiFyCoreFormsAddonsRecordMenu',
            [
                'admin_menu' => [
                    'menu_title' => __('Formulaires', 'tify'),
                    'menu_slug'  => 'tify_forms_record',
                    'icon_url'   => 'dashicons-clipboard',
                ],
                'cb'         => ListTable::class,
                'db'         => 'tify_forms_record',
            ],
            'admin'
        );

        Templates::register(
            'tiFyCoreFormsAddonsRecordListTable',
            [
                'admin_menu' => [
                    'parent_slug' => 'tify_forms_record',
                    'menu_slug'   => 'tify_forms_record',
                    'menu_title'  => __('Enregistrements', 'tify'),
                    'position'    => 1,
                ],
            ],
            'admin'
        );

        if (self::$export) :
            Templates::register(
                'tiFyCoreFormsAddonsRecordExport',
                [
                    'admin_menu' => [
                        'parent_slug' => 'tify_forms_record',
                        'menu_slug'   => 'tify_forms_record_export',
                        'menu_title'  => __('Exporter', 'tify'),
                        'position'    => 2,
                    ],
                    'cb'         => Export::class,
                    'db'         => 'tify_forms_record',
                ],
                'admin'
            );
        endif;
    }

    /**
     * Déclaration de la gestion des données en base.
     *
     * @param Db $db Classe de rappel de traitement des données en base.
     *
     * @return  void
     */
    public function tify_db_register($db)
    {
        $db->register(
            'tify_forms_record',
            self::$dbAttrs
        );
    }

    /**
     * Autorisation de téléchargement du fichier d'export
     *
     * @param string $abspath Chemin absolu vers la ressource à permettre de télécharger.
     * @param Download $download Classe de rappel de traitement de téléchargement de média.
     *
     * @return void
     */
    public function tify_media_download_register($abspath, $download)
    {
        $authorize = $this->appRequest('GET')->get('authorize');

        if (get_transient($_REQUEST['$authorize'])) :
            $download->register($abspath);
        endif;
    }

    /**
     * Court-circuitage de l'issue d'un traitement de formulaire réussi.
     *
     * @param Handle $handle Classe de rappel de traitement du formulaire.
     *
     * @return void
     */
    public function cb_handle_successfully($handle)
    {
        $datas = [
            'form_id'        => $this->form()->getID(),
            'record_session' => $this->form()->getSession(),
            'record_status'  => 'publish',
            'record_date'    => current_time('mysql'),
            'item_meta'      => $this->form()->getFieldsValues(),
        ];
        $db = $this->appServiceGet(Db::class);

        // Définition de la base de données (front)
        if (! $db->has('tify_forms_record')) :
            $db->register('tify_forms_record', self::$dbAttrs);
        endif;

        $db->get('tify_forms_record')->handle()->create($datas);
    }
}