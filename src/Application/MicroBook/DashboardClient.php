<?php

namespace Composer\Application\MicroBook;

use Composer\Application\Analysis\Models\Monitor;
use Composer\Application\User\Models\UserWeChatOpenid;
use Composer\Application\WeChat\Models\Authorizer;
use Composer\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardClient extends Controller
{
    private function getAppid()
    {
        $authorizer =  Authorizer::where(['app_type' => 1])->first();
        return $authorizer['appid'];
    }

    private function queryViewCount($baseWhere)
    {
        return Monitor::where($baseWhere)->where('page_event_key', 'like', '跳转详情-%');
    }
    private function queryPlayCount($baseWhere)
    {
        return Monitor::where($baseWhere)->where('page_event_key', 'like', '播放音频-%');
    }
    private function queryShareCount($baseWhere)
    {
        return Monitor::where($baseWhere)->where(function ($query) {
            $query->orWhere('page_event_key', 'like', '生成专属图片分享-%')
                ->orWhere('page_event_key', 'like', '分享给好友或群-%')
                ->orWhere('page_event_key', 'like', '微刊搜索分享-%')
                ->orWhere('page_event_type', 'share');
        });
    }
    // 查询7天数据
    private function queryWeekCount($query, $date, $date1 = null)
    {
        return $query->where('created_at', '<=', $date1 ?: date('Y-m-d H:i:s'))->where('created_at', '>=', $date . ' 00:00:00');
    }
    // 查询7天数据分天计数
    private function queryGroupDayCount($query)
    {
        return $query->select(DB::raw("to_char(created_at, 'YYYY-MM-DD') AS date"), DB::raw('count(1) as count'))->groupBy('date')->get()->transform(function ($item) {
            return $item['count'];
        });
    }
    // 查询环比
    private function getChain($count1, $count2)
    {
        return round((($count1 - $count2) / $count2 * 100), 2);
    }

    public function statistic()
    {
        $appid = $this->getAppid();
        $baseWhere = ['wechat_appid' => $appid];

        $userTotal = UserWeChatOpenid::where(['appid' => $appid])->count();

        $date1 = date('Y-m-d');
        $date2 = date('Y-m-d', strtotime('-1 day'));
        $week1 = date('Y-m-d', strtotime('-6 day'));
        $week2 = date('Y-m-d', strtotime('-13 day'));

        $viewCount = $this->queryViewCount($baseWhere)->count();
        $viewGroupDay = $this->queryGroupDayCount($this->queryWeekCount($this->queryViewCount($baseWhere), $week1));
        $viewWeekCount = $this->queryWeekCount($this->queryViewCount($baseWhere), $week1)->count();
        $viewWeekCount1 = $this->queryWeekCount($this->queryViewCount($baseWhere), $week1, $week2)->count() ?: 1;
        $viewCount1 = $this->queryViewCount($baseWhere)->whereDate('created_at', $date1)->count();
        $viewCount2 = $this->queryViewCount($baseWhere)->whereDate('created_at', $date2)->count() ?: 1;

        $shareCount = $this->queryShareCount($baseWhere)->count();
        $shareGroupDay = $this->queryGroupDayCount($this->queryWeekCount($this->queryShareCount($baseWhere), $week1));
        $shareWeekCount = $this->queryWeekCount($this->queryShareCount($baseWhere), $week1)->count();
        $shareWeekCount1 = $this->queryWeekCount($this->queryShareCount($baseWhere), $week1, $week2)->count() ?: 1;
        $shareCount1 = $this->queryShareCount($baseWhere)->whereDate('created_at', $date1)->count();
        $shareCount2 = $this->queryShareCount($baseWhere)->whereDate('created_at', $date2)->count() ?: 1;

        $playCount = $this->queryPlayCount($baseWhere)->count();
        $playGroupDay = $this->queryGroupDayCount($this->queryWeekCount($this->queryPlayCount($baseWhere), $week1));
        $playWeekCount = $this->queryWeekCount($this->queryPlayCount($baseWhere), $week1)->count();
        $playWeekCount1 = $this->queryWeekCount($this->queryPlayCount($baseWhere), $week1, $week2)->count() ?: 1;
        $playCount1 = $this->queryPlayCount($baseWhere)->whereDate('created_at', $date1)->count();
        $playCount2 = $this->queryPlayCount($baseWhere)->whereDate('created_at', $date2)->count() ?: 1;

        return $this->success([
            'user_total' => $userTotal,
            'view_count' => [
                'count' => $viewCount,
                'week_count' => $viewWeekCount,
                'week' => $this->getChain($viewWeekCount, $viewWeekCount1),
                'group_day' => $viewGroupDay,
                'day' => $this->getChain($viewCount1, $viewCount2)
            ],
            'share_count' => [
                'count' => $shareCount,
                'week_count' => $shareWeekCount,
                'week' => $this->getChain($shareWeekCount, $shareWeekCount1),
                'group_day' => $shareGroupDay,
                'day' => $this->getChain($shareCount1, $shareCount2)
            ],
            'play_count' => [
                'count' => $playCount,
                'week_count' => $playWeekCount,
                'week' => $this->getChain($playWeekCount, $playWeekCount1),
                'group_day' => $playGroupDay,
                'day' => $this->getChain($playCount1, $playCount2)
            ],
        ]);
    }

    public function categoryTop()
    {
        $appid = $this->getAppid();
        $baseWhere = ['wechat_appid' => $appid];
        $group = Monitor::where($baseWhere)
            ->where('page_event_key', 'like', '微刊分类-%')
            ->select('page_event_key as title', DB::raw('count(1) as count'))
            ->groupBy('title')
            ->orderBy('count', 'DESC')
            ->get()->transform(function ($item) {
                $item['title'] = str_replace('微刊分类-', '', $item['title']);
                return $item;
            });
        return $this->success($group);
    }

    public function categoryLine(Request $request)
    {
        $appid = $this->getAppid();
        $baseWhere = ['wechat_appid' => $appid];

        $input = $request->only(['date_type', 'date_value']);
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
        $data = Monitor::where($baseWhere)
            ->where('page_event_key', 'like', '微刊分类-%')
            ->whereBetween('created_at', $input['date_value'])
            ->select([DB::raw("to_char(created_at, '{$dateType}') AS date"), 'page_event_key as title', DB::raw('count(1) as count')])
            ->groupBy('date', 'title')
            ->get()->transform(function ($item) {
                $item['title'] = str_replace('微刊分类-', '', $item['title']);
                return $item;
            });
        return $this->success($data);
    }

    public function source()
    {
        $appid = $this->getAppid();
        $baseWhere = ['wechat_appid' => $appid];

        $source = ['默认进入', '分享好友进入', '分享海报进入', '分享列表进入', '搜索分享进入'];
        $target = ['阅读分类列表', '读文章', '听文章', '分享好友', '分享海报', '分享分类列表', '分享搜索列表'];

        $shareSource
            = Monitor::where($baseWhere)->where('page_event_key', 'like', '分享给好友或群回流-%')->count();
        $shareSearchSource = Monitor::where($baseWhere)->where('page_event_key', '微刊搜索分享回流')->count();
        $sharePosterSource = Monitor::where($baseWhere)->where('page_event_key', 'like', '生成专属图片分享回流-%')->count();
        $shareCategoryListSource = Monitor::where($baseWhere)->where('page_event_key', 'like', '微刊分类分享回流-%')->count();
        $source = Monitor::where($baseWhere)->where('page_event_type', 'view')->count();

        $categoryListTarget = Monitor::where($baseWhere)->where(function ($query) {
            $query->where('page_event_key', 'like', '微刊分类-%')->orWhere(function ($query) {
                $query->whereNull('page_event_key')->where('page_event_type', 'view');
            });
        })->count();
        $viewArticleTarget = Monitor::where(
            $baseWhere
        )->where('page_event_key', 'like', '跳转详情-%')->count();
        $playArticleTarget = Monitor::where(
            $baseWhere
        )->where('page_event_key', 'like', '播放音频-%')->count();
        $shareTarget = Monitor::where($baseWhere)->where('page_event_key', 'like', '分享给好友或群-%')->count();
        $sharePosterTarget = Monitor::where($baseWhere)->where('page_event_key', 'like', '生成专属图片分享-%')->count();
        $shareCategoryListTarget = Monitor::where(
            $baseWhere
        )->where('page_event_type', 'share')->count();
        $shareSearchTarget = Monitor::where($baseWhere)->where('page_event_key', 'like', '微刊搜索分享回流-%')->count();

        $data = [
            [
                'source' => '默认进入',
                'target' => '微刊',
                'value' => ($source - $shareSource - $sharePosterSource - $shareCategoryListSource - $shareSearchSource)
            ], [
                'source' => '分享好友进入',
                'target' => '微刊',
                'value' => $shareSource
            ], [
                'source' => '分享海报进入',
                'target' => '微刊',
                'value' => $sharePosterSource
            ], [
                'source' => '分享列表进入',
                'target' => '微刊',
                'value' => $shareCategoryListSource
            ], [
                'source' => '搜索分享进入',
                'target' => '微刊',
                'value' => $shareSearchSource
            ], [
                'source' => '微刊',
                'target' => '阅读分类列表',
                'value' => $categoryListTarget
            ], [
                'source' => '微刊',
                'target' => '读文章',
                'value' => $viewArticleTarget
            ], [
                'source' => '微刊',
                'target' => '听文章',
                'value' => $playArticleTarget
            ], [
                'source' => '微刊',
                'target' => '分享好友',
                'value' => $shareTarget
            ], [
                'source' => '微刊',
                'target' => '分享海报',
                'value' => $sharePosterTarget
            ], [
                'source' => '微刊',
                'target' => '分享分类列表',
                'value' => $shareCategoryListTarget
            ], [
                'source' => '微刊',
                'target' => '分享搜索列表',
                'value' => $shareSearchTarget
            ]
        ];

        return $this->success($data);
    }
}
