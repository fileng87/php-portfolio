<?php

/**
 * Project Card Component
 *
 * Expects the following variables to be set before inclusion:
 * - $title (string): Project title
 * - $description (string): Project description
 * - $github_link (string): URL to the GitHub repository
 * - $demo_link (string|null): URL to the live demo
 * - $tags (array|null): Array of technology/language strings
 * - $stars (int|null): Star count (optional)
 * - $forks (int|null): Fork count (optional)
 */

// Default values
$title = $title ?? '未命名專案';
$description = $description ?? 'No description available.';
$github_link = $github_link ?? '#';
$demo_link = $demo_link ?? null;
$tags = $tags ?? [];
$stars = $stars ?? 0;
$forks = $forks ?? 0;
$style_attribute = $style_attribute ?? '';

?>

<div class="content-card project-card animate-fade-in-up" <?php echo $style_attribute; ?>>
    <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
    <div class="card-body">
        <p class="project-description"><?php echo htmlspecialchars($description); ?></p>
        <div class="project-stats">
            <span title="Stars">⭐ <?php echo $stars; ?></span>
            <span title="Forks">🔱 <?php echo $forks; ?></span>
        </div>
        <div class="project-tags">
            <?php $limited_tags = array_slice($tags, 0, 4); // Limit tags inside 
            ?>
            <?php foreach ($limited_tags as $tag): ?>
                <span class="tech-tag"><?php echo htmlspecialchars($tag); ?></span>
            <?php endforeach; ?>
            <?php if (count($tags) > 4): ?>
                <span class="more-tags-indicator">...</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="project-links">
        <a href="<?php echo htmlspecialchars($github_link); ?>" rel="noopener noreferrer">GitHub</a>
        <?php if ($demo_link): ?>
            <a href="<?php echo htmlspecialchars($demo_link); ?>" rel="noopener noreferrer" class="live-demo-link">
                <span class="icon-globe">🌍</span> Live Demo
            </a>
        <?php endif; ?>
    </div>
</div>