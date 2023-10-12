<?php
use App\Controller\FgoApiController;
use App\Controller\UsersController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('index', '/fgo/index')
    ->controller([FgoApiController::class, 'index']);

    $routes->add('class', '/fgo/class')
        ->controller([FgoApiController::class, 'class']);

    $routes->add('servants', '/fgo/{classe}')
    ->controller([FgoApiController::class, 'servantList']);

    $routes->add('servant', '/fgo/servant/{id}')
    ->controller([FgoApiController::class, 'servant']);

    $routes->add('registration', '/fgo/user/registration')
    ->controller([UsersController::class, 'registration']);

    $routes->add('profile', '/fgo/user/profile')
    ->controller([UsersController::class, 'userProfile']);
};
?>