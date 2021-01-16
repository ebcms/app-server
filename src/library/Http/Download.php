<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Server\Traits\DirTrait;
use Ebcms\App;
use Ebcms\Session;

class Download extends Common
{
    use DirTrait;

    public function get(
        App $app,
        Session $session
    ) {
        $upgrade = $session->get('upgrade');
        try {
            $content = file_get_contents($upgrade['source']);
            if (md5($content) != $upgrade['md5']) {
                return $this->failure('校验失败！');
            }
        } catch (\Throwable $th) {
            return $this->failure('升级包下载失败，请稍后再试~');
        }

        $tmpfile = $app->getAppPath() . '/runtime/upgrade.tmp';

        file_put_contents($tmpfile, $content);

        $upgrade['tmpfile'] = $tmpfile;
        $session->set('upgrade', $upgrade);

        return $this->success('下载成功！');
    }
}
