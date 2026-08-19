<?php

// ==========================================================
// KONEKSI DAN KONFIGURASI
// ==========================================================
include "../../_Config/Connection.php";
include "../../_Config/SettingGeneral.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Zona waktu
date_default_timezone_set('Asia/Jakarta');


// ==========================================================
// FUNGSI HTML ESCAPE
// ==========================================================
function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


// ==========================================================
// VALIDASI SESI
// ==========================================================
if (empty($SessionIdAccess)) {
    echo '
        <div class="alert alert-danger">
            <small>
                Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!
            </small>
        </div>
    ';
    exit;
}


// ==========================================================
// VALIDASI NIK
// ==========================================================
if (empty($_POST['nik'])) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b> Silahkan isi form NIK terlebih dulu
            </small>
        </div>
    ';
    exit;
}


// ==========================================================
// SANITASI NIK
// ==========================================================
$nik = validateAndSanitizeInput($_POST['nik']);

if (empty($nik)) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b> NIK tidak valid.
            </small>
        </div>
    ';
    exit;
}


// ==========================================================
// GENERATE TOKEN SATUSEHAT
// ==========================================================
$tokenResult = generateTokenSatuSehat($Conn);

if (
    !is_array($tokenResult) ||
    ($tokenResult['status'] ?? '') !== 'success'
) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                ' . e($tokenResult['message'] ?? 'Gagal mendapatkan token SATUSEHAT.') . '
            </small>
        </div>
    ';
    exit;
}

$token = $tokenResult['token'] ?? '';

if (empty($token)) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b> Token SATUSEHAT tidak ditemukan.
            </small>
        </div>
    ';
    exit;
}


// ==========================================================
// AMBIL KONFIGURASI SATUSEHAT
// ==========================================================
$status_active = 1;

$stmt = $Conn->prepare("
    SELECT 
        url_connection_satu_sehat,
        organization_id
    FROM connection_satu_sehat
    WHERE status_connection_satu_sehat = ?
    LIMIT 1
");

if (!$stmt) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b> Gagal mempersiapkan konfigurasi SATUSEHAT.
            </small>
        </div>
    ';
    exit;
}

$stmt->bind_param("i", $status_active);
$stmt->execute();

$resultConfig = $stmt->get_result();
$config = $resultConfig->fetch_assoc();

$stmt->close();


if (!$config) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                Koneksi SATUSEHAT tidak ditemukan.
            </small>
        </div>
    ';
    exit;
}


// ==========================================================
// VALIDASI URL SATUSEHAT
// ==========================================================
$url_api = trim($config['url_connection_satu_sehat'] ?? '');

if (empty($url_api)) {
    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                URL koneksi SATUSEHAT belum dikonfigurasi.
            </small>
        </div>
    ';
    exit;
}

$url_api = rtrim($url_api, '/');


// ==========================================================
// BUAT URL PENCARIAN PRACTITIONER BERDASARKAN NIK
// ==========================================================
$identifier_system = 'https://fhir.kemkes.go.id/id/nik';

$url_tujuan =
    $url_api .
    '/fhir-r4/v1/Practitioner?identifier=' .
    rawurlencode($identifier_system . '|' . $nik);


// ==========================================================
// REQUEST KE SATUSEHAT
// ==========================================================
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url_tujuan,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',

    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json'
    ],

    CURLOPT_TIMEOUT => 30,

    // Untuk production sebaiknya TIDAK dimatikan.
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2
]);

$response = curl_exec($curl);

$http_code = curl_getinfo(
    $curl,
    CURLINFO_HTTP_CODE
);

$curl_error = curl_error($curl);

curl_close($curl);


// ==========================================================
// HANDLE CURL ERROR
// ==========================================================
if ($curl_error) {

    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                CURL Error
                <br>
                Keterangan :
                ' . e($curl_error) . '
            </small>
        </div>
    ';

    exit;
}


// ==========================================================
// VALIDASI RESPONSE JSON
// ==========================================================
$result = json_decode(
    $response,
    true
);

if (
    json_last_error() !== JSON_ERROR_NONE ||
    !is_array($result)
) {

    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                Response SATUSEHAT bukan JSON valid.
                <br>
                Keterangan :
                ' . e(substr($response, 0, 500)) . '
            </small>
        </div>
    ';

    exit;
}


// ==========================================================
// VALIDASI HTTP RESPONSE
// ==========================================================
if ($http_code !== 200) {

    $msg = 'Gagal mencari Practitioner di SATUSEHAT.';

    if (
        ($result['resourceType'] ?? '') === 'OperationOutcome'
        && !empty($result['issue'])
        && is_array($result['issue'])
    ) {

        $issue = $result['issue'][0] ?? [];

        $msg =
            $issue['details']['text']
            ?? $issue['diagnostics']
            ?? $issue['code']
            ?? $msg;
    }

    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                ' . e($msg) . '
                <br>
                HTTP Code : ' . e($http_code) . '
            </small>
        </div>
    ';

    exit;
}


// ==========================================================
// VALIDASI BUNDLE
// ==========================================================
if (
    ($result['resourceType'] ?? '') !== 'Bundle'
) {

    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                Response SATUSEHAT bukan Bundle Practitioner.
            </small>
        </div>
    ';

    exit;
}


// ==========================================================
// AMBIL ENTRY
// ==========================================================
$entries = $result['entry'] ?? [];

