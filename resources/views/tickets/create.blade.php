<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Submit Ticket - IT Service Desk</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="ticket-create-page">

        <div class="ticket-create-container">

            <div class="ticket-breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a>

                <span>→</span>

                <span>
                    @switch($category)
                    @case('hardware')
                    Hardware / Software
                    @break

                    @case('application')
                    Aplikasi / Design
                    @break

                    @case('email')
                    Email
                    @break

                    @default
                    Buat Ticket
                    @endswitch
                </span>
            </div>

            <div class="ticket-create-header">

                <div>
                    <div class="ticket-create-eyebrow">
                        MIS SERVICE DESK
                    </div>

                    <h1>
                        Apa yang Anda butuhkan?
                    </h1>

                    <p>
                        Lengkapi informasi berikut agar tim MIS dapat
                        membantu menangani permintaan Anda.
                    </p>
                </div>

                <a
                    href="{{ route('dashboard') }}"
                    class="ticket-back-button">
                    ← Kembali
                </a>

            </div>

            @if ($errors->any())
            <div>
                <strong>Periksa kembali:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('tickets.store') }}" class="ticket-form">
                @csrf

                {{-- REQUEST TYPE --}}
                <div class="ticket-field ticket-service-field">

                    <label for="request_type_id">
                        Apa yang Anda butuhkan?
                    </label>

                    <select
                        name="request_type_id"
                        id="request_type_id"
                        class="ticket-input"
                        required>

                        <option value="">
                            -- Pilih kebutuhan --
                        </option>

                        @foreach ($requestTypes as $type)

                        <option
                            value="{{ $type->id }}"
                            data-code="{{ $type->code }}"
                            @selected(old('request_type_id')==$type->id)>
                            {{ $type->name }}
                        </option>

                        @endforeach

                    </select>

                </div>

                {{-- HARDWARE SECTION --}}
                <div
                    id="hardware-fields"
                    class="ticket-dynamic-section"
                    style="display:none;">

                    <h2>Informasi Perangkat</h2>


                    <div class="ticket-field">
                        <label for="device_name">
                            Nama Perangkat / Model
                        </label>

                        <input
                            type="text"
                            id="device_name"
                            name="details[device_name]"
                            value="{{ old('details.device_name') }}"
                            placeholder="Contoh: Laptop Yafa / Printer Finance">
                    </div>

                    <div class="ticket-field">
                        <label for="device_user">
                            Pengguna / Pemilik perangkat
                        </label>

                        <input
                            type="text"
                            id="device_user"
                            name="details[device_user]"
                            value="{{ old('details.device_user') }}"
                            placeholder="Contoh: Yafa - Finance">
                    </div>

                    <div class="ticket-field">
                        <label for="device_location">
                            Lokasi perangkat
                        </label>

                        <input
                            type="text"
                            id="device_location"
                            name="details[location]"
                            value="{{ old('details.location') }}"
                            placeholder="Contoh: Ruang Finance">
                    </div>

                    <div class="ticket-field">
                        <label for="hardware_problem">
                            Apa masalah / kebutuhan perangkat?
                        </label>

                        <textarea
                            id="hardware_problem"
                            name="details[problem]"
                            rows="4">{{ old('details.problem') }}</textarea>
                    </div>

                    <div class="ticket-field">
                        <label for="hardware_since">
                            Sejak kapan terjadi?
                        </label>

                        <input
                            type="date"
                            id="hardware_since"
                            name="details[since]"
                            value="{{ old('details.since') }}">
                    </div>

                    <div class="ticket-field">
                        <label for="hardware_impact">
                            Dampaknya terhadap pekerjaan
                        </label>

                        <textarea
                            id="hardware_impact"
                            name="details[impact]"
                            rows="3">{{ old('details.impact') }}</textarea>
                    </div>

                </div>

                <div id="replacement-fields" style="display:none;" class="ticket-dynamic-section">

                    <h2>Detail Penggantian</h2>

                    <div class="ticket-field">
                        <label for="replacement_item">
                            Item yang ingin diganti *
                        </label>

                        <input
                            type="text"
                            id="replacement_item"
                            name="details[replacement_item]"
                            value="{{ old('details.replacement_item') }}"
                            placeholder="Contoh: RAM, SSD, Speaker, Baterai">
                    </div>

                    <div class="ticket-field">
                        <label for="parent_device">
                            Perangkat yang terkait
                        </label>

                        <input
                            type="text"
                            id="parent_device"
                            name="details[parent_device]"
                            value="{{ old('details.parent_device') }}"
                            placeholder="Contoh: Laptop Dell Latitude 5420">
                    </div>

                    <div class="ticket-field">
                        <label for="device_user">
                            Pengguna / Pemilik
                        </label>

                        <input
                            type="text"
                            id="replacement_device_user"
                            name="details[device_user]"
                            value="{{ old('details.device_user') }}"
                            placeholder="Contoh: Mail - Finance">
                    </div>

                    <div class="ticket-field">
                        <label for="replacement_location">
                            Lokasi
                        </label>

                        <input
                            type="text"
                            id="replacement_location"
                            name="details[location]"
                            value="{{ old('details.location') }}"
                            placeholder="Contoh: Ruang Finance">
                    </div>

                    <div class="ticket-field">
                        <label for="replacement_condition">
                            Kondisi item saat ini *
                        </label>

                        <textarea
                            id="replacement_condition"
                            name="details[condition]"
                            rows="3"
                            placeholder="Contoh: RAM rusak / SSD sudah tidak terbaca">{{ old('details.condition') }}</textarea>
                    </div>

                    <div class="ticket-field">
                        <label for="replacement_reason">
                            Alasan penggantian *
                        </label>

                        <textarea
                            id="replacement_reason"
                            name="details[replacement_reason]"
                            rows="4"
                            placeholder="Jelaskan kenapa item perlu diganti">{{ old('details.replacement_reason') }}</textarea>
                    </div>

                </div>

                {{-- SOFTWARE SECTION --}}
                <div id="software-fields" style="display:none;" class="ticket-dynamic-section">

                    <h2>Permintaan Software</h2>

                    <div class="ticket-field">
                        <label for="software_name">
                            Software yang dibutuhkan
                        </label>

                        <input
                            type="text"
                            id="software_name"
                            name="details[software_name]"
                            value="{{ old('details.software_name') }}"
                            placeholder="Contoh: Microsoft Visio">
                    </div>

                    <div class="ticket-field">
                        <label for="software_purpose">
                            Untuk kebutuhan apa?
                        </label>

                        <textarea
                            id="software_purpose"
                            name="details[purpose]"
                            rows="4"
                            placeholder="Jelaskan kebutuhan software tersebut">{{ old('details.purpose') }}</textarea>
                    </div>

                    <div class="ticket-field">
                        <label for="software_version">
                            Versi software (jika diketahui)
                        </label>

                        <input
                            type="text"
                            id="software_version"
                            name="details[version]"
                            value="{{ old('details.version') }}"
                            placeholder="Contoh: 2024">
                    </div>

                </div>

                {{-- APPLICATION SECTION --}}
                <div id="application-fields" style="display:none;" class="ticket-dynamic-section">

                    <h2>Pembuatan / Pengembangan Aplikasi</h2>

                    <div class="ticket-field">
                        <label for="app_name">
                            Aplikasi / Sistem
                        </label>

                        <input
                            type="text"
                            id="app_name"
                            name="details[app_name]"
                            value="{{ old('details.app_name') }}"
                            placeholder="Contoh: HR System">
                    </div>

                    <div class="ticket-field">
                        <label for="app_request_kind">
                            Jenis kebutuhan
                        </label>

                        <select
                            id="app_request_kind"
                            name="details[request_kind]"
                            class="ticket-input">
                            <option value="">
                                -- Pilih jenis kebutuhan --
                            </option>

                            <option
                                value="NEW"
                                @selected(old('details.request_kind')==='NEW' )>
                                Aplikasi Baru
                            </option>

                            <option
                                value="CHANGE"
                                @selected(old('details.request_kind')==='CHANGE' )>
                                Perubahan Aplikasi
                            </option>

                            <option
                                value="PROBLEM"
                                @selected(old('details.request_kind')==='PROBLEM' )>
                                Aplikasi Bermasalah
                            </option>
                        </select>
                    </div>

                    <div class="ticket-field">
                        <label for="app_requirement">
                            Jelaskan kebutuhan
                        </label>

                        <textarea
                            id="app_requirement"
                            name="details[requirement]"
                            rows="5"
                            placeholder="Jelaskan kebutuhan atau masalah yang ingin disampaikan">{{ old('details.requirement') }}</textarea>
                    </div>

                </div>

                {{-- DESIGN SECTION --}}
                <div id="design-fields" style="display:none;" class="ticket-dynamic-section">

                    <h2>Graphic Design</h2>

                    <div class="ticket-field">
                        <label for="design_request">
                            Kebutuhan desain
                        </label>

                        <textarea
                            id="design_request"
                            name="details[design_request]"
                            rows="4"
                            placeholder="Contoh: Pembuatan ID Card untuk karyawan baru">{{ old('details.design_request') }}</textarea>
                    </div>

                    <div class="ticket-field">
                        <label for="design_purpose">
                            Untuk siapa / digunakan untuk
                        </label>

                        <input
                            type="text"
                            id="design_purpose"
                            name="details[purpose]"
                            value="{{ old('details.purpose') }}"
                            placeholder="Contoh: Karyawan baru / kebutuhan promosi">
                    </div>

                </div>

                {{-- EMAIL SECTION --}}
                <div
                    id="email-fields"
                    style="display:none;"
                    class="ticket-dynamic-section">

                    <h2 id="email-title">
                        Permintaan Email
                    </h2>

                    {{-- EMAIL REQUEST --}}
                    <div
                        id="email-request-fields"
                        class="ticket-email-subsection"
                        style="display:none;">

                        <div class="ticket-field">

                            <label for="email_user">
                                Untuk siapa?
                            </label>

                            <input
                                type="text"
                                id="email_user"
                                name="details[email_user]"
                                value="{{ old('details.email_user') }}"
                                placeholder="Contoh: Yafa - Finance">

                        </div>

                        <div class="ticket-field">

                            <label for="email_purpose">
                                Tujuan penggunaan email
                            </label>

                            <textarea
                                id="email_purpose"
                                name="details[purpose]"
                                rows="4"
                                placeholder="Jelaskan kebutuhan email tersebut">{{ old('details.purpose') }}</textarea>

                        </div>

                        <div class="ticket-field">

                            <label for="email_address_request">
                                Alamat email (jika sudah diketahui)
                            </label>

                            <input
                                type="email"
                                id="email_address_request"
                                name="details[email_address]"
                                value="{{ old('details.email_address') }}"
                                placeholder="Contoh: Yafa@sdianmandiri.co.id">

                        </div>

                    </div>


                    {{-- EMAIL RELEASE --}}
                    <div
                        id="email-release-fields"
                        class="ticket-email-subsection"
                        style="display:none;">

                        <div class="ticket-field">

                            <label for="email_address_release">
                                Alamat email
                            </label>

                            <input
                                type="email"
                                id="email_address_release"
                                name="details[email_address]"
                                value="{{ old('details.email_address') }}"
                                placeholder="Contoh: Yafa@sdianmandiri.co.id">

                        </div>

                        <div class="ticket-field">

                            <label for="release_reason">
                                Alasan pengeluaran email
                            </label>

                            <textarea
                                id="release_reason"
                                name="details[release_reason]"
                                rows="4"
                                placeholder="Jelaskan alasan email perlu dikeluarkan">{{ old('details.release_reason') }}</textarea>

                        </div>

                    </div>


                    {{-- EMAIL CLOSURE --}}
                    <div
                        id="email-closure-fields"
                        class="ticket-email-subsection"
                        style="display:none;">

                        <div class="ticket-field">

                            <label for="email_address_closure">
                                Alamat email
                            </label>

                            <input
                                type="email"
                                id="email_address_closure"
                                name="details[email_address]"
                                value="{{ old('details.email_address') }}"
                                placeholder="Contoh: Yafa@sdianmandiri.co.id">

                        </div>

                        <div class="ticket-field">

                            <label for="closure_reason">
                                Alasan penutupan email
                            </label>

                            <textarea
                                id="closure_reason"
                                name="details[closure_reason]"
                                rows="4"
                                placeholder="Jelaskan alasan email perlu ditutup">{{ old('details.closure_reason') }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- COMMON --}}
                <div class="ticket-field">

                    <label for="priority">
                        Prioritas
                    </label>

                    <select
                        name="priority"
                        id="priority"
                        class="ticket-input"
                        required>

                        <option value="LOW">
                            Low
                        </option>

                        <option
                            value="NORMAL"
                            @selected(old('priority', 'NORMAL' )==='NORMAL' )>
                            Normal
                        </option>

                        <option value="HIGH">
                            High
                        </option>

                        <option value="URGENT">
                            Urgent
                        </option>

                    </select>

                </div>


                <div class="ticket-field">

                    <label for="needed_at">
                        Dibutuhkan kapan?
                    </label>

                    <input
                        type="datetime-local"
                        id="needed_at"
                        name="needed_at"
                        class="ticket-input"
                        value="{{ old('needed_at') }}">

                </div>


                <div class="ticket-submit-area">

                    <button type="submit">
                        Ajukan Permintaan
                    </button>

                </div>
            </form>


            <script>
                const requestType =
                    document.getElementById('request_type_id');

                const hardwareFields =
                    document.getElementById('hardware-fields');

                const replacementFields =
                    document.getElementById('replacement-fields');

                const softwareFields =
                    document.getElementById('software-fields');

                const applicationFields =
                    document.getElementById('application-fields');

                const designFields =
                    document.getElementById('design-fields');

                const emailFields =
                    document.getElementById('email-fields');

                const emailRequestFields =
                    document.getElementById('email-request-fields');

                const emailReleaseFields =
                    document.getElementById('email-release-fields');

                const emailClosureFields =
                    document.getElementById('email-closure-fields');


                function setSectionState(section, visible) {

                    section.style.display = visible ? 'flex' : 'none';

                    section.querySelectorAll('input, textarea, select')
                        .forEach(input => {
                            input.disabled = !visible;
                        });
                }


                function updateForm() {

                    const option =
                        requestType.options[
                            requestType.selectedIndex
                        ];

                    const code =
                        option?.dataset.code ?? '';


                    // Reset tampilan section
                    hardwareFields.style.display = 'none';
                    replacementFields.style.display = 'none';
                    softwareFields.style.display = 'none';
                    applicationFields.style.display = 'none';
                    designFields.style.display = 'none';

                    // Reset Email
                    emailFields.style.display = 'none';

                    setSectionState(
                        emailRequestFields,
                        false
                    );

                    setSectionState(
                        emailReleaseFields,
                        false
                    );

                    setSectionState(
                        emailClosureFields,
                        false
                    );


                    // HARDWARE REPAIR
                    if (code === 'HARDWARE_REPAIR') {

                        hardwareFields.style.display = 'block';

                    }


                    // HARDWARE REPLACEMENT
                    if (code === 'HARDWARE_REPLACEMENT') {

                        replacementFields.style.display = 'block';

                    }


                    // SOFTWARE
                    if (code === 'SOFTWARE') {

                        softwareFields.style.display = 'block';

                    }


                    // APPLICATION
                    if (code === 'APPLICATION') {

                        applicationFields.style.display = 'block';

                    }


                    // DESIGN
                    if (code === 'DESIGN') {

                        designFields.style.display = 'block';

                    }


                    // EMAIL REQUEST
                    if (code === 'EMAIL_REQUEST') {

                        emailFields.style.display = 'block';

                        setSectionState(
                            emailRequestFields,
                            true
                        );

                    }


                    // EMAIL RELEASE
                    if (code === 'EMAIL_RELEASE') {

                        emailFields.style.display = 'block';

                        setSectionState(
                            emailReleaseFields,
                            true
                        );

                    }


                    // EMAIL CLOSURE
                    if (code === 'EMAIL_CLOSURE') {

                        emailFields.style.display = 'block';

                        setSectionState(
                            emailClosureFields,
                            true
                        );

                    }

                }


                requestType.addEventListener(
                    'change',
                    updateForm
                );


                updateForm();
            </script>

        </div>

    </div>

</body>

</html>