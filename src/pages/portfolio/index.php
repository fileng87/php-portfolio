<?php

/**
 * Portfolio 頁面模組
 */

// 設定頁面標題和樣式
$page_title = '作品集 - 鳳梨的個人網站';
$page_styles = [
    '/src/pages/portfolio/style.css',
    '/src/components/project-card/style.css'
];

// 使用 ob_start 捕獲頁面內容
ob_start();
// Define project data
$projects = [
    [
        'title' => 'snake',
        'description' => '貪吃蛇小遊戲',
        'github_link' => '#',
        'demo_link' => '#',
        'tags' => ['JavaScript', 'HTML Canvas', 'CSS'],
        'stars' => 15,
        'forks' => 3
    ],
    [
        'title' => 'rickroll-warning',
        'description' => '用來檢測瑞克搖的瀏覽器插件',
        'github_link' => '#',
        'demo_link' => null,
        'tags' => ['JavaScript'],
        'stars' => 5,
        'forks' => 1
    ],
    [
        'title' => 'portfolio',
        'description' => '個人網站',
        'github_link' => '#',
        'demo_link' => '#',
        'tags' => ['PHP', 'HTML', 'CSS', 'JavaScript'],
        'stars' => 1,
        'forks' => 0
    ],
];
?>
<div class="page-content portfolio-page">
    <!-- Terminal Header -->
    <div class="terminal-header portfolio-terminal-header animate-fade-in-up">
        <span class="prompt-icon">&lt;/&gt;</span> $ ls ./projects <span class="cursor"></span>
    </div>

    <div class="portfolio-grid">
        <?php foreach ($projects as $index => $project): ?>
            <?php
            $title = $project['title'] ?? '未命名專案';
            $description = $project['description'] ?? 'No description available.';
            $github_link = $project['github_link'] ?? '#';
            $demo_link = $project['demo_link'] ?? null;
            $tags = $project['tags'] ?? [];
            $stars = $project['stars'] ?? 0;
            $forks = $project['forks'] ?? 0;

            // Set the CSS variable inline for animation delay
            $style_attribute = 'style="--card-index: ' . $index . ';"';

            include __DIR__ . '/../../components/project-card/index.php';
            ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
$page_content = ob_get_clean();

// 載入佈局
include __DIR__ . '/../../layout/index.php';
?>