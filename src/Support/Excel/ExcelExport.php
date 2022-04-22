<?php

namespace Composer\Support\Excel;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel as Excels;

class ExcelExport implements WithEvents, FromArray, WithMapping, WithHeadings
{
    protected $data;
    protected $headings;
    protected $columnWidth = []; //设置列宽       key：列  value:宽
    protected $rowHeight = [];  //设置行高       key：行  value:高
    protected $mergeCells = []; //合并单元格    value:A1:K8
    protected $font = [];       //设置字体       key：A1:K8  value:Arial
    protected $fontSize = [];       //设置字体大小       key：A1:K8  value:11
    protected $bold = [];       //设置粗体       key：A1:K8  value:true
    protected $background = []; //设置背景颜色    key：A1:K8  value:#F0F0F0F
    protected $vertical = [];   //设置定位       key：A1:K8  value:center
    protected $sheetName; //sheet title
    protected $borders = []; //设置边框颜色  key：A1:K8  value:#000000
    //设置页面属性时如果无效   更改excel格式尝试即可

    protected $dataMergeKey = []; //需要合并的单元格 数组 ['A1:A2', 'B1:C1', 'D1:E1', 'F1:G1', 'H1:I1'];

    //构造函数传值
    public function __construct($data = [], $headings = [], $sheetName = 'sheet', $dataMergeKey = [])
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->sheetName = $sheetName;
        $this->dataMergeKey = $dataMergeKey;
    }
    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return  $this->data;
    }
    public function map($row): array
    {
        return $row;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class  => function (AfterSheet $event) {
                //设置列宽
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                //设置行高，$i为数据行数
                for ($i = 0; $i <= 1265; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(20);
                }
                // $cells = ['A1:A2', 'B1:C1', 'D1:E1', 'F1:G1', 'H1:I1'];
                if ($this->dataMergeKey) {
                    foreach ($this->dataMergeKey as   $v) {
                        //设置区域单元格垂直居中
                        $event->sheet->getDelegate()->getStyle($v)->getAlignment()->setVertical('center');
                        //设置区域单元格水平居中
                        $event->sheet->getDelegate()->getStyle($v)->getAlignment()->setHorizontal('center');
                        $event->sheet->getDelegate()->mergeCells($v);
                    }
                }
                if ($this->sheetName) {
                    $event->sheet->getDelegate()->setTitle($this->sheetName);
                }
            }
        ];
    }
    public function example()
    {
        $header = ["产品名", "浏览量（PV）", '独立访客（UV）','创建时间'];
        $excelData =[
            ['机器人','10','20','2022-10-13'],
            ['机器人','10','20','2022-10-13'],
            ['机器人','10','20','2022-10-13'],
        ];
        $name = "导出文件名称";
        $export = new \Composer\Support\Excel\ExcelExport($excelData, $header, $name);
        return Excels::download($export, $name . '.xls');
    }
}
