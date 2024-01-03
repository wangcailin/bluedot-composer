<?php

namespace Composer\Support\Excel;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;

class BaseExport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithEvents, WithStrictNullComparison
{
    /*  使用
    $exportService = new BaseExport();
    $exportService->setExportData($data);
    $exportService->setHeadingArr(['订单号', '课程名称', '支付金额', '支付方式', '报名时间', '支付时间', '买家信息', '操作人', '订单状态']);
    $exportService->setSheetTitle('订单列表');
    $exportService->setExportFileTitle('订单列表.xlsx'); //可以不传递，如果不传递
    return $exportService->download();
    */

    protected $isExportData = 0;
    protected $exportData = [];
    protected $sheetTitle = '请输入excel的表头';
    protected $exportFileTitle = '';
    protected $headingArr = [];

    public function setExportData(array $exportData)
    {
        $this->exportData = $exportData;
        $this->isExportData = 1;
    }

    public function setSheetTitle(string $sheetTitle)
    {
        $this->sheetTitle = $sheetTitle;
    }

    public function setHeadingArr(array $headingArr)
    {
        $this->headingArr = $headingArr;
    }

    public function setExportFileTitle(string $exportFileTitle)
    {
        $this->exportFileTitle = $exportFileTitle;
    }

    private function getExportFileTitle()
    {
        return $this->exportFileTitle ?: $this->sheetTitle . '.xlsx';
    }

    public function download()
    {
        if ($this->headingArr) {
            throw new ApiException('导出失败,请输出表头', ApiErrorCode::VALIDATION_ERROR);
        }

        if ($this->sheetTitle) {
            throw new ApiException('导出失败,请输入Excel表格的标题', ApiErrorCode::VALIDATION_ERROR);
        }

        if ($this->isExportData) {
            throw new ApiException('导出失败,请输入导出数据', ApiErrorCode::VALIDATION_ERROR);
        }

        return \Maatwebsite\Excel\Facades\Excel::download($this, $this->getExportFileTitle());
    }
    /**
     * 注册事件，触发操作
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class  => function (AfterSheet $event) {
                //设置列宽 第一列高度30
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(50);
                //设置行高，$i为数据行数，应用到 1265 行
                for ($i = 0; $i <= 1265; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(30);
                }
                //设置区域单元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:N1')->getAlignment()->setVertical('center');
            }
        ];
    }

    /**
     * 设置列宽
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 16, 'B' => 13, 'C' => 13, 'D' => 20, 'E' => 50, 'F' => 8, 'G' => 10, 'H' => 13,
            'I' => 8, 'J' => 12, 'K' => 12, 'L' => 12, 'M' => 20, 'N' => 20, 'O' => 15, 'P' => 20,
            'Q' => 20, 'R' => 20, 'S' => 30, 'T' => 30, 'U' => 20, 'V' => 20, 'W' => 20, 'X' => 20,
        ];
    }
    /**
     * 设置表头的标题
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->headingArr;
    }

    /**
     * 导出数据
     *
     * @return array
     */
    public function array(): array
    {
        return $this->exportData;
    }

    /**
     * Excel 表格的标题
     *
     * @return string
     */
    public function title(): string
    {
        return $this->sheetTitle;
    }
}
