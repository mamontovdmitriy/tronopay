<?php

namespace App\Helper;

use Endroid\QrCode\QrCode;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('nformat', [$this, 'formatNumber']),
            new TwigFilter('qr', [$this, 'urlQR']),
        ];
    }

    public function formatNumber(int $price, int $digits = 8, string $delimiter = '.'): string
    {
        $format = (string) $price / pow(10, $digits);

        return str_replace([',','.'], $delimiter, $format);
    }

    public function urlQR(string $url, int $size = 400): string
    {
        $qr = new QrCode($url);

        $qr->setSize($size);

        return $qr->writeDataUri();
    }
}
