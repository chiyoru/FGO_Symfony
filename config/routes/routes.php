<?php
use App\Controller\FgoApiController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('index', '/fgo/index')
        ->controller([FgoApiController::class, 'fetchFgoDB']);

    $routes->add('servants', '/fgo/servants')
    ->controller([FgoApiController::class, 'servantList']);

    $routes->add('servant', '/fgo/servant/{id}')
    ->controller([FgoApiController::class, 'servant']);
};
?>