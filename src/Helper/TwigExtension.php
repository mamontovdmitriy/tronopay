<?php

namespace App\Helper;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    private string $logo;

    public function __construct(string $logo)
    {
        $this->logo = $logo;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('nformat', [$this, 'formatNumber']),
            new TwigFilter('qr', [$this, 'urlQR']),
        ];
    }

    public function formatNumber(int $price, int $digits = 8, string $delimiter = '.'): string
    {
        $format = (string)$price / pow(10, $digits);

        return str_replace([',', '.'], $delimiter, $format);
    }

    public function urlQR(string $url, int $size = 300): string
    {
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize($size)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $logo = Logo::create($this->logo)->setResizeToWidth(50);

        $qr = (new PngWriter())->write($qrCode, $logo);

        return $qr->getDataUri();
    }
}
