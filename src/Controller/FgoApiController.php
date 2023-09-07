<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class FgoApiController extends AbstractController
{
    #[Route('/fgo/api', name: 'app_fgo_api')]
    // public function index(): Response
    // {
    //     return $this->render('fgo_api/index.html.twig', [
    //         'controller_name' => 'FgoApiController',
    //     ]);
    // }

    // public function __construct(
    //     private HttpClientInterface $client,
    // ) {
    // }


    public function fetchFgoDB(HttpClientInterface $client): Response
    {
        $response = $client->request(
            'GET',
            'https://api.atlasacademy.io/export/NA/nice_servant.json'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        $a = new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);

        // $b = $propertyAccessor->getValue($a, '[originalName]');

        // return $b;

        return $this->render(
            'fgo_api/index.html.twig',
            ['servant' => $content,
            'nb_servant' => count($content) - 1
            ]
        );
    }
}
