<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

/** @var \App\Models\OfferLetter|null $template */
$template = \App\Models\OfferLetter::first();

if (!$template) {
    fwrite(STDERR, "No offer letter template found.\n");
    exit(1);
}

$content = (string) ($template->content ?? '');

if (str_contains($content, '@{{signature}}') || str_contains($content, '{{signature}}')) {
    echo "Signature placeholder already present.\n";
    exit(0);
}

$updated = $content;

// Replace React-style image binding with placeholder.
$updated = preg_replace('/<img[^>]*src=\{headImg\}[^>]*>/i', '@{{signature}}', $updated) ?? $updated;

// Insert placeholder right after the "Thanks & Regard, HSBE LIMITED" block.
$pattern = '/(Thanks\s*&\s*Regard[,]*\s*<br\s*\/?>\s*HSBE\s+LIMITED)\s*/i';
$updated2 = preg_replace($pattern, "$1<br />\n@{{signature}}\n", $updated, 1);

if (is_string($updated2) && $updated2 !== $updated) {
    $updated = $updated2;
} else {
    // Fallback: append at end if block not found.
    $updated .= "\n<br /><br />\n@{{signature}}\n";
}

$template->content = $updated;
$template->save();

echo "Template updated with @{{signature}} placeholder.\n";

