<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Register\Register;
use Composer\Http\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Spatie\QueryBuilder\AllowedFilter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RegisterClient extends Controller
{
    use \Composer\Application\Event\Traits\GetList;
    use \Composer\Application\Event\Traits\GetOneUser;
    public function __construct(Register $register)
    {
        $this->model = $register;
        $this->allowedFilters = [
            AllowedFilter::exact('event_id'),
        ];
    }

    public function verify($id, $state)
    {
        $this->preformVerify($id, $state);
        $this->model->where('id', $id)->update(['state' => $state]);
        return $this->success();
    }

    public function preformVerify($id, $state)
    {
    }

    public function export()
    {
        $this->buildFilter();
        $list = $this->model->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cell = ['姓名', '手机号', '邮箱', '公司', '职位', '报名时间', '状态'];
        foreach ($cell as $key => $value) {
            $sheet->setCellValue(chr(65 + $key) . '1', $value);
        }
        $state = ['未审核', '已通过', '已拒绝'];
        foreach ($list as $key => $value) {
            $username = '';
            $jobs = '';
            $company = '';
            $email = '';
            $phone = '';
            if ($value['phone']) {
                $phone = $value['phone'];
            }
            if ($value['email']) {
                $email = $value['email'];
            }
            if ($value['extend']) {
                if (!empty($value['extend']['first_name']) && !empty($value['extend']['last_name'])) {
                    $username = $value['extend']['first_name'] . $value['extend']['last_name'];
                }
                if (!empty($value['extend']['job'])) {
                    $jobs = $value['extend']['job'];
                }
                if (!empty($value['extend']['company'])) {
                    $company = $value['extend']['company'];
                }
            }
            $cell = [$username, $phone, $email, $company, $jobs, $value['created_at'], $state[$value['state']]];
            foreach ($cell as $k => $v) {
                $sheet->setCellValue(chr(65 + $k) . ($key + 2), $v);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
        return $objWriter->save('php://output');
    }
}
