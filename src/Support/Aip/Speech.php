<?php

namespace Composer\Support\Aip;

/**
 * 语音合成类库
 */
class Speech
{

    private $client;
    public function __construct()
    {
        $this->client = new AipSpeech(env('AIP_APP_ID', ''), env('AIP_API_KEY', ''), env('AIP_SECRET_KEY', ''));
    }

    /**
     * @param  string $speech
     * @param  string $format
     * @param  int $rate
     * @param  array $options
     * @return array
     */
    public function asr($speech, $format, $rate, $options = array())
    {
        return $this->client->asr($speech, $format, $rate, $options);
    }

    /**
     * @param  string $text
     * @param  string $lang
     * @param  int $ctp
     * @param  array $options
     * @return array
     */
    public function synthesis($text, $lang = 'zh', $ctp = 1, $options = array())
    {
        return $this->client->synthesis($text, $lang, $ctp, $options);
    }
}
