import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SiswaService {

  // Ganti URL ini sesuai lokasi server PHP kamu
  apiURL = 'http://localhost/api';

  constructor(private http: HttpClient) { }

  // Tambah data siswa
  tambahSiswa(data: any): Observable<any> {
    return this.http.post(`${this.apiURL}/tambah_siswa.php`, data);
  }

  // Ambil semua data siswa
  getSiswa(): Observable<any> {
    return this.http.get(`${this.apiURL}/tampil_siswa.php`);
  }
   tambahGuru(data: any) {
    return this.http.post<any>(`this.apiURL + /input-guru`, data);
  }

}
