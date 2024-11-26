<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use App\Models\PayslipBatchModel;

class PayslipBatchController extends BaseController
{
    public function index()
    {
        $model = new PayslipBatchModel();

        $data['payslip_batches'] = $model->findAll();

        return view('payslip_batches/index', $data);
    }

    public function view($id)
    {
        $model = new PayslipBatchModel();

        $data['payslip_batch'] = $model->find($id);

        return view('payslip_batches/view', $data);
    }

    public function create()
    {
        helper(['form', 'url']);

        if ($this->request->getMethod() === 'post' && $this->validate([
                'pay_date' => 'required|valid_date',
                'file'     => 'uploaded[file]|max_size[file,2048]|ext_in[file,pdf,doc,docx]',
            ])) {
            $file = $this->request->getFile('file');

            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);

                $model = new PayslipBatchModel();
                $model->save([
                    'pay_date' => $this->request->getPost('pay_date'),
                    'file'     => $newName,
                ]);

                return redirect()->to('/erp/system-settings#payslips')->with('success', 'Payslip batch created successfully.');
            }
        } else {
            return redirect()->to('/erp/system-settings#payslips')->withInput()->with('error', 'Payslip batch creation failed.');

        }
    }
}