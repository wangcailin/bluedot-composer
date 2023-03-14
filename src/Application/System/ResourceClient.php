<?php

namespace Composer\Application\System;

use Composer\Http\Controller;
use Composer\Application\System\Models\Resource;
use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;
use Composer\Support\Aliyun\OssClient;
use Composer\Support\Aliyun\OssServerClient;
use Illuminate\Http\Request;

class ResourceClient extends Controller
{
    public function __construct(Resource $resource)
    {
        $this->model = $resource;
    }

    public function create()
    {
        $request = request();
        $file = $request->file('file');
        $appSource = $request->input('app_source', '');

        if ($file) {
            $fileName = $file->getClientOriginalName();
            $fileMimeType = $file->getClientMimeType();
            $fileExtension = $file->extension();
            $fileSize = $file->getSize();
            $fileUid = uniqid();
            $filePath = $this->getFilePathPrefix() . $fileUid . '.' . $fileExtension;
            $result = OssServerClient::putObject($filePath, $file->get());
            if ($result && isset($result['info']) && isset($result['info']['url'])) {
                $data = [
                    'title' => $fileName,
                    'mime_type' => $fileMimeType,
                    'extension' => $fileExtension,
                    'size' => $fileSize,
                    'url' => $result['info']['url'],
                    'app_source' => $appSource,
                ];
                $this->model::create($data);
                return $this->success(['url' => $result['info']['url'], 'uid' => $fileUid]);
            }
        }
        throw new ApiException('上传失败', ApiErrorCode::VALIDATION_ERROR);
    }

    public function getFilePathPrefix()
    {
        return 'uploads/' . date('Ymd') . '/';
    }

    public function getOssUploadUrl(Request $request)
    {
        $dir = $request->input('dir', 'upload/');
        $response = OssClient::getUploadUrl($dir);
        return $this->success($response);
    }
}
