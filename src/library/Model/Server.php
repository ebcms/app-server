<?php

declare(strict_types=1);

namespace App\Ebcms\Server\Model;

use Ebcms\App;
use Throwable;

class Server
{
    public function query(string $path, array $param = []): array
    {
        try {
            $url = $this->getApiAddr() . $path . '?' . http_build_query(array_merge($param, $this->getCommonParam()));
            $res = (array)json_decode(file_get_contents($url, false, stream_context_create([
                'http' => [
                    'method' => "GET",
                    'timeout' => 10,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ])), true);
            if (!isset($res['status'])) {
                return [
                    'status' => 0,
                    'message' => '接口错误！',
                ];
            }
        } catch (Throwable $th) {
            return [
                'status' => 0,
                'message' => '接口错误：' . $th->getMessage(),
            ];
        }
        return $res;
    }

    public function getApiAddr(): string
    {
        $json_file = App::getInstance()->getPackages()['ebcms/server']['dir'] . '/composer.json';
        return json_decode(file_get_contents($json_file), true)['extra']['api'];
    }

    public function getCommonParam(): array
    {
        $res = [];
        $json = (array)json_decode(file_get_contents(App::getInstance()->getAppPath() . '/composer.json'), true);
        $res['_for'] = $json['name'];
        $res['_version'] = $json['version'];
        $res['_installed'] = $this->getInstalled();
        $res['_site_url'] = $this->getSiteUrl();
        return $res;
    }

    public function getInstalled(): array
    {
        $installed = [];
        foreach (glob(App::getInstance()->getAppPath() . '/plugin/*/plugin.json') as $value) {
            $name = pathinfo(dirname($value), PATHINFO_FILENAME);
            if (
                file_exists(App::getInstance()->getAppPath() . '/config/plugin/' . $name . '/install.lock')
            ) {
                $tmp = json_decode(file_get_contents($value), true);
                $installed[$name] = $tmp['version'] ?? '0.0.0';
            }
        }
        return $installed;
    }

    private function getSiteUrl(): string
    {
        if (
            (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
            || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
        ) {
            $url = 'https';
        } else {
            $url = 'http';
        }
        $url .= '://';
        if ($_SERVER['SERVER_PORT'] != "80") {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }
        return $url;
    }
}
