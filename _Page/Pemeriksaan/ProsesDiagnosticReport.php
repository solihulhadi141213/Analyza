<?php
    // ============================================================
    // HEADER
    // ============================================================
    header('Content-Type: application/json; charset=utf-8');

    // ============================================================
    // KONFIGURASI
    // ============================================================
    date_default_timezone_set('Asia/Jakarta');

    require_once "../../_Config/Connection.php";
    require_once "../../_Config/GlobalFunction.php";
    require_once "../../_Config/Session.php";

    function sanitizeQuillHtml($html){
        if (!is_string($html)) {
            return '';
        }

        $html = trim($html);

        if ($html === '') {
            return '';
        }

        // Hilangkan script
        $html = preg_replace(
            '#<script\b[^>]*>(.*?)</script>#is',
            '',
            $html
        );

        // Hilangkan style
        $html = preg_replace(
            '#<style\b[^>]*>(.*?)</style>#is',
            '',
            $html
        );

        // Hilangkan iframe/object/embed
        $html = preg_replace(
            '#<(iframe|object|embed)[^>]*>.*?</\1>#is',
            '',
            $html
        );

        // Tag yang diperbolehkan
        $allowedTags = '<p><br><strong><b><em><i><u><s><strike><blockquote><pre><code><ol><ul><li><h1><h2><h3><h4><h5><h6><a>';

        $html = strip_tags(
            $html,
            $allowedTags
        );

        // Hapus event handler JavaScript
        $html = preg_replace(
            '/\son\w+\s*=\s*"[^"]*"/i',
            '',
            $html
        );

        $html = preg_replace(
            "/\son\w+\s*=\s*'[^']*'/i",
            '',
            $html
        );

        // Hilangkan javascript: pada href
        $html = preg_replace_callback(
            '/href\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i',
            function () {
                return '';
            },
            $html
        );

        return trim($html);
    }

    // ============================================================
    // RESPONSE JSON
    // ============================================================
    function jsonResponse($status, $message, $payload = '')
    {
        echo json_encode([
            'status'  => $status,
            'message' => $message,
            'payload' => $payload
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // ============================================================
    // VALIDASI METHOD
    // ============================================================
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(
            'error',
            'Metode request tidak valid.'
        );
    }

    // ============================================================
    // VALIDASI SESSION
    // ============================================================
    if (empty($SessionIdAccess)) {
        jsonResponse(
            'error',
            'Sesi akses sudah berakhir! Silahkan login ulang.'
        );
    }

    // ============================================================
    // AMBIL DATA REQUEST
    // ============================================================
    $id_laboratorium = trim(
        (string) ($_POST['id_laboratorium'] ?? '')
    );

    $id_laboratorium_diagnostic = trim(
        (string) ($_POST['id_laboratorium_diagnostic'] ?? '')
    );

    $conclusion_raw = $_POST['conclusion'] ?? '';

    $clinical_raw = $_POST['clinical'] ?? '';

    $icd_10_code = trim(
        (string) ($_POST['icd_10_code'] ?? '')
    );

    $icd_10_display = trim(
        (string) ($_POST['icd_10_display'] ?? '')
    );

    $icd_10_system = trim(
        (string) ($_POST['icd_10_system'] ?? '')
    );

    $pernyataan_petugas = trim(
        (string) ($_POST['pernyataan_petugas'] ?? '')
    );

    // ============================================================
    // VALIDASI ID LABORATORIUM
    // ============================================================
    if ($id_laboratorium === '') {
        jsonResponse(
            'error',
            'ID Laboratorium tidak boleh kosong.'
        );
    }

    // ============================================================
    // VALIDASI DATA RAW DARI QUILL
    // ============================================================
    // Sebelum sanitasi, pastikan data memang diterima dari AJAX.
    if (trim($conclusion_raw) === '') {
        jsonResponse(
            'error',
            'Kesimpulan (Conclusion) tidak diterima dari form.',
            'POST conclusion kosong.'
        );
    }

    if (trim($clinical_raw) === '') {
        jsonResponse(
            'error',
            'Kondisi Klinis (Clinical) tidak diterima dari form.',
            'POST clinical kosong.'
        );
    }

    // ============================================================
    // FUNGSI VALIDASI ISI RICH TEXT
    // ============================================================
    function richTextToPlainText($html)
    {
        // Decode entity HTML
        $text = html_entity_decode(
            (string) $html,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Hilangkan tag HTML
        $text = strip_tags($text);

        // Hilangkan nbsp Unicode
        $text = str_replace(
            "\xC2\xA0",
            ' ',
            $text
        );

        // Hilangkan entity nbsp biasa
        $text = str_replace(
            '&nbsp;',
            ' ',
            $text
        );

        // Normalisasi whitespace
        $text = preg_replace(
            '/\s+/u',
            ' ',
            $text
        );

        return trim($text);
    }

    // ============================================================
    // VALIDASI CONTENT QUILL SEBELUM SANITASI
    // ============================================================
    $conclusion_plain_raw = richTextToPlainText(
        $conclusion_raw
    );

    $clinical_plain_raw = richTextToPlainText(
        $clinical_raw
    );

    if ($conclusion_plain_raw === '') {
        jsonResponse(
            'error',
            'Kesimpulan (Conclusion) tidak boleh kosong.'
        );
    }

    if ($clinical_plain_raw === '') {
        jsonResponse(
            'error',
            'Kondisi Klinis (Clinical) tidak boleh kosong.'
        );
    }

    // ============================================================
    // SANITASI RICH TEXT
    // ============================================================
    $conclusion = sanitizeQuillHtml($conclusion_raw);
    $clinical   = sanitizeQuillHtml($clinical_raw);

    // ============================================================
    // VALIDASI HASIL SANITASI
    // ============================================================
    $conclusion_plain = richTextToPlainText(
        $conclusion
    );

    $clinical_plain = richTextToPlainText(
        $clinical
    );

    if ($conclusion_plain === '') {
        jsonResponse(
            'error',
            'Kesimpulan (Conclusion) kosong setelah proses sanitasi.',
            'Periksa fungsi sanitizeRichTextHTML().'
        );
    }

    if ($clinical_plain === '') {
        jsonResponse(
            'error',
            'Kondisi Klinis (Clinical) kosong setelah proses sanitasi.',
            'Periksa fungsi sanitizeRichTextHTML().'
        );
    }

    // ============================================================
    // VALIDASI ICD10
    // ============================================================
    if ($icd_10_code === '') {
        jsonResponse(
            'error',
            'Kode ICD10 tidak boleh kosong.'
        );
    }

    if ($icd_10_display === '') {
        jsonResponse(
            'error',
            'Display ICD10 tidak boleh kosong.'
        );
    }

    if ($icd_10_system === '') {
        jsonResponse(
            'error',
            'System ICD10 tidak boleh kosong.'
        );
    }

    // ============================================================
    // VALIDASI PERNYATAAN PETUGAS
    // ============================================================
    if ($pernyataan_petugas !== '1') {
        jsonResponse(
            'error',
            'Anda harus menyetujui pernyataan petugas laboratorium.'
        );
    }

    // ============================================================
    // SANITASI DATA TEXT BIASA
    // ============================================================
    $id_laboratorium = validateAndSanitizeInput(
        $id_laboratorium
    );

    $id_laboratorium_diagnostic = validateAndSanitizeInput(
        $id_laboratorium_diagnostic
    );

    $icd_10_code = validateAndSanitizeInput(
        $icd_10_code
    );

    $icd_10_display = validateAndSanitizeInput(
        $icd_10_display
    );

    $icd_10_system = validateAndSanitizeInput(
        $icd_10_system
    );

    // ============================================================
    // CEK KONEKSI DATABASE
    // ============================================================
    if (!isset($Conn) || !$Conn) {
        jsonResponse(
            'error',
            'Koneksi database tidak tersedia.'
        );
    }

    // ============================================================
    // MULAI DATABASE TRANSACTION
    // ============================================================
    $Conn->begin_transaction();

    try {

        // ========================================================
        // CEK DATA LABORATORIUM
        // ========================================================
        $checkLaboratorium = $Conn->prepare(
            "SELECT id_laboratorium
            FROM laboratorium
            WHERE id_laboratorium = ?
            LIMIT 1"
        );

        if (!$checkLaboratorium) {
            throw new Exception(
                'Gagal mempersiapkan query pemeriksaan laboratorium.'
            );
        }

        $checkLaboratorium->bind_param(
            "s",
            $id_laboratorium
        );

        if (!$checkLaboratorium->execute()) {
            throw new Exception(
                'Gagal memeriksa data laboratorium.'
            );
        }

        $resultLaboratorium = $checkLaboratorium->get_result();

        if ($resultLaboratorium->num_rows === 0) {

            $checkLaboratorium->close();

            throw new Exception(
                'Data laboratorium tidak ditemukan.'
            );
        }

        $checkLaboratorium->close();

        // ========================================================
        // INSERT DIAGNOSTIC REPORT
        // ========================================================
        if ($id_laboratorium_diagnostic === '') {

            // Generate UUID baru
            $id_laboratorium_diagnostic = generateUUIDv4();

            $insertDiagnostic = $Conn->prepare(
                "INSERT INTO laboratorium_diagnostic (
                    id_laboratorium_diagnostic,
                    id_laboratorium,
                    conclusion,
                    clinical,
                    icd_10_code,
                    icd_10_display,
                    icd_10_system
                ) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$insertDiagnostic) {
                throw new Exception(
                    'Gagal mempersiapkan query insert Diagnostic Report.'
                );
            }

            $insertDiagnostic->bind_param(
                "sssssss",
                $id_laboratorium_diagnostic,
                $id_laboratorium,
                $conclusion,
                $clinical,
                $icd_10_code,
                $icd_10_display,
                $icd_10_system
            );

            if (!$insertDiagnostic->execute()) {

                $error = $insertDiagnostic->error;

                $insertDiagnostic->close();

                throw new Exception(
                    'Gagal menyimpan Diagnostic Report: ' . $error
                );
            }

            $insertDiagnostic->close();

            $message = 'Diagnostic Report berhasil disimpan.';

        } else {

            // ====================================================
            // CEK DATA DIAGNOSTIC
            // ====================================================
            $checkDiagnostic = $Conn->prepare(
                "SELECT id_laboratorium_diagnostic
                FROM laboratorium_diagnostic
                WHERE id_laboratorium_diagnostic = ?
                AND id_laboratorium = ?
                LIMIT 1"
            );

            if (!$checkDiagnostic) {
                throw new Exception(
                    'Gagal mempersiapkan query pemeriksaan Diagnostic Report.'
                );
            }

            $checkDiagnostic->bind_param(
                "ss",
                $id_laboratorium_diagnostic,
                $id_laboratorium
            );

            if (!$checkDiagnostic->execute()) {
                throw new Exception(
                    'Gagal memeriksa data Diagnostic Report.'
                );
            }

            $resultDiagnostic = $checkDiagnostic->get_result();

            // Jika ID diagnostic tidak ditemukan,
            // lakukan INSERT untuk menghindari UPDATE 0 baris.
            if ($resultDiagnostic->num_rows === 0) {

                $checkDiagnostic->close();

                $insertDiagnostic = $Conn->prepare(
                    "INSERT INTO laboratorium_diagnostic (
                        id_laboratorium_diagnostic,
                        id_laboratorium,
                        conclusion,
                        clinical,
                        icd_10_code,
                        icd_10_display,
                        icd_10_system
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                if (!$insertDiagnostic) {
                    throw new Exception(
                        'Gagal mempersiapkan query insert Diagnostic Report.'
                    );
                }

                $insertDiagnostic->bind_param(
                    "sssssss",
                    $id_laboratorium_diagnostic,
                    $id_laboratorium,
                    $conclusion,
                    $clinical,
                    $icd_10_code,
                    $icd_10_display,
                    $icd_10_system
                );

                if (!$insertDiagnostic->execute()) {

                    $error = $insertDiagnostic->error;

                    $insertDiagnostic->close();

                    throw new Exception(
                        'Gagal menyimpan Diagnostic Report: ' . $error
                    );
                }

                $insertDiagnostic->close();

                $message = 'Diagnostic Report berhasil disimpan.';

            } else {

                $checkDiagnostic->close();

                // ====================================================
                // UPDATE DIAGNOSTIC REPORT
                // ====================================================
                $updateDiagnostic = $Conn->prepare(
                    "UPDATE laboratorium_diagnostic
                    SET
                        conclusion = ?,
                        clinical = ?,
                        icd_10_code = ?,
                        icd_10_display = ?,
                        icd_10_system = ?
                    WHERE
                        id_laboratorium_diagnostic = ?
                        AND id_laboratorium = ?"
                );

                if (!$updateDiagnostic) {
                    throw new Exception(
                        'Gagal mempersiapkan query update Diagnostic Report.'
                    );
                }

                $updateDiagnostic->bind_param(
                    "sssssss",
                    $conclusion,
                    $clinical,
                    $icd_10_code,
                    $icd_10_display,
                    $icd_10_system,
                    $id_laboratorium_diagnostic,
                    $id_laboratorium
                );

                if (!$updateDiagnostic->execute()) {

                    $error = $updateDiagnostic->error;

                    $updateDiagnostic->close();

                    throw new Exception(
                        'Gagal mengupdate Diagnostic Report: ' . $error
                    );
                }

                $updateDiagnostic->close();

                $message = 'Diagnostic Report berhasil diperbarui.';
            }
        }

        // ========================================================
        // UPDATE STATUS LABORATORIUM
        // ========================================================
        $status_laboratorium = "Selesai";

        $updateLaboratorium = $Conn->prepare(
            "UPDATE laboratorium
            SET status = ?
            WHERE id_laboratorium = ?"
        );

        if (!$updateLaboratorium) {
            throw new Exception(
                'Gagal mempersiapkan query update status laboratorium.'
            );
        }

        $updateLaboratorium->bind_param(
            "ss",
            $status_laboratorium,
            $id_laboratorium
        );

        if (!$updateLaboratorium->execute()) {

            $error = $updateLaboratorium->error;

            $updateLaboratorium->close();

            throw new Exception(
                'Gagal memperbarui status laboratorium: ' . $error
            );
        }

        $updateLaboratorium->close();

        // ========================================================
        // COMMIT TRANSACTION
        // ========================================================
        $Conn->commit();

        // ========================================================
        // RESPONSE SUCCESS
        // ========================================================
        jsonResponse(
            'success',
            $message
        );

    } catch (Throwable $e) {

        // ========================================================
        // ROLLBACK TRANSACTION
        // ========================================================
        $Conn->rollback();

        jsonResponse(
            'error',
            'Terjadi kesalahan pada proses penyimpanan Diagnostic Report.',
            $e->getMessage()
        );
    }
?>