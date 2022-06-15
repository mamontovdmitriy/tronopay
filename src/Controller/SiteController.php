<?php

namespace App\Controller;

use App\Form\CreateAccountForm;
use App\Service\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale<%supported_locales%>}')]
class SiteController extends AbstractController
{

    #[Route(path: '/', name: 'main')]
    public function main(Request $request, AccountService $accountService): Response
    {
        $form = $this->createForm(CreateAccountForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $accountService->createNewAccount($form->getData()['email']);

                $this->addFlash('success', 'mgs_check_email');
            } catch (\Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }

            return $this->redirectToRoute('main');
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/pay/{address}', name: 'pay')]
    public function pay(string $address): Response
    {
        // todo check address
//        if (false) {
//            return $this->createNotFoundException();
//        }

        return $this->render('default/pay.html.twig', ['address' => $address]);
    }
}
