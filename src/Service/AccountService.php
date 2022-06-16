<?php

namespace App\Service;

class AccountService
{
    private WalletService $walletService;
    private EmailService $emailService;

    public function __construct(WalletService $walletService, EmailService $emailService)
    {
        $this->walletService = $walletService;
        $this->emailService = $emailService;
    }

    public function createNewAccount(string $email): void
    {
        // create new wallet
        $addresses = $this->walletService->createAddress();
        // todo fill email template
        // todo send email to user
        $this->emailService->sendAccountInformation($email, $addresses);
    }
}
