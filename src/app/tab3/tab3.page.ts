import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertController } from '@ionic/angular';

@Component({
  selector: 'app-tab3',
  templateUrl: 'tab3.page.html',
  styleUrls: ['tab3.page.scss'],
  standalone: false,
})
export class Tab3Page implements OnInit {

  // Login dan menu
  username = '';
  password = '';
  isLoggedIn = false;
  menu = ''; // 'guru' atau 'absensi'
// Data Absensi Guru
absensiGuruList: any[] = [];
totalGuruHadir = 0;
totalGuruIzin = 0;
totalGuruSakit = 0;
totalGuruAlpha = 0;
// Data Absensi Guru

filterTanggalGuru = ''; // untuk filter tanggal absensi guru
isLoadingGuru = false;

  // Data Guru
  guru = {
    nama: '',
    nip: '',
    alamat: '',
    email: '',
    no_hp: '',
    jenis_kelamin: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    foto: ''
  };
  apiURL = 'https://samuel16.ti-zone.io/input-guru.php';

  // Data Absensi
  absensiList: any[] = [];
  kelasList: string[] = ['7A','7B','7C','7D','8A','8B','8C','8D','9A','9B','9C','9D'];
  filterKelas = '';
  filterTanggal = '';

  totalHadir = 0;
  totalIzin = 0;
  totalSakit = 0;
  totalAlpha = 0;

  constructor(
    private http: HttpClient,
    private alertCtrl: AlertController
  ) {}

  ngOnInit() {}

  async showAlert(header: string, message: string) {
    const alert = await this.alertCtrl.create({
      header,
      message,
      buttons: ['OK']
    });
    await alert.present();
  }

  // Login
  login() {
    const userHardcoded = 'admin';
    const passHardcoded = 'admin123';

    if (this.username === userHardcoded && this.password === passHardcoded) {
      this.isLoggedIn = true;
      this.menu = 'guru'; // default menu setelah login
      this.showAlert('Berhasil', 'Login berhasil!');
    } else {
      this.showAlert('Gagal', 'Username atau password salah.');
    }
  }

  // Tambah Guru
  tambahGuru() {
    const headers = { 'Content-Type': 'application/json' };
    this.http.post<any>(this.apiURL, this.guru, { headers }).subscribe(res => {
      if (res.status) {
        this.showAlert('Sukses', 'Data guru berhasil disimpan.');
        this.resetGuruForm();
      } else {
        this.showAlert('Error', 'Gagal menyimpan data.');
      }
    }, err => {
      console.error('Error: ', err);
      this.showAlert('Error', 'Terjadi kesalahan koneksi API.');
    });
  }

  resetGuruForm() {
    this.guru = {
      nama: '',
      nip: '',
      alamat: '',
      email: '',
      no_hp: '',
      jenis_kelamin: '',
      tempat_lahir: '',
      tanggal_lahir: '',
      foto: ''
    };
  }

  // Upload Foto Guru ke Base64
  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = () => {
        this.guru.foto = (reader.result as string).split(',')[1];
      };
      reader.readAsDataURL(file);
    }
  }

  // Get Data Absensi
  getAbsensi() {
    let url = 'https://samuel16.ti-zone.io/get-absensi-siswa.php';
    const params = [];

    if (this.filterKelas) params.push(`kelas=${this.filterKelas}`);
    if (this.filterTanggal) params.push(`tanggal=${this.filterTanggal}`);

    if (params.length > 0) {
      url += '?' + params.join('&');
    }

    this.http.get<any>(url).subscribe(res => {
      if (res.status) {
        this.absensiList = res.data;
        this.hitungRekap();
      } else {
        this.absensiList = [];
        this.showAlert('Info', 'Data absensi tidak ditemukan.');
      }
    }, err => {
      console.error('Error get absensi: ', err);
      this.showAlert('Error', 'Gagal mengambil data absensi.');
    });
  }
  // Tambahkan function ini di Tab3Page
setMenu(menuName: string) {
  this.menu = menuName;
  if (menuName === 'rekapGuru') {
    this.getAbsensiGuru();
  }
}

 getAbsensiGuru() {
  this.isLoadingGuru = true;

  let url = 'https://samuel16.ti-zone.io/get-absensi-guru.php';
  const params = [];

  if (this.filterTanggalGuru) {
    const tanggalFormatted = this.filterTanggalGuru.split('T')[0];
    params.push(`tanggal=${tanggalFormatted}`);
  }

  params.push(`_=${new Date().getTime()}`);
  url += '?' + params.join('&');

  this.http.get<any>(url).subscribe(res => {
    if (res.status) {
      this.absensiGuruList = res.data;
    } else {
      this.absensiGuruList = [];
      this.showAlert('Info', 'Data absensi guru tidak ditemukan.');
    }
    this.isLoadingGuru = false;
  }, err => {
    console.error('Error get absensi guru: ', err);
    this.showAlert('Error', 'Gagal mengambil data absensi guru.');
    this.isLoadingGuru = false;
  });
}



hitungRekapGuru() {
  this.totalGuruHadir = this.absensiGuruList.filter(a => a.status === 'Hadir').length;
  this.totalGuruIzin  = this.absensiGuruList.filter(a => a.status === 'Izin').length;
  this.totalGuruSakit = this.absensiGuruList.filter(a => a.status === 'Sakit').length;
  this.totalGuruAlpha = this.absensiGuruList.filter(a => a.status === 'Alpha').length;
}

  // Hitung Rekap
  hitungRekap() {
    this.totalHadir = this.absensiList.filter(a => a.status === 'Hadir').length;
    this.totalIzin  = this.absensiList.filter(a => a.status === 'Izin').length;
    this.totalSakit = this.absensiList.filter(a => a.status === 'Sakit').length;
    this.totalAlpha = this.absensiList.filter(a => a.status === 'Alpha').length;
  }

  // Export ke Word
  exportToWord() {
    let content = '<h3>Data Absensi Siswa</h3><table border="1" cellspacing="0" cellpadding="5">';
    content += '<tr><th>No</th><th>Nama</th><th>NIS</th><th>Mapel</th><th>Jam Masuk</th><th>Guru</th><th>Tanggal</th><th>Status</th></tr>';

    this.absensiList.forEach((a, i) => {
      content += `<tr>
        <td>${i+1}</td>
        <td>${a.nama}</td>
        <td>${a.nis}</td>
        <td>${a.mapel}</td>
        <td>${a.jam_masuk}</td>
        <td>${a.nama_guru}</td>
        <td>${a.tanggal}</td>
        <td>${a.status}</td>
      </tr>`;
    });

    content += '</table>';

    const blob = new Blob(['\ufeff' + content], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'absensi_siswa.doc';
    link.click();
  }

  // Export ke Excel
  exportToExcel() {
    let content = '<table border="1">';
    content += '<tr><th>No</th><th>Nama</th><th>NIS</th><th>Mapel</th><th>Jam Masuk</th><th>Guru</th><th>Tanggal</th><th>Status</th></tr>';

    this.absensiList.forEach((a, i) => {
      content += `<tr>
        <td>${i+1}</td>
        <td>${a.nama}</td>
        <td>${a.nis}</td>
        <td>${a.mapel}</td>
        <td>${a.jam_masuk}</td>
        <td>${a.nama_guru}</td>
        <td>${a.tanggal}</td>
        <td>${a.status}</td>
      </tr>`;
    });

    content += '</table>';

    const blob = new Blob([content], { type: 'application/vnd.ms-excel' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'absensi_siswa.xls';
    link.click();
  }
}
