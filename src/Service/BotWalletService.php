<?php

namespace App\Service;

class BotWalletService
{
    const TOKEN_TRON = 'TRX';
    const ADDRESS_USDT = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
    const ADDRESS_USDC = 'TEkxiTehnzSmSe2XqrBj4w32RUN966rdz8';
    const ADDRESS_USDD = 'TPYmHEhy5n8TCEfYGqW2rPxsghSfzghPDn';
    const ADDRESS_TUSD = 'TUpMhErZL2fhh4sVNULAbNKLokS4GjC1F4';

    public function __construct(private readonly string $urlTronBalance)
    {
    }

    public function getBalance(string $address): array
    {
        $response = $this->getResponse($address);
        if (!$response['success']) {
            return [];
        }

        $data = $response['data'] ?? [];
        $row = !empty($data) ? reset($data) : [];
        $list = $this->getBalanceList($row);

        return $this->makeBalance($list);
    }

    private function getBalanceList(array $data): array
    {
        if (empty($data)) {
            return [self::TOKEN_TRON => 0];
        }

        $list = [self::TOKEN_TRON => $data['balance']];

        foreach (($data['trc20'] ?? []) as $token) {
            foreach ($token as $name => $balance) {
                $list[$name] = $balance;
            }
        }

        return $list;
    }

    private function makeBalance(array $list): array
    {
        $resultList = [];
        foreach ($list as $token => $balance) {
            $name = $this->getTokenName($token);
            $value = $this->getTokenBalance($token, $balance);
            $resultList[$name] = $value;
        }

        return $resultList;
    }

    private function getTokenName(string $token): string
    {
        $names = [
            self::ADDRESS_USDC => 'USDC (TRC20)',
            self::ADDRESS_USDD => 'USDD (TRC20)',
            self::ADDRESS_TUSD => 'TUSD (TRC20)',
            self::ADDRESS_USDT => 'USDT (TRC20)',
        ];

        return $names[$token] ?? $token;
    }

    private function getTokenBalance(string $token, float $balance): float
    {
        $rates =[
            'TRX' => 0.000001,
            self::ADDRESS_USDC => 0.000001,
            self::ADDRESS_USDD => 0.000000000000000001,
            self::ADDRESS_TUSD => 0.000000000000000001,
            self::ADDRESS_USDT => 0.000001,

        ];

        $rate = $rates[$token] ?? 1;

        return $rate * $balance;
    }

    private function getResponse(string $address): array
    {
        $url = str_replace('{{ address }}', $address, $this->urlTronBalance);

        $json = file_get_contents($url);
        if ($json === '') {
            throw new \RuntimeException('Response is empty!');
        }

        $array = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $array;
    }
}