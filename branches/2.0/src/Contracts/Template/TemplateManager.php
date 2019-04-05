<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

interface TemplateManager
{
    /**
     * Récupération d'un motif d'affichage.
     *
     * @param string $name Nom de qualification du motif.
     *
     * @return TemplateFactory|null
     */
    public function get(string $name): ?TemplateFactory;

    /**
     * Déclaration d'un motif d'affichage.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param array $attrs Liste des attributs de configuration de la disposition.
     *
     * @return static
     */
    public function register(string $name, array $attrs = []): TemplateManager;

    /**
     * Récupération du chemin absolu vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesDir(?string $path = ''): ?string;

    /**
     * Récupération de l'url absolue vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesUrl(?string $path = ''): ?string;

    /**
     * Définition d'un motif d'affichage.
     *
     * @param string $name Nom de qualification de la disposition.
     * @param TemplateFactory $pattern Motif d'affichage.
     *
     * @return static
     */
    public function set(string $name, TemplateFactory $pattern): TemplateManager;
}