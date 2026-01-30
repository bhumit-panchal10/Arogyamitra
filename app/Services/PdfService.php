<?php

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PdfService
{
    public static function make(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $fontConfig = (new FontVariables())->getDefaults();
        $fontData = $fontConfig['fontdata'];

        return new Mpdf([
            'mode' => 'utf-8',

            'fontDir' => array_merge($fontDirs, [
                resource_path('fonts'),
            ]),

            'fontdata' => $fontData + [
                'notosansgujarati' => [
                    'R' => 'NotoSansGujarati-Regular.ttf',
                    'B' => 'NotoSansGujarati-Bold.ttf',
                ],
                'notosansdevanagari' => [
                    'R' => 'NotoSansDevanagari-Regular.ttf',
                    'B' => 'NotoSansDevanagari-Bold.ttf',
                ],
            ],

            'default_font' => 'notosansdevanagari',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);
    }
}
