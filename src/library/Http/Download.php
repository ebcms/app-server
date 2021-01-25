<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Server\Traits\DirTrait;
use Ebcms\Session;
use Throwable;

class Download extends Common
{
    use DirTrait;

    public function get(
        Session $session
    ) {
        $upgrade = $session->get('upgrade');
        try {
            $content = file_get_contents($upgrade['source']);
        } catch (Throwable $th) {
            return $this->failure('升级包下载失败，请稍后再试~');
        }

        if (md5($content) != $upgrade['md5']) {
            return $this->failure('校验失败！');
        }

        $tmpfile = tempnam(sys_get_temp_dir(), 'serverupgrade');

        file_put_contents($tmpfile, $content);

        $upgrade['tmpfile'] = $tmpfile;
        $session->set('upgrade', $upgrade);

        return $this->success('下载成功！');
    }
}
