<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\FactoryNotices;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Form\Factory\ResolverTrait;
use tiFy\Kernel\Notices\Notices as NoticesController;

class Notices extends NoticesController implements FactoryNotices
{
    use ResolverTrait;

    /**
     * Liste des types de notifications permis.
     * @var array
     */
    protected $types = ['error', 'success'];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $params Liste des paramètres.
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct($params = [], FormFactory $form)
    {
        $this->form = $form;

        app()->singleton(
            "form.factory.notices.{$this->form()->name()}.params",
            function ($attrs) {
                /**
                 * Liste des paramètres.
                 * @var array {
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
                return app()->resolve('params.bag', [$attrs]);
            }
        )->build(
            [
                array_merge(
                    [
                        'error'   => [
                            'title'       => '',
                            'show'        => -1,
                            'teaser'      => '...',
                            'field'       => false,
                            'dismissible' => false
                        ],
                        'success' => [
                            'message' => __(
                                'Votre demande a bien été prise en compte et sera traitée dès que possible',
                                'tify'
                            )
                        ]
                    ],
                    $params
                )
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add($type, $message = '', $data = [])
    {
        return parent::add($type, $message, array_merge(['order' => 0], $data));
    }

    /**
     * {@inheritdoc}
     */
    public function params($key = null, $default = null)
    {
        /** @var ParamsBag $factory */
        $factory = app("form.factory.notices.{$this->form()->name()}.params");

        if (is_null($key)) :
            return $factory;
        endif;

        return $factory->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        return parent::viewer($view, $data);
    }
}