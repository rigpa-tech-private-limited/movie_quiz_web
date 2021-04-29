<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendancesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return ["Event Name", "Device Name", "BIB Number", "First Name", "Last Name", "Email", "Mobile Number", "Category", "In Time", "Attendance Status", "Reject Reason", "COVID Status"];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        if (Session::get('eventId')) {
            return Attendance::select(DB::raw('events.name as event_name,CONCAT(devices.name, " (", devices.operator_name, " - " , devices.operator_mobile ,")") as device_name,participants.bib_number,participants.first_name,participants.last_name,participants.email,participants.mobile_number,participants.category,in_time,IF(att_status = "1", "Approved", "Rejected") AS status,reject_reason,IF(covid_status = "1", "Negative", "Unknown") AS covid_status'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('devices', 'devices.id', '=', 'attendances.device_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->where('attendances.event_id', Session::get('eventId'))
                ->where(function ($query) {
                    $query->where('in_time', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('reject_reason', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('devices.name', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('participants.first_name', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('participants.bib_number', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('participants.last_name', 'like', '%' . Session::get('eventSearch') . '%');
                })->orderBy('devices.id', 'ASC')->get();
        } else {
            return Attendance::select(DB::raw('events.name as event_name,CONCAT(devices.name, " (", devices.operator_name, " - " , devices.operator_mobile ,")") as device_name,participants.bib_number,participants.first_name,participants.last_name,participants.email,participants.mobile_number,participants.category,in_time,IF(att_status = "1", "Approved", "Rejected") AS status,reject_reason,IF(covid_status = "1", "Negative", "Unknown") AS covid_status'))
                ->join('events', 'events.id', '=', 'attendances.event_id')
                ->join('devices', 'devices.id', '=', 'attendances.device_id')
                ->join('participants', 'participants.id', '=', 'attendances.participant_id')
                ->whereNotNull('attendances.created_at')
                ->where(function ($query) {
                    $query->where('in_time', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('reject_reason', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('devices.name', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('participants.first_name', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('participants.bib_number', 'like', '%' . Session::get('eventSearch') . '%')
                        ->orwhere('participants.last_name', 'like', '%' . Session::get('eventSearch') . '%');
                })->orderBy('devices.id', 'ASC')->get();
        }

    }
}
