<?php

namespace Composer\Application\WeChat;

use Composer\Application\Analysis\Models\Monitor;
use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\Models\Qrcode;
use Composer\Application\WeChat\WeChat;
use Composer\Http\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Spatie\QueryBuilder\AllowedFilter;

class QrcodeClient extends Controller
{
    public $weChat;

    public function __construct(WeChat $weChat, Qrcode $qrcode)
    {
        $this->weChat = $weChat;
        $this->model = $qrcode;
        $this->allowedFilters = [
            AllowedFilter::exact('appid'),
            'name',
        ];
    }

    public function performBuildFilterList()
    {
        $this->model->with('authorizer')->withCount(['monitor as monitor_count_pv', 'monitor as monitor_count_uv' => function (Builder $query) {
            $query->select(DB::raw('COUNT(DISTINCT openid)'));
        }, 'monitor as monitor_count_subscribe' => function (Builder $query) {
            $query->where('wechat_event_msg', 'subscribe')->select(DB::raw('COUNT(DISTINCT openid)'));
        }]);
    }

    public function performCreate()
    {
        $data = request()->all();

        $scene_str = substr(md5(uniqid(rand(), 1)), 8, 16);

        $result = $this->weChat->getOfficialAccount($data['appid'])->qrcode->forever($scene_str);

        $data['scene_str'] = $scene_str;
        $data['ticket'] = $result['ticket'];
        $data['url'] = $result['url'];

        $this->data = $data;
        if ($this->authUserId) {
            $this->createAuthUserId();
        }
    }

    public function getSceneStr($scene_str)
    {
        $row = $this->model->firstWhere('scene_str', $scene_str);
        return $this->success($row);
    }

    public function getLogoQrcode($id)
    {
        $qrcode = $this->model->find($id);
        $authorizer = Authorizer::firstWhere('appid', $qrcode->appid);
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $qrcode->ticket;
        $qrcodeImg = $this->sendGetByCurl($url, 3);
        $logoImg = $this->sendGetByCurl($authorizer->head_img, 3);

        $filename = storage_path() . '/tmp/' . $qrcode->appid . '.jpg';
        file_put_contents($filename, $logoImg);
        $qrcode = Image::make($qrcodeImg);
        $logo = Image::make($this->radiusImg($filename, 16))->resize(100, 100);
        return $qrcode->insert($logo, 'center')->response('jpeg');
    }

    public function getSceneStrQrcode(Request $request)
    {
        $input = $request->only(['scene_str', 'appid']);
        $app = $this->weChat->getOfficialAccount($input['appid']);
        $result = $app->qrcode->forever($input['scene_str']);
        return $this->success($result);
    }

    public function getSceneStrStatistics(Request $request)
    {
        $input = $request->only(['scene_str', 'date_type', 'date_value']);
        $data = [];
        $dateType = 'YYYY-MM-DD';

        switch ($input['date_type']) {
            case 'year':
                $input['date_value'][0] .= '-01-01';
                $input['date_value'][1] .= '-01-01';
                $dateType = 'YYYY';
                break;
            case 'month':
                $input['date_value'][0] .= '-01';
                $input['date_value'][1] .= '-01';
                $dateType = 'YYYY-MM';
                break;
            case 'day':
                $dateType = 'YYYY-MM-DD';
                break;
        }
        Monitor::select([DB::raw("to_char(created_at, '{$dateType}') AS date"), DB::raw('count(distinct unionid) AS uv'), DB::raw('count(1) AS pv')])
            ->where('wechat_event_key', $input['scene_str'])
            ->whereBetween('created_at', $input['date_value'])
            ->groupBy('date')
            ->get()
            ->map(function ($v) use (&$data) {
                $data[] = ['name' => 'PV', 'value' => $v->pv, 'date' => $v->date];
                $data[] = ['name' => 'UV', 'value' => $v->uv, 'date' => $v->date];
            });
        return $this->success($data);
    }

    /**
     * 将图片四直角处理成圆角
     *
     * @param $src_img 目标图片
     * @param $width 宽
     * @param $height 高
     * @param int $radius 圆角半径
     * @return resource
     */
    protected function radiusImg($imgpath, $radius = 15)
    {
        $src_img = imagecreatefromjpeg($imgpath);

        $wh = getimagesize($imgpath);
        $w = $wh[0];
        $h = $wh[1];
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r = $radius; //圆 角半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                    //不在四角的范围内,直接画
                    imagesetpixel($img, $x, $y, $rgbColor);
                } else {
                    //在四角的范围内选择画
                    //上左
                    $y_x = $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //上右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下左
                    $y_x = $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                    //下右
                    $y_x = $w - $r; //圆心X坐标
                    $y_y = $h - $r; //圆心Y坐标
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
            }
        }
        return $img;
    }

    protected function sendGetByCurl($url, $time)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}
