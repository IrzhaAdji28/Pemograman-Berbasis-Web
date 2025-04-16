<?php
// Mulai session
session_start();

// Inisialisasi counter nomor penerbangan jika belum ada
if (!isset($_SESSION['counter_penerbangan'])) {
    $_SESSION['counter_penerbangan'] = 1;
}

// Inisialisasi array riwayat penerbangan jika belum ada
if (!isset($_SESSION['riwayat_penerbangan'])) {
    $_SESSION['riwayat_penerbangan'] = [];
}

// Data bandara dan pajak
$bandara_asal = [
    ["nama" => "Soekarno Hatta", "pajak" => 65000],
    ["nama" => "Husein Sastranegara", "pajak" => 50000],
    ["nama" => "Abdul Rachman Saleh", "pajak" => 40000],
    ["nama" => "Juanda", "pajak" => 30000]
];

$bandara_tujuan = [
    ["nama" => "Ngurah Rai", "pajak" => 85000],
    ["nama" => "Hasanuddin", "pajak" => 70000],
    ["nama" => "Inanwatan", "pajak" => 90000],
    ["nama" => "Sultan Iskandar Muda", "pajak" => 60000]
];

// Urutkan bandara berdasarkan nama
usort($bandara_asal, function($a, $b) {
    return strcmp($a["nama"], $b["nama"]);
});

usort($bandara_tujuan, function($a, $b) {
    return strcmp($a["nama"], $b["nama"]);
});

// Proses form submission
$data_penerbangan = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["maskapai"]) && 
    isset($_POST["bandara_asal"]) && isset($_POST["bandara_tujuan"]) && isset($_POST["harga_tiket"])) {
    
    $maskapai = $_POST["maskapai"];
    $index_asal = (int)$_POST["bandara_asal"];
    $index_tujuan = (int)$_POST["bandara_tujuan"];
    $harga_tiket = floatval($_POST["harga_tiket"]);
    
    // Validasi indeks
    if (isset($bandara_asal[$index_asal]) && isset($bandara_tujuan[$index_tujuan])) {
        // Hitung pajak
        $pajak_asal = $bandara_asal[$index_asal]["pajak"];
        $pajak_tujuan = $bandara_tujuan[$index_tujuan]["pajak"];
        $total_pajak = $pajak_asal + $pajak_tujuan;
        
        // Hitung total harga
        $total_harga = $harga_tiket + $total_pajak;
        
        // Generate nomor penerbangan
        $nomor_penerbangan = "PENERBANGAN" . str_pad($_SESSION['counter_penerbangan']++, 4, "0", STR_PAD_LEFT);
        
        // Simpan data penerbangan
        $data_penerbangan = [
            "nomor" => $nomor_penerbangan,
            "tanggal" => date("d-m-Y"),
            "maskapai" => $maskapai,
            "asal" => $bandara_asal[$index_asal]["nama"],
            "tujuan" => $bandara_tujuan[$index_tujuan]["nama"],
            "harga_tiket" => $harga_tiket,
            "pajak" => $total_pajak,
            "total_harga" => $total_harga
        ];
        
        // Tambahkan ke riwayat penerbangan
        $_SESSION['riwayat_penerbangan'][] = $data_penerbangan;
    }
}

// Hapus riwayat jika diminta
if (isset($_GET['hapus_riwayat'])) {
    $_SESSION['riwayat_penerbangan'] = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fungsi untuk format mata uang
function formatRupiah($jumlah) {
    return "Rp " . number_format($jumlah, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pendaftaran Penerbangan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Sistem Pendaftaran Penerbangan</h1>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="maskapai">Nama Maskapai:</label>
                <input type="text" id="maskapai" name="maskapai" required>
            </div>
            
            <div class="form-group">
                <label for="bandara_asal">Bandara Asal:</label>
                <select id="bandara_asal" name="bandara_asal" required>
                    <option value="">-- Pilih Bandara Asal --</option>
                    <?php foreach ($bandara_asal as $index => $bandara): ?>
                        <option value="<?= $index ?>"><?= $bandara['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="bandara_tujuan">Bandara Tujuan:</label>
                <select id="bandara_tujuan" name="bandara_tujuan" required>
                    <option value="">-- Pilih Bandara Tujuan --</option>
                    <?php foreach ($bandara_tujuan as $index => $bandara): ?>
                        <option value="<?= $index ?>"><?= $bandara['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="harga_tiket">Harga Tiket:</label>
                <input type="number" id="harga_tiket" name="harga_tiket" min="0" required>
            </div>
            
            <button type="submit">Daftarkan Penerbangan</button>
        </form>
        
        <?php if ($data_penerbangan): ?>
        <div class="hasil">
            <h2>Detail Penerbangan</h2>
            <table>
                <tr>
                    <th>Nomor</th>
                    <td><?= $data_penerbangan["nomor"] ?></td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td><?= $data_penerbangan["tanggal"] ?></td>
                </tr>
                <tr>
                    <th>Nama Maskapai</th>
                    <td><?= $data_penerbangan["maskapai"] ?></td>
                </tr>
                <tr>
                    <th>Asal Penerbangan</th>
                    <td><?= $data_penerbangan["asal"] ?></td>
                </tr>
                <tr>
                    <th>Tujuan Penerbangan</th>
                    <td><?= $data_penerbangan["tujuan"] ?></td>
                </tr>
                <tr>
                    <th>Harga Tiket</th>
                    <td><?= formatRupiah($data_penerbangan["harga_tiket"]) ?></td>
                </tr>
                <tr>
                    <th>Pajak</th>
                    <td><?= formatRupiah($data_penerbangan["pajak"]) ?></td>
                </tr>
                <tr>
                    <th>Total Harga Tiket</th>
                    <td><?= formatRupiah($data_penerbangan["total_harga"]) ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
        
    </div>
</body>
</html>