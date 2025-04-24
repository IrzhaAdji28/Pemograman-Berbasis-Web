<?php
require_once 'koneksi.php';

function tambahMatakuliah($kodemk, $nama, $jumlah_sks) {
    global $conn;
    $sql = "INSERT INTO matakuliah (kodemk, nama, jumlah_sks) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $kodemk, $nama, $jumlah_sks);
    return $stmt->execute();
}

function ambilSemuaMatakuliah() {
    global $conn;
    $result = $conn->query("SELECT * FROM matakuliah");
    $matakuliah = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $matakuliah[] = $row;
        }
    }
    return $matakuliah;
}

function ambilMatakuliahByKode($kodemk) {
    global $conn;
    $sql = "SELECT * FROM matakuliah WHERE kodemk = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kodemk);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
}

function updateMatakuliah($kodemk, $nama, $jumlah_sks) {
    global $conn;
    $sql = "UPDATE matakuliah SET nama = ?, jumlah_sks = ? WHERE kodemk = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $nama, $jumlah_sks, $kodemk);
    return $stmt->execute();
}

function hapusMatakuliah($kodemk) {
    global $conn;
    $sql = "DELETE FROM matakuliah WHERE kodemk = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kodemk);
    return $stmt->execute();
}

function tampilkanMatakuliah() {
    $matakuliah = ambilSemuaMatakuliah();
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='thead-dark'><tr><th>Kode</th><th>Nama</th><th>SKS</th><th>Aksi</th></tr></thead><tbody>";
    foreach ($matakuliah as $mk) {
        echo "<tr>";
        echo "<td>{$mk['kodemk']}</td>";
        echo "<td>{$mk['nama']}</td>";
        echo "<td>{$mk['jumlah_sks']}</td>";
        echo "<td><a href='?page=matakuliah&action=edit&kodemk={$mk['kodemk']}' class='btn btn-warning btn-sm'>Edit</a>
              <a href='?page=matakuliah&action=hapus&kodemk={$mk['kodemk']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus data?\")'>Hapus</a></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}

function formMatakuliah($mode = 'tambah', $kodemk = '') {
    $judul = ($mode == 'tambah') ? 'Tambah Mata Kuliah' : 'Edit Mata Kuliah';
    $data = ($mode == 'edit') ? ambilMatakuliahByKode($kodemk) : null;

    echo "<h3>$judul</h3>";
    echo "<form method='post' action='?page=matakuliah&action=save'>";
    if ($mode == 'edit') {
        echo "<input type='hidden' name='mode' value='edit'>";
        echo "<div class='form-group'><label>Kode Mata Kuliah:</label><input type='text' class='form-control' name='kodemk' value='{$data['kodemk']}' readonly></div>";
    } else {
        echo "<input type='hidden' name='mode' value='tambah'>";
        echo "<div class='form-group'><label>Kode Mata Kuliah:</label><input type='text' class='form-control' name='kodemk' required></div>";
    }
    echo "<div class='form-group'><label>Nama Mata Kuliah:</label><input type='text' class='form-control' name='nama' value='" . ($mode == 'edit' ? $data['nama'] : '') . "' required></div>";
    echo "<div class='form-group'><label>Jumlah SKS:</label><input type='number' class='form-control' name='jumlah_sks' value='" . ($mode == 'edit' ? $data['jumlah_sks'] : '') . "' required min='1' max='6'></div>";
    echo "<button type='submit' class='btn btn-primary'>Simpan</button><a href='?page=matakuliah' class='btn btn-secondary'>Batal</a></form>";
}

function prosesMatakuliah() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mode = $_POST['mode'];
        $kodemk = $_POST['kodemk'];
        $nama = $_POST['nama'];
        $jumlah_sks = $_POST['jumlah_sks'];

        if ($mode == 'tambah') {
            echo tambahMatakuliah($kodemk, $nama, $jumlah_sks)
                ? "<div class='alert alert-success'>Data mata kuliah berhasil ditambahkan</div>"
                : "<div class='alert alert-danger'>Gagal menambahkan data mata kuliah</div>";
        } elseif ($mode == 'edit') {
            echo updateMatakuliah($kodemk, $nama, $jumlah_sks)
                ? "<div class='alert alert-success'>Data mata kuliah berhasil diperbarui</div>"
                : "<div class='alert alert-danger'>Gagal memperbarui data mata kuliah</div>";
        }
    }
    tampilkanMatakuliah();
}

function prosesHapusMatakuliah($kodemk) {
    echo hapusMatakuliah($kodemk)
        ? "<div class='alert alert-success'>Data mata kuliah berhasil dihapus</div>"
        : "<div class='alert alert-danger'>Gagal menghapus data mata kuliah</div>";
    tampilkanMatakuliah();
}
?>