import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertController } from '@ionic/angular';

@Component({
  selector: 'app-tab4',
  templateUrl: './tab4.page.html',
  styleUrls: ['./tab4.page.scss'],
  standalone: false,
})
export class Tab4Page implements OnInit {

  nip = '';
  nama = '';
  isLoggedIn = false;
  menu = '';

  guruList: any[] = [];
  mapelList: string[] = [
    'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS',
    'Pendidikan Agama', 'Penjaskes', 'Seni Budaya', 'Prakarya', 'PPKn'
  ];
  jamMasukList: string[] = [
    '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00',
    '10:30', '11:00', '11:30'
  ];
  kelasList: string[] = [
    '7A','7B','7C','7D','8A','8B','8C','8D','9A','9B','9C','9D'
  ];

  absensi = {
    guru_nip: '',
    mapel: '',
    jam_masuk: '',
    kelas: '',
    tanggal: '',
    status: ''
  };

  constructor(
    private http: HttpClient,
    private alertCtrl: AlertController
  ) {}

  ngOnInit() {
    this.getGuru();
  }

  getGuru() {
    this.http.get<any>('https://samuel16.ti-zone.io/get-guru.php').subscribe(res => {
      if (res.status) {
        this.guruList = res.data;
      }
    }, err => {
      this.showAlert('Error', 'Koneksi ke API gagal.');
    });
  }

  loginGuru() {
    const guru = this.guruList.find(g => g.nip === this.nip && g.nama.toLowerCase() === this.nama.toLowerCase());
    if (guru) {
      this.isLoggedIn = true;
      this.absensi.guru_nip = guru.nip;
      this.showAlert('Berhasil', 'Login berhasil!');
    } else {
      this.showAlert('Gagal', 'NIP atau Nama tidak ditemukan.');
    }
  }

  simpanAbsensi() {
    this.http.post<any>('https://samuel16.ti-zone.io/absensi-guru.php', this.absensi).subscribe(res => {
      if (res.status) {
        this.showAlert('Berhasil', res.message);
        this.absensi = {
          guru_nip: this.absensi.guru_nip, mapel: '', jam_masuk: '', kelas: '', tanggal: '', status: ''
        };
      } else {
        this.showAlert('Error', res.message);
      }
    }, err => {
      this.showAlert('Error', 'Koneksi ke API gagal.');
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
