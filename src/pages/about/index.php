<?php

/**
 * About 頁面模組
 */

// 設定頁面標題和樣式
$page_title = '關於我 - 鳳梨的個人網站';
$page_styles = ['/src/pages/about/style.css'];

// 使用 ob_start 捕獲頁面內容
ob_start();
?>
<div class="page-content about-page">
    <div class="terminal-header animate-fade-in-up">
        <span>&gt;_</span> $ whoami <span class="cursor"></span>
    </div>

    <div class="about-grid">
        <!-- Avatar Card -->
        <div class="content-card avatar-card animate-fade-in-up" style="--card-index: 0;">
            <h3 class="card-title">./avatar.png</h3>
            <img src="https://www.gravatar.com/avatar/294e918fb7535a26528daa36902007b2?s=400" alt="我的頭像" class="profile-avatar" />
        </div>

        <!-- Personal Info Card -->
        <div class="content-card info-card animate-fade-in-up" style="--card-index: 1;">
            <h3 class="card-title">./personal_info.txt</h3>
            <p>
                我喜歡研究奇奇怪怪的東西，是真的奇奇怪怪的東西，像是會嘴砲你的機器人之類的
                <br />
                爆肝趕專題真的會讓我裂開
                <br />
                喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔喔 喔喔喔喔喔喔 喔
            </p>
        </div>

        <!-- Skills Card -->
        <div class="content-card skills-card animate-fade-in-up" style="--card-index: 2;">
            <h3 class="card-title">./skills.md</h3>
            <div class="skills-columns">
                <div>
                    <h4>Frontend:</h4>
                    <ul>
                        <li>React</li>
                        <li>Next.js</li>
                        <li>TypeScript</li>
                        <li>Tailwind CSS</li>
                    </ul>
                </div>
                <div>
                    <h4>Backend:</h4>
                    <ul>
                        <li>Express</li>
                        <li>NestJS</li>
                        <li>FastAPI</li>
                        <li>PostgreSQL</li>
                        <li>MongoDB</li>
                    </ul>
                </div>
            </div>
            <div>
                <h4>Tools:</h4>
                <ul>
                    <li>Git</li>
                    <li>VS Code</li>
                    <li>Docker</li>
                </ul>
            </div>
        </div>

        <!-- GitHub Stats Card (Placeholder) -->
        <div class="content-card stats-card animate-fade-in-up" style="--card-index: 3;">
            <h3 class="card-title">./github_stats.md</h3>
            <ul class="stats-list">
                <li><span>Total Stars</span><span>6</span></li>
                <li><span>Total Commits</span><span>411</span></li>
                <li><span>Total PRs</span><span>8</span></li>
                <li><span>Total Issues</span><span>7</span></li>
                <li><span>Contributed to (last year)</span><span>0</span></li>
            </ul>
        </div>

        <!-- Coding Time Card (Placeholder) -->
        <div class="content-card stats-card animate-fade-in-up" style="--card-index: 4;">
            <h3 class="card-title">./coding_time.md</h3>
            <ul class="stats-list">
                <li><span>Total Time</span><span>1,125h 4m</span></li>
                <li><span>Daily Average</span><span>2h 41m</span></li>
                <li><span>Active Days</span><span>418d</span></li>
                <li><span>TypeScript</span><span>474h 58m</span></li>
                <li><span>C#</span><span>165h 32m</span></li>
                <li><span>JavaScript</span><span>123h 15m</span></li>
            </ul>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();

// 載入佈局
include __DIR__ . '/../../layout/index.php';
?>