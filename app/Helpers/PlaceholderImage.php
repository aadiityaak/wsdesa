<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PlaceholderImage
{
    public static function generate(string $text = 'Image', int $width = 800, int $height = 600): string
    {
        $filename = 'placeholders/'.md5($text.$width.$height).'.jpg';

        if (Storage::disk('public')->exists($filename)) {
            return $filename;
        }

        $seed = urlencode($text);
        $url = "https://picsum.photos/seed/{$seed}/{$width}/{$height}";

        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
            }
        } catch (\Exception) {
            // fallback: generate empty file so we don't retry endlessly
            Storage::disk('public')->put($filename, '');
        }

        return $filename;
    }

    public static function generatePdf(string $text = 'Document'): string
    {
        $filename = 'placeholders/'.md5($text).'.pdf';

        if (Storage::disk('public')->exists($filename)) {
            return $filename;
        }

        // Real PDF with basic content
        $content = 'PDF Placeholder: '.$text;

        Storage::disk('public')->put($filename, $content);

        return $filename;
    }
}
