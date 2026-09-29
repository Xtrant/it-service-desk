@extends('layouts.app')

@section('title', 'Login - IT Service Desk')

@section('body_class', 'login-page')

@section('content')

    <main class="login-container">

        <section class="login-card">

            <div class="login-brand">

                <div class="login-brand-icon">
                    🛠
                </div>

                <div>
                    <div class="login-brand-name">
                        IT Service Desk
                    </div>

                    <div class="login-brand-subtitle">
                        MIS Support Portal
                    </div>
                </div>

            </div>


            <div class="login-header">

                <div class="dashboard-eyebrow">
                    AUTHENTICATION
                </div>

                <h1>
                    Login
                </h1>

                <p>
                    Masuk untuk mengakses IT Service Desk.
                </p>

            </div>


            @if ($errors->any())

                <div class="login-error">
                    {{ $errors->first() }}
                </div>

            @endif


            <form
                method="POST"
                action="{{ route('login.store') }}"
                class="login-form">

                @csrf

                <div class="login-field">

                    <label for="employee_number">
                        NIP
                    </label>

                    <input
                        id="employee_number"
                        type="text"
                        name="employee_number"
                        value="{{ old('employee_number') }}"
                        autocomplete="username"
                        required
                        autofocus
                    >

                </div>


                <div class="login-field">

                    <label for="password">
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >

                </div>


                <div class="login-options">

                    <label class="login-remember">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >

                        <span>
                            Ingat saya
                        </span>

                    </label>

                </div>


                <button
                    type="submit"
                    class="login-submit">

                    Login

                </button>

            </form>


            <div class="login-footer">
                IT Service Desk · MIS
            </div>

        </section>

    </main>

@endsection