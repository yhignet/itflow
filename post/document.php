<?php

/*
 * ITFlow - GET/POST request handler for client documents (and document folders)
 */

if (isset($_POST['add_document'])) {

    validateTechRole();

    $client_id = intval($_POST['client_id']);
    $name = sanitizeInput($_POST['name']);
    $content = mysqli_real_escape_string($mysqli,$_POST['content']);
    $content_raw = sanitizeInput($_POST['name'] . " " . str_replace("<", " <", $_POST['content']));
    // Content Raw is used for FULL INDEX searching. Adding a space before HTML tags to allow spaces between newlines, bulletpoints, etc. for searching.

    $folder = intval($_POST['folder']);

    // Document add query
    $add_document = mysqli_query($mysqli,"INSERT INTO documents SET document_name = '$name', document_content = '$content', document_content_raw = '$content_raw', document_template = 0, document_folder_id = $folder, document_client_id = $client_id");
    $document_id = mysqli_insert_id($mysqli);

    // Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Document', log_action = 'Create', log_description = 'Created $name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id");

    $_SESSION['alert_message'] = "Document <strong>$name</strong> created";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_POST['add_document_template'])) {

    validateTechRole();

    $client_id = intval($_POST['client_id']);
    $name = sanitizeInput($_POST['name']);
    $content = mysqli_real_escape_string($mysqli,$_POST['content']);
    $content_raw = sanitizeInput($_POST['name'] . " " . str_replace("<", " <", $_POST['content']));
    // Content Raw is used for FULL INDEX searching. Adding a space before HTML tags to allow spaces between newlines, bulletpoints, etc. for searching.

    // Document add query
    $add_document = mysqli_query($mysqli,"INSERT INTO documents SET document_name = '$name', document_content = '$content', document_content_raw = '$content_raw', document_template = 1, document_folder_id = 0, document_client_id = 0");
    $document_id = mysqli_insert_id($mysqli);

    // Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Document Template', log_action = 'Create', log_description = '$session_name created document template $name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id, log_entity_id = $document_id");

    $_SESSION['alert_message'] = "Document template <strong>$name</strong> created";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_POST['add_document_from_template'])) {

    // ROLE Check
    validateTechRole();

    // GET POST Data
    $client_id = intval($_POST['client_id']);
    $document_name = sanitizeInput($_POST['name']);
    $document_template_id = intval($_POST['document_template_id']);
    $folder = intval($_POST['folder']);

    //GET Document Info
    $sql_document = mysqli_query($mysqli,"SELECT * FROM documents WHERE document_id = $document_template_id");

    $row = mysqli_fetch_array($sql_document);

    $document_template_name = sanitizeInput($row['document_name']);
    $content = mysqli_real_escape_string($mysqli,$row['document_content']);
    $content_raw = sanitizeInput($_POST['name'] . " " . str_replace("<", " <", $row['document_content']));

    // Document add query
    $add_document = mysqli_query($mysqli,"INSERT INTO documents SET document_name = '$document_name', document_content = '$content', document_content_raw = '$content_raw', document_template = 0, document_folder_id = $folder, document_client_id = $client_id");

    $document_id = mysqli_insert_id($mysqli);

    // Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Document', log_action = 'Create', log_description = 'Document $document_name created from template $document_template_name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id, log_entity_id = $document_id");

    $_SESSION['alert_message'] = "Document <strong>$document_name</strong> created from template";

    header("Location: client_document_details.php?client_id=$client_id&document_id=$document_id");

}

