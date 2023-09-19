<?php
use App\Controller\FgoApiController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('index', '/fgo/index')
        ->controller([FgoApiController::class, 'index']);

    $routes->add('servants', '/fgo/{classe}')
    ->controller([FgoApiController::class, 'servantList']);

    $routes->add('servant', '/fgo/servant/{id}')
    ->controller([FgoApiController::class, 'servant']);
};
?>