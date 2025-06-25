// SIDEBAR DROPDOWN
const allDropdown = document.querySelectorAll('#sidebar .side-dropdown'); 
const sidebar = document.getElementById('sidebar'); 

allDropdown.forEach(item=> { 
	const a = item.parentElement.querySelector('a:first-child'); 
	a.addEventListener('click', function (e) { 
		e.preventDefault(); 

		if(!this.classList.contains('active')) { 
			allDropdown.forEach(i=> { 
				const aLink = i.parentElement.querySelector('a:first-child'); 

				aLink.classList.remove('active'); 
				i.classList.remove('show'); 
			})
		}

		this.classList.toggle('active'); 
		item.classList.toggle('show'); 
	})
})

// SIDEBAR COLLAPSE
const toggleSidebar = document.querySelector('nav .toggle-sidebar'); 
const allSideDivider = document.querySelectorAll('#sidebar .divider'); 

if(sidebar.classList.contains('hide')) { 
	allSideDivider.forEach(item=> { 
		item.textContent = '-' 
	})
	allDropdown.forEach(item=> { 
		const a = item.parentElement.querySelector('a:first-child'); 
		a.classList.remove('active'); 
		item.classList.remove('show'); 
	})
} else {
	allSideDivider.forEach(item=> { 
		item.textContent = item.dataset.text; 
	})
}

toggleSidebar.addEventListener('click', function () { 
	sidebar.classList.toggle('hide'); 

	if(sidebar.classList.contains('hide')) { 
		allSideDivider.forEach(item=> { 
			item.textContent = '-' 
		})

		allDropdown.forEach(item=> { 
			const a = item.parentElement.querySelector('a:first-child'); 
			a.classList.remove('active'); 
			item.classList.remove('show'); 
		})
	} else {
		allSideDivider.forEach(item=> { 
			item.textContent = item.dataset.text; 
		})
	}
})

sidebar.addEventListener('mouseleave', function () { 
	if(this.classList.contains('hide')) { 
		allDropdown.forEach(item=> { 
			const a = item.parentElement.querySelector('a:first-child'); 
			a.classList.remove('active'); 
			item.classList.remove('show'); 
		})
		allSideDivider.forEach(item=> { 
			item.textContent = '-' 
		})
	}
})

sidebar.addEventListener('mouseenter', function () { 
	if(this.classList.contains('hide')) { 
		allDropdown.forEach(item=> { 
			const a = item.parentElement.querySelector('a:first-child'); 
			a.classList.remove('active'); 
			item.classList.remove('show'); 
		})
		allSideDivider.forEach(item=> { 
			item.textContent = item.dataset.text; 
		})
	}
})

// PROFILE DROPDOWN
const profile = document.querySelector('nav .profile'); 
const imgProfile = profile.querySelector('img'); 
const dropdownProfile = profile.querySelector('.profile-link'); 

imgProfile.addEventListener('click', function () { 
	dropdownProfile.classList.toggle('show'); 
})

// MENU
const allMenu = document.querySelectorAll('main .content-data .head .menu'); 

allMenu.forEach(item=> { 
	const icon = item.querySelector('.icon'); 
	const menuLink = item.querySelector('.menu-link'); 

	icon.addEventListener('click', function () { 
		menuLink.classList.toggle('show'); 
	})
})

window.addEventListener('click', function (e) { 
	if(e.target !== imgProfile) { 
		if(e.target !== dropdownProfile) { 
			if(dropdownProfile.classList.contains('show')) { 
				dropdownProfile.classList.remove('show'); 
			}
		}
	}

	allMenu.forEach(item=> { 
		const icon = item.querySelector('.icon'); 
		const menuLink = item.querySelector('.menu-link'); 

		if(e.target !== icon) { 
			if(e.target !== menuLink) { 
				if (menuLink.classList.contains('show')) { 
					menuLink.classList.remove('show') 
				}
			}
		}
	})
    
    const openModals = document.querySelectorAll('.modal');
    openModals.forEach(modal => {
        if (e.target == modal) {
            modal.classList.remove('show');
        }
    });
})

