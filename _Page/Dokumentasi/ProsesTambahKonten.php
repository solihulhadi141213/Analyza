<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    if(empty($SessionIdAccess)){
        echo json_encode([
            "status"=>"error",
            "message"=>"Sesi Login Berakhir"
        ]);
        exit;
    }

    if(empty($_POST['id_dokumentasi']) || empty($_POST['type_content']) || empty($_POST['order'])){
        echo json_encode([
            "status"=>"error",
            "message"=>"Data wajib tidak lengkap"
        ]);
        exit;
    }

    $id_dokumentasi = intval($_POST['id_dokumentasi']);
    $type_content   = $_POST['type_content'];
    $order          = $_POST['order'];
    $order_by       = !empty($_POST['order_by']) ? intval($_POST['order_by']) : null;

    $value_content = "";
    $file_size     = null;
    $file_type     = null;


    /* =====================================================
    HANDLE CONTENT TYPE
    ===================================================== */

    // ================= LIST =================
    if($type_content === "list"){

        if(empty($_POST['value_content']) || !is_array($_POST['value_content'])){
            echo json_encode(["status"=>"error","message"=>"List tidak boleh kosong"]);
            exit;
        }

        $ul = "<ul>";
        foreach($_POST['value_content'] as $item){
            $item = htmlspecialchars(trim($item), ENT_QUOTES, 'UTF-8');
            if($item !== ""){
                $ul .= "<li>{$item}</li>";
            }
        }
        $ul .= "</ul>";

        $value_content = $ul;
    }


    // ================= IMAGE / VIDEO =================
    if($type_content === "image" || $type_content === "video"){

        if(empty($_FILES['value_content']['name'])){
            echo json_encode(["status"=>"error","message"=>"File wajib dipilih"]);
            exit;
        }

        if($_FILES['value_content']['error'] !== UPLOAD_ERR_OK){
            echo json_encode(["status"=>"error","message"=>"Terjadi kesalahan upload"]);
            exit;
        }

        $file = $_FILES['value_content'];
        $size = $file['size'];

        // Ambil MIME asli
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
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
                echo json_encode(["status"=>"error","message"=>"Format gambar tidak valid"]);
                exit;
            }

            if($size > 5 * 1024 * 1024){
                echo json_encode(["status"=>"error","message"=>"Maksimal 5MB"]);
                exit;
            }

            $dir = "../../assets/Dokumentasi/image/";
        }

        if($type_content === "video"){

            if(!in_array($mime, $allowedVideo)){
                echo json_encode(["status"=>"error","message"=>"Format video tidak valid"]);
                exit;
            }

            if($size > 50 * 1024 * 1024){
                echo json_encode(["status"=>"error","message"=>"Maksimal 50MB"]);
                exit;
            }

            $dir = "../../assets/Dokumentasi/video/";
        }

        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_name  = generateRandomString(20) . "." . $ext;
        $target    = $dir . $new_name;

        if(!move_uploaded_file($file['tmp_name'], $target)){
            echo json_encode(["status"=>"error","message"=>"Upload gagal"]);
            exit;
        }

        $value_content = $new_name;
        $file_size     = $size;
        $file_type     = $mime; // <<< MIME TYPE DISIMPAN
    }


    // ================= TEXT =================
    if($type_content === "text"){

        if(empty($_POST['value_content'])){
            echo json_encode(["status"=>"error","message"=>"Konten tidak boleh kosong"]);
            exit;
        }

        // Tetap simpan HTML Quill
        $value_content = $_POST['value_content'];
    }


    // ================= LINK =================
    if($type_content === "image_link" || $type_content === "video_link"){

        if(empty($_POST['value_content'])){
            echo json_encode(["status"=>"error","message"=>"URL kosong"]);
            exit;
        }

        $url = filter_var($_POST['value_content'], FILTER_VALIDATE_URL);

        if(!$url){
            echo json_encode(["status"=>"error","message"=>"URL tidak valid"]);
            exit;
        }

        $value_content = $url;
    }



    /* =====================================================
    HANDLE ORDER SYSTEM
    ===================================================== */

    mysqli_begin_transaction($Conn);

    try{

        if(empty($order_by)){

            $stmt = $Conn->prepare("
                SELECT MAX(order_content) as max_order
                FROM dokumentasi_content
                WHERE id_dokumentasi = ?
            ");
            $stmt->bind_param("i", $id_dokumentasi);
            $stmt->execute();
            $result = $stmt->get_result();
            $data   = $result->fetch_assoc();

            $order_content = $data['max_order'] ? $data['max_order'] + 1 : 1;

        } else {

            if($order === "down"){
                $order_content = $order_by + 1;
            } else {
                $order_content = $order_by;
            }

            $stmt = $Conn->prepare("
                UPDATE dokumentasi_content
                SET order_content = order_content + 1
                WHERE id_dokumentasi = ?
                AND order_content >= ?
            ");
            $stmt->bind_param("ii", $id_dokumentasi, $order_content);
            $stmt->execute();
        }


        // INSERT DATA
        $stmt = $Conn->prepare("
            INSERT INTO dokumentasi_content
            (id_dokumentasi, order_content, type_content, value_content, file_size, file_type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iissis",
            $id_dokumentasi,
            $order_content,
            $type_content,
            $value_content,
            $file_size,
            $file_type
        );

        $stmt->execute();
        $insert_id = $stmt->insert_id;

        mysqli_commit($Conn);

        echo json_encode([
            "status"=>"success",
            "message"=>"Konten berhasil ditambahkan",
            "payload"=>[
                "insert_id"=>$insert_id
            ]
        ]);
        exit;

    } catch(Exception $e){

        mysqli_rollback($Conn);

        echo json_encode([
            "status"=>"error",
            "message"=>"Gagal menyimpan data"
        ]);
        exit;
    }
?>