if (
    empty($entries)
    || !is_array($entries)
) {

    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                Data Practitioner Tidak Ditemukan!
            </small>
        </div>
    ';

    exit;
}


// ==========================================================
// HITUNG DATA
// ==========================================================
$jumlah_practitioner = count($entries);


// ==========================================================
// TAMPILKAN HASIL
// ==========================================================
foreach ($entries as $entry_list) {

    // Pastikan resource tersedia
    if (
        !isset($entry_list['resource'])
        || !is_array($entry_list['resource'])
    ) {
        continue;
    }

    $resource = $entry_list['resource'];

    // ======================================================
    // DATA DASAR PRACTITIONER
    // ======================================================
    $resource_id = $resource['id'] ?? '';

    $gender = $resource['gender'] ?? '';

    $birthDate = $resource['birthDate'] ?? '';

    $birthDateDisplay = '-';

    if (!empty($birthDate)) {

        $timestamp = strtotime($birthDate);

        if ($timestamp !== false) {
            $birthDateDisplay = date(
                'd F Y',
                $timestamp
            );
        }
    }


    // ======================================================
    // NAMA PRACTITIONER
    // ======================================================
    $nama_practitioner = '';

    if (
        !empty($resource['name'])
        && is_array($resource['name'])
    ) {

        $nameData = $resource['name'][0] ?? [];

        $nama_practitioner =
            $nameData['text']
            ?? '';

        if (empty($nama_practitioner)) {

            $nama_practitioner =
                $nameData['family']
                ?? '';

            if (
                !empty($nameData['given'])
                && is_array($nameData['given'])
            ) {

                $nama_practitioner =
                    implode(
                        ' ',
                        $nameData['given']
                    )
                    . ' '
                    . $nama_practitioner;
            }
        }
    }


    // ======================================================
    // ADDRESS
    // ======================================================
    $addresses = $resource['address'] ?? [];

    if (
        !is_array($addresses)
        || empty($addresses)
    ) {

        $addresses = [];
    }


    // ======================================================
    // TAMPILKAN DATA
    // ======================================================
    echo '
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <div class="row mb-2">
                    <div class="col-4">
                        <small>Tanggal Lahir</small>
                    </div>

                    <div class="col-1">
                        <small>:</small>
                    </div>

                    <div class="col-7">
                        <small class="text-grayish">
                            ' . e($birthDateDisplay) . '
                        </small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4">
                        <small>Gender</small>
                    </div>

                    <div class="col-1">
                        <small>:</small>
                    </div>

                    <div class="col-7">
                        <small class="text-grayish">
                            ' . e($gender ?: '-') . '
                        </small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4">
                        <small>ID Practitioner</small>
                    </div>

                    <div class="col-1">
                        <small>:</small>
                    </div>

                    <div class="col-6">
                        <small class="text-grayish put_id_practitioner">
                            ' . e($resource_id ?: '-') . '
                        </small>
                    </div>

                    <div class="col-1">

                        ' . (
                            !empty($resource_id)
                            ? '
                                <a 
                                    href="javascript:void(0);"
                                    class="get_id_practitioner2"
                                    data-id="' . e($resource_id) . '"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-original-title="Tempelkan ID Practitioner"
                                >
                                    <small>
                                        <i class="bi bi-clipboard-check"></i>
                                    </small>
                                </a>
                            '
                            : ''
                        ) . '

                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4">
                        <small>Nama</small>
                    </div>

                    <div class="col-1">
                        <small>:</small>
                    </div>

                    <div class="col-7">
                        <small class="text-grayish">
                            ' . e($nama_practitioner ?: '-') . '
                        </small>
                    </div>
                </div>
    ';


    // ======================================================
    // ADDRESS
    // ======================================================
    if (!empty($addresses)) {

        foreach ($addresses as $address) {

            if (!is_array($address)) {
                continue;
            }

            $country =
                $address['country']
                ?? '-';

            $city =
                $address['city']
                ?? '-';

            echo '
                <div class="row mb-2">

                    <div class="col-4">
                        <small>Negara</small>
                    </div>

                    <div class="col-1">
                        <small>:</small>
                    </div>

                    <div class="col-7">
                        <small class="text-grayish">
                            ' . e($country) . '
                        </small>
                    </div>

                </div>

                <div class="row mb-2">

                    <div class="col-4">
                        <small>Kab/Kota</small>
                    </div>

                    <div class="col-1">
                        <small>:</small>
                    </div>

                    <div class="col-7">
                        <small class="text-grayish">
                            ' . e($city) . '
                        </small>
                    </div>

                </div>
            ';
        }

    } else {

        echo '
            <div class="row mb-2">

                <div class="col-4">
                    <small>Alamat</small>
                </div>

                <div class="col-1">
                    <small>:</small>
                </div>

                <div class="col-7">
                    <small class="text-grayish">
                        -
                    </small>
                </div>

            </div>
        ';
    }


    echo '
            </div>
        </div>
    ';
}


// ==========================================================
// JIKA SEMUA ENTRY TIDAK VALID
// ==========================================================
if ($jumlah_practitioner === 0) {

    echo '
        <div class="alert alert-danger text-center">
            <small>
                <b>Oopssss!</b>
                Data Practitioner Tidak Ditemukan!
            </small>
        </div>
    ';

    exit;
}

?>