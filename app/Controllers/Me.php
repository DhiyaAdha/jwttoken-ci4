<?php

namespace App\Controllers;

// Mengimpor class-class yang diperlukan
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

// JWT
use \Firebase\JWT\JWT; // Mengimpor class JWT dari pustaka Firebase JWT
use \Firebase\JWT\Key; // Mengimpor class Key untuk mendekode JWT

class Me extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    // Menggunakan trait ResponseTrait untuk membantu dalam mengelola respon HTTP
    use ResponseTrait; //added
    public function index()
    {
        // Mendapatkan kunci rahasia JWT dari environment variables
        $key = getenv('TOKEN_SECRET');

        // Mengambil header Authorization dari permintaan HTTP
        $header = $this->request->getServer('HTTP_AUTHORIZATION');

        // Memeriksa apakah header Authorization tersedia
        if (!$header) {
            // Jika tidak ada header, mengembalikan respon 401 Unauthorized
            return $this->failUnauthorized('Token Required');
        }

        // Memisahkan token dari kata kunci "Bearer" yang biasanya ada di header Authorization
        $tokenArray = explode(' ', $header);

        // Memeriksa apakah token memiliki format yang benar (harus ada dua bagian setelah dipisah)
        if (count($tokenArray) !== 2) {
            // Jika format token tidak valid, mengembalikan respon dengan pesan kesalahan
            return $this->fail('Invalid Token Format');
        }

        // Mengambil token JWT yang sebenarnya dari array hasil pemisahan
        $token = $tokenArray[1];

        try {
            // Mendekode token menggunakan kunci rahasia dan algoritma HS256
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Membuat respon dengan data dari token yang telah didekode
            $response = [
                'id' => $decoded->uid, // Mengambil ID pengguna dari token
                'email' => $decoded->email // Mengambil email pengguna dari token
            ];

            // Mengembalikan respon dengan data pengguna
            return $this->respond($response);
        } catch (\Firebase\JWT\ExpiredException $e) {
            // Jika token sudah kedaluwarsa, mengembalikan respon dengan pesan kesalahan
            return $this->fail('Token Expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            // Jika tanda tangan token tidak valid, mengembalikan respon dengan pesan kesalahan
            return $this->fail('Invalid Signature');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan umum lainnya saat mendekode token, mengembalikan respon dengan pesan kesalahan
            return $this->fail('Invalid Token');
        }
    }

    
}
