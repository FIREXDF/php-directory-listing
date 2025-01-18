<?php
// Define constants and variables
define('TIME_START', microtime(true));
const VERSION = 'v1.0.0-rc.5';
const RUNNING_IN_CONSOLE = PHP_SAPI === 'cli';
ob_start();

// Function to scan directories recursively
function run() {
    global $path;
    $excluded_files = ['LICENSE', 'directory-listing.php']; // Files to exclude

    foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $file) {
        // Ignore dotfiles and excluded files
        if (substr(basename($file), 0, 1) === '.' || in_array(basename($file), $excluded_files)) {
            continue;
        }
        makeRow($file);
    }
}

// Function to generate a row for the file or directory
function makeRow($file, $parentDir) {
    global $img;

    $filename = basename($file) . (is_dir($file) ? '/' : '');
    $relativePath = $parentDir === '.' ? $filename : ltrim($parentDir . '/' . $filename, './');
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $size = is_dir($file) ? '-' : formatFileSize(filesize($file));
    $date = date('Y-m-d H:i', filemtime($file));
    $time = date('c', filemtime($file));

    $icon = $img['unknown.gif'];
    $alt = '[   ]';

    if (is_dir($file)) {
        $icon = $img['folder.gif'];
        $alt = '[DIR]';
    } elseif (is_file($file)) {
        // Assign appropriate icons based on file type
        if (in_array($ext, ['gif', 'jpg', 'jpeg', 'png', 'bmp'])) {
            $icon = $img['image2.gif'];
            $alt = '[IMG]';
        } elseif (in_array($ext, ['mp4', 'avi', 'mkv', 'mov', 'flv'])) {
            $icon = $img['movie.gif'];
            $alt = '[VID]';
        } elseif (in_array($ext, ['txt', 'php', 'html', 'css', 'js'])) {
            $icon = $img['text.gif'];
            $alt = '[TXT]';
        }
    }

    // Create the table row
    echo <<<HTML
<tr>
<td valign="top"><img src="$icon" alt="$alt"></td>
<td><a href="$relativePath">$relativePath</a></td>
<td align="right"><time datetime="$time">$date</time></td>
<td align="right">$size</td>
<td>&nbsp;</td>
</tr>
HTML;
}

// Function to format file size
function formatFileSize($size) {
    if ($size < 1024) {
        return $size . ' B';
    } elseif ($size < 1048576) {
        return round($size / 1024, 2) . ' kB';
    } elseif ($size < 1073741824) {
        return round($size / 1048576, 2) . ' MB';
    } else {
        return round($size / 1073741824, 2) . ' GB';
    }
}

// Output HTML header
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Directory Listing</title>
    <meta name="robots" content="noindex">
</head>
<body>
<h1>Directory Listing</h1>
<table>
    <thead>
    <tr>
        <th valign="top"><img src="<?php echo $img['blank.gif'] ?>" alt="[ICO]"></th>
        <th>Name</th>
        <th>Last modified</th>
        <th>Size</th>
        <th>Description</th>
    </tr>
    <tr><th colspan="5"><hr></th></tr>
    </thead>
    <tbody>
    <?php run(); ?>
    </tbody>
    <tfoot>
    <tr><th colspan="5"><hr></th></tr>
    </tfoot>
</table>
</body>
</html>
<?php
// End of script
file_put_contents('index.html', ob_get_clean());
?>
