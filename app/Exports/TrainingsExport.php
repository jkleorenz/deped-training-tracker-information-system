<?php

namespace App\Exports;

use App\Models\Training;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @param  Collection  $data  Either Collection of Training (with users) for "all", or Collection of items for single user
     * @param  User|null  $forUser  When set, export is for this user's trainings only
     */
    public function __construct(
        protected Collection $data,
        protected ?User $forUser = null
    ) {}

    public function headings(): array
    {
        if ($this->forUser) {
            return [
                'Title',
                'Type',
                'Provider',
                'Venue',
                'Start Date',
                'End Date',
                'Hours',
                'Attended Date',
                'Remarks',
            ];
        }
        return [
            'Personnel',
            'Employee ID',
            'Department',
            'Title',
            'Type',
            'Provider',
            'Venue',
            'Start Date',
            'End Date',
            'Hours',
            'Attended Date',
            'Remarks',
        ];
    }

    /**
     * For "all" export, flatten to one row per user-training. For single user, use collection as-is.
     */
    public function collection(): Collection
    {
        if ($this->forUser) {
            return $this->data;
        }
        $flat = collect();
        foreach ($this->data as $training) {
            $users = $training->users ?? collect();
            if ($users->isEmpty()) {
                $flat->push((object) ['training' => $training, 'user' => null]);
            } else {
                foreach ($users as $user) {
                    $flat->push((object) ['training' => $training, 'user' => $user]);
                }
            }
        }
        return $flat;
    }

    private function allExportRow(Training $training, ?User $user): array
    {
        $pivot = $user ? ($user->pivot ?? null) : null;
        return [
            $user ? $user->name : '-',
            $user?->employee_id ?? '-',
            $user?->department ?? '-',
            $training->title,
            $training->type,
            $training->provider,
            $training->venue,
            $training->start_date?->format('Y-m-d'),
            $training->end_date?->format('Y-m-d'),
            $training->hours,
            $pivot && $pivot->attended_date ? (\Carbon\Carbon::parse($pivot->attended_date)->format('Y-m-d')) : '-',
            $pivot?->remarks ?? '',
        ];
    }

    public function map($row): array
    {
        if ($this->forUser) {
            $t = $row instanceof Training ? $row : $row->training;
            $pivot = $row instanceof Training ? ($row->pivot ?? null) : ($row->pivot ?? null);
            return [
                $t->title,
                $t->type,
                $t->provider,
                $t->venue,
                $t->start_date?->format('Y-m-d'),
                $t->end_date?->format('Y-m-d'),
                $t->hours,
                $t->pivot && $t->pivot->attended_date ? (\Carbon\Carbon::parse($t->pivot->attended_date)->format('Y-m-d')) : '',
                $t->pivot?->remarks ?? '',
            ];
        }

        $training = $row->training;
        $user = $row->user;
        return $this->allExportRow($training, $user);
    }
}