if (isset($_POST['edit_document'])) {

    validateTechRole();

    $document_id = intval($_POST['document_id']);
    $client_id = intval($_POST['client_id']);
    $name = sanitizeInput($_POST['name']);
    $content = mysqli_real_escape_string($mysqli,$_POST['content']);
    $content_raw = sanitizeInput($_POST['name'] . " " . str_replace("<", " <", $_POST['content']));
    // Content Raw is used for FULL INDEX searching. Adding a space before HTML tags to allow spaces between newlines, bulletpoints, etc. for searching.
    $folder = intval($_POST['folder']);

    // Document edit query
    mysqli_query($mysqli,"UPDATE documents SET document_name = '$name', document_content = '$content', document_content_raw = '$content_raw', document_folder_id = $folder WHERE document_id = $document_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Document', log_action = 'Modify', log_description = '$session_name updated document $name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id, log_entity_id = $document_id");


    $_SESSION['alert_message'] = "Document <strong>$name</strong> updated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_POST['edit_document_template'])) {

    validateTechRole();

    $document_id = intval($_POST['document_id']);
    $name = sanitizeInput($_POST['name']);
    $content = mysqli_real_escape_string($mysqli,$_POST['content']);
    $content_raw = sanitizeInput($_POST['name'] . " " . str_replace("<", " <", $_POST['content']));
    // Content Raw is used for FULL INDEX searching. Adding a space before HTML tags to allow spaces between newlines, bulletpoints, etc. for searching.

    // Document edit query
    mysqli_query($mysqli,"UPDATE documents SET document_name = '$name', document_content = '$content', document_content_raw = '$content_raw' WHERE document_id = $document_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Document Template', log_action = 'Modify', log_description = '$session_name modified document template $name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_user_id = $session_user_id, log_entity_id = $document_id");


    $_SESSION['alert_message'] = "Document Template <strong>$name</strong> updated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_GET['delete_document'])) {

    validateAdminRole();

    $document_id = intval($_GET['delete_document']);

    mysqli_query($mysqli,"DELETE FROM documents WHERE document_id = $document_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Document', log_action = 'Delete', log_description = '$document_id', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_user_id = $session_user_id");

    $_SESSION['alert_message'] = "Document deleted";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_POST['add_folder'])) {

    validateTechRole();

    $client_id = intval($_POST['client_id']);
    $folder_name = sanitizeInput($_POST['folder_name']);

    // Document folder add query
    $add_folder = mysqli_query($mysqli,"INSERT INTO folders SET folder_name = '$folder_name', folder_client_id = $client_id");
    $folder_id = mysqli_insert_id($mysqli);

    // Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Folder', log_action = 'Create', log_description = '$session_name created folder $folder_name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id, log_entity_id = $folder_id");

    $_SESSION['alert_message'] = "Folder <strong>$folder_name</strong> created";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_POST['rename_folder'])) {

    validateTechRole();

    $folder_id = intval($_POST['folder_id']);
    $client_id = intval($_POST['client_id']);
    $folder_name = sanitizeInput($_POST['folder_name']);

    // Folder edit query
    mysqli_query($mysqli,"UPDATE folders SET folder_name = '$folder_name' WHERE folder_id = $folder_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Folder', log_action = 'Modify', log_description = '$session_name renamed folder to $folder_name', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_client_id = $client_id, log_user_id = $session_user_id, log_entity_id = $folder_id");

    $_SESSION['alert_message'] = "Folder <strong>$folder_name</strong> renamed";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_GET['delete_folder'])) {

    validateAdminRole();

    $folder_id = intval($_GET['delete_folder']);

    mysqli_query($mysqli,"DELETE FROM folders WHERE folder_id = $folder_id");

    // Move files in deleted folder back to the root folder /
    $sql_documents = mysqli_query($mysqli,"SELECT * FROM documents WHERE document_folder_id = $folder_id");
    while($row = mysqli_fetch_array($sql_documents)) {
        $document_id = intval($row['document_id']);

        mysqli_query($mysqli,"UPDATE documents SET document_folder_id = 0 WHERE document_id = $document_id");
    }

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Folder', log_action = 'Delete', log_description = '$folder_id', log_ip = '$session_ip', log_user_agent = '$session_user_agent', log_user_id = $session_user_id");

    $_SESSION['alert_message'] = "Folder deleted";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}
