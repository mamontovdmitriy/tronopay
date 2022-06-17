<?php

namespace App\Controller;

use App\Helper\TwigExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheController extends AbstractController
{
    const CACHE_TIME = 86400;

    #[Route(path: '/img/{address}', name: 'qrcode')]
    public function qrcode(string $address, TwigExtension $twigExtension, CacheInterface $cache): Response
    {
        $qrCodeData = $cache->get('qrcode', static function (ItemInterface $item) use ($address, $twigExtension) {
            $item->expiresAfter(self::CACHE_TIME);

            return $twigExtension->urlQR($address);
        });

        $response = new Response($qrCodeData);
        $response->setSharedMaxAge(self::CACHE_TIME);

        return $response;
    }
}
