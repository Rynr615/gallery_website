# Website Gallery

## Deskripsi
Ini adalah website yang dibuat menggunakan bahasa pemrograman PHP. Website memiliki berbagai fitur yang memungkinkan pengguna untuk mendaftar, mengelola profile, mengelola album, mengelola foto dan masih banyak yang lainnya.


## Fitur
Fitur | Guest | User | Admin | Super Admin 
| :--- | :---: | :---: | :---: | :---:
Register  | &#x2713;  | &#x2713;  | &#x2713; | &#x2713;
Login   | &#x2717;  | &#x2713;  | &#x2713; |&#x2713;
Logout   | &#x2717;  | &#x2713;  | &#x2713; | &#x2713;
Foto  | &#x2713;  | &#x2713;  | &#x2713; | &#x2713;
Album  | &#x2713;  | &#x2713;  | &#x2713; | &#x2713;
Report   |&#x2717;  | &#x2713;  | &#x2713; | &#x2713;
Like  | &#x2717;  | &#x2713;  | &#x2713; | &#x2713;
Komentar  | &#x2717;  | &#x2713;  | &#x2713; | &#x2713;
Data User  | &#x2717;  | &#x2717;  | &#x2713; | &#x2713;

#### Fitur ( Tambahan )
_Backup dan restore database_

## Cara Penggunaan
Untuk menggunakan aplikasi ini, pastikan Anda memiliki Git dan Node.js (beserta npm) terinstall di komputer Anda. Berikut adalah langkah-langkah untuk meng-clone dan menjalankan aplikasi:

1. Clone repository ini:
    ```bash
    git clone https://github.com/Rynr615/gallery_website.git
    ```
2. [Download](https://nodejs.org/en/download) dan Install Node.js.
3. [Download](https://getcomposer.org/download/) dan Install Composer.
4. Install PHP Mailer
    ```bash
    "phpmailer/phpmailer": "^6.9.1"
    ``` 
    atau
    ```bash
    composer require phpmailer/phpmailer
    ```

5. Pindah ke direktori proyek:
    ```bash
    cd gallery_website
    ```

6. Jalankan Tailwind:
    ```bash
    npm run build-css -watch
    ```
- _Lakukan perintah berikut jika ingin melakukan backup dan restore database, dan pastikan bahwa kita sudah ada di direktori database._
- _Jangan lupa untuk buka command prompt dari terminal atau bawaan dari VSCode_
7. Backup Database:
    ```bash
    php backup_database.php
    ```
8. Restore Database:
    ```bash
    php restore_database.php
    ```

### Catatan
Pastikan untuk mengikuti instruksi di atas dengan benar untuk menjalankan aplikasi dengan lancar. Jika Anda mengalami masalah, pastikan untuk memeriksa dokumentasi resmi Node.js dan Tailwind CSS. Selamat mencoba! :3👍