function hideAllPages() { 
    const allPages = document.querySelectorAll('.page-content'); 
    allPages.forEach(page => { 
        page.style.display = 'none'; 
        page.classList.remove('active');
    });
}

function showPage(pageId) { 
    hideAllPages(); 
    const targetPage = document.getElementById(pageId); 
    if (targetPage) { 
        targetPage.style.display = 'block'; 
        targetPage.classList.add('active');
    }
}

function updateSidebarMenuVisibility(userLevel) { 
    const sideMenuLinks = document.querySelectorAll('#sidebar .side-menu li'); 
    sideMenuLinks.forEach(item => { 
        const itemLevel = item.getAttribute('data-level'); 
        if (!itemLevel || item.classList.contains('divider')) { 
            item.style.display = 'block'; 
        } else if (itemLevel === userLevel) { 
            item.style.display = 'block'; 
        } else {
            item.style.display = 'none'; 
        }
    });
}

const sideMenu = document.querySelectorAll('#sidebar .side-menu a'); 
sideMenu.forEach(item => { 
    item.addEventListener('click', function(e) { 
        e.preventDefault(); 

        sideMenu.forEach(link => link.classList.remove('active'));
        this.classList.add('active'); 

        const pageId = this.dataset.page; 
        if (pageId) { 
            showPage('page-' + pageId);
            if (pageId === 'dashboard') {
                loadDashboardCounts(); 
            } else if (pageId === 'donatur') { 
                loadDonaturData(); 
            } else if (pageId === 'distributor') { 
                loadDistributorData(); 
            } else if (pageId === 'barang-donasi') { 
                loadBarangDonasiData();
            } else if (pageId === 'permintaan-distribusi-admin') { 
                loadPermintaanDistribusiAdminData();
            } else if (pageId === 'histori-distribusi-donasi-admin') { 
                loadHistoriDistribusiDonasiAdminData(); 
            } else if (pageId === 'pesan') { 
                loadPesanData();
            } else if (pageId === 'histori-donasi-donatur') { 
                loadHistoriDonasiDonaturData();
            } else if (pageId === 'riwayat-distribusi-donatur') {
                loadRiwayatDistribusiDonaturData();
            } else if (pageId === 'lihat-barang-tersedia') {
                loadBarangTersediaData();
            } else if (pageId === 'permintaan-distribusi-distributor') { 
                loadPermintaanDistribusiDistributorData();
            } else if (pageId === 'konfirmasi-penyelesaian') {
                loadKonfirmasiPenyelesaianData();
            } else if (pageId === 'histori-distribusi-distributor') { 
                loadHistoriDistribusiDistributorData();
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', () => { 
    updateSidebarMenuVisibility(currentUserLevel); 
    showPage('page-dashboard');
    loadDashboardCounts();
});

const logoutBtn = document.getElementById('logoutBtn'); 
if (logoutBtn) { 
    logoutBtn.addEventListener('click', (e) => { 
        e.preventDefault(); 
        currentUserLevel = '';
        updateSidebarMenuVisibility(currentUserLevel); 
        showPage('page-dashboard');
        alert('Anda telah logout!');
    });
}

async function fetchData(url) { 
    try {
        const response = await fetch('php/' + url);
        if (!response.ok) { 
            throw new Error(`HTTP error! status: ${response.status}`); 
        }
        return await response.json(); 
    } catch (error) { 
        console.error('Error fetching data:', error); 
        alert('Gagal mengambil data: ' + error.message); 
        return [];
    }
}

async function postData(url, data) { 
    try {
        const response = await fetch('php/' + url, {
            method: 'POST', 
            headers: { 
                'Content-Type': 'application/json', 
            },
            body: JSON.stringify(data), 
        });
        const responseData = await response.json(); 
        if (!response.ok) { 
            throw new Error(responseData.message || `HTTP error! status: ${response.status}`); 
        }
        return responseData; 
    } catch (error) { 
        console.error('Error posting data:', error); 
        alert('Gagal menyimpan data: ' + error.message); 
        return { success: false, message: error.message }; 
    }
}

async function postFormData(url, formData) { 
    try {
        const response = await fetch('php/' + url, {
            method: 'POST', 
            body: formData,
        });
        const responseData = await response.json(); 
        if (!response.ok) { 
            throw new Error(responseData.message || `HTTP error! status: ${response.status}`); 
        }
        return responseData; 
    } catch (error) { 
        console.error('Error posting form data:', error); 
        alert('Gagal menyimpan data form: ' + error.message); 
        return { success: false, message: error.message }; 
    }
}

async function loadDashboardCounts() { 
    const data = await fetchData('api.php?resource=dashboard_counts');
    const container = document.getElementById('dashboard-cards-container');
    container.innerHTML = '';

    function createCard(title, value, iconClass) {
        return `
            <div class="card">
                <div class="head">
                    <div>
                        <h2>${value !== null ? value : 0}</h2>
                        <p>${title}</p>
                    </div>
                    <i class='bx ${iconClass} icon'></i>
                </div>
            </div>
        `;
    }

    if (currentUserLevel === 'Admin') {
        container.innerHTML += createCard('Permintaan Baru', data.permintaan_menunggu, 'bxs-bell');
        container.innerHTML += createCard('Perlu Dikonfirmasi', data.menunggu_konfirmasi, 'bxs-user-check');
        container.innerHTML += createCard('Total Donatur', data.donatur, 'bxs-donate-heart');
        container.innerHTML += createCard('Total Distributor', data.distributor, 'bxs-group');
        container.innerHTML += createCard('Total Barang Donasi', data.barang_donasi, 'bxs-box');
        container.innerHTML += createCard('Total Distribusi Selesai', data.distribusi_donasi, 'bxs-check-shield');
    } 
    else if (currentUserLevel === 'Donatur') {
        container.innerHTML += createCard('Total Donasi Saya', data.total_donasi_saya, 'bxs-box');
        container.innerHTML += createCard('Donasi Dalam Proses', data.donasi_diproses, 'bxs-time-five');
        container.innerHTML += createCard('Donasi Telah Tersalurkan', data.donasi_tersalurkan, 'bxs-check-shield');
    }
    else if (currentUserLevel === 'Distributor') {
        container.innerHTML += createCard('Barang Siap Diambil', data.siap_diambil, 'bxs-package');
        container.innerHTML += createCard('Distribusi Diproses', data.diproses_distributor, 'bxs-archive-out');
        container.innerHTML += createCard('Distribusi Selesai', data.distribusi_selesai, 'bxs-check-shield');
    }
}


async function loadDonaturData() { 
    const tableBody = document.querySelector('#page-donatur table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="5">Loading...</td></tr>'; 
    const data = await fetchData('api.php?resource=donatur'); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama}</td>
                    <td>${d.email || '-'}</td>
                    <td>${d.telepon || '-'}</td>
                    <td>${d.tanggal_daftar}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="5">Tidak ada data donatur.</td></tr>'; 
    }
}

async function loadDistributorData() { 
    const tableBody = document.querySelector('#page-distributor table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>'; 
    const data = await fetchData('api.php?resource=distributor'); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => {
            const row = `
                <tr>
                    <td>${d.nama}</td> 
                    <td>${d.jenis_distributor}</td>
                    <td>${d.email || '-'}</td>
                    <td>${d.telepon || '-'}</td>
                    <td>${d.alamat || '-'}</td>
                    <td>${d.tanggal_daftar}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="7">Tidak ada data distributor.</td></tr>'; 
    }
}

async function loadBarangDonasiData() { 
    const tableBody = document.querySelector('#page-barang-donasi table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>'; 
    const data = await fetchData('api.php?resource=barang_donasi'); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.kategori}</td>
                    <td>${d.jumlah}</td>
                    <td>${d.kondisi_barang}</td>
                    <td>${d.tanggal_donasi}</td>
                    <td>${d.status_barang}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="7">Tidak ada data barang donasi.</td></tr>'; 
    }
}

async function loadPermintaanDistribusiAdminData() { 
    const tableBody = document.querySelector('#page-permintaan-distribusi-admin table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="6">Memuat data...</td></tr>';
    const data = await fetchData('api.php?resource=permintaan_distribusi_admin'); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.nama_distributor}</td>
                    <td>${d.jumlah_disalurkan}</td>
                    <td>${d.status_penyaluran}</td>
                    <td>
                        <button class="approve-btn" data-id="${d.distribusi_id}">Proses</button>
                        <button class="reject-btn" data-id="${d.distribusi_id}">Tolak</button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Tidak ada permintaan distribusi yang menunggu.</td></tr>'; 
    }
}

async function loadBarangTersediaData() {
    const tableBody = document.querySelector('#page-lihat-barang-tersedia table tbody');
    tableBody.innerHTML = '<tr><td colspan="6">Memuat data...</td></tr>';
    
    const data = await fetchData('api.php?resource=barang_tersedia');
    
    tableBody.innerHTML = '';
    if (data.length > 0) {
        data.forEach(d => {
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.kategori}</td>
                    <td>${d.jumlah_tersedia}</td>
                    <td>${d.kondisi_barang}</td>
                    <td>
                        <button class="request-btn" data-barang-id="${d.barang_id}" data-nama-barang="${d.nama_barang}" data-max-jumlah="${d.jumlah_tersedia}">
                            Ajukan Permintaan
                        </button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Saat ini tidak ada barang yang tersedia.</td></tr>';
    }
}

async function loadPermintaanDistribusiDistributorData() { 
    const tableBody = document.querySelector('#page-permintaan-distribusi-distributor table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="6">Memuat data...</td></tr>';
    
    const data = await fetchData('api.php?resource=permintaan_distribusi_distributor'); 
    
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => {
            let aksiHtml = '-';
            
            if (d.status_penyaluran === 'Diproses') {
                aksiHtml = `<button class="confirm-pickup-btn" data-id="${d.distribusi_id}">Konfirmasi Penerimaan</button>`;
            } else if (d.status_penyaluran === 'Diproses Distributor') {
                aksiHtml = `<button class="mark-complete-btn" data-id="${d.distribusi_id}">Tandai Selesai</button>`;
            }

            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.jumlah_disalurkan}</td>
                    <td>${d.tanggal_pengajuan}</td>
                    <td>${d.status_penyaluran}</td>
                    <td>${aksiHtml}</td>
                </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Anda belum pernah mengajukan permintaan.</td></tr>';
    }
}

async function loadHistoriDistribusiDonasiAdminData() { 
    const tableBody = document.querySelector('#page-histori-distribusi-donasi-admin table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>'; 
    const data = await fetchData('api.php?resource=histori_distribusi_donasi_admin'); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.nama_distributor}</td>
                    <td>${d.tanggal_penyaluran || '-'}</td>
                    <td>${d.jumlah_disalurkan}</td>
                    <td>${d.status_penyaluran}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Tidak ada histori distribusi donasi.</td></tr>'; 
    }
}

async function loadPesanData() { 
    const tableBody = document.querySelector('#page-pesan table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>'; 
    const data = await fetchData('api.php?resource=pesan'); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama}</td>
                    <td>${d.email || '-'}</td>
                    <td>${d.pesan}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="4">Tidak ada pesan.</td></tr>'; 
    }
}

async function loadHistoriDonasiDonaturData(donaturId) { 
    const tableBody = document.querySelector('#page-histori-donasi-donatur table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>'; 
    const data = await fetchData(`api.php?resource=histori_donasi_donatur&donatur_id=${donaturId}`); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.jumlah}</td>
                    <td>${d.kondisi_barang}</td>
                    <td>${d.tanggal_donasi}</td>
                    <td>${d.status_barang}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Tidak ada histori donasi.</td></tr>'; 
    }
}

async function loadRiwayatDistribusiDonaturData() {
    const tableBody = document.querySelector('#page-riwayat-distribusi-donatur table tbody');
    tableBody.innerHTML = '<tr><td colspan="5">Memuat data...</td></tr>';

    const data = await fetchData('api.php?resource=riwayat_distribusi_donatur');

    tableBody.innerHTML = '';
    if (data.length > 0) {
        data.forEach(d => {
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.jumlah_disalurkan}</td>
                    <td>${d.nama_distributor}</td>
                    <td>${d.tanggal_penyaluran || '-'}</td>
                    <td>${d.status_penyaluran}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="5">Belum ada riwayat distribusi untuk donasi Anda.</td></tr>';
    }
}

async function loadHistoriDistribusiDistributorData(distributorId) { 
    const tableBody = document.querySelector('#page-histori-distribusi-distributor table tbody'); 
    tableBody.innerHTML = '<tr><td colspan="5">Loading...</td></tr>'; 
    const data = await fetchData(`api.php?resource=histori_distribusi_distributor&distributor_id=${distributorId}`); 
    tableBody.innerHTML = '';
    if (data.length > 0) { 
        data.forEach(d => { 
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.tanggal_penyaluran}</td>
                    <td>${d.jumlah_disalurkan}</td>
                    <td>${d.status_penyaluran}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row); 
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="5">Tidak ada histori distribusi.</td></tr>'; 
    }
}

const donaturModal = document.getElementById('donaturModal'); 
const distributorModal = document.getElementById('distributorModal'); 

const addDonaturBtn = document.getElementById('addDonaturBtn'); 
const addDistributorBtn = document.getElementById('addDistributorBtn'); 

const closeButtons = document.querySelectorAll('.close-button'); 

const donaturRegistrationForm = document.getElementById('donaturRegistrationForm'); 
const distributorRegistrationForm = document.getElementById('distributorRegistrationForm'); 
const addBarangDonasiAdminBtn = document.getElementById('addBarangDonasiAdminBtn');
const adminTambahDonasiForm = document.getElementById('adminTambahDonasiForm');
const tambahDonasiDonaturForm = document.querySelector('#page-tambah-donasi-donatur form'); 

if (addDonaturBtn) { 
    addDonaturBtn.onclick = function() { 
        if (currentUserLevel === 'Admin') {
            donaturModal.classList.add('show');
        } else {
            alert('Anda tidak memiliki izin untuk melakukan tindakan ini.'); 
        }
    }
}

if (addDistributorBtn) { 
    addDistributorBtn.onclick = function() { 
        if (currentUserLevel === 'Admin') {
            distributorModal.classList.add('show');
        } else {
            alert('Anda tidak memiliki izin untuk melakukan tindakan ini.'); 
        }
    }
}

closeButtons.forEach(button => { 
    button.onclick = function() { 
        donaturModal.classList.remove('show');
        distributorModal.classList.remove('show');
    }
});

window.onclick = function(event) { 
    if (event.target == donaturModal) { 
        donaturModal.classList.remove('show');
    }
    if (event.target == distributorModal) { 
        distributorModal.classList.remove('show');
    }
}

if (donaturRegistrationForm) { 
    donaturRegistrationForm.addEventListener('submit', async function(e) { 
        e.preventDefault(); 
        const formData = new FormData(this); 
        const data = Object.fromEntries(formData.entries()); 

        const result = await postData('api.php?resource=donatur', data); 
        if (result.message) { 
            alert(result.message); 
            if (result.id) {
                loadDonaturData();
                donaturModal.classList.remove('show');
                this.reset();
                loadDashboardCounts();
            }
        }
    });
}

if (distributorRegistrationForm) { 
    distributorRegistrationForm.addEventListener('submit', async function(e) { 
        e.preventDefault(); 
        const formData = new FormData(this); 
        const data = Object.fromEntries(formData.entries()); 

        const result = await postData('api.php?resource=distributor', data); 
        if (result.message) { 
            alert(result.message); 
            if (result.message.includes('berhasil')) {
                 loadDistributorData();
                 distributorModal.classList.remove('show');
                 this.reset();
                 loadDashboardCounts();
            }
        }
    });
}

if (addBarangDonasiAdminBtn) {
    addBarangDonasiAdminBtn.addEventListener('click', async () => {
        const modal = document.getElementById('adminTambahDonasiModal');
        const selectDonatur = document.getElementById('adminDonaturIdSelect');
        if (modal && selectDonatur) {
            selectDonatur.innerHTML = '<option value="">Memuat donatur...</option>';
            const donaturList = await fetchData('api.php?resource=donatur');
            selectDonatur.innerHTML = '<option value="">-- Pilih Donatur --</option>';
            if (donaturList.length > 0) {
                donaturList.forEach(donatur => {
                    const option = `<option value="${donatur.id_user}">${donatur.nama}</option>`;
                    selectDonatur.insertAdjacentHTML('beforeend', option);
                });
            }
            modal.classList.add('show');
        } else {
            alert('Modal untuk Tambah Barang Donasi tidak ditemukan.');
        }
    });
}

if (adminTambahDonasiForm) {
    adminTambahDonasiForm.addEventListener('submit', async function(e) {
        e.preventDefault(); 
        const formData = new FormData(this);
        const result = await postFormData('api.php?resource=barang_donasi', formData);
        if (result.message) {
            alert(result.message); 
            if (result.id) {
                document.getElementById('adminTambahDonasiModal').style.display = 'none';
                this.reset(); 
                loadBarangDonasiData();
                loadDashboardCounts(); 
            }
        }
    });
}

if (tambahDonasiDonaturForm) { 
    tambahDonasiDonaturForm.addEventListener('submit', async function(e) { 
        e.preventDefault(); 
        const formData = new FormData(this); 
        
        formData.append('donatur_id', currentUserId);

        const result = await postFormData('api.php?resource=barang_donasi', formData); 
        if (result.message) { 
            alert(result.message); 
            if (result.id) { 
                this.reset();
                if (document.getElementById('page-histori-donasi-donatur').classList.contains('active')) { 
                    loadHistoriDonasiDonaturData(currentDonaturId); 
                }
                loadDashboardCounts();
            }
        }
    });
}

const permintaanModal = document.getElementById('permintaanModal');
const permintaanForm = document.getElementById('permintaanDistribusiForm');

document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('request-btn')) {
        const barangId = e.target.dataset.barangId;
        const namaBarang = e.target.dataset.namaBarang;
        const maxJumlah = e.target.dataset.maxJumlah;

        document.getElementById('permintaanBarangId').value = barangId;
        document.getElementById('permintaanNamaBarang').textContent = namaBarang;
        const jumlahInput = document.getElementById('permintaanJumlah');
        jumlahInput.value = 1;
        jumlahInput.max = maxJumlah;
        
        permintaanModal.classList.add('show');
    }

    if (e.target && e.target.classList.contains('close-button')) {
        const modal = e.target.closest('.modal');
        if (modal) {
            modal.classList.remove('show');
        }
    }
    
    // if (e.target && e.target.classList.contains('close-button') && e.target.closest('#permintaanModal')) {
    //     permintaanModal.classList.remove('show');
    // }
});

if (permintaanForm) {
    permintaanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        const result = await postData('api.php?resource=ajukan_permintaan_distribusi', data);
        if (result.message) {
            alert(result.message);
            if (result.message.includes('berhasil')) {
                permintaanModal.classList.remove('show');
                this.reset();
                document.querySelector('a[data-page="permintaan-distribusi-distributor"]').click();
            }
        }
    });
}

document.addEventListener('click', async function(e) {
    // --- AKSI ADMIN ---
    if (e.target && e.target.classList.contains('approve-btn')) {
        const distribusiId = e.target.dataset.id;
        if (confirm(`Anda yakin ingin MEMPROSES permintaan #${distribusiId}? Barang akan disiapkan untuk distributor.`)) {
            const data = { distribusi_id: distribusiId, new_status: 'Diproses' };
            const result = await postData('api.php?resource=update_distribusi_status', data);
            alert(result.message);
            if (result.message.includes('berhasil')) loadPermintaanDistribusiAdminData();
        }
    }

    if (e.target && e.target.classList.contains('reject-btn')) {
        const distribusiId = e.target.dataset.id;
        if (confirm(`Anda yakin ingin MENOLAK permintaan #${distribusiId}?`)) {
            const data = { distribusi_id: distribusiId, new_status: 'Ditolak' };
            const result = await postData('api.php?resource=update_distribusi_status', data);
            alert(result.message);
            if (result.message.includes('berhasil')) loadPermintaanDistribusiAdminData();
        }
    }

    // --- AKSI DISTRIBUTOR ---
    if (e.target && e.target.classList.contains('confirm-pickup-btn')) {
        const distribusiId = e.target.dataset.id;
        if (confirm(`Konfirmasi bahwa Anda telah MENERIMA barang untuk permintaan #${distribusiId}?`)) {
            const data = { distribusi_id: distribusiId, new_status: 'Diproses Distributor' };
            const result = await postData('api.php?resource=update_distribusi_status', data);
            alert(result.message);
            if (result.message.includes('berhasil')) loadPermintaanDistribusiDistributorData();
        }
    }

    if (e.target && e.target.classList.contains('mark-complete-btn')) {
        const distribusiId = e.target.dataset.id;
        const selesaiModal = document.getElementById('selesaiModal');
        document.getElementById('selesaiDistribusiIdText').textContent = `#${distribusiId}`;
        document.getElementById('selesaiDistribusiId').value = distribusiId;
        selesaiModal.classList.add('show');
    }
    
    // Logika untuk tombol close modal
    if (e.target && e.target.classList.contains('close-button')) {
        const modal = e.target.closest('.modal');
        if (modal) modal.classList.remove('show');
    }

    if (e.target && e.target.classList.contains('final-confirm-btn')) {
        const distribusiId = e.target.dataset.id;
        if (confirm(`Anda yakin ingin menyelesaikan transaksi #${distribusiId}? Aksi ini final.`)) {
            const data = {
                distribusi_id: distribusiId,
                new_status: 'Selesai'
            };
            const result = await postData('api.php?resource=update_distribusi_status', data);
            alert(result.message);
            
            if (result.message.includes('berhasil')) {
                loadKonfirmasiPenyelesaianData();
            }
        }
    }
});

const formSelesai = document.getElementById('formSelesaiDistribusi');
if (formSelesai) {
    formSelesai.addEventListener('submit', async function(e) {
        e.preventDefault(); 
        
        const formData = new FormData(this);

        const result = await postFormData('api.php?resource=selesaikan_distribusi', formData);

        alert(result.message);

        if (result.message.includes('berhasil') || result.message.includes('SUKSES')) {
            document.getElementById('selesaiModal').style.display = 'none';
            this.reset();
            loadPermintaanDistribusiDistributorData(); 
        }
    });
}

async function loadKonfirmasiPenyelesaianData() {
    const tableBody = document.querySelector('#page-konfirmasi-penyelesaian table tbody');
    tableBody.innerHTML = '<tr><td colspan="6">Memuat data...</td></tr>';
    const data = await fetchData('api.php?resource=permintaan_konfirmasi_selesai');
    tableBody.innerHTML = '';
    if (data.length > 0) {
        data.forEach(d => {
            const row = `
                <tr>
                    <td>${d.nama_distributor}</td>
                    <td>${d.nama_barang}</td>
                    <td>${d.catatan_distribusi || '-'}</td>
                    <td>
                        <a href="${d.bukti_foto_penyaluran}" target="_blank">Lihat Bukti</a>
                    </td>
                    <td>
                        <button class="final-confirm-btn" data-id="${d.distribusi_id}">Konfirmasi & Selesaikan</button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Tidak ada permintaan yang menunggu konfirmasi.</td></tr>';
    }
}

async function loadHistoriDistribusiDistributorData() {
    const tableBody = document.querySelector('#page-histori-distribusi-distributor table tbody');
    tableBody.innerHTML = '<tr><td colspan="6">Memuat data...</td></tr>';

    const data = await fetchData('api.php?resource=histori_distribusi_distributor');

    tableBody.innerHTML = '';
    if (data.length > 0) {
        data.forEach(d => {
            const row = `
                <tr>
                    <td>${d.nama_barang}</td>
                    <td>${d.jumlah_disalurkan}</td>
                    <td>${d.tanggal_pengajuan || '-'}</td>
                    <td>${d.tanggal_penyaluran || '-'}</td>
                    <td>${d.status_penyaluran}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="6">Tidak ada riwayat distribusi.</td></tr>';
    }
}