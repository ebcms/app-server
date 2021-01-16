<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Server\Model\Server;
use Ebcms\Session;

class Source extends Common
{
    public function get(
        Server $server,
        Session $session
    ) {
        $res = $server->query('/source');
        if ($res['status'] == 200) {
            $session->set('upgrade', $res['data']);
            return $this->success('获取成功！', '', $res['data']);
        } else {
            return $this->failure($res['message']);
        }
    }
}
