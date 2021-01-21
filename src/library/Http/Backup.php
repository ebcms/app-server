<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Server\Traits\DirTrait;
use Ebcms\App;
use Ebcms\Session;

class Backup extends Common
{
    use DirTrait;

    public function get(
        App $app,
        Session $session
    ) {
        $upgrade = $session->get('upgrade');
        $upgrade['backup_path'] = $app->getAppPath() . '/backup/' . 'server_' . date('YmdHis');
        $upgrade['backup_dirs'] = [
            'config',
            'hook',
            'plugin',
            'vendor',
            'composer.json',
            'composer.lock',
        ];
        $this->backup($upgrade['backup_dirs'], $app->getAppPath(), $upgrade['backup_path']);
        $session->set('upgrade', $upgrade);
        return $this->success('备份成功！', '', $upgrade);
    }

    private function backup(array $items, string $path, string $target)
    {
        foreach ($items as $item) {
            if (is_file($path . '/' . $item)) {
                if (!is_dir(dirname($target . '/' . $item))) {
                    mkdir(dirname($target . '/' . $item), 0755, true);
                }
                copy($path . '/' . $item, $target . '/' . $item);
            } else {
                $this->copyDir($path . '/' . $item, $target . '/' . $item);
            }
        }
    }
}
