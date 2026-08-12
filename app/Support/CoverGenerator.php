<?php

namespace App\Support;

use App\Models\Book;

class CoverGenerator
{
    public static function ensure(Book $book): string
    {
        if ($book->cover_image && file_exists(public_path($book->cover_image))) {
            return $book->cover_image;
        }

        $filename = self::filename($book);
        $path = public_path($filename);

        if (!file_exists($path)) {
            self::write($path, self::render($book));
        }

        $book->cover_image = $filename;
        $book->saveQuietly();

        return $filename;
    }

    public static function render(Book $book): string
    {
        [$from, $to] = self::coverGradient($book->id);

        $lines = self::wrapTitle($book->title);
        $lineCount = count($lines);
        $startY = 210 - (($lineCount - 1) * 22);

        $text = '';
        foreach ($lines as $i => $line) {
            $y = $startY + ($i * 44);
            $text .= "<text x=\"150\" y=\"{$y}\" font-family=\"Georgia, 'Times New Roman', serif\" font-size=\"28\" font-weight=\"bold\" fill=\"#ffffff\" text-anchor=\"middle\">" . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</text>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="420" viewBox="0 0 300 420">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . "<stop offset=\"0%\" stop-color=\"{$from}\"/>"
            . "<stop offset=\"100%\" stop-color=\"{$to}\"/>"
            . '</linearGradient></defs>'
            . '<rect width="300" height="420" fill="url(#g)"/>'
            . '<rect width="300" height="420" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="10"/>'
            . "<text x=\"150\" y=\"90\" font-family=\"Arial, sans-serif\" font-size=\"14\" letter-spacing=\"4\" fill=\"rgba(255,255,255,0.7)\" text-anchor=\"middle\" text-transform=\"uppercase\">PERPUSTAKAAN SEKOLAH</text>"
            . $text
            . '</svg>';
    }

    private static function filename(Book $book): string
    {
        $key = $book->isbn ?: (string) $book->id;
        return 'images/covers/' . $key . '.svg';
    }

    private static function write(string $path, string $content): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($path, $content);
    }

    private static function coverGradient(int $seed): array
    {
        $gradients = [
            ['#4f46e5', '#7c3aed'],
            ['#0891b2', '#6d28d9'],
            ['#db2777', '#9333ea'],
            ['#ea580c', '#db2777'],
            ['#16a34a', '#0891b2'],
            ['#0f766e', '#4f46e5'],
            ['#7c3aed', '#db2777'],
            ['#b45309', '#be185d'],
            ['#1d4ed8', '#0e7490'],
            ['#9d174d', '#6d28d9'],
        ];

        return $gradients[$seed % count($gradients)];
    }

    private static function wrapTitle(string $title, int $maxChars = 22): array
    {
        $words = explode(' ', trim($title));
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
            } else {
                $lines[] = $current;
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }
}
