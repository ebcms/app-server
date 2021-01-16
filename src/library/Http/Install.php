<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Server\Traits\DirTrait;
use Ebcms\App;
use Exception;
use Throwable;
use Ebcms\Session;
use ZipArchive;

use function Composer\Autoload\includeFile;

class Install extends Common
{
    use DirTrait;

    public function get(
        App $app,
        Session $session
    ) {
        $upgrade = $session->get('upgrade');
        try {
            $this->unZip($upgrade['tmpfile'], $app->getAppPath());

            $upgrade_file = $app->getAppPath() . '/upgrade.php';
            if (file_exists($upgrade_file)) {
                includeFile($upgrade_file);
            }

            if (file_exists($upgrade_file)) {
                unlink($upgrade_file);
            }
            if (file_exists($upgrade['tmpfile'])) {
                unlink($upgrade['tmpfile']);
            }

            return $this->success('更新成功!');
        } catch (Throwable $th) {
            try {
                foreach ($upgrade['backup_dirs'] as $dir) {
                    if (file_exists($app->getAppPath() . $dir)) {
                        unlink($app->getAppPath() . $dir);
                    } elseif (is_dir($app->getAppPath() . $dir)) {
                        $this->delDir($app->getAppPath() . $dir);
                    }
                }
                $this->copyDir($upgrade['backup_path'], $app->getAppPath());
            } catch (\Throwable $th2) {
                return $this->failure('(还原失败!)' . $th->getMessage() . $th2->getMessage());
            }
            return $this->failure($th->getMessage());
        }
    }

    private function unZip($file, $destination)
    {
        $zip = new ZipArchive();
        if ($zip->open($file) !== TRUE) {
            throw new Exception('Could not open archive');
        }
        $zip->extractTo($destination);
        $zip->close();
    }
}
