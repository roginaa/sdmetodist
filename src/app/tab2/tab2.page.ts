import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertController } from '@ionic/angular';

@Component({
  selector: 'app-tab2',
  templateUrl: 'tab2.page.html',
  styleUrls: ['tab2.page.scss'],
  standalone: false,
})
export class Tab2Page implements OnInit {
  menu = 'inputSiswa';

  // Data lists
  kelasList: string[] = ['7A','7B','7C','7D','8A','8B','8C','8D','9A','9B','9C','9D'];
  mapelList: string[] = [
    'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS',
    'Pendidikan Agama', 'Penjaskes', 'Seni Budaya', 'Prakarya', 'PPKn'
  ];
  jamMasukList: string[] = [
    '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00',
    '10:30', '11:00', '11:30'
  ];
  guruList: string[] = [];

  // Form data
  data: any = {};
  absensi: any = {
    nis: '',
    mapel: '',
    jam_masuk: '',
    nama_guru: '',
    tanggal: '',
    status: ''
  };

  siswaList: any[] = [];
  absensiList: any[] = [];
  filterKelas = '';
  filterTanggal = '';

  constructor(private http: HttpClient, private alertCtrl: AlertController) {}

  ngOnInit() {
    this.getGuru();
  }

  closeMenu() {
    (document.querySelector('ion-menu') as any).close();
  }

  // Input siswa
  simpan() {
    this.http.post('https://samuel16.ti-zone.io/tambah_siswa.php', this.data).subscribe(res => {
      this.showAlert('Sukses', 'Data siswa berhasil disimpan.');
      this.data = {};
    });
  }

  // Load guru dari API
  getGuru() {
    this.http.get<any>('https://samuel16.ti-zone.io/get-guru.php').subscribe(res => {
      if (res.status) {
        this.guruList = res.data.map((g: any) => g.nama);
      }
    });
  }

  // Ambil siswa by kelas
  getSiswaByKelas(kelas: string) {
    this.http.get<any>(`https://samuel16.ti-zone.io/get-siswa-by-class.php?kelas=${kelas}`).subscribe(res => {
      if (res.status) {
        this.siswaList = res.data;
      }
    });
  }

  // Simpan absensi siswa
  simpanAbsensi() {
    this.http.post<any>('https://samuel16.ti-zone.io/absensi-siswa.php', this.absensi).subscribe(res => {
      if (res.status) {
        this.showAlert('Berhasil', res.message);
      } else {
        this.showAlert('Gagal', res.message);
      }
    });
  }

  // Tampilkan rekap absensi
  getAbsensi() {
    const params = `?kelas=${this.filterKelas}&tanggal=${this.filterTanggal}`;
    this.http.get<any>('https://samuel16.ti-zone.io/get-absensi.php' + params).subscribe(res => {
      if (res.status) {
        this.absensiList = res.data;
      }
    });
  }

  async showAlert(header: string, message: string) {
    const alert = await this.alertCtrl.create({
      header,
      message,
      buttons: ['OK']
    });
    await alert.present();
  }
}
