<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace Composer\Support\Aip;

use Composer\Support\Aip\Lib\AipBase;

class AipOcr extends AipBase
{
    /**
     * 图文转换器 doc_convert api url
     * @var string
     */
    private $docConvertUrl = 'https://aip.baidubce.com/rest/2.0/ocr/v1/doc_convert/request';

    /**
     * 图文转换器查询接口 get_request_result api url
     * @var string
     */
    private $docConvertResultUrl = 'https://aip.baidubce.com/rest/2.0/ocr/v1/doc_convert/get_request_result';

    /**
     * 格式化结果
     * @param $content string
     * @return mixed
     */
    protected function proccessResult($content)
    {
        return json_decode($content, true);
    }

    /**
     * 图文转换接口
     *
     * @param [type] $data   https://cloud.baidu.com/doc/OCR/s/Elf3sp7cz
     * @return void
     */
    public function docConvertRequest($data)
    {
        return $this->request($this->docConvertUrl, $data);
    }

    /**
     * 图文转换查询接口
     *
     * @param [type] $data
     * @return void
     */
    public function docConvertResultRequest($data)
    {
        return $this->request($this->docConvertResultUrl, $data);
    }
}
