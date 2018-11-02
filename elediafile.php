<?php
require_once('config.php');
require_once('lib/filelib.php');

$relativepath = get_file_argument();
$forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);

// Relative path must start with '/'
if (!$relativepath) {
    print_error('invalidargorconf');
} else if ($relativepath[0] != '/') {
    print_error('pathdoesnotstartslash');
}

// Extract relative path components
$args = explode('/', ltrim($relativepath, '/'));

if (count($args) < 3) { // Always at least context, component and filearea
    print_error('invalidarguments');
}

if (!$elediafile_contextid = get_config(null, 'elediafile_contextid')) {
    send_file_not_found();
    exit;
}
$elediafile_contextid = intval($elediafile_contextid);

$contextid = (int)array_shift($args);
if($contextid !== $elediafile_contextid) {
    send_file_not_found();
    exit;
}

$component = clean_param(array_shift($args), PARAM_SAFEDIR);
$filearea  = clean_param(array_shift($args), PARAM_SAFEDIR);

list($context, $course, $cm) = get_context_info_array($contextid);

$fs = get_file_storage();

if ($component == 'mod_folder') {
    array_shift($args); // Ignore revision - designed to prevent caching problems only
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_folder/content/0/$relativepath";

    $filetype = mimeinfo('icon', $relativepath);
    switch($filetype) {
        case 'text':
        case 'html':
        case 'image': // Is needed for ico files.
        case 'jpeg':
        case 'gif':
        case 'png':
        case 'pdf':
        case 'flash':
            if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
                send_file_not_found();
            }
            send_stored_file($file, 86400, 0, false);
            exit;
            break;

        default:
            send_file_not_found();
    }

// ========================================================================================================================
}
send_file_not_found();
