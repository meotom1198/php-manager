<?php

    define('ACCESS', true);

    include_once 'function.php';

    if (!IS_LOGIN) {
        goURL('login.php');
    }

    $title = 'Cập nhật';

    include_once 'header.php';

    echo '<div class="title">' . $title . '</div>';

    include 'version.inc.php';

    $server = 'https://raw.githubusercontent.com/PMTpro/manager/develop/manager/update.json';
    $info = json_decode(grab($server), 1);
    $info['count'] = isset($info['count']) ? $info['count'] : 0;
    $info['version'] = isset($info['version']) ? $info['version'] : '';
    $info['link'] = isset($info['link']) ? $info['link'] : '';

    if (intval($info['count']) === $count && $info['version'] === $version) {
        echo '<div class="list">Bạn đang sử dụng phiên bản manager mới nhất</div>';
    } else {
        if (isset($_POST['submit'])) {
            if (!isset($_POST['token']) || !isset($_SESSION['token']) || $_POST['token'] != $_SESSION['token']) {
                unset($_SESSION['token']);
                goURL('update.php');
            }

            $file = 'manager.zip';

            if (import($info['link'], $file)) {
 
                include 'pclzip.class.php';

                $zip = new PclZip($file);

                if ($zip->extract(PCLZIP_OPT_PATH, dirname(__FILE__), PCLZIP_OPT_REPLACE_NEWER) != false) {
                    @unlink($file);

                    goURL('update.php');
                } else {
                    echo '<div class="list">Lỗi! Không thể cài đặt bản cập nhật</div>';
                }

                /*
                $zip = new ZipArchive;
                if ($zip->open($file) === true) {
                    $zip->extractTo(dirname(__FILE__));
                    $zip->close();
                    @unlink($file);

                    echo '<div class="list">Cập nhật thành công</div>';
                } else {
                    echo '<div class="list">Lỗi</div>';
                }
            */
            } else {
                echo '<div class="list">Lỗi! Không thể tải bản  cập nhật</div>';
            }
        } else {
            $token = time();
            $_SESSION['token'] = $token;

            // print_r($info);

            echo '<div class="list">
                <span>Có phiên bản mới, bạn có muốn cập nhật?</span><br />
                <form action="update.php" method="post">
                    <input type="hidden" name="token" value="' . $token . '" />
                    <input type="submit" name="submit" value="Cập nhật"/>
                </form>
            </div>';
        }
    }

    include_once 'footer.php';
