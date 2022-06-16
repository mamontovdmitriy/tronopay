<?php

namespace App\Service;

use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Tron;
use IEXBase\TronAPI\TronAddress;

class WalletService
{
    private Tron $tron;

    public function __construct(string $urlTronNode)
    {
        $this->tron = new Tron(new HttpProvider($urlTronNode));
    }

    public function createAddress(): TronAddress
    {
        return $this->tron->generateAddress();
    }
}
