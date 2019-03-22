<?php

namespace tiFy\Contracts\Wp;

interface Ctags
{
    /**
     * Vérifie si la page d'affichage courante correspond au contexte soumis en argument.
     * @param null $ctags
     *
     * @return bool|mixed
     */
    public function is($ctags = null);

    /**
     * Récupération de l'alias du contexte d'affichage de la page courante.
     *
     * @return string
     */
    public function current();
}