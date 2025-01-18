<?php
/**
 * Directory listing script with subfolder support
 *
 * @version v1.0.1
 * @author ...
 */
define('TIME_START', microtime(true));
const VERSION = 'v1.0.1';
const RUNNING_IN_CONSOLE = PHP_SAPI === 'cli';
ob_start();

if (RUNNING_IN_CONSOLE) {
    file_put_contents('php://stdout', 'Generating directory listing... ');
}

// Determine the current path from the URL or fallback to script directory
$base_path = getcwd();
$request_path = isset($_GET['path']) ? $_GET['path'] : '';
$path = realpath($base_path . DIRECTORY_SEPARATOR . $request_path);

// Validate the resolved path to prevent directory traversal attacks
if ($path === false || strpos($path, $base_path) !== 0) {
    http_response_code(403);
    die('Access denied.');
}

// Path label can be overridden by a file named .dl-pathlabel
$pathlabel = file_exists($path . DIRECTORY_SEPARATOR . '.dl-pathlabel') 
    ? file_get_contents($path . DIRECTORY_SEPARATOR . '.dl-pathlabel') 
    : $path;

$img = [ /* Same as before */ ];

function run($path) {
    global $img;
    foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $file) {
        // Ignore dotfiles
        if (substr(basename($file), 0, 1) === '.') {
            continue;
        }
        makeRow($file, $path);
    }
}

function makeRow($file, $path) {
    global $img;
    $filename = basename($file) . (is_dir($file) ? '/' : '');
    $url_path = htmlspecialchars($_GET['path'] ?? '') . '/' . urlencode(basename($file));
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $size = is_dir($file) ? ' - ' : formatFileSize(filesize($file));
    $date = date('Y-m-d H:i', filemtime($file));
    $time = date('c', filemtime($file));

    $icon = $img['unknown.gif'];
    $alt = '[   ]';
    if (is_dir($file)) {
        $icon = $img['folder.gif'];
        $alt = '[DIR]';
    } elseif (is_file($file)) {
        // Determine icon by file type
        // ...
    }
    $row = <<<HTML
<tr>
<td valign="top"><img src="$icon" alt="$alt"></td>
<td><a href="?path=$url_path">$filename</a></td>
<td align="right"><time datetime="$time">$date</time></td>
<td align="right">$size</td>
<td>&nbsp;</td>
</tr>
HTML;
    echo '        '. str_replace(PHP_EOL, '', $row) . PHP_EOL;
}

function formatFileSize($int) {
    // Same as before
}

function getAddress() {
    // Same as before
}
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 3.2 Final//EN'>
<html lang="en"><head><title>Index of <?php echo(htmlspecialchars($pathlabel))?></title><meta name="robots" content="noindex"></head><body>
<h1>Index of <?php echo(htmlspecialchars($pathlabel))?></h1>
<table>
    <thead><tr><th valign="top"><img src="<?php echo $img['blank.gif'] ?>" alt="[ICO]"><th>Name</th><th>Last modified</th><th>Size</th><th>Description</th></tr><tr><th colspan="5"><hr></th></tr></thead>
    <tbody>
    <?php if ($path !== $base_path): ?>
    <tr><td valign="top"><img src="<?php echo $img['back.gif'] ?>" alt="[PARENTDIR]"></td><td><a href="?path=<?php echo urlencode(dirname($_GET['path'] ?? '')) ?>">Parent Directory</a></td><td>&nbsp;</td><td align="right"> - </td><td>&nbsp;</td></tr>
    <?php endif; ?>
    <?php run($path) ?>
    </tbody>
    <tfoot><tr><th colspan="5"><hr></th></tr></tfoot>
</table>
<address><?php echo getAddress() ?></address>
</body></html>
<?php
file_put_contents('index.html', (RUNNING_IN_CONSOLE ? ob_get_clean() : ob_get_flush()));

if (RUNNING_IN_CONSOLE) {
    file_put_contents('php://stdout', 'Done! Finished in ' . number_format((microtime(true) - TIME_START) * 1000, 2) .'ms.' . PHP_EOL);
}
