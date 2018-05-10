<?php

namespace tiFy\Form\Forms;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Forms\FormItemController;
use tiFy\Partial\Partial;

class FormNoticesController extends AbstractCommonDependency
{
    /**
     * Liste des types d'erreurs.
     * @var string[]
     */
    protected $codes = [
        'error', 'info', 'success', 'warning'
    ];

    /**
     * Liste des attributs de configuration.
     * @var array {
     *
     *      @var array $error {
     *          Liste des attributs de configuration des messages d'erreurs
     *
     *          @var string $title Titre de l'intitulés d'affichage la liste princial des erreurs.
     *          @var int $show Affichage de la liste principale des erreurs. -1(toutes, par défaut)|0(masquer)|n(nombre maximum).
     *          @var string $teaser Indicateur d'affichage de la liste de message incomplète. '...' par défaut.
     *          @var bool $field Affichage des erreurs au niveau des champs de formulaire. Force le masquage de l'affichage principal si vrai.
     *          @var bool $dismissible Affichage d'un bouton de masquage.
     *      }
     *      @var array $success {
     *          Liste des attributs de configuration du message de succès
     *
     *          @var string $message Message par défaut.
     *      }
     * }
     */
    protected $options = [
        'error'   => [
            'title'       => '',
            'show'        => -1,
            'teaser'      => '...',
            'field'       => false,
            'dismissible' => false
        ],
        'success' => [
            'message' => ''
        ]
    ];

    /**
     * Liste des messages de notification par type
     * @var array
     */
    protected $notices = [];

    /**
     * Liste des données embarquées par les message de notification.
     * @var array
     */
    protected $datas = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param FormItemController $Form Classe de rappel du controleur de formulaire associé.
     * @param array $options Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct(FormItemController $form, $options = [])
    {            
        parent::__construct($form);

        $this->parseOptions($options);
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $options Liste des attributs de configuration.
     *
     * @return void
     */
    public function parseOptions($options = [])
    {
        $this->options = $this->recursiveParseArgs($options, $this->options);

        if(! $this->options['success']['message']) :
        elseif(is_string($this->options['success'])) :
            $this->options['success']['message'] = $this->options['success'];
        else :
            $this->options['success']['message'] = __('Votre demande a bien été prise en compte et sera traitée dès que possible', 'tify');
        endif;
        $this->add('success', [$this->options['success']['message']]);
    }

    /**
     * Récupération d'attribut de configuration selon un type
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     * @param string $key Clé d'indexe de l'attribut à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function getOption($code, $key, $default = null)
    {
        return Arr::get($this->options, "{$code}.{$key}", $default);
    }

    /**
     * Définition d'un message de notification selon un type.
     *
     * @param string $code Type du message de notification. error|success|info|warning.
     * @param string $message Inititulé du message de notification.
     * @param array $data Liste des données embarquées.
     *
     * @return void
     */
    public function add($code, $message, $data = [])
    {
        $id = Str::random();
        Arr::add($this->notices, "{$code}.{$id}", $message);

        $data = array_merge(
            [
                'order' => 0
            ],
            (array)$data
        );
        Arr::add($this->datas, "{$code}.{$id}", $message);
    }

    /**
     * Vérification d'existance d'un message de notification selon un type.
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     *
     * @return bool
     */
    public function has($code = 'error')
    {
        return Arr::has($this->notices, $code);
    }

    /**
     * Récupération de la liste des messages de notification selon un type.
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     *
     * @return array
     */
    public function get($code = 'error')
    {
        return Arr::get($this->notices, $code, '');
    }

    /**
     * Suppression de la liste des messages de notification selon un type.
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     *
     * @return array
     */
    public function reset($code = 'error')
    {
        Arr::forget($this->notices, $code);
        Arr::forget($this->datas, $code);
    }

    /**
     * Récupération de message de message de notification selon une liste d'arguments.
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     * @param array $args Liste d'arguments de données.
     */
    public function query($code = 'error', $args = [])
    {
        if (!$datas = Arr::get($this->datas, $code, [])) :
            return [];
        endif;

        $results = [];
        foreach ($datas as $id => $data) :
            $exists = @array_intersect($data, $args);

            if ($exists !== $args) :
                continue;
            endif;

            $results[$id] = $data;
        endforeach;

        return $results;
    }

    /**
     * Compte le nombre de messages de notification selon un type.
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     *
     * @return int
     */
    public function count($code = 'error')
    {
        if($notices = $this->get($code)) :
            return count($notices);
        else :
            return 0;
        endif;
    }

    /**
     * Affichage des messages de notification selon un type.
     *
     * @param string $code Type du message de notification. error(défaut)|success|info|warning.
     *
     * @return string
     */
    public function display( $code = 'error' )
    {
        if($notices = $this->get($code)) :
            $count = count($notices);
            $datas = Arr::sort($this->datas[$code], 'order');

            $text = "<ol class=\"tiFyForm-NoticesMessages tiFyForm-NoticesMessages--{$code}\">\n";
            foreach($datas as $key => $message) :
                $text .= "\t<li class=\"tiFyForm-NoticesMessage tiFyForm-NoticesMessage--{$code}\">";
                $text .= Arr::get($notices, $key, '');
                $text .= "\t</li>\n";
            endforeach;
            $text .= "</ol>\n";
        endif;

        $args['text'] = $notices ? $text : '';
        $args['type'] = $code;

        $output = (string)Partial::Notice($args, false);

        return $output;
    }
}