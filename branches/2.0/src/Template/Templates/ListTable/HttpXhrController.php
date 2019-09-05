<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use League\Route\Http\Exception\NotFoundException;
use tiFy\Template\Factory\FactoryHttpXhrController;
use tiFy\Template\Templates\ListTable\Contracts\HttpXhrController as HttpXhrControllerContract;

class HttpXhrController extends FactoryHttpXhrController implements HttpXhrControllerContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     *
     * @throws NotFoundException
     */
    public function post()
    {
        if ($draw = $this->factory->request()->input('draw')) {
            $cols = [];
            if ($this->factory->items()->exists()) {
                foreach ($this->factory->items() as $i => $item) {
                    foreach ($this->factory->columns() as $name => $col) {
                        /** @var Column $col */
                        $cols[$i][$name]['attrs'] = $col->get('attrs', []);
                        $cols[$i][$name]['render'] = $col->render();
                    }
                }
            }

            return [
                'data'            => $cols,
                'draw'            => $draw,
                'pagenum'         => $this->factory->pagination()->getCurrentPage(),
                'pagination'      => (string)$this->factory->viewer('pagination', ['which' => 'bottom']),
                'recordsTotal'    => $this->factory->pagination()->getTotal(),
                'recordsFiltered' => $this->factory->pagination()->getTotal(),
                'search'          => (string)$this->factory->viewer('search')
            ];
        } elseif ($action = $this->factory->request()->input('action')) {
            if ($row_action = $this->factory->rowActions()->get($action)) {
                return $row_action->httpController(func_get_args());
            } else {
                throw new NotFoundException();
            }
        } else {
            throw new NotFoundException();
        }
    }
}