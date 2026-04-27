<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Helper;

class Image
{
    /**
     * Download image via cURL, resize to 500x500 with white background
     */
    public function downloadAndResize($imageUrl, $destination)
    {
        $imageData = file_get_contents($imageUrl);

        if (!$imageData) {
            return false;
        }

        $source = imagecreatefromstring($imageData);

        if (!$source) {
            return false;
        }

        $srcWidth  = imagesx($source);
        $srcHeight = imagesy($source);

        /*
         Target canvas
        */
        $canvasSize = 500;

        /*
         Target bottle size
        */
        $targetHeight = 200;

        $ratio = $srcWidth / $srcHeight;

        $targetWidth = (int)($targetHeight * $ratio);

        /*
         Create white canvas
        */
        $canvas = imagecreatetruecolor($canvasSize, $canvasSize);

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        /*
         Center position
        */
        $dstX = (int)(($canvasSize - $targetWidth) / 2);
        $dstY = (int)(($canvasSize - $targetHeight) / 2);

        imagecopyresampled(
            $canvas,
            $source,
            $dstX,
            $dstY,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $srcWidth,
            $srcHeight
        );

        imagejpeg($canvas, $destination, 90);

        imagedestroy($source);
        imagedestroy($canvas);

        return true;
    }

    /**
     * Robust cURL image download
     */
    private function downloadViaCurl(string $url): string
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Magento Beautyfort Importer'
        ]);

        $data = curl_exec($ch);

        if ($data === false) {
            throw new \Exception('cURL download failed: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('Image HTTP status ' . $httpCode);
        }

        return $data;
    }
}
