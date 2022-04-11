<?php

namespace Composer\Application\MicroBook;

use Composer\Application\MicroBook\Models\Relation\MicroBookArticleTag;
use Composer\Application\MicroBook\Models\MicroBookArticle;
use Composer\Application\MicroBook\Models\MicroBookUserKeyword;
use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\WeChat;
use Composer\Http\Controller;
use Composer\Support\Aip\Speech;
use Composer\Support\OSS;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class ArticleClient extends Controller
{
    public function __construct(MicroBookArticle $microBookArticle)
    {
        $this->model = $microBookArticle;
        $this->allowedFilters = [
            'title',
            AllowedFilter::callback('category_id', function ($query, $value) {
                return $query->whereJsonContains('category_ids', [(int) $value]);
            }),
        ];
        $this->defaultSort = '-sort';
        $this->allowedSorts = ['sort', 'update_time', 'create_time', 'id'];
    }

    public function performBuildFilter()
    {
        $this->model->with(['tag']);
    }

    public function afterCreate()
    {
        $this->performEvent();
    }

    public function performUpdate()
    {
        $this->performEvent();
    }

    private function performEvent()
    {
        $tagIds = request()->input('tag_ids');
        if ($tagIds) {
            MicroBookArticleTag::where('article_id', $this->row['id'])->whereNotIn('tag_id', $tagIds)->delete();
            foreach ($tagIds as $key => $value) {
                MicroBookArticleTag::firstOrCreate(['article_id' => $this->row['id'], 'tag_id' => $value]);
            }
        } else {
            MicroBookArticleTag::where('article_id', $this->row['id'])->delete();
        }
    }

    public function keyword(Request $request)
    {
        $input = $request->only(['user_id', 'unionid', 'article_id']);
        $keyword = $request->input('keyword', []);
        foreach ($keyword as $key => $value) {
            $input['keyword'] = $value;
            MicroBookUserKeyword::create($input);
        }
        return $this->success();
    }
}
