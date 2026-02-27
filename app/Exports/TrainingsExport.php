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
     * @param  array<User>  $forUsers  When non-empty, export is for these users' trainings (multi-user, same format as "all")
     */
    public function __construct(
        protected Collection $data,
        protected ?User $forUser = null,
        protected array $forUsers = []
    ) {}

    public function headings(): array
    {
        if ($this->forUser) {
            return [
                'Title',
                'Type of L&D',
                'Provider',
                'Venue',
                'Start Date',
                'End Date',
                'Hours',
                'Attended Date',
            ];
        }
        // "all" and multi-user use same columns
        return [
            'Personnel',
            'Employee ID',
            'Title',
            'Type of L&D',
            'Provider',
            'Venue',
            'Start Date',
            'End Date',
            'Hours',
            'Attended Date',
        ];
    }

    /**
     * For "all" export, flatten to one row per user-training. For single user, use collection as-is.
     * For multiple users ($forUsers), flatten each user's trainings into rows.
     */
    public function collection(): Collection
    {
        if ($this->forUser) {
            return $this->data;
        }
        if ($this->forUsers !== []) {
            $flat = collect();
            foreach ($this->forUsers as $user) {
                $trainings = $user->trainings()->orderBy('trainings.start_date', 'desc')->get();
                foreach ($trainings as $training) {
                    $flat->push((object) ['training' => $training, 'user' => $user]);
                }
            }
            return $flat;
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
        $pivot = $training->pivot ?? ($user ? ($user->pivot ?? null) : null);
        return [
            $user ? $user->name : '-',
            $user?->employee_id ?? '-',
            $training->title,
            $training->type_of_ld ? ucfirst($training->type_of_ld) . ($training->type_of_ld_specify ? ' (' . $training->type_of_ld_specify . ')' : '') : '-',
            $training->provider,
            $training->venue,
            $training->start_date?->format('Y-m-d'),
            $training->end_date?->format('Y-m-d'),
            $training->hours,
            $pivot && $pivot->attended_date ? (\Carbon\Carbon::parse($pivot->attended_date)->format('Y-m-d')) : '-',
        ];
    }

    public function map($row): array
    {
        if ($this->forUser) {
            $t = $row instanceof Training ? $row : $row->training;
            $pivot = $row instanceof Training ? ($row->pivot ?? null) : ($row->pivot ?? null);
            return [
                $t->title,
                $t->type_of_ld ? ucfirst($t->type_of_ld) . ($t->type_of_ld_specify ? ' (' . $t->type_of_ld_specify . ')' : '') : '',
                $t->provider,
                $t->venue,
                $t->start_date?->format('Y-m-d'),
                $t->end_date?->format('Y-m-d'),
                $t->hours,
                $t->pivot && $t->pivot->attended_date ? (\Carbon\Carbon::parse($t->pivot->attended_date)->format('Y-m-d')) : '',
            ];
        }

        $training = $row->training;
        $user = $row->user;
        return $this->allExportRow($training, $user);
    }
}
