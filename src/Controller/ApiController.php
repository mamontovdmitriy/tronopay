<?php

namespace App\Controller;

use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

#[Route(path: '/api')]
class ApiController extends AbstractController
{
    #[Route(path: '/address', name: 'api_create_address', methods: ["POST", "GET"])]
    public function createAddress(WalletService $walletService): Response
    {
        return $this->json($walletService->createAddress()->getRawData(), Response::HTTP_CREATED);
    }

    #[Route(path: '/restore-address', name: 'api_restore_address', methods: ["POST"])]
    public function restoreAddress(Request $request, WalletService $walletService): Response
    {
        $privateKey = $request->get('private_key');
        if ($privateKey === null) {
            throw new InvalidArgumentException('The param "private_key" is required.');
        }

        $address = $walletService->restoreAddress($privateKey)->getRawData();

        return $this->json($address);
    }
}
