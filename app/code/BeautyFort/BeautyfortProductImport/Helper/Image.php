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
        * Match our manually-created Figma product images.
        */
        $canvasSize = 500;

        /*
        * Maximum area occupied by supplier image.
        *
        * 350px = approximately 70% of the 500px canvas.
        */
        $maxArtworkWidth  = 350;
        $maxArtworkHeight = 350;

        /*
        * Scale supplier image UP or DOWN to fit the
        * artwork area while preserving aspect ratio.
        *
        * Important: unlike the previous version,
        * this deliberately allows upscaling.
        */
        $scale = min(
            $maxArtworkWidth / $srcWidth,
            $maxArtworkHeight / $srcHeight
        );

        $targetWidth = max(
            1,
            (int) round($srcWidth * $scale)
        );

        $targetHeight = max(
            1,
            (int) round($srcHeight * $scale)
        );

        /*
        * Create 500x500 white canvas.
        */
        $canvas = imagecreatetruecolor(
            $canvasSize,
            $canvasSize
        );

        $white = imagecolorallocate(
            $canvas,
            255,
            255,
            255
        );

        imagefill(
            $canvas,
            0,
            0,
            $white
        );

        /*
        * Centre supplier image.
        */
        $dstX = (int) round(
            ($canvasSize - $targetWidth) / 2
        );

        $dstY = (int) round(
            ($canvasSize - $targetHeight) / 2
        );

        /*
        * High-quality resize.
        */
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

        /*
        * High quality JPEG.
        */
        $saved = imagejpeg(
            $canvas,
            $destination,
            95
        );

        imagedestroy($source);
        imagedestroy($canvas);

        return $saved;
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
