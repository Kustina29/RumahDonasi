<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$currentUserLevel = $_SESSION['level_user'] ?? 'Guest';
$currentUserId = $_SESSION['id_user'] ?? 0;
$currentUsername = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="css/index.css">
	<title>Rumah Donasi - Dashboard</title>
</head>
<section id="sidebar">
		<a href="#" class="brand"><i class='bx bxs-home icon'></i>Rumah Donasi</a>
		<ul class="side-menu">
			<li><a href="#" class="active" data-page="dashboard"><i class='bx bxs-dashboard icon' ></i> Dashboard</a></li>
			<li class="divider" data-text="main">Main</li>

			<li data-level="Admin"><a href="#" data-page="donatur"><i class='bx bxs-donate-heart icon' ></i> Donatur</a></li>
			<li data-level="Admin"><a href="#" data-page="distributor"><i class='bx bxs-group icon' ></i> Distributor</a></li>
			<li data-level="Admin"><a href="#" data-page="barang-donasi"><i class='bx bxs-box icon' ></i> Barang Donasi</a></li>
			<li data-level="Admin"><a href="#" data-page="permintaan-distribusi-admin"><i class='bx bxs-share-alt icon' ></i> Permintaan Distribusi</a></li>
			<li data-level="Admin"><a href="#" data-page="konfirmasi-penyelesaian"><i class='bx bx-check-shield icon' ></i> Konfirmasi Penyelesaian</a></li>
			<li data-level="Admin"><a href="#" data-page="histori-distribusi-donasi-admin"><i class='bx bxs-book-alt icon' ></i> Histori Distribusi Donasi</a></li>
			<li data-level="Admin"><a href="#" data-page="pesan"><i class='bx bxs-message icon' ></i> Pesan</a></li>
			
			<li data-level="Donatur"><a href="#" data-page="tambah-donasi-donatur"><i class='bx bxs-add-to-queue icon' ></i> Tambah Donasi</a></li>
			<li data-level="Donatur"><a href="#" data-page="histori-donasi-donatur"><i class='bx bxs-book-alt icon' ></i> Histori Donasi</a></li>
			<li data-level="Donatur"><a href="#" data-page="riwayat-distribusi-donatur"><i class='bx bx-line-chart icon' ></i> Riwayat Distribusi</a></li>

			<li data-level="Distributor"><a href="#" data-page="lihat-barang-tersedia"><i class='bx bx-package icon' ></i> Lihat Barang Tersedia</a></li>
			<li data-level="Distributor"><a href="#" data-page="permintaan-distribusi-distributor"><i class='bx bxs-share-alt icon' ></i> Status Permintaan</a></li>
			<li data-level="Distributor"><a href="#" data-page="histori-distribusi-distributor"><i class='bx bxs-book-alt icon' ></i> Histori Distribusi</a></li>
		</ul>
		<div class="ads">
			<div class="wrapper">
				<p>Selamat Datang, <span><?php echo htmlspecialchars($currentUsername); ?></span></p>
			</div>
		</div>
	</section>
	<section id="content">
		<nav>
			<i class='bx bx-menu toggle-sidebar' ></i>
			<form action="#">
				<div class="form-group">
					<input type="text" placeholder="Search...">
					<i class='bx bx-search icon' ></i>
				</div>
			</form>
			<span class="divider"></span>
			<div class="profile">
				<img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NHx8cGVvcGxlfHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="">
				<ul class="profile-link">
					<li><a href="#"><i class='bx bxs-user-circle icon' ></i> Profile</a></li>
					<li><a href="#"><i class='bx bxs-cog' ></i> Settings</a></li>
					<li><a href="php/logout.php"><i class='bx bxs-log-out-circle' ></i> Logout</a></li>
				</ul>
			</div>
		</nav>
		<main>
			<div id="page-dashboard" class="page-content active">
				<h1 class="title">Dashboard</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Dashboard</a></li>
				</ul>
				<div class="info-data" id="dashboard-cards-container"></div>
			</div>

			<div id="page-donatur" class="page-content" style="display: none;">
				<h1 class="title">Data Donatur</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Donatur</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<button id="addDonaturBtn" class="add-btn">Tambah Donatur</button>
						<p>Tabel informasi mengenai Donatur</p>
						<table>
							<thead>
								<tr>
									<th>Nama</th>
									<th>Email</th>
									<th>Telepon</th>
									<th>Tanggal Daftar</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
				<div id="donaturModal" class="modal">
					<div class="modal-content">
						<span class="close-button">&times;</span>
						<h2>Daftar Akun Donatur Baru</h2>
						<form id="donaturRegistrationForm">
							<label for="donaturNama">Nama:</label>
							<input type="text" id="donaturNama" name="nama" required><br>
							<label for="donaturEmail">Email:</label>
							<input type="email" id="donaturEmail" name="email"><br>
							<label for="donaturUsername">Username:</label>
							<input type="text" id="donaturUsername" name="username" required><br>
							<label for="donaturPassword">Password:</label>
							<input type="password" id="donaturPassword" name="password" required><br>
							<label for="donaturTelepon">Telepon:</label>
							<input type="text" id="donaturTelepon" name="telepon"><br>
							<button type="submit">Daftar Donatur</button>
						</form>
					</div>
				</div>
			</div>

			<div id="page-distributor" class="page-content" style="display: none;">
				<h1 class="title">Data Distributor</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Distributor</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<button id="addDistributorBtn" class="add-btn">Tambah Distributor</button>
						<p>Tabel informasi mengenai Distributor</p>
						<table>
							<thead>
								<tr>
									<th>Nama Distributor</th>
									<th>Jenis</th>
									<th>Email</th>
									<th>Telepon</th>
									<th>Alamat</th>
									<th>Tanggal Bergabung</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div id="distributorModal" class="modal">
					<div class="modal-content">
						<span class="close-button">&times;</span>
						<h2>Daftar Akun Distributor Baru</h2>
						<form id="distributorRegistrationForm">
							<label for="distributorNama">Nama Distributor:</label>
							<input type="text" id="distributorNama" name="nama" required><br>
							<label for="distributorJenis">Jenis Distributor:</label>
							<select id="distributorJenis" name="jenis_distributor" required>
								<option value="Yayasan">Yayasan</option>
								<option value="Organisasi Sosial">Organisasi Sosial</option>
								<option value="Individu Relawan">Individu Relawan</option>
								<option value="Lainnya">Lainnya</option>
							</select><br>
							<label for="distributorEmail">Email:</label>
							<input type="email" id="distributorEmail" name="email"><br>
							<label for="distributorTelepon">Telepon:</label>
							<input type="text" id="distributorTelepon" name="telepon" required><br>
							<label for="distributorAlamat">Alamat:</label>
							<textarea id="distributorAlamat" name="alamat"></textarea><br>
							<label for="distributorDeskripsi">Deskripsi:</label>
							<textarea id="distributorDeskripsi" name="deskripsi"></textarea><br>
							<label for="distributorUsername">Username:</label>
							<input type="text" id="distributorUsername" name="username" required><br>
							<label for="distributorPassword">Password:</label>
							<input type="password" id="distributorPassword" name="password" required><br>
							<button type="submit">Daftar Distributor</button>
						</form>
					</div>
				</div>
			</div>

			<div id="page-barang-donasi" class="page-content" style="display: none;">
				<h1 class="title">Data Barang Donasi</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Barang Donasi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">

						<button id="addBarangDonasiAdminBtn" class="add-btn">Tambah Barang Donasi</button>

						<p>Tabel informasi mengenai Barang Donasi</p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Kategori</th>
									<th>Jumlah</th>
									<th>Kondisi</th>
									<th>Tanggal Donasi</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="adminTambahDonasiModal" class="modal">
				<div class="modal-content">
					<span class="close-button">&times;</span>
					<h2>Tambah Barang Donasi (oleh Admin)</h2>

					<form id="adminTambahDonasiForm" enctype="multipart/form-data">
						<label for="donatur_id">Pilih Donatur:</label>
						<select id="adminDonaturIdSelect" name="donatur_id" required></select>

						<label for="adminNamaBarang">Nama Barang:</label>
						<input type="text" id="adminNamaBarang" name="nama_barang" required>

						<label for="adminKategori">Kategori:</label>
						<input type="text" id="adminKategori" name="kategori" required>

						<label for="adminDeskripsi">Deskripsi Barang:</label>
						<textarea id="adminDeskripsi" name="deskripsi_barang"></textarea>

						<label for="adminJumlah">Jumlah:</label>
						<input type="number" id="adminJumlah" name="jumlah" required min="1">

						<label for="adminSatuan">Satuan:</label>
						<select id="adminSatuan" name="satuan" required>
							<option value="Unit">Unit</option>
							<option value="Pcs">Pcs</option>
							<option value="Pasang">Pasang</option>
							<option value="Lainnya">Lainnya</option>
						</select>

						<label for="adminKondisi">Kondisi Barang:</label>
						<select id="adminKondisi" name="kondisi_barang" required>
							<option value="Baru">Baru</option>
							<option value="Bekas Layak Pakai">Bekas Layak Pakai</option>
							<option value="Rusak Ringan">Rusak Ringan</option>
						</select>

						<label for="adminFoto">Foto Barang (Opsional):</label>
						<input type="file" id="adminFoto" name="foto" accept="image/*">

						<button type="submit">Tambah Donasi</button>
					</form>
				</div>
			</div>

			<div id="page-permintaan-distribusi-admin" class="page-content" style="display: none;">
				<h1 class="title">Permintaan Distribusi (Admin)</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Permintaan Distribusi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Tabel informasi mengenai Permintaan Distribusi</p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Nama Distributor</th>
									<th>Jumlah Disalurkan</th>
									<th>Status Penyaluran</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-konfirmasi-penyelesaian" class="page-content" style="display: none;">
				<h1 class="title">Konfirmasi Penyelesaian Distribusi</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Konfirmasi Penyelesaian</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Verifikasi bukti yang diunggah oleh distributor dan selesaikan transaksi.</p>
						<table>
							<thead>
								<tr>
									<th>Distributor</th>
									<th>Barang</th>
									<th>Catatan</th>
									<th>Bukti Foto</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-histori-distribusi-donasi-admin" class="page-content" style="display: none;">
				<h1 class="title">Histori Distribusi Donasi (Admin)</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Histori Distribusi Donasi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Tabel informasi mengenai Histori Distribusi Donasi</p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Nama Distributor</th>
									<th>Tanggal Penyaluran</th>
									<th>Jumlah Disalurkan</th>
									<th>Status Penyaluran</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-pesan" class="page-content" style="display: none;">
				<h1 class="title">Pesan</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Pesan</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Tabel atau informasi mengenai Pesan dari Contact Us akan ditampilkan di sini.</p>
						<table>
							<thead>
								<tr>
									<th>Nama</th>
									<th>Email</th>
									<th>Pesan</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-tambah-donasi-donatur" class="page-content" style="display: none;">
				<h1 class="title">Tambah Donasi (Donatur)</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Tambah Donasi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Formulir untuk menambah barang donasi.</p>
						<form>
							<label for="nama_barang_donatur">Nama Barang:</label>
							<input type="text" id="nama_barang_donatur" name="nama_barang" required><br><br>
							<label for="kategori_donatur">Kategori:</label>
							<input type="text" id="kategori_donatur" name="kategori" required><br><br>
							<label for="deskripsi_barang_donatur">Deskripsi Barang:</label>
							<textarea id="deskripsi_barang_donatur" name="deskripsi_barang"></textarea><br><br>
							<label for="satuan_donatur">Satuan:</label>
							<select id="satuan_donatur" name="satuan" required>
								<option value="Unit">Unit</option>
								<option value="Pcs">Pcs</option>
								<option value="Pasang">Pasang</option>
								<option value="Lainnya">Lainnya</option>
							</select><br><br>
							<label for="jumlah_donatur">Jumlah:</label>
							<input type="number" id="jumlah_donatur" name="jumlah" required><br><br>
							<label for="kondisi_barang_donatur">Kondisi Barang:</label>
							<select id="kondisi_barang_donatur" name="kondisi_barang" required>
								<option value="Baru">Baru</option>
								<option value="Bekas Layak Pakai">Bekas Layak Pakai</option>
								<option value="Rusak Ringan">Rusak Ringan</option>
							</select><br><br>
							<label for="foto_donatur">Foto Barang:</label>
							<input type="file" id="foto_donatur" name="foto"><br><br>
							<button type="submit">Ajukan Donasi</button>
						</form>
					</div>
				</div>
			</div>

			<div id="page-histori-donasi-donatur" class="page-content" style="display: none;">
				<h1 class="title">Histori Donasi (Donatur)</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Histori Donasi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Tabel Histori Donasi dari <?php echo htmlspecialchars($currentUsername); ?></p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Jumlah</th>
									<th>Kondisi</th>
									<th>Tanggal Donasi</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-riwayat-distribusi-donatur" class="page-content" style="display: none;">
				<h1 class="title">Riwayat Distribusi Donasi Anda</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Riwayat Distribusi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Berikut adalah riwayat penyaluran dari barang-barang yang pernah Anda donasikan.</p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Jumlah</th>
									<th>Disalurkan Kepada</th>
									<th>Tanggal Selesai</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-lihat-barang-tersedia" class="page-content" style="display: none;">
				<h1 class="title">Barang Donasi Tersedia</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Barang Tersedia</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Berikut adalah daftar barang yang tersedia untuk didistribusikan. Klik "Ajukan Permintaan" untuk membuat permintaan.</p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Kategori</th>
									<th>Jumlah Tersedia</th>
									<th>Kondisi</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="permintaanModal" class="modal">
				<div class="modal-content">
					<span class="close-button">&times;</span>
					<h2>Buat Permintaan Distribusi</h2>
					<form id="permintaanDistribusiForm">
						<input type="hidden" id="permintaanBarangId" name="barang_id">
						
						<label>Nama Barang:</label>
						<p id="permintaanNamaBarang" style="font-weight: bold;"></p>
						
						<label for="permintaanJumlah">Jumlah yang Diminta:</label>
						<input type="number" id="permintaanJumlah" name="jumlah_disalurkan" required min="1">
						
						<button type="submit">Kirim Permintaan</button>
					</form>
				</div>
			</div>

			<div id="prosesDistribusiModal" class="modal">
				<div class="modal-content">
					<span class="close-button">&times;</span>
					<h2>Proses Permintaan Distribusi</h2>
					<form id="prosesDistribusiForm" enctype="multipart/form-data">
						<p>Anda akan memproses permintaan <strong id="prosesDistribusiIdText"></strong>.</p>
						<input type="hidden" id="prosesDistribusiId" name="distribusi_id">
						
						<label for="buktiFoto">Upload Foto Bukti Penyaluran:</label>
						<input type="file" id="buktiFoto" name="bukti_foto_penyaluran" accept="image/*" required>
						
						<label for="catatanDistribusi">Catatan (Opsional):</label>
						<textarea id="catatanDistribusi" name="catatan_distribusi"></textarea>
						
						<button type="submit">Proses dan Salurkan Sekarang</button>
					</form>
				</div>
			</div>

			<div id="page-permintaan-distribusi-distributor" class="page-content" style="display: none;">
				<h1 class="title">Status Permintaan Distribusi (Distributor)</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Permintaan Distribusi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Tabel riwayat dan status permintaan distribusi yang telah <?php echo htmlspecialchars($currentUsername); ?> ajukan.</p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Jumlah Diminta</th>
									<th>Tanggal Pengajuan</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								</tbody>
						</table>
					</div>
				</div>
			</div>

			<div id="page-histori-distribusi-distributor" class="page-content" style="display: none;">
				<h1 class="title">Histori Distribusi (Distributor)</h1>
				<ul class="breadcrumbs">
					<li><a href="#">Home</a></li>
					<li class="divider">/</li>
					<li><a href="#" class="active">Histori Distribusi</a></li>
				</ul>
				<div class="data">
					<div class="content-data">
						<p>Berikut adalah histori distribusi <?php echo htmlspecialchars($currentUsername); ?></p>
						<table>
							<thead>
								<tr>
									<th>Nama Barang</th>
									<th>Jumlah</th>
									<th>Tanggal Pengajuan</th>
									<th>Tanggal Selesai Disalurkan</th>
									<th>Status Final</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div id="selesaiModal" class="modal">
				<div class="modal-content">
					<span class="close-button">&times;</span>
					<h2>Tandai Distribusi Selesai</h2>
					<form id="formSelesaiDistribusi" enctype="multipart/form-data">
						<p>Anda akan menyelesaikan permintaan <strong id="selesaiDistribusiIdText"></strong>.</p>
						<p>Silakan unggah bukti bahwa barang telah diterima oleh penerima akhir.</p>
						
						<input type="hidden" id="selesaiDistribusiId" name="distribusi_id">
						
						<label for="buktiFotoPenyaluran">Upload Foto Bukti Penyaluran:</label>
						<input type="file" id="buktiFotoPenyaluran" name="bukti_foto_penyaluran" accept="image/*" required>
						
						<label for="catatanDistribusiFinal">Catatan Tambahan (Opsional):</label>
						<textarea id="catatanDistribusiFinal" name="catatan_distribusi" placeholder="Contoh: Diserahkan kepada Bapak Budi di RT 05..."></textarea>
						
						<button type="submit">Konfirmasi dan Selesaikan Distribusi</button>
					</form>
				</div>
			</div>
		</main>
		</section>
	<script>
		const currentUserLevel = '<?php echo $currentUserLevel; ?>';
		const currentUserId = <?php echo $currentUserId; ?>;
	</script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="js/index.js"></script>
</body>
</html>