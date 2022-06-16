<?php

namespace App\Controller;

use App\Helper\TwigExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CacheController extends AbstractController
{
    #[Route(path: '/img/{address}', name: 'qrcode')]
    public function qrcode(string $address, TwigExtension $twigExtension): Response
    {
        $response = new Response($twigExtension->urlQR($address));

        $response->setSharedMaxAge(86400);

        return $response;
    }
}
