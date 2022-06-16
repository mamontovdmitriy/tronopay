<?php

namespace App\Service;

class AccountService
{
    private WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function createNewAccount(string $email): void
    {
        // create new wallet
        $addresses = $this->walletService->createAddress();
        dump($addresses);
        // todo fill email template
        // todo send email to user
    }
}
