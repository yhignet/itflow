<?php

/*
 * ITFlow - GET/POST request handler for client files/uploads
 */

if (isset($_POST['add_file'])) {
    $client_id = intval($_POST['client_id']);
    $file_name = sanitizeInput($_POST['new_name']);

    $extarr = explode('.', $_FILES['file']['name']);
    $file_extension = sanitizeInput(strtolower(end($extarr)));

    // If the user-inputted name is empty, revert to the name of the file on disk/uploaded
    if (empty($file_name)) {
        $file_name = sanitizeInput($_FILES['file']['name']);
    }

    if (!file_exists("uploads/clients/$client_id")) {
        mkdir("uploads/clients/$client_id");
    }

    //Check to see if a file is attached
    if ($_FILES['file']['tmp_name'] != '') {

        if ($file_reference_name = checkFileUpload($_FILES['file'], array('jpg', 'jpeg', 'gif', 'png', 'webp', 'pdf', 'txt', 'md', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'xlsm', 'zip', 'tar', 'gz'))) {

            $file_tmp_path = $_FILES['file']['tmp_name'];

            // directory in which the uploaded file will be moved
            $upload_file_dir = "uploads/clients/$client_id/";
            $dest_path = $upload_file_dir . $file_reference_name;

            move_uploaded_file($file_tmp_path, $dest_path);

            mysqli_query($mysqli,"INSERT INTO files SET file_reference_name = '$file_reference_name', file_name = '$file_name', file_ext = '$file_extension', file_client_id = $client_id");

            //Logging
            $file_id = intval(mysqli_insert_id($mysqli));
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'File', log_action = 'Upload', log_description = '$file_name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id, log_entity_id = $file_id");

            $_SESSION['alert_message'] = 'File successfully uploaded.';

        } else {

            $_SESSION['alert_message'] = 'There was an error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
        }
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_POST['delete_file'])) {

    validateAdminRole();
    validateCSRFToken($_POST['csrf_token']);

    $file_id = intval($_POST['file_id']);

    $sql_file = mysqli_query($mysqli,"SELECT * FROM files WHERE file_id = $file_id");
    $row = mysqli_fetch_array($sql_file);
    $client_id = intval($row['file_client_id']);
    $file_name = sanitizeInput($row['file_name']);
    $file_reference_name = sanitizeInput($row['file_reference_name']);

    unlink("uploads/clients/$client_id/$file_reference_name");

    mysqli_query($mysqli,"DELETE FROM files WHERE file_id = $file_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'File', log_action = 'Delete', log_description = '$file_name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = '$client_id', log_user_id = $session_user_id, log_entity_id = $file_id");

    $_SESSION['alert_type'] = "error";
    $_SESSION['alert_message'] = "File <strong>$file_name</strong> deleted";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}
