<?php

namespace App\Helpers;

use App\Models\Post;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QRHelper 
{
    protected $qrOptions;
    protected $qrImageFormat = "jpg";
    protected $qrImagePath = "images/qrcodes";
    public function __construct($version = 5, $eccLevel = QRCode::ECC_L){
        $this->qrOptions = new QROptions([
            'version'    => $version,
            'outputType' => QRCode::OUTPUT_IMAGE_JPG,
            'eccLevel'   => $eccLevel,
            'imageTransparent' => false,
            'imagickFormat' => 'jpg',
            'imageTransparencyBG' => [255, 255, 255],
        ]);
    }

    public function generate(string $code = null, bool $file = true, bool $returnBase64 = false) : string {
        if(empty($code)){
            $code = Str::random(16);
        }
        $qrcode = new QRCode($this->qrOptions);
        $path = null;
        if($file){
            $path = Storage::disk('public')->path($this->qrImagePath);
            if(!file_exists($path)){
                mkdir($path, 0777, true);
            }
        }
        $base64 = $qrcode->render($code, $path);
        return $returnBase64 ? $base64 : $code;
    }

}
