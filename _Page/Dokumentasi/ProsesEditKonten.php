<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    $response = [
        "status"  => "error",
        "message" => "Terjadi kesalahan sistem"
    ];

    if(empty($SessionIdAccess)){
        $response["message"] = "Sesi Login Berakhir";
        echo json_encode($response);
        exit;
    }

    $id_dokumentasi = intval($_POST['id_dokumentasi'] ?? 0);
    $id_dokumentasi_content = intval($_POST['id_dokumentasi_content'] ?? 0);

    if($id_dokumentasi <= 0 || $id_dokumentasi_content <= 0){
        $response["message"] = "Data wajib tidak lengkap";
        echo json_encode($response);
        exit;
    }

    // Ambil data lama agar update sesuai tipe konten asli
    $stmt = $Conn->prepare("
        SELECT type_content, value_content, file_size, file_type
        FROM dokumentasi_content
        WHERE id_dokumentasi_content = ? AND id_dokumentasi = ?
    ");
    $stmt->bind_param("ii", $id_dokumentasi_content, $id_dokumentasi);
    if(!$stmt->execute()){
        $response["message"] = "Gagal mengambil data konten";
        echo json_encode($response);
        exit;
    }

    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    $stmt->close();

    if(empty($oldData)){
        $response["message"] = "Data konten dokumentasi tidak ditemukan";
        echo json_encode($response);
        exit;
    }

    $type_content = $oldData['type_content'];
    $value_content = $oldData['value_content'];
    $file_size = $oldData['file_size'];
    $file_type = $oldData['file_type'];

    // Ambil value dari berbagai kemungkinan nama input form edit
    $raw_value_content = $_POST['value_content'] ?? null;
    $raw_value_content_edit = $_POST['value_content_edit'] ?? null;

    $single_value = '';
    if(is_string($raw_value_content)){
        $single_value = trim($raw_value_content);
    } elseif(is_string($raw_value_content_edit)) {
        $single_value = trim($raw_value_content_edit);
    }

    $list_values = [];
    if(is_array($raw_value_content)){
        $list_values = $raw_value_content;
    }
    if(is_array($raw_value_content_edit)){
        $list_values = $raw_value_content_edit;
    }

    // Proses update value per tipe konten
    if($type_content === "text"){
        if($single_value === ''){
            $response["message"] = "Konten tidak boleh kosong";
            echo json_encode($response);
            exit;
        }
        $value_content = $single_value;
        $file_size = null;
        $file_type = null;
    }

    if($type_content === "list"){
        if(empty($list_values)){
            $response["message"] = "List tidak boleh kosong";
            echo json_encode($response);
            exit;
        }

        $ul = "<ul>";
        $valid_count = 0;
        foreach($list_values as $item){
            $item = htmlspecialchars(trim($item), ENT_QUOTES, 'UTF-8');
            if($item !== ""){
                $ul .= "<li>{$item}</li>";
                $valid_count++;
            }
        }
        $ul .= "</ul>";

        if($valid_count === 0){
            $response["message"] = "List tidak boleh kosong";
            echo json_encode($response);
            exit;
        }

        $value_content = $ul;
        $file_size = null;
        $file_type = null;
    }

    if($type_content === "image_link" || $type_content === "video_link"){
        if($single_value === ''){
            $response["message"] = "URL tidak boleh kosong";
            echo json_encode($response);
            exit;
        }

        $url = filter_var($single_value, FILTER_VALIDATE_URL);
        if(!$url){
            $response["message"] = "URL tidak valid";
            echo json_encode($response);
            exit;
        }

        $value_content = $url;
        $file_size = null;
        $file_type = null;
    }

    if($type_content === "image" || $type_content === "video"){
        // Jika form dikirim via serialize(), file memang tidak terkirim.
        // Maka value lama tetap dipakai.
        if(!empty($_FILES['value_content']['name'])){
            if($_FILES['value_content']['error'] !== UPLOAD_ERR_OK){
                $response["message"] = "Terjadi kesalahan upload file";
                echo json_encode($response);
                exit;
            }

            $file = $_FILES['value_content'];
            $size = $file['size'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedImage = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/bmp'
            ];
            $allowedVideo = [
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/quicktime',
                'video/x-msvideo',
                'video/x-matroska'
            ];

            if($type_content === "image"){
                if(!in_array($mime, $allowedImage)){
                    $response["message"] = "Format gambar tidak valid";
                    echo json_encode($response);
                    exit;
                }
                if($size > 5 * 1024 * 1024){
                    $response["message"] = "Ukuran gambar maksimal 5MB";
                    echo json_encode($response);
                    exit;
                }
                $dir = __DIR__ . "/../../assets/Dokumentasi/image/";
                $oldFilePath = __DIR__ . "/../../assets/Dokumentasi/image/" . $oldData['value_content'];
            } else {
                if(!in_array($mime, $allowedVideo)){
                    $response["message"] = "Format video tidak valid";
                    echo json_encode($response);
                    exit;
                }
                if($size > 50 * 1024 * 1024){
                    $response["message"] = "Ukuran video maksimal 50MB";
                    echo json_encode($response);
                    exit;
                }
                $dir = __DIR__ . "/../../assets/Dokumentasi/video/";
                $oldFilePath = __DIR__ . "/../../assets/Dokumentasi/video/" . $oldData['value_content'];
            }

            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $new_name = generateRandomString(20) . "." . $ext;
            $target = $dir . $new_name;

            if(!move_uploaded_file($file['tmp_name'], $target)){
                $response["message"] = "Upload file gagal";
                echo json_encode($response);
                exit;
            }

            if(!empty($oldData['value_content']) && is_file($oldFilePath)){
                @unlink($oldFilePath);
            }

            $value_content = $new_name;
            $file_size = $size;
            $file_type = $mime;
        }
    }

    // Simpan perubahan
    if($type_content === "image" || $type_content === "video"){
        $stmt = $Conn->prepare("
            UPDATE dokumentasi_content
            SET value_content = ?, file_size = ?, file_type = ?
            WHERE id_dokumentasi_content = ? AND id_dokumentasi = ?
        ");
        $stmt->bind_param(
            "sisii",
            $value_content,
            $file_size,
            $file_type,
            $id_dokumentasi_content,
            $id_dokumentasi
        );
    } else {
        $stmt = $Conn->prepare("
            UPDATE dokumentasi_content
            SET value_content = ?, file_size = NULL, file_type = NULL
            WHERE id_dokumentasi_content = ? AND id_dokumentasi = ?
        ");
        $stmt->bind_param(
            "sii",
            $value_content,
            $id_dokumentasi_content,
            $id_dokumentasi
        );
    }

    if(!$stmt->execute()){
        $response["message"] = "Gagal memperbarui data";
        $stmt->close();
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    echo json_encode([
        "status"  => "success",
        "message" => "Konten berhasil diperbarui",
        "id"      => $id_dokumentasi
    ]);
?>
