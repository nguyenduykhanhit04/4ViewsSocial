<?php

namespace App\Http\Controllers;

use App\Models\ViolenceWarning;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseApi; // Giả sử bạn để helper này
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ResponseApi();
    }

    public function listReports()
    {
        $warnings = ViolenceWarning::with('infringe')->get(); 

        if ($warnings->isEmpty()) {
            return $this->response->BadRequest();
        }

        $reports = $warnings->map(function($warning) {
            $violationCount = ViolenceWarning::where('infringe_id', $warning->infringe_id)->count();

            return [
                'id' => $warning->id,
                'username' => $warning->infringe?->username ?? 'Unknown', 
                'violation_count' => $violationCount,
                'login_fail_count' => $warning->infringe?->login_fail ?? 0,
                'status' => 'Chưa xử lý',
            ];
        });

        return $this->response->success($reports);
    }

}
