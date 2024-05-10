@section('title')
    Tambah Siswa
@endsection

<x-dashboard-layouts::main>
    <x-dashboard::ui.page-header title="Siswa" desc="Semua data siswa yang tersedia">
        <x-dashboard::ui.page-header.item href="{{ route('dashboard.students.index') }}" label="Siswa" />
        <x-dashboard::ui.page-header.item label="Tambah" active />
    </x-dashboard::ui.page-header>

    <x-dashboard::ui.card title="Form Data Siswa">
        <form action="{{ route('dashboard.students.store') }}" method="POST">
            @csrf
            <x-dashboard::ui.input type="email" label="Email" name="email" value="{{ old('email') }}"
                placeholder="Masukan Email" required />

            <x-dashboard::ui.input type="text" label="NISN" name="nisn" value="{{ old('nisn') }}"
                placeholder="Masukan NISN" required />

            <x-dashboard::ui.input type="text" label="Nama" name="name" value="{{ old('name') }}"
                placeholder="Masukan Nama" required />

            <x-dashboard::ui.input.select label="Jenis Kelamin" name="gender" :options="['male' => 'Laki-laki', 'female' => 'Perempuan']" :selected="old('gender')"
                required />

            <x-dashboard::ui.input type="date" label="Tanggal Lahir" name="date_of_birth"
                value="{{ old('date_of_birth') }}" placeholder="Masukan Tanggal Lahir" required />

            <x-dashboard::ui.input.select label="Agama" name="religion" :options="[
                \App\Enums\Religion::ISLAM->value => 'Islam',
                \App\Enums\Religion::CHRISTIANITY->value => 'Kristen',
                \App\Enums\Religion::CATHOLICISM->value => 'Katolik',
                \App\Enums\Religion::HINDUISM->value => 'Hindu',
                \App\Enums\Religion::BUDDHISM->value => 'Buddha',
                \App\Enums\Religion::CONFUCIANISM->value => 'Konghucu',
            ]" :selected="old('religion')" required />

            <x-dashboard::ui.input.select label="Status Yatim" name="orphan_status" :options="[
                \App\Enums\OrphanStatus::ORPHAN_BOTH->value => 'Yatim Piatu',
                \App\Enums\OrphanStatus::ORPHAN_FATHER->value => 'Yatim',
                \App\Enums\OrphanStatus::ORPHAN_MOTHER->value => 'Piatu',
                \App\Enums\OrphanStatus::NONE->value => 'Tidak Yatim Piatu',
            ]" :selected="old('orphan_status')"
                required />

            <x-dashboard::ui.input type="text" label="Nomor Telepon" name="phone_number"
                value="{{ old('phone_number') }}" placeholder="Masukan Nomor Telepon" required />

            <x-dashboard::ui.input.text-area label="Alamat" name="address" placeholder="Masukan Alamat"
                value="{{ old('address') }}" required />


            <x-dashboard::ui.input.select label="Kelas" name="classroom_id" :options="$classrooms" :selected="old('classroom_id')" />

            <x-dashboard::ui.input.select label="Orang Tua" name="student_parent_id" :options="$studentParents" :selected="old('student_parent_id')"
                required />

            <x-dashboard::shared.note.create-account />

            <div class="d-flex justify-content-end">
                <x-dashboard::ui.button type="submit">
                    Kirim
                </x-dashboard::ui.button>
            </div>
        </form>
    </x-dashboard::ui.card>
</x-dashboard-layouts::main>
