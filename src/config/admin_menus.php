<?php

use App\Ebcms\Server\Model\Server;
use Ebcms\App;
use Ebcms\Router;

return App::getInstance()->execute(function (
    Server $server,
    Router $router
): array {
    $res = [];
    if ($data = $server->query('/source')) {
        if ($data['status'] == 200) {
            $res[] = [
                'title' => '系统升级',
                'url' => $router->buildUrl('/ebcms/server/index'),
                'priority' => 0,
                'badge' => '重要',
                'icon' => '<svg t="1610163897853" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3957" width="20" height="20"><path d="M33.28 509.44a476.16 476.16 0 1 0 952.32 0 476.16 476.16 0 1 0-952.32 0z" fill="#F93E0B" p-id="3958"></path><path d="M762.368 468.326l-236.8-234.803c-7.885-7.885-21.709-7.885-29.594 0l-236.8 234.803c-9.881 13.824-1.996 35.533 15.77 35.533h118.374v138.138c0 21.709 17.767 39.475 39.476 39.475h157.85c21.708 0 39.474-17.766 39.474-39.475V503.859h118.375c17.817 0 25.702-21.709 13.875-35.533zM600.576 740.608H422.963c-15.77 0-29.593 13.824-29.593 29.594s13.824 29.593 29.593 29.593h177.613c15.77 0 29.594-13.824 29.594-29.593s-13.824-29.594-29.594-29.594z" fill="#FFFFFF" p-id="3959"></path></svg>',
            ];
        }
    }
    return $res;
});
