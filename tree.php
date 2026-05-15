<?php
/**
 *  – Directory Tree Viewer
 * Place this file anywhere inside bizzykart/ and open in browser.
 * Shows full folder + file structure.
 */

$root = dirname(__FILE__);
$rootName = basename($root);

function buildTree(string $dir, string $prefix = ''): string
{
    $output  = '';
    $items   = scandir($dir);
    $filtered = array_values(array_filter($items, fn($i) => $i !== '.' && $i !== '..'));
    $total   = count($filtered);

    foreach ($filtered as $index => $item) {
        $path     = $dir . DIRECTORY_SEPARATOR . $item;
        $isLast   = ($index === $total - 1);
        $connector = $isLast ? '└── ' : '├── ';
        $childPfx  = $isLast ? '    ' : '│   ';
        $isDir    = is_dir($path);
        $icon     = $isDir ? '📁' : '📄';

        $output .= $prefix . $connector . $icon . ' ' . $item . "\n";

        if ($isDir) {
            $output .= buildTree($path, $prefix . $childPfx);
        }
    }

    return $output;
}

$tree = '📁 ' . $rootName . "\n" . buildTree($root);

// Count totals
$totalDirs  = iterator_count(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    )
);
$totalFiles = iterator_count(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    )
) - $totalDirs;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Site – Directory Tree</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; padding: 30px 20px; }
.wrap { max-width: 860px; margin: 0 auto; }
.head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
h1 { font-size: 20px; font-weight: 700; color: #f97316; }
.meta { font-size: 13px; color: #64748b; }
.meta span { color: #94a3b8; font-weight: 600; }
.box { background: #1e293b; border: 1px solid #334155; border-radius: 12px; overflow: hidden; }
.box-head { display: flex; align-items: center; justify-content: space-between; padding: 13px 18px; border-bottom: 1px solid #334155; background: #162032; }
.box-head p { font-size: 13px; color: #64748b; }
.copy-btn { padding: 6px 16px; background: #f97316; color: #fff; border: none; border-radius: 7px; font-size: 12.5px; font-weight: 700; cursor: pointer; transition: background .15s; }
.copy-btn:hover { background: #ea580c; }
pre { padding: 22px 20px; font-family: 'Courier New', monospace; font-size: 13px; line-height: 1.7; white-space: pre; overflow-x: auto; color: #cbd5e1; }
pre .dir  { color: #93c5fd; }
pre .file { color: #cbd5e1; }
.note { margin-top: 16px; font-size: 12.5px; color: #475569; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="head">
    <h1>Site – Directory Tree</h1>
    <div class="meta">
      Scanned from: <span><?= htmlspecialchars($root) ?></span>
    </div>
  </div>

  <div class="box">
    <div class="box-head">
      <p>Root: <strong style="color:#e2e8f0"><?= htmlspecialchars($rootName) ?>/</strong>
         &nbsp;·&nbsp; Folders+Files: <strong style="color:#f97316"><?= $totalDirs + $totalFiles ?></strong>
      </p>
      <button class="copy-btn" onclick="copyTree()">Copy Tree</button>
    </div>
    <pre id="tree"><?= htmlspecialchars($tree) ?></pre>
  </div>

  <div class="note">⚠️ Delete this file from your server after use.</div>
</div>

<script>
function copyTree() {
  var text = document.getElementById('tree').innerText;
  navigator.clipboard.writeText(text).then(function() {
    var btn = document.querySelector('.copy-btn');
    btn.textContent = 'Copied!';
    setTimeout(function() { btn.textContent = 'Copy Tree'; }, 2000);
  });
}
</script>
</body>
</html>