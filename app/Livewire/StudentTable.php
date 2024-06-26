<?php

namespace App\Livewire;

use App\Enums\Gender;
use App\Enums\OrphanStatus;
use App\Enums\Religion;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class StudentTable extends DataTableComponent
{
    protected $model = Student::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setSearchStatus(false);
        $this->setFiltersVisibilityStatus(false);
        $this->setAdditionalSelects(['siswa.id as id']);
    }

    public function filters(): array
    {
        return [
            TextFilter::make('NISN Siswa', 'student_nisn')
                ->config([
                    'placeholder' => 'Cari NISN siswa',
                    'max' => 10,
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('siswa.nisn', 'like', '%'.$value.'%');
                }),
            TextFilter::make('Nama Siswa', 'student_name')
                ->config([
                    'placeholder' => 'Cari siswa',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('siswa.nama', 'like', '%'.$value.'%');
                }),
            TextFilter::make('Nama Kelas', 'classroom_name')
                ->config([
                    'placeholder' => 'Cari kelas',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('classroom.nama', 'like', '%'.$value.'%');
                }),
            SelectFilter::make('Jenis Kelamin', 'jenis_kelamin')
                ->options([
                    '' => 'Pilih',
                    Gender::MALE->value => 'Laki-laki',
                    Gender::FEMALE->value => 'Perempuan',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('jenis_kelamin', $value);
                }),
        ];
    }

    public array $bulkActions = [
        'deleteSelected' => 'Hapus',
    ];

    public function deleteSelected()
    {
        Student::whereIn('id', $this->getSelected())->get()->each(function ($student) {
            if ($student->account->foto_profil) {
                Storage::delete($student->account->foto_profil);
            }
        });

        Student::whereIn('id', $this->getSelected())->delete();
    }

    public function builder(): Builder
    {
        return Student::query()
            ->with('classroom')
            ->latest('siswa.created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('NISN', 'nisn')
                ->sortable()
                ->secondaryHeaderFilter('student_nisn')
                ->collapseOnMobile(),

            Column::make('Nama Siswa', 'nama')
                ->sortable()
                ->secondaryHeaderFilter('student_name'),

            Column::make('Email', 'account.email')
                ->sortable()
                ->collapseAlways(),

            Column::make('Nama Kelas', 'classroom.nama')
                ->sortable()
                ->secondaryHeaderFilter('classroom_name')
                ->collapseOnMobile(),

            Column::make('Jenis Kelamin', 'jenis_kelamin')
                ->format(function ($value) {
                    return view('datatable.students.gender-column', [
                        'gender' => $value,
                    ]);
                })
                ->secondaryHeaderFilter('jenis_kelamin')
                ->collapseAlways(),

            Column::make('Tanggal Lahir', 'tanggal_lahir')
                ->collapseAlways(),

            Column::make('Agama', 'agama')
                ->format(fn ($value) => $this->displayReligion($value))
                ->collapseAlways(),

            Column::make('Status Yatim', 'status')
                ->format(fn ($value) => $this->displayOrphanStatus($value))
                ->collapseAlways(),

            Column::make('Alamat', 'alamat')
                ->collapseAlways(),

            Column::make('Nama Orang Tua', 'studentParent.nama')
                ->collapseAlways(),

            Column::make('Aksi')
                ->label(function ($row) {
                    return view('datatable.students.action-column', [
                        'id' => $row->id,
                    ]);
                }),
        ];
    }

    private function displayOrphanStatus($value)
    {
        $statuses = [
            OrphanStatus::YATIM_PIATU->value => 'Yatim Piatu',
            OrphanStatus::YATIM->value => 'Yatim',
            OrphanStatus::PIATU->value => 'Piatu',
            OrphanStatus::NONE->value => 'Tidak Yatim Piatu',
        ];

        return $statuses[$value];
    }

    private function displayReligion($value)
    {
        $religions = [
            Religion::ISLAM->value => 'Islam',
            Religion::CHRISTIANITY->value => 'Kristen',
            Religion::CATHOLICISM->value => 'Katolik',
            Religion::HINDUISM->value => 'Hindu',
            Religion::BUDDHISM->value => 'Buddha',
            Religion::CONFUCIANISM->value => 'Konghucu',
        ];

        return $religions[$value];
    }
}
