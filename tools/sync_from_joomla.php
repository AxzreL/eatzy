<?php
/**
 * EATZY - Sync local Joomla CMS content to Vercel Standalone App
 */

$pages = [
    'menu' => [
        'url' => 'http://localhost/EATZY/index.php?option=com_eatzy&view=menu',
        'out' => __DIR__ . '/../index.html'
    ],
    'book' => [
        'url' => 'http://localhost/EATZY/index.php?option=com_eatzy&view=book',
        'out' => __DIR__ . '/../book.html'
    ],
    'track' => [
        'url' => 'http://localhost/EATZY/index.php?option=com_eatzy&view=track',
        'out' => __DIR__ . '/../track.html'
    ]
];

echo "Fetching latest pages from local Joomla CMS...\n";

foreach ($pages as $key => $p) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header'  => "User-Agent: EATZY-Vercel-Sync/1.0\r\n"
        ]
    ]);
    
    $html = @file_get_contents($p['url'], false, $context);
    if (!$html) {
        echo " [WARNING] Could not fetch {$p['url']}. Make sure XAMPP Apache is running.\n";
        continue;
    }

    // Convert Joomla route links to standalone Vercel links
    $replacements = [
        '/EATZY/index.php/component/eatzy?view=menu' => 'index.html',
        '/EATZY/index.php/component/eatzy?view=book' => 'book.html',
        '/EATZY/index.php/component/eatzy?view=track' => 'track.html',
        'index.php?option=com_eatzy&view=menu' => 'index.html',
        'index.php?option=com_eatzy&view=book' => 'book.html',
        'index.php?option=com_eatzy&view=track' => 'track.html',
        '/EATZY/administrator/index.php?option=com_eatzy&view=kds' => 'track.html#eatzy-sim-controls',
        'action="index.php"' => 'action="track.html"',
        '/EATZY/components/com_eatzy/assets/css/eatzy.css' => 'components/com_eatzy/assets/css/eatzy.css',
        '/EATZY/components/com_eatzy/assets/js/eatzy.js' => 'components/com_eatzy/assets/js/eatzy.js',
        '/EATZY/components/com_eatzy/assets/js/booking.js' => 'components/com_eatzy/assets/js/booking.js',
        '/EATZY/images/foods/' => 'images/foods/'
    ];

    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    // Make sure tracker lookup form has clean action
    if ($key === 'track') {
        $html = str_replace('<input type="hidden" name="option" value="com_eatzy">', '', $html);
        $html = str_replace('<input type="hidden" name="view" value="track">', '', $html);
        $html = str_replace('class="eatzy-sim-controls"', 'id="eatzy-sim-controls" class="eatzy-sim-controls"', $html);
    }

    file_put_contents($p['out'], $html);
    echo " [OK] Updated " . basename($p['out']) . " (" . number_format(strlen($html)) . " bytes)\n";
}

echo "Joomla sync complete!\n";
