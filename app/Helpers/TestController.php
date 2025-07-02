<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    // function ini digunakan untuk mengirim notifikasi approval dari atasan ke user / bawahannya
    public function sendNotifApproval(){
        // ini adalah project id firebase, jangan diubah.
        $projectId = 'mucnet-mobile';

        // START DYNAMIC VARIABLE, Sesuaikan Isinya, silahkan diubah sesuai kebutuhan.
            // isi user id dari user / bawahan
            $staff_id = 484;

            // isi nama dari atasan yg meng approve
            $fullname = "Mahrizal";

            // isi nama approval, berikut yg tersedia:
            // 1. Manual Atttendance
            // 2. Overtime
            // 3. Unlock Timesheet
            // 4. Unlock Overtime
            $name_request = "Manual Atttendance";

            // isi status approval, berikut yg tersedia:
            // 1. approved
            // 2. rejected
            $status_name = "approved";
        // END

        $message = [
            "message" => [
                "topic" => "approve_request_$staff_id",
                "notification" => [
                    "title" => "$name_request",
                    "body" => "$fullname $status_name your request",
                ],
                "data" => [
                    "route" => ""
                ]
            ]
        ];

        try {
            $accessToken = getAccessToken();
            $response = sendMessage($accessToken, $projectId, $message);
            echo 'Message sent successfully: ' . print_r($response, true);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // function ini digunakan untuk mengirim notifikasi request approval dari user / bawahan ke atasannya

    public function sendNotifRequestApproval(){
        // ini adalah project id firebase, jangan diubah.
        $projectId = 'mucnet-mobile';

        // -- START DYNAMIC VARIABLE, Sesuaikan Isinya, silahkan diubah sesuai kebutuhan --
            // isi user_id atasan
            $pm_id = 105;
            
            // ini topic, berikut isi topic yang tersedia berdasarkan fungsinya:
            // 1. leave_pm_recive = untuk request Leave
            // 2. unlock_req_OT_pm_recive = untuk request request Unlock Overtime Plan
            // 3. unlock_req_timesheet_pm_recive = untuk request request Unlock Timesheet
            $topic = "leave_pm_recive";

            // isi nama user / bawahan
            $fullname = "Danti";

            // isi date
            $date_time = "2024-08-31";

            // isi nama request, sesuaikan denga topic. Berikut isi topic yang tersedia:
            // 1. Leave
            // 2. Unlock Overtime Plan
            // 3. request Unlock Timesheet
            $name_request = "Leave";
            
            // isi route, sesuaikan denga topic. Berikut isi route yang tersedia:
            // 1. approval_leave
            // 2. approval_unlock_ot
            // 3. approval_unlock_timesheet
            $route = "approval_leave";
        
        // -- END --

        $message = [
            "topic" => "{$topic}_{$pm_id}",
            "notification" => [
                "title" => $name_request,
                "body" => "{$fullname} request for {$date_time}"
            ],
            "data" => [
                "route" => $route
            ]
        ];
        

        try {
            $accessToken = getAccessToken();
            $response = sendMessage($accessToken, $projectId, $message);
            echo 'Message sent successfully: ' . print_r($response, true);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
