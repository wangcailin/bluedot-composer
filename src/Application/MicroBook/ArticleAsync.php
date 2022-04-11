<?php

namespace Composer\Application\MicroBook;

use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\WeChat;
use Composer\Application\MicroBook\Models\MicroBookArticle;
use Composer\Support\Aip\Nlp;
use Composer\Support\Aip\Speech;
use Composer\Support\OSS;
use GatewayWorker\Lib\Gateway;

class ArticleAsync
{
    public function handle($type, $appid)
    {
        $weChat = new WeChat();
        $speech = new Speech();

        $officialAccount = $weChat->getOfficialAccount($appid);

        $auth = Authorizer::firstWhere(['app_type' => 1]);
        if (!$auth) {
            exit();
        }

        $miniProgram = $weChat->getMiniProgram($auth['appid']);
        $nlp = new Nlp();

        $stats = $officialAccount->material->stats();

        $start = 0;
        $limit = 10;
        $count = $stats['news_count'];
        $progress = 0;
        $time = time();

        do {
            Gateway::sendToGroup(json_encode(['count' => $count, 'progress' => $progress, 'status' => 'ing']));

            $list = $officialAccount->material->list('news', $start, $limit);

            foreach ($list['item'] as $key => $item) {
                $progress += 1;
                Gateway::sendToGroup(json_encode(['count' => $count, 'progress' => $progress, 'status' => 'ing']));

                if ($type == 1 && $time > $item['content']['update_time']) {
                    continue;
                }

                $updateTime = date('Y-m-d H:i:s', $item['content']['update_time']);
                $createTime = date('Y-m-d H:i:s', $item['content']['create_time']);

                $mediaId = $item['media_id'];
                foreach ($item['content']['news_item'] as $k => $newsItem) {
                    $keywords = $this->keywords($nlp, $newsItem['title'], $newsItem['content']);

                    $where = ['title' => $newsItem['title'], 'media_id' => $mediaId];
                    $data = [
                        'appid' => $appid,
                        'url' => $newsItem['url'],
                        'thumb_url' => str_replace('http://mmbiz.qpic.cn', 'https://mmbiz.qpic.cn', $newsItem['thumb_url']),
                        'show_cover_pic' => $newsItem['show_cover_pic'],
                        'keywords' => $keywords,
                        'create_time' => $createTime,
                        'update_time' => $updateTime,
                    ];
                    $row = MicroBookArticle::updateOrCreate($where, $data);
                    $row = $row->toArray();
                    $this->voice(
                        $speech,
                        array_merge($row, ['content_text' => html_trans_form(strip_tags($newsItem['content']))])
                    );
                    $this->qrcode($miniProgram, $row);
                }
            }
            $start += $limit;
        } while ($start < $count);
        Gateway::sendToGroup(json_encode(['count' => $count, 'progress' => $progress, 'status' => 'success']));
    }

    /**
     * 语音
     */
    private function voice($app, $value)
    {
        $tmp  = $value['title'] . '。' . $value['content_text'];
        $text = str_split($tmp, 512);
        $media = '';
        foreach ($text as $k => $val) {
            try {
                if (!is_array($t = $app->synthesis($val))) {
                    $media .= $t;
                } else {
                    var_dump($t);
                }
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }
        $result = OSS::putObject('microbook/voice/' . date('Ym') . '/' . $value['id'] . '.mp3', $media);
        MicroBookArticle::where('id', $value['id'])->update(['voice' => $result['info']['url']]);
    }

    /**
     * 分享二维码
     */
    private function qrcode($app, $value)
    {
        $response = $app->app_code->getUnlimit('poster__' . $value['id'], ['page' => 'pages/login/index']);
        $result = OSS::putObject('microbook/qrcode/' . date('Ym') . '/' . $value['id'] . '.png', $response->getBody()->getContents());
        MicroBookArticle::where('id', $value['id'])->update(['qrcode' => $result['info']['url']]);
    }

    /**
     * 百度关键字
     */
    private function keywords($app, $title, $content)
    {
        $text = html_trans_form(strip_tags($content));
        $keywords = [];
        $res = $app->keyword($title, $text);
        if (empty($res['error_code'])) {
            if (count($res['items'])) {
                foreach ($res['items'] as $k) {
                    $keywords[] = $k['tag'];
                }
            }
        }
        return $keywords;
    }
}
