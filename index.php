<?php
// Koneksi
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_barang";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Proses Tambah Data
if (isset($_POST['tambah'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];

    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $folder = "uploads/";
    move_uploaded_file($tmp, $folder . $foto);

    $query = "INSERT INTO barang (kode_barang, nama_barang, harga_barang, jumlah, foto)
              VALUES ('$kode', '$nama', '$harga', '$jumlah', '$foto')";
    mysqli_query($conn, $query);
    header("Location: index.php");
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $id");
    header("Location: index.php");
}

// Proses Edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];

    if ($_FILES['foto']['name'] != "") {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($tmp, "uploads/" . $foto);
        $update = "UPDATE barang SET kode_barang='$kode', nama_barang='$nama', harga_barang='$harga',
                    jumlah='$jumlah', foto='$foto' WHERE id_barang=$id";
    } else {
        $update = "UPDATE barang SET kode_barang='$kode', nama_barang='$nama', harga_barang='$harga',
                    jumlah='$jumlah' WHERE id_barang=$id";
    }

    mysqli_query($conn, $update);
    header("Location: index.php");
}

// Ambil data untuk form edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = $id"));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Data Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h2 class="mb-4">Data Barang</h2>

    <!-- Form Tambah/Edit -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="id" value="<?= $edit ? $edit['id_barang'] : '' ?>">
        <div class="row g-2">
            <div class="col-md-2"><input type="text" name="kode" class="form-control" placeholder="Kode" value="<?= $edit['kode_barang'] ?? '' ?>" required></div>
            <div class="col-md-2"><input type="text" name="nama" class="form-control" placeholder="Nama Barang" value="<?= $edit['nama_barang'] ?? '' ?>" required></div>
            <div class="col-md-2"><input type="number" name="harga" class="form-control" placeholder="Harga" value="<?= $edit['harga_barang'] ?? '' ?>" required></div>
            <div class="col-md-2"><input type="number" name="jumlah" class="form-control" placeholder="Jumlah" value="<?= $edit['jumlah'] ?? '' ?>" required></div>
            <div class="col-md-2"><input type="file" name="foto" class="form-control" <?= $edit ? '' : 'required' ?>></div>
            <div class="col-md-2">
                <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>" class="btn btn-<?= $edit ? 'warning' : 'primary' ?> w-100">
                    <?= $edit ? 'Update' : 'Tambah' ?>
                </button>
            </div>
        </div>
    </form>

    <!-- Tabel Data -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $data = mysqli_query($conn, "SELECT * FROM barang ORDER BY id_barang DESC");
        while ($row = mysqli_fetch_assoc($data)) {
        ?>
            <tr>
                <td><?= $row['kode_barang'] ?></td>
                <td><?= $row['nama_barang'] ?></td>
                <td><?= $row['harga_barang'] ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td><img src="uploads/<?= $row['foto'] ?>" width="60"></td>
                <td>
                    <a href="?edit=<?= $row['id_barang'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?hapus=<?= $row['id_barang'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-sm btn-danger">Hapus</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</body>
</html>
