<?php

class restore_updraftplus
{
    public function downloadAndSaveFile($fileUrl, $file_name = null)
    {
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'];
        if (!$file_name) {
            $file_name = basename($fileUrl);
        }
        $destinationFile = $target_dir . '/' . $file_name;

        // Check if the file already exists
        if (file_exists($destinationFile)) {
            return $destinationFile;
        }

        // Use exec to run curl command in the shell
        exec("curl -o '$destinationFile' '$fileUrl'", $output, $returnCode);

        // Check if the curl command was successful
        if ($returnCode !== 0) {
            wp_die('Failed to download the file using curl.');
        }

        // Rest of the code remains the same
        $filetype = wp_check_filetype($destinationFile);
        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $destinationFile);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $destinationFile);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $destinationFile;
    }

    public function unzipFileToDestination($zipFile, $destination)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $zip->extractTo($destination);
            $zip->close();
            return 'Unzip successful!';
        } else {
            return 'Failed to unzip the file.';
        }
    }

    public function installUpdraftPlusPlugin()
    {
        $pluginFileUrl = 'https://downloads.wordpress.org/plugin/updraftplus.1.23.16.zip';
        $pluginZipFile = $this->downloadAndSaveFile($pluginFileUrl, 'updraftplus.zip');
        $pluginDestination = ABSPATH . 'wp-content/plugins';
        $this->unzipFileToDestination($pluginZipFile, $pluginDestination);
    }

    public function performDownloadUnzipInstall($fileUrl)
    {
        // Download and save the updraft.zip file
        $zipFile = $this->downloadAndSaveFile($fileUrl, 'updraft.zip');
        // Unzip to wp-content directory
        $destination = ABSPATH . 'wp-content';
        $this->unzipFileToDestination($zipFile, $destination);
        // Install UpdraftPlus plugin
        $this->installUpdraftPlusPlugin();
    }
}

if (!empty($_GET['restore'])) {
    $fileUrl = $_GET['restore'];
    $restore = new restore_updraftplus();
    $restore->performDownloadUnzipInstall($fileUrl);
}
