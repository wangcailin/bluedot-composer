<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\DataDownload;
use Composer\Http\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class DataDownloadClient extends Controller
{
    public function __construct(DataDownload $dataDownload)
    {
        $this->model = $dataDownload;
        $this->allowedFilters = [AllowedFilter::exact('event_id')];
    }

    public function create()
    {
        $this->performCreate();
        $list = $this->model->updateOrCreate(['event_id' => $this->data['event_id']], $this->data);
        return $this->success($list);
    }

    public function get($id)
    {
        $this->row = $this->model->firstWhere('event_id', $id);
        return $this->success($this->row);
    }

    public function sendMail(Request $request)
    {

    }
}
