<?php declare(strict_types=1);

namespace tiFy\Contracts\Mail;

use DateTime, Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

interface Mailer
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): Mailer;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): Mailer;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return mixed|ParamsBag
     */
    public function config($key = null, $default = null);

    /**
     * Définition de la liste des paramètres globaux par défaut des mails.
     *
     * @param array $attrs
     *
     * @return void
     */
    public static function setDefaults(array $attrs = []): void;

    /**
     * Mise en file d'un email dans la queue d'expedition de mail.
     *
     * @param Mailable $mail Instance de l'email.
     * @param DateTime|string $date Date d'expédition.
     * @param array $params Liste des paramètres d'expédition complémentaire.
     *
     * @return int
     */
    public function addQueue(Mailable $mail, $date = 'now', array $params = []): int;

    /**
     * Réinitialisation du pilote d'expédition des emails.
     *
     * @return static
     */
    public function clearDriver(): Mailer;

    /**
     * Création de l'email.
     *
     * @param Mailable|array|null $attrs Instance de l'email|Liste des paramètres de configuration|Email courant si null.
     *
     * @return Mailable
     */
    public function create($attrs = null): Mailable;

    /**
     * Affichage du message en mode déboguage.
     *
     * @param Mailable|array|null $attrs Instance de l'email|Liste des paramètres de configuration|Email courant si null.
     *
     * @return void
     */
    public function debug($attrs = null): void;

    /**
     * Récupération de l'instance du gestionnaire d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Récupération de paramètres par défaut.
     *
     * @param string|null $key Clé d'indice du paramètres.
     * @param mixed $defaults Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getDefaults(?string $key = null, $defaults = null);

    /**
     * Récupération du pilote de traitement des e-mails.
     *
     * @return MailerDriver
     */
    public function getDriver(): MailerDriver;

    /**
     * Récupération de l'instance de gestionnaire de file de mails.
     *
     * @return MailerQueue
     *
     * @todo
     */
    public function getQueue(): MailerQueue;

    /**
     * Préparation du mail pour l'expédition.
     *
     * @return static
     */
    public function prepare(): Mailer;

    /**
     * Mise en file du message.
     *
     * @param Mailable|array|null $attrs Instance de l'email|Liste des paramètres de configuration|Email courant si null.
     * @param string|DateTime $date Date de programmation d'expédition du mail. Par defaut, envoi immédiat.
     * @param array $extras Données complémentaires.
     *
     * @return int
     *
     * @todo
     */
    public function queue($attrs = null, $date = 'now', array $extras = []): int;

    /**
     * Résolution de service fourni par le gestionnaire.
     *
     * @param string $alias
     *
     * @return object|mixed|null
     */
    public function resolve(string $alias);

    /**
     * Vérification de résolution possible d'un service fourni par le gestionnaire.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function resolvable(string $alias): bool;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return LocalFilesystem|string|null
     */
    public function resources(?string $path = null);

    /**
     * Envoi d'un message.
     *
     * @param Mailable|array|null $attrs Instance de l'email|Liste des paramètres de configuration|Email courant si null.
     *
     * @return boolean
     */
    public function send($attrs = null): bool;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): Mailer;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): Mailer;
}