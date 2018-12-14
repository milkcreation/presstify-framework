<?php
namespace tiFy\Core\Ui;

use tiFy\Core\Labels\Labels;
use tiFy\Core\Db\Db;

class Factory extends \tiFy\App\FactoryConstructor
{
    // Fonctions d'aide
    use \tiFy\Core\Ui\Common\Traits\Helpers;

    // Paramètres
    use \tiFy\Core\Ui\Common\Traits\Params;

    // Actions
    use \tiFy\Core\Ui\Common\Traits\Actions;

    // Notifications
    use \tiFy\Core\Ui\Common\Traits\Notices;

    /**
     * Liste des attributs de configuration des gabarits parent
     * @var array
     */
    protected static $Parents = [];

    /**
     * Identifiant de qualification du gabarit parent courant
     * @var null|string
     */
    protected $ParentId = null;

    /**
     * Récupération de la liste des attributs de configuration des gabarits parent
     *
     * @return array
     */
    final public static function getParentList()
    {
        return static::$Parents;
    }

    /**
     * Récupération de la liste des identifiants de qualification des gabarits parents
     *
     * @return string[]
     */
    final public static function getParentIds()
    {
        return array_keys(static::getParentList());
    }


    /**
     * Récupération des attributs de configuration d'un garabit parent
     *
     * @param string $parent_id Identifiant de qualification du parent
     *
     * @return array
     */
    final public function getParentAttrList($parent_id)
    {
        if (!$parents = static::getParentList()) :
            return [];
        endif;

        if(isset($parents[$parent_id])) :
            return $parents[$parent_id];
        endif;

        return [];
    }

    /**
     * Récupération de l'identifiant de qualification du gabarit parent courant
     *
     * @return object
     */
    final public function getParentId()
    {
        return $this->ParentId;
    }

    /**
     * Récupération d'un attribut du gabarit parent courant
     *
     * @param string $name Identifiant de qualification de l'attribut
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    final public function getParentAttr($name, $default = '')
    {
        if (!$parent_id = $this->getParentId()) :
            return $default;
        endif;

        if (!$parent_attrs = static::getParentAttrList($parent_id)) :
            return $default;
        endif;

        if (isset($parent_attrs[$name])) :
            return $parent_attrs[$name];
        endif;

        return $default;
    }

    /**
     * Rcupération de l'identifiant de qualification d'un gabarit parent
     *
     * @param obj|string $classname
     *
     * @return string
     */
    final public function queryParentId($classname)
    {
        $current_id = (new \ReflectionClass($classname))->getShortName();
        if (in_array($current_id, static::getParentIds())) :
            return $current_id;
        endif;

        if (!$parent_class = get_parent_class($classname)):
            return false;
        endif;

        if (!preg_match("#". preg_quote(self::tFyAppAttr('Namespace'), '\\') ."#", $parent_class)) :
            return false;
        endif;

        return $this->queryParentId($parent_class);
    }

    /**
     * Récupération de la liste des classes de rappel des gabarits de traitement externe
     *
     * @return array|\tiFy\Core\Ui\Factory[]
     */
    public function getHandleList()
    {
        return [];
    }

    /**
     * Récupération d'une classe de rappel de gabarit de traitement externe
     *
     * @param string $task Tâche du gabarit (edit|list|import ...)
     *
     * @return null|\tiFy\Core\Ui\Factory
     */
    public function getHandle($task)
    {
        if (!$handle_list = $this->getHandleList()) :
            return;
        endif;

        if(!isset($handle_list[$task])) :
            return;
        endif;

        return $handle_list[$task];
    }
}