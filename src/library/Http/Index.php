<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Server\Model\Server;
use Ebcms\Template;

class Index extends Common
{
    public function get(
        Server $server,
        Template $template
    ) {
        $data = $server->query('/version');
        if ($data['status'] == 200) {
            return $template->renderFromFile('index@ebcms/server', $data['data']);
        } else {
            return $this->failure($data['message']);
        }
    }
}
