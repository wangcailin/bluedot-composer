<?php

namespace Composer\Application\Event\Live;

use Composer\Application\Event\Models\Live\PPT as PPTModels;
use Composer\Support\OSS;
use Illuminate\Support\Env;

class PPT
{
    public function __construct()
    {
    }

    /**
     * 将pdf文件转化为多张png图片
     * @param string $pdf  pdf所在路径 （/www/pdf/abc.pdf pdf所在的绝对路径）
     * @param string $path 新生成图片所在路径 (/www/pngs/)
     *
     * @return array|bool
     */
    public static function pdf2png($pdf, $eventId)
    {
        if (!file_exists($pdf)) {
            return false;
        }
        $client = new \WebSocket\Client("wss://" . Env::get('APP_URL', '') . "/api/wss/platform/event/live?event_id={$eventId}");
        $client->send(json_encode([
            'event_id' => $eventId,
            'type' => 'start',
        ]));
        $im = new \Imagick();
        $im->setResolution(240, 240); //设置分辨率 值越大分辨率越高
        $im->setCompressionQuality(120);
        $im->readImage($pdf);
        $fileList = [];
        foreach ($im as $k => $v) {
            $v->setImageFormat('jpg');
            $filepath = 'upload/' . date('Ym') . '/' . md5($k . time()) . '.jpg';
            $result = OSS::putObject($filepath, $v->getImageBlob());
            $fileList[] = $result['info']['url'];
            $client->send(json_encode([
                'event_id' => $eventId,
                'pagination' => ($k + 1) . '/' . count($im),
                'type' => 'ing',
            ]));
        }
        PPTModels::updateOrCreate(['event_id' => $eventId], ['filelist' => $fileList]);
        $client->send(json_encode([
            'filelist' => $fileList,
            'event_id' => $eventId,
            'type' => 'done',
        ]));
        $client->close();
    }
}
