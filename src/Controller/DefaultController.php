<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'default')]
    public function default(Request $request): Response
    {
        $locale = $request->getSession()->get('_locale')
            ?: $request->get('_locale', $request->getDefaultLocale());

        return $this->redirectToRoute('main', ['_locale' => $locale]);
    }
}
