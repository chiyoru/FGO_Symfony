<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Form\Type\ClassesType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\Request;

class FgoApiController extends AbstractController
{
    public function index(HttpClientInterface $client, Request $request): Response
    {
        return $this->render(
            'fgo_api/index.html.twig'
        );
    }

    public function servantList(HttpClientInterface $client, Request $request): Response
    {
        $session = $request->getSession(); //Create session
        $session->remove('classe'); //Remove it on load of page

        $routeParameters = $request->attributes->get('_route_params'); //get route parameters
        $classe = $routeParameters['classe']; //get classe name

        $session->set('classe', $classe); //Put it in a session for a later use

        $response = $client->request( //Api
            'GET',
            'https://api.atlasacademy.io/nice/NA/servant/search?className='. $classe
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        $result = $content;
        return $this->render(
            'fgo_api/servants.html.twig',
            [
                'servant' => $result,
                'nb_servant' => count($result) - 1,
                'classe' => $classe
            ]
        );
    }

    public function servant(HttpClientInterface $client, Request $request): Response
    {

        $routeParameters = $request->attributes->get('_route_params');
        $idServant = $routeParameters['id'];
        $session = $request->getSession();

        $classe = $session->get('classe');
        $response = $client->request(
            'GET',
            'https://api.atlasacademy.io/nice/NA/servant/search?className=' . $classe
        );

        $statusCode = $response->getStatusCode();
        // $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        if ($idServant == 'BB_(Summer)') {
            foreach ($content as $servants => $key) {
                if ($key['name'] != 'BB') {
                    unset($content[$servants]);
                }
                if($key['rarity'] != 5){
                    unset($content[$servants]);
                }
            }
        } else  if ($idServant == 'BB') {
            foreach ($content as $servants => $key) {
                if ($key['name'] != 'BB') {
                    unset($content[$servants]);
                }
                if($key['rarity'] != 4){
                    unset($content[$servants]);
                }
            }
        } else {
            foreach ($content as $servants => $key) {
                if ($key['name'] != str_replace(array('_', '-'), array(' ', '/'), $idServant)) {
                    unset($content[$servants]);
                }
            }
        }

        $result = array_merge(...$content);
        return $this->render(
            'fgo_api/servant.html.twig',
            [
                'servant' => $result,
                'debug' => true

            ]
        );
    }
}
