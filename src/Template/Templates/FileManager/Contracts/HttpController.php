<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager\Contracts;

use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Template\FactoryHttpController;

interface HttpController extends FactoryHttpController
{
    /**
     * Répartiteur des requêtes HTTP de la méthode GET.
     *
     * @return mixed
     */
    public function handleGet();
}