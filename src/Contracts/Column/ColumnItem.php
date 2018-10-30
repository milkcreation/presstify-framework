<?php

namespace tiFy\Contracts\Column;

use tiFy\Contracts\Column\ColumnDisplayInterface;
use tiFy\Contracts\Kernel\ParametersBagInterface;
use tiFy\Contracts\Wp\WpScreenInterface;

interface ColumnItem extends ParametersBagInterface
{
    /**
     * Récupération du contenu de l'affichage.
     *
     * @return string|ColumnDisplayInterface
     */
    public function getContent();

    /**
     * {@inheritdoc}
     */
    public function getHeader();

    /**
     * Récupération de l'indice de qualification.
     *
     * @return integer
     */
    public function getIndex();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'ordre d'affichage.
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Récupération de l'instance de l'écran d'affichage.
     *
     * @return WpScreenInterface
     */
    public function getScreen();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Vérification de l'activation.
     *
     * @return boolean
     */
    public function isActive();

    /**
     * Chargement de l'écran courant Wordpress.
     *
     * @param WpScreenInterface $screen Instance de l'écran courant.
     *
     * @return void
     */
    public function load(WpScreenInterface $current_screen);
}