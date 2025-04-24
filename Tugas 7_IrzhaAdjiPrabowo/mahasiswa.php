<?php
require_once 'koneksi.php';

function tambahMahasiswa($npm, $nama, $jurusan, $alamat) {
    global $conn;
    $sql = "INSERT INTO mahasiswa (npm, nama, jurusan, alamat) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $npm, $nama, $jurusan, $alamat);
    return $stmt->execute();
}

function ambilSemuaMahasiswa() {
    global $conn;
    $result = $conn->query("SELECT * FROM mahasiswa");
    $mahasiswa = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mahasiswa[] = $row;
        }
    }
    return $mahasiswa;
}

function ambilMahasiswaByNPM($npm) {
    global $conn;
    $sql = "SELECT * FROM mahasiswa WHERE npm = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $npm);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
}

function updateMahasiswa($npm, $nama, $jurusan, $alamat) {
    global $conn;
    $sql = "UPDATE mahasiswa SET nama = ?, jurusan = ?, alamat = ? WHERE npm = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama, $jurusan, $alamat, $npm);
    return $stmt->execute();
}

function hapusMahasiswa($npm) {
    global $conn;
    $sql = "DELETE FROM mahasiswa WHERE npm = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $npm);
    return $stmt->execute();
}

function tampilkanMahasiswa() {
    $mahasiswa = ambilSemuaMahasiswa();
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='thead-dark'>
            <tr>
                <th>NPM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
          </thead>
          <tbody>";
    foreach ($mahasiswa as $mhs) {
        echo "<tr>";
        echo "<td>{$mhs['npm']}</td>";
        echo "<td>{$mhs['nama']}</td>";
        echo "<td>{$mhs['jurusan']}</td>";
        echo "<td>{$mhs['alamat']}</td>";
        echo "<td>
                <a href='?page=mahasiswa&action=edit&npm={$mhs['npm']}' class='btn btn-warning btn-sm'>Edit</a>
                <a href='?page=mahasiswa&action=hapus&npm={$mhs['npm']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus data?\")'>Hapus</a>
              </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}

function formMahasiswa($mode = 'tambah', $npm = '') {
    $judul = ($mode == 'tambah') ? 'Tambah Mahasiswa' : 'Edit Mahasiswa';
    $data = ($mode == 'edit') ? ambilMahasiswaByNPM($npm) : null;
    echo "<h3>$judul</h3>";
    echo "<form method='post' action='?page=mahasiswa&action=save'>";
    if ($mode == 'edit') {
        echo "<input type='hidden' name='mode' value='edit'>";
        echo "<div class='form-group'>
                <label>NPM:</label>
                <input type='text' class='form-control' name='npm' value='{$data['npm']}' readonly>
              </div>";
    } else {
        echo "<input type='hidden' name='mode' value='tambah'>";
        echo "<div class='form-group'>
                <label>NPM:</label>
                <input type='text' class='form-control' name='npm' required>
              </div>";
    }
    echo "<div class='form-group'>
            <label>Nama:</label>
            <input type='text' class='form-control' name='nama' value='" . ($mode == 'edit' ? $data['nama'] : '') . "' required>
          </div>";
    echo "<div class='form-group'>
            <label>Jurusan:</label>
            <select class='form-control' name='jurusan' required>
                <option value=''>Pilih Jurusan</option>
                <option value='Teknik Informatika'" . ($mode == 'edit' && $data['jurusan'] == 'Teknik Informatika' ? ' selected' : '') . ">Teknik Informatika</option>
                <option value='Sistem Operasi'" . ($mode == 'edit' && $data['jurusan'] == 'Sistem Operasi' ? ' selected' : '') . ">Sistem Operasi</option>
            </select>
          </div>";
    echo "<div class='form-group'>
            <label>Alamat:</label>
            <textarea class='form-control' name='alamat' rows='3' required>" . ($mode == 'edit' ? $data['alamat'] : '') . "</textarea>
          </div>";
    echo "<button type='submit' class='btn btn-primary'>Simpan</button>
          <a href='?page=mahasiswa' class='btn btn-secondary'>Batal</a>
         </form>";
}

function prosesMahasiswa() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mode = $_POST['mode'];
        $npm = $_POST['npm'];
        $nama = $_POST['nama'];
        $jurusan = $_POST['jurusan'];
        $alamat = $_POST['alamat'];
        if ($mode == 'tambah') {
            echo tambahMahasiswa($npm, $nama, $jurusan, $alamat) ? "<div class='alert alert-success'>Data mahasiswa berhasil ditambahkan</div>" : "<div class='alert alert-danger'>Gagal menambahkan data mahasiswa</div>";
        } elseif ($mode == 'edit') {
            echo updateMahasiswa($npm, $nama, $jurusan, $alamat) ? "<div class='alert alert-success'>Data mahasiswa berhasil diperbarui</div>" : "<div class='alert alert-danger'>Gagal memperbarui data mahasiswa</div>";
        }
    }
    tampilkanMahasiswa();
}

function prosesHapusMahasiswa($npm) {
    echo hapusMahasiswa($npm) ? "<div class='alert alert-success'>Data mahasiswa berhasil dihapus</div>" : "<div class='alert alert-danger'>Gagal menghapus data mahasiswa</div>";
    tampilkanMahasiswa();
}
?>
