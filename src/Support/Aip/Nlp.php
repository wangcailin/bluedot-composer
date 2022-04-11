<?php

namespace Composer\Support\Aip;

use Composer\Support\Aip\AipNlp;

class Nlp
{

    private $client;
    public function __construct()
    {
        $this->client = new AipNlp(env('AIP_APP_ID', ''), env('AIP_API_KEY', ''), env('AIP_SECRET_KEY', ''));
    }

    /**
     * 词法分析
     * @param $text
     * @param array $option
     * @return array
     */
    public function lexer($text, $option = [])
    {
        return $this->client->lexer($text, $option);
    }

    /**
     * 文章标签
     * @param $title
     * @param $content
     * @return array
     */
    public function keyword($title, $content)
    {
        return $this->client->keyword($title, $content);
    }

    /**
     * 文章分类
     * @param $title
     * @param $content
     * @return array
     */
    public function topic($title, $content)
    {
        return $this->client->topic($title, $content);
    }

    /**
     * 文本纠错
     * @param $text
     * @param array $options
     * @return array
     */
    public function ecnet($text, $options = [])
    {
        return $this->client->ecnet($text, $options);
    }

    /**
     * 新闻摘要
     * @param $content
     * @param int $maxSummaryLen
     * @param array $options
     * @return mixed
     */
    public function newsSummary($content, $maxSummaryLen = 200, $options = [])
    {
        return $this->client->newsSummary($content, $maxSummaryLen, $options);
    }

    /**
     * 情感倾向分析
     * @param $text
     * @param array $options
     * @return array
     */
    public function sentimentClassify($text, $options = [])
    {
        return $this->client->sentimentClassify($text, $options);
    }

    /**
     * 评论观点抽取
     * @param $text
     * @param array $options
     * @return array
     */
    public function commentTag($text, $options = [])
    {
        return $this->client->commentTag($text, $options);
    }
}
