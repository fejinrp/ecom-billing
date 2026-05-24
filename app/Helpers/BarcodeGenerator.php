<?php

namespace App\Helpers;

class BarcodeGenerator
{
    /**
     * Mappings of Code39 characters to narrow/wide bar patterns.
     * 'n' represents narrow element, 'w' represents wide element.
     * Even indices (0, 2, 4, 6, 8) represent bars (black).
     * Odd indices (1, 3, 5, 7) represent spaces (white).
     */
    protected static $barChar = [
        '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn', 'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw',
        'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw', 'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn',
        'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn', 'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn',
        'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww', 'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww',
        'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn', 'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn',
        'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn', 'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw',
        'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw', 'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn', '*' => 'nwnnwnwnn',
        '$' => 'nwnwnwnnn', '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn'
    ];

    /**
     * Generate standard Code39 barcode as crisp SVG markup.
     *
     * @param string $code The alphanumeric string to encode
     * @param float $widthFactor Horizontal scaling multiplier (narrow bar width in px)
     * @param int $height Height of barcode in px
     * @return string Raw SVG vector markup
     */
    public static function code39($code, $widthFactor = 1.0, $height = 40)
    {
        // Code39 is always enclosed in start/stop '*' characters
        $code = '*' . strtoupper(trim($code)) . '*';
        
        $wideWidth = $widthFactor * 2.5;
        $narrowWidth = $widthFactor;
        $gap = $narrowWidth; // inter-character gap
        
        // 1. Calculate total width of barcode SVG
        $totalWidth = 0;
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            if (!isset(self::$barChar[$char])) {
                continue; // Skip characters not supported by standard Code39
            }
            $seq = self::$barChar[$char];
            for ($bar = 0; $bar < 9; $bar++) {
                if ($seq[$bar] === 'n') {
                    $totalWidth += $narrowWidth;
                } else {
                    $totalWidth += $wideWidth;
                }
            }
            // Add space for inter-character gap if not the last character
            if ($i < strlen($code) - 1) {
                $totalWidth += $gap;
            }
        }
        
        // 2. Generate SVG Elements
        $svg = '<svg width="' . $totalWidth . '" height="' . $height . '" viewBox="0 0 ' . $totalWidth . ' ' . $height . '" class="mx-auto" xmlns="http://www.w3.org/2000/svg">';
        $x = 0;
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            if (!isset(self::$barChar[$char])) {
                continue;
            }
            $seq = self::$barChar[$char];
            for ($bar = 0; $bar < 9; $bar++) {
                $isBar = ($bar % 2 === 0); // Black bar (even indices), White space (odd indices)
                $lineWidth = ($seq[$bar] === 'n') ? $narrowWidth : $wideWidth;
                
                if ($isBar) {
                    $svg .= '<rect x="' . $x . '" y="0" width="' . $lineWidth . '" height="' . $height . '" fill="#000" />';
                }
                
                $x += $lineWidth;
            }
            // Add inter-character gap
            if ($i < strlen($code) - 1) {
                $x += $gap;
            }
        }
        $svg .= '</svg>';
        
        return $svg;
    }
}
