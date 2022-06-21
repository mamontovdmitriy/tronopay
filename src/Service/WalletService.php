<?php

namespace App\Service;

use Elliptic\EC;
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

    public function validate(string $address): bool
    {
        return $this->tron->isAddress($address);
    }

    public function restoreAddress(string $privateKey): TronAddress
    {
        $ec = new EC('secp256k1');

        $priv = $ec->keyFromPrivate($privateKey);
        $pubKeyHex = $priv->getPublic(false, "hex");

        $pubKeyBin = hex2bin($pubKeyHex);
        $addressHex = $this->tron->getAddressHex($pubKeyBin);
        $addressBin = hex2bin($addressHex);
        $addressBase58 = $this->tron->getBase58CheckAddress($addressBin);

        return new TronAddress([
            'private_key'    => $priv->getPrivate('hex'),
            'public_key'     => $pubKeyHex,
            'address_hex'    => $addressHex,
            'address_base58' => $addressBase58,
        ]);
    }
}
