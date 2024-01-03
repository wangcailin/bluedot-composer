<?php

namespace Composer\Support\Excel;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Facades\Excel;

class MultiSheetExport implements WithMultipleSheets
{
    /* 使用例子 1
    return (new MultiSheetExport())->addSheets(
        '下载数据',
        ['unionid', '昵称'],
        $data['users'] ?? [],
    )->addSheets(
        '资料数据',
        ['资料名称', '发布时间', '上传人', '权限', '文档类型'],
        $data['downloads'] ?? [],
    )->download('资料下载.xlsx');
    */

    /* 使用例子 2
    return (new MultiSheetExport())->addBaseExportSheets(
        BaseExport的对象
    )->addBaseExportSheets(
        BaseExport的对象
    )->download('资料下载.xlsx');
    */
    protected $sheets = [];

    public function addBaseExportSheets(BaseExport $export)
    {
        $this->sheets[] = $export;
        return $this;
    }

    public function addSheets($sheetTitle, $headingArr, $exportData)
    {
        $export = new BaseExport();
        $export->setExportData($exportData);
        $export->setHeadingArr($headingArr);
        $export->setSheetTitle($sheetTitle);
        $this->sheets[] = $export;
        return $this;
    }

    public function sheets(): array
    {
        return $this->sheets;
    }

    public function download($title)
    {
        return Excel::download($this, $title);
    }
}
