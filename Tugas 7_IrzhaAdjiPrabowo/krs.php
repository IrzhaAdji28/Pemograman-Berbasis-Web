<?php
require_once 'koneksi.php';
require_once 'mahasiswa.php';
require_once 'matakuliah.php';

function tambahKRS($mahasiswa_npm, $matakuliah_kodemk) {
    global $conn;
    $sql = "INSERT INTO krs (mahasiswa_npm, matakuliah_kodemk) VALUES (?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $mahasiswa_npm, $matakuliah_kodemk);
    
    return $stmt->execute();
}

function ambilSemuaKRS() {
    global $conn;
    $sql = "SELECT k.id, m.npm, m.nama as nama_mahasiswa, mk.kodemk, mk.nama as nama_matakuliah, mk.jumlah_sks 
            FROM krs k
            JOIN mahasiswa m ON k.mahasiswa_npm = m.npm
            JOIN matakuliah mk ON k.matakuliah_kodemk = mk.kodemk
            ORDER BY m.nama, mk.nama";
    
    $result = $conn->query($sql);
    
    $krs = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $krs[] = $row;
        }
    }
    
    return $krs;
}

function ambilKRSByID($id) {
    global $conn;
    $sql = "SELECT k.id, k.mahasiswa_npm, k.matakuliah_kodemk, m.nama as nama_mahasiswa, mk.nama as nama_matakuliah 
            FROM krs k
            JOIN mahasiswa m ON k.mahasiswa_npm = m.npm
            JOIN matakuliah mk ON k.matakuliah_kodemk = mk.kodemk
            WHERE k.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

function ambilKRSByMahasiswaNPM($npm) {
    global $conn;
    $sql = "SELECT k.id, m.npm, m.nama as nama_mahasiswa, mk.kodemk, mk.nama as nama_matakuliah, mk.jumlah_sks 
            FROM krs k
            JOIN mahasiswa m ON k.mahasiswa_npm = m.npm
            JOIN matakuliah mk ON k.matakuliah_kodemk = mk.kodemk
            WHERE m.npm = ?
            ORDER BY mk.nama";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $npm);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    $krs = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $krs[] = $row;
        }
    }
    
    return $krs;
}

function updateKRS($id, $mahasiswa_npm, $matakuliah_kodemk) {
    global $conn;
    $sql = "UPDATE krs SET mahasiswa_npm = ?, matakuliah_kodemk = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $mahasiswa_npm, $matakuliah_kodemk, $id);
    
    return $stmt->execute();
}

function hapusKRS($id) {
    global $conn;
    $sql = "DELETE FROM krs WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

function tampilkanKRS() {
    $dataKRS = ambilSemuaKRS();
    
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='thead-dark'>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Mata Kuliah</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
          </thead>
          <tbody>";
    
    $no = 1;
    foreach ($dataKRS as $krs) {
        echo "<tr>";
        echo "<td>" . $no . "</td>";
        echo "<td>" . $krs['nama_mahasiswa'] . "</td>";
        echo "<td>" . $krs['nama_matakuliah'] . "</td>";
        echo "<td><span style='color: #ff66b2;'>" . $krs['nama_mahasiswa'] . "</span> Mengambil Mata Kuliah <span style='color: #ff66b2;'>" . $krs['nama_matakuliah'] . "</span> (" . $krs['jumlah_sks'] . " SKS)</td>";
        echo "<td>
                <a href='?page=krs&action=edit&id=" . $krs['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                <a href='?page=krs&action=hapus&id=" . $krs['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus data?\")'>Hapus</a>
              </td>";
        echo "</tr>";
        $no++;
    }
    
    echo "</tbody></table>";
}

function formKRS($mode = 'tambah', $id = '') {
    $judul = ($mode == 'tambah') ? 'Tambah KRS' : 'Edit KRS';
    $data = ($mode == 'edit') ? ambilKRSByID($id) : null;
    
    echo "<h3>$judul</h3>";
    echo "<form method='post' action='?page=krs&action=save'>";
    
    if ($mode == 'edit') {
        echo "<input type='hidden' name='mode' value='edit'>";
        echo "<input type='hidden' name='id' value='" . $id . "'>";
    } else {
        echo "<input type='hidden' name='mode' value='tambah'>";
    }
    
    echo "<div class='form-group'>
            <label>Mahasiswa:</label>
            <select class='form-control' name='mahasiswa_npm' required>";
    
    $mahasiswa = ambilSemuaMahasiswa();
    echo "<option value=''>Pilih Mahasiswa</option>";
    foreach ($mahasiswa as $mhs) {
        $selected = ($mode == 'edit' && $data['mahasiswa_npm'] == $mhs['npm']) ? 'selected' : '';
        echo "<option value='" . $mhs['npm'] . "' $selected>" . $mhs['npm'] . " - " . $mhs['nama'] . "</option>";
    }
    echo "</select></div>";
    
    echo "<div class='form-group'>
            <label>Mata Kuliah:</label>
            <select class='form-control' name='matakuliah_kodemk' required>";
    
    $matakuliah = ambilSemuaMatakuliah();
    echo "<option value=''>Pilih Mata Kuliah</option>";
    foreach ($matakuliah as $mk) {
        $selected = ($mode == 'edit' && $data['matakuliah_kodemk'] == $mk['kodemk']) ? 'selected' : '';
        echo "<option value='" . $mk['kodemk'] . "' $selected>" . $mk['kodemk'] . " - " . $mk['nama'] . " (" . $mk['jumlah_sks'] . " SKS)</option>";
    }
    echo "</select></div>";
    
    echo "<button type='submit' class='btn btn-primary'>Simpan</button>
          <a href='?page=krs' class='btn btn-secondary'>Batal</a>
         </form>";
}

function prosesKRS() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mode = $_POST['mode'];
        $mahasiswa_npm = $_POST['mahasiswa_npm'];
        $matakuliah_kodemk = $_POST['matakuliah_kodemk'];
        
        if ($mode == 'tambah') {
            if (tambahKRS($mahasiswa_npm, $matakuliah_kodemk)) {
                echo "<div class='alert alert-success'>Data KRS berhasil ditambahkan</div>";
            } else {
                echo "<div class='alert alert-danger'>Gagal menambahkan data KRS</div>";
            }
        } else if ($mode == 'edit') {
            $id = $_POST['id'];
            if (updateKRS($id, $mahasiswa_npm, $matakuliah_kodemk)) {
                echo "<div class='alert alert-success'>Data KRS berhasil diperbarui</div>";
            } else {
                echo "<div class='alert alert-danger'>Gagal memperbarui data KRS</div>";
            }
        }
    }
    
    tampilkanKRS();
}

function prosesHapusKRS($id) {
    if (hapusKRS($id)) {
        echo "<div class='alert alert-success'>Data KRS berhasil dihapus</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data KRS</div>";
    }
    
    tampilkanKRS();
}
?>
