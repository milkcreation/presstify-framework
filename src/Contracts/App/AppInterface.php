<?php

namespace tiFy\Contracts\App;

interface AppInterface
{
    /**
     * Ajout d'une action Wordpress.
     *
     * @param string $tag Identification de l'accroche.
     * @param string $method Méthode de la classe à executer.
     * @param int $priority Priorité d'execution.
     * @param int $accepted_args Nombre d'argument permis.
     *
     * @return bool
     */
    public function appAddAction($tag, $method = '', $priority = 10, $accepted_args = 1);

    /**
     * Ajout d'un filtre Wordpress.
     *
     * @param string $tag Identification de l'accroche.
     * @param string $class_method Méthode de la classe à executer.
     * @param int $priority Priorité d'execution.
     * @param int $accepted_args Nombre d'argument permis.
     *
     * @return bool
     */
    public function appAddFilter($tag, $method = '', $priority = 10, $accepted_args = 1);

    /**
     * Initialisation du controleur d'application.
     * @internal Lancé à l'issue de l'initialisation complète.
     *
     * @return void
     */
    public function appBoot();
}