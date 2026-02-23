<?php

require_once __DIR__ . '/../../includes/db.php';

class AuthController{
    public function register($data){
        global $koneksi;

        $first_name = mysqli_real_escape_string($koneksi, trim($data['first_name']));
        $last_name = mysqli_real_escape_string($koneksi, trim($data['last_name']));
        $username = mysqli_real_escape_string($koneksi, trim($data['username']));
        $email = mysqli_real_escape_string($koneksi, trim($data['email']));
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        $checkEmail = mysqli_query($koneksi, "SELECT id FROM users WHERE email = '$email'");
        if(mysqli_num_rows($checkEmail) > 0){
            return ['succes' => false, 'message' => 'Email sudah terdaftar!'];
        }

        $checkUsername = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($checkUsername) > 0) {
            return ['success' => false, 'message' => 'Username sudah digunakan!'];
        }

        // Simpan ke database
        $query = "INSERT INTO users (first_name, last_name, username, email, password) 
                  VALUES ('$first_name', '$last_name', '$username', '$email', '$password')";

        if (mysqli_query($koneksi, $query)) {
            return ['success' => true, 'message' => 'Registrasi berhasil!'];
        } else {
            return ['success' => false, 'message' => 'Terjadi kesalahan, coba lagi!'];
        }
    }
}