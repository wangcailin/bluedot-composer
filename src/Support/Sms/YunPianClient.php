<?php

namespace Composer\Support\Sms;


class YunPianClient
{
    public $tplSendUrl = "https://sms.yunpian.com/v2/sms/tpl_single_send.json";
    public $textSendUrl = "https://sms.yunpian.com/v2/sms/single_send.json";
    public $apiKey;
    public $ch;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Accept:text/plain;charset=utf-8',
            'Content-Type:application/x-www-form-urlencoded',
            'charset=utf-8',
        ));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    /**
     * 单条发送接口
     *
     * @param [type] $mobile
     * @param [type] $text
     * @return void
     */
    public function sendTextSms($mobile, $text)
    {
        $data = [
            'text' => $text,
            'apikey' => $this->apiKey,
            'mobile' => $mobile,
        ];
        return $this->curlSend($data, $this->textSendUrl);
    }

    /**
     * 指定模板发送接口
     *
     * @param [type] $mobile
     * @param [type] $templateId
     * @param [type] $tplValues
     * @return void
     */
    public function sendTemplateSms($mobile, $templateId, $tplValues)
    {
        $tplValue = $this->formTemplateValues($tplValues);
        $data = [
            'tpl_id' => $templateId,
            'tpl_value' => $tplValue,
            'apikey' => $this->apiKey,
            'mobile' => $mobile,
        ];
        return $this->curlSend($data, $this->tplSendUrl);
    }

    private function curlSend($data, $url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($this->ch);
        $response = json_decode($result, true);
        return $response;
    }

    private function formTemplateValues($values)
    {
        $result = '';
        foreach ($values as $key => $value) {
            $result .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        return rtrim($result, '&');
    }
}
