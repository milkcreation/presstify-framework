<?php
namespace tiFy\Core\Router\Admin;

use tiFy\Core\Router\Router;
use tiFy\Core\Taboox\Options\Admin;

class ContentHook extends Admin
{
    /**
     * Classe de rappel de la gestion des routes déclarées.
     * @var Router
     */
    protected $router;

    /**
     * Initialisation globale.
     *
     * @return void
     */
    public function init()
    {
        $this->router = Router::get();
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        foreach ($this->router->getRouteList() as $name => $obj) :
            if(! $option_name = $obj->getOptionName()) :
                continue;
            endif;
            \register_setting($this->page, $obj->getOptionName());
        endforeach;
    }

    /**
     * Formulaire de saisie.
     *
     * @return string
     */
    public function form()
    {
?>
<table class="form-table">
    <tbody>
    <?php foreach ($this->router->getRouteList() as $name => $obj) : ?>
        <tr>
            <th><?php echo $obj->getTitle(); ?></th>
            <td>
            <?php
            if ($dropdown = \wp_dropdown_pages(
                    [
                        'name'             => $obj->getOptionName(),
                        'post_type'        => $obj->getObjectName(),
                        'selected'         => $obj->getSelected(),
                        'sort_column'      => $obj->getAttr('listorder'),
                        'show_option_none' => $obj->getAttr('show_option_none'),
                        'echo'             => 0
                    ]
                )
            ) :
                echo $dropdown;
            else :
                _e('Aucune page publiée sur ce site.', 'tify');
            endif;
            ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
    }
}