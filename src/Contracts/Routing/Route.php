<?php

namespace tiFy\Contracts\Routing;

use League\Route\ContainerAwareInterface;
use League\Route\Middleware\StackAwareInterface as MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;

interface Route extends ContainerAwareInterface, MiddlewareAwareInterface, StrategyAwareInterface
{
    /**
     * Récupération de la liste des arguments passée dans la requête HTTP courante.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Récupération de l'hôte HTTP.
     *
     * @return string
     */
    public function getHost();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du schéma HTTP.
     *
     * @return string
     */
    public function getScheme();

    /**
     * Récupération du motif de traitement des arguments de l'url.
     *
     * @return string
     */
    public function getPattern();

    /**
     * Récupération du numéro de port HTTP.
     *
     * @return int
     */
    public function getPort();

    /**
     * Récupération de l'url associée.
     *
     * @param array $params Liste des variables passée en argument. Tableau indexé.
     * @param boolean $absolute Activation de la récupération de l'url absolue.
     * 
     * @return string
     *
     * @throws \LogicException
     */
    public function getUrl($params = [], $absolute = true);

    /**
     * Vérifie si la route répond à la requête HTTP courante.
     *
     * @return boolean
     */
    public function isCurrent();

    /**
     * Définition de la liste des variables passées en argument dans la requête HTTP courante.
     *
     * @param array $args Liste des variables.
     *
     * @return void
     */
    public function setArgs($args = []);

    /**
     * Définition de l'indicateur de route en réponse à la requête courante.
     *
     * @return void
     */
    public function setCurrent();

    /**
     * Définition de l'hôte HTTP.
     *
     * @param string $host Hôte HTTP.
     *
     * @return $this
     */
    public function setHost($host);

    /**
     * Définition du nom de qualification.
     *
     * @param string $name Nom de qualification
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Définition du schéma HTTP.
     *
     * @param string $scheme Schéma HTTP.
     *
     * @return $this
     */
    public function setScheme($scheme);

    /**
     * Définition du numéro de port HTTP.
     *
     * @param int $port Numéro du port HTTP.
     *
     * @return $this
     */
    public function setPort($port);
}