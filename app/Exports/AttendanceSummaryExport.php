<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AttendanceSummaryExport implements FromCollection, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use RegistersEventListeners;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $attendancesCategoryWise;
        $attendancesDevicewise;
        if (Session::get('eventId')) {
            $attendancesCategoryWise = DB::table('attendances')
                ->select('participants.category', DB::raw('IF(att_status = "1", "Approved", "Rejected") AS status'), DB::raw('count(*) as total'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->where('attendances.event_id', Session::get('eventId'))
                ->groupBy('participants.category', 'attendances.att_status')
                ->get()->toArray();
        } else {
            $attendancesCategoryWise = DB::table('attendances')
                ->select('participants.category', DB::raw('IF(att_status = "1", "Approved", "Rejected") AS status'), DB::raw('count(*) as total'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->whereNotNull('attendances.created_at')
                ->groupBy('participants.category', 'attendances.att_status')
                ->get()->toArray();
        }
        // Category Wise
        $categoryReportData = [];
        $categoryHeaders1 = ['Category Wise Report', '', '', ''];
        $categoryHeaders2 = ['', '', '', ''];
        $categoryHeaders3 = ['Category', 'Approved', 'Rejected', 'Did not scan'];
        $category = DB::table('participants')
            ->select('participants.category', DB::raw('count(*) as total'))
            ->where('participants.event_id', Session::get('eventId'))
            ->groupBy('participants.category')
            ->get()->toArray();
        $checkcategory = '';
        array_push($categoryReportData, $categoryHeaders1, $categoryHeaders2, $categoryHeaders3);
        for ($i = 0; $i < count($category); $i++) {
            if ($category[$i]->category != $checkcategory) {
                array_push($categoryReportData, [$category[$i]->category, 0, 0, $category[$i]->total]);
                $checkcategory = $category[$i]->category;
            }
        }

        for ($j = 0; $j < count($categoryReportData); $j++) {
            for ($k = 0; $k < count($attendancesCategoryWise); $k++) {
                if ($categoryReportData[$j][0] == $attendancesCategoryWise[$k]->category) {
                    if ($attendancesCategoryWise[$k]->status == 'Approved') {
                        $categoryReportData[$j][1] = $attendancesCategoryWise[$k]->total;
                        $categoryReportData[$j][3] = ($categoryReportData[$j][3] - $attendancesCategoryWise[$k]->total);
                    }
                    if ($attendancesCategoryWise[$k]->status == 'Rejected') {
                        $categoryReportData[$j][2] = $attendancesCategoryWise[$k]->total;
                        $categoryReportData[$j][3] = ($categoryReportData[$j][3] - $attendancesCategoryWise[$k]->total);
                    }
                }
            }
        }

        DB::enableQueryLog();
        // Device Wise
        if (Session::get('eventId')) {
            $attendancesDevicewise = DB::table('attendances')
                ->select('devices.id', DB::raw('IF(att_status = "1", "Approved", "Rejected") AS status'), DB::raw('count(*) as total'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->join('devices', 'devices.id', '=', 'attendances.device_id')
                ->where('attendances.event_id', Session::get('eventId'))
                ->groupBy('devices.id', 'attendances.att_status')
                ->get()->toArray();
        } else {

            $attendancesDevicewise = DB::table('attendances')
                ->select('devices.id', DB::raw('IF(att_status = "1", "Approved", "Rejected") AS status'), DB::raw('count(*) as total'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->join('devices', 'devices.id', '=', 'attendances.device_id')
                ->whereNotNull('attendances.created_at')
                ->groupBy('devices.id', 'attendances.att_status')
                ->get()->toArray();
        }
        $deviceReportData = [];
        $deviceHeaders1 = ['', '', '', ''];
        $deviceHeaders2 = ['', '', '', ''];
        $deviceHeaders3 = ['Device Wise Report', '', '', ''];
        $deviceHeaders4 = ['', '', '', ''];
        $deviceHeaders5 = ['Device', 'Approved', 'Rejected', 'Did not scan'];
        $device = DB::table('devices')
            ->select('devices.id', DB::raw('count(*) as total'))
            ->join('participants', 'participants.event_id', '=', 'devices.event_id')
            ->where('devices.event_id', Session::get('eventId'))
            ->whereNotNull('devices.operator_name')
            ->whereNotNull('devices.operator_mobile')
            ->groupBy('devices.id')
            ->get()->toArray();
        $checkdevice = '';

        array_push($deviceReportData, $deviceHeaders1, $deviceHeaders2, $deviceHeaders3, $deviceHeaders4, $deviceHeaders5);
        for ($i = 0; $i < count($device); $i++) {
            if ($device[$i]->id != $checkdevice) {
                array_push($deviceReportData, [$device[$i]->id, 0, 0, $device[$i]->total]);
                $checkdevice = $device[$i]->id;
            }
        }

        $deviceArray = DB::table('devices')
            ->select('id', DB::raw('CONCAT(name, " (", operator_name, " - " , operator_mobile ,")") as device_name'))
            ->where('event_id', Session::get('eventId'))
            ->whereNotNull('operator_name')
            ->whereNotNull('operator_mobile')
            ->get()->toArray();

        for ($j = 0; $j < count($deviceReportData); $j++) {
            for ($k = 0; $k < count($attendancesDevicewise); $k++) {
                if ($deviceReportData[$j][0] == $attendancesDevicewise[$k]->id) {
                    if ($attendancesDevicewise[$k]->status == 'Approved') {
                        $deviceReportData[$j][1] = $attendancesDevicewise[$k]->total;
                        $deviceReportData[$j][3] = ($deviceReportData[$j][3] - $attendancesDevicewise[$k]->total);
                    }
                    if ($attendancesDevicewise[$k]->status == 'Rejected') {
                        $deviceReportData[$j][2] = $attendancesDevicewise[$k]->total;
                        $deviceReportData[$j][3] = ($deviceReportData[$j][3] - $attendancesDevicewise[$k]->total);
                    }
                }
            }
        }
        for ($jk = 0; $jk < count($deviceReportData); $jk++) {
            for ($kl = 0; $kl < count($deviceArray); $kl++) {
                if ($jk > 4) {
                    if ($deviceReportData[$jk][0] == $deviceArray[$kl]->id) {
                        $deviceReportData[$jk][0] = $deviceArray[$kl]->device_name;
                    }
                }
            }
        }

        // Time Wise
        if (Session::get('eventId')) {
            $attendancesTimewise = DB::table('attendances')
                ->select('attendances.in_time', DB::raw('IF(att_status = "1", "Approved", "Rejected") AS status'), DB::raw('count(*) as total'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->join('devices', 'devices.id', '=', 'attendances.device_id')
                ->where('attendances.event_id', Session::get('eventId'))
                ->groupBy('attendances.in_time', 'attendances.att_status')
                ->orderBy(DB::raw('UNIX_TIMESTAMP(attendances.in_time)'))
                ->get()->toArray();
        } else {
            $attendancesTimewise = DB::table('attendances')
                ->select('attendances.in_time', DB::raw('IF(att_status = "1", "Approved", "Rejected") AS status'), DB::raw('count(*) as total'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->join('devices', 'devices.id', '=', 'attendances.device_id')
                ->whereNotNull('attendances.created_at')
                ->groupBy('attendances.in_time', 'attendances.att_status')
                ->orderBy(DB::raw('UNIX_TIMESTAMP(attendances.in_time)'))
                ->get()->toArray();
        }
        // dd($attendancesTimewise);
        $checktime = '';
        $splitTimeWiseData = [];
        for ($i = 0; $i < count($attendancesTimewise); $i++) {
            $timeArr = ['', '', '', 0, 0];
            if (date('Y-m-d', strtotime($attendancesTimewise[$i]->in_time)) != $checktime) {
                $checktime = date('Y-m-d', strtotime($attendancesTimewise[$i]->in_time));
                if ($checktime != '' & $i > 0) {
                    $timeArr[2] = date('H:i:s', strtotime($attendancesTimewise[$i - 1]->in_time));
                }
                $timeArr[0] = date('Y-m-d', strtotime($attendancesTimewise[$i]->in_time));
                $timeArr[1] = date('H:i:s', strtotime($attendancesTimewise[$i]->in_time));
                $timeArr[2] = $timeArr[2];
                $timeArr[3] = $timeArr[3];
                $timeArr[4] = $timeArr[4];
                array_push($splitTimeWiseData, $timeArr);
            }
            if ($i == (count($attendancesTimewise) - 1)) {
                $timeArr[0] = $timeArr[0];
                $timeArr[1] = $timeArr[1];
                $timeArr[2] = date('H:i:s', strtotime($attendancesTimewise[$i]->in_time));
                $timeArr[3] = $timeArr[3];
                $timeArr[4] = $timeArr[4];
                array_push($splitTimeWiseData, $timeArr);
            }
        }

        // dd($splitTimeWiseData);
        $splitTimeWiseArr = [];
        for ($m = 0; $m < count($splitTimeWiseData); $m++) {
            if ($m > 0) {
                $time = strtotime($splitTimeWiseData[$m - 1][1]);
                $round = 10 * 60;
                $rounded = floor($time / $round) * $round;
                $datetime1 = new Carbon($splitTimeWiseData[$m - 1][0] . ' ' . date("H:i:s", $rounded));
                $datetime2 = new Carbon($splitTimeWiseData[$m - 1][0] . ' ' . $splitTimeWiseData[$m][2]);
                $interval = $datetime1->diff($datetime2);
                $calMins = (($interval->format('%h') * 60) + $interval->format('%i'));
                $intervalCount = (($calMins < 10) ? 1 : ceil($calMins / 10));
                $startTime = date("H:i:s", $rounded);
                $timeArr = ['', '', '', '', ''];
                for ($n = 1; $n <= ($intervalCount); $n++) {
                    $timeArr[0] = $splitTimeWiseData[$m - 1][0];
                    $timeArr[1] = $startTime;
                    $time = Carbon::parse($startTime);
                    $endTime = $time->addMinutes(10);
                    $timeArr[2] = $endTime->format('H:i:s');
                    $startTime = $endTime->format('H:i:s');
                    $timeArr[3] = $splitTimeWiseData[$m - 1][3];
                    $timeArr[4] = $splitTimeWiseData[$m - 1][4];
                    array_push($splitTimeWiseArr, $timeArr);
                }
            }
        }
        // dd($splitTimeWiseArr);
        for ($j = 0; $j < count($splitTimeWiseArr); $j++) {
            for ($k = 0; $k < count($attendancesTimewise); $k++) {
                if ($splitTimeWiseArr[$j][0] != '') {
                    $now = time();
                    $startTime = strtotime($splitTimeWiseArr[$j][0] . ' ' . $splitTimeWiseArr[$j][1], $now);
                    $endTime = strtotime($splitTimeWiseArr[$j][0] . ' ' . $splitTimeWiseArr[$j][2], $now);
                    $point = strtotime($attendancesTimewise[$k]->in_time, $now);
                    if ($point >= $startTime && $point <= $endTime) {

                        //echo "<br>startTime<br />" . $splitTimeWiseArr[$j][0] . "<br>total<br />" . $attendancesTimewise[$k]->total;
                        if ($attendancesTimewise[$k]->status == 'Approved') {
                            $splitTimeWiseArr[$j][3] = $splitTimeWiseArr[$j][3] + $attendancesTimewise[$k]->total;
                        }
                        if ($attendancesTimewise[$k]->status == 'Rejected') {
                            $splitTimeWiseArr[$j][4] = $splitTimeWiseArr[$j][4] + $attendancesTimewise[$k]->total;
                        }
                    }
                }
            }
        }

        // dd($splitTimeWiseArr);
        $timeReportData = [];
        $timeHeaders1 = ['', '', '', ''];
        $timeHeaders2 = ['', '', '', ''];
        $timeHeaders3 = ['Time Wise Report', '', '', ''];
        $timeHeaders4 = ['', '', '', ''];
        $timeHeaders5 = ['Date Time', 'Approved', 'Rejected', ''];

        array_push($timeReportData, $timeHeaders1, $timeHeaders2, $timeHeaders3, $timeHeaders4, $timeHeaders5);
        $chkDate = '';
        for ($k = 0; $k < count($splitTimeWiseArr); $k++) {
            if ($splitTimeWiseArr[$k][0] != $chkDate) {
                array_push($timeReportData, [$splitTimeWiseArr[$k][0], '', '', '']);
                array_push($timeReportData, ['  ' . $splitTimeWiseArr[$k][1] . ' - ' . $splitTimeWiseArr[$k][2], $splitTimeWiseArr[$k][3], $splitTimeWiseArr[$k][4], '']);
                $chkDate = $splitTimeWiseArr[$k][0];
            } else {
                array_push($timeReportData, ['  ' . $splitTimeWiseArr[$k][1] . ' - ' . $splitTimeWiseArr[$k][2], $splitTimeWiseArr[$k][3], $splitTimeWiseArr[$k][4], '']);
            }
        }

        $mergedReportData = array_merge($categoryReportData, $deviceReportData, $timeReportData);

        // dd($device, $mergedReportData, $categoryReportData, $deviceReportData, DB::getQueryLog());
        $collection = new Collection();
        for ($l = 0; $l < count($mergedReportData); $l++) {
            $collection->push((object) [
                'title' => $mergedReportData[$l][0],
                'approved' => $mergedReportData[$l][1],
                'rejected' => $mergedReportData[$l][2],
                'didnotscan' => $mergedReportData[$l][3],
            ]);
        }
        return ($collection);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A3:D3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        // $styleArray = ['font' => [
        //     'bold' => true,
        // ]];
        // $event->getSheet()->getDelegate()->getStyle('A1:G1')->applyFromArray($styleArray);
        $event->getSheet()->getDelegate()->getStyle('A1:G1')->getFont()->setName('Calibri')->setSize(14);
    }
}
