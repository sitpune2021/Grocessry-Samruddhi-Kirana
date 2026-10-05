@extends('layouts.app')

@section('content')

<div class="content-wrapper">

    <div class="container-xxl container-p-y">

        <div class="card role-form-card">


            <!-- =========================
                 HEADER
            ========================== -->

            <div class="role-form-header">

                <div>

                    @if ($mode == 'add')
                        <h4 class="role-form-title">
                            Add Role
                        </h4>

                        <p class="role-form-subtitle">
                            Create a new role
                        </p>
                    @endif


                    @if ($mode == 'edit')
                        <h4 class="role-form-title">
                            Edit Role
                        </h4>

                        <p class="role-form-subtitle">
                            Update role information
                        </p>
                    @endif


                    @if ($mode == 'show')
                        <h4 class="role-form-title">
                            Role
                        </h4>

                        <p class="role-form-subtitle">
                            View role information
                        </p>
                    @endif

                </div>

            </div>


            <!-- =========================
                 BODY
            ========================== -->

            <div class="card-body role-form-body">

                <form
                    action="{{ $mode == 'edit' ? route('roles.update', $role->id) : route('roles.store') }}"
                    method="POST">

                    @csrf

                    @if ($mode == 'edit')
                        @method('PUT') <!-- Use PUT method for editing -->
                    @endif


                    <!-- =========================
                         FORM ROW
                    ========================== -->

                    <div class="role-form-row">


                        <!-- ROLE NAME -->

                        <div class="role-form-group">

                            <label class="role-form-label">

                                Role Name

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                name="name"
                                class="form-control role-form-input"
                                placeholder="Enter role name"
                                value="{{ old('name', $role->name ?? '') }}"
                                @if($mode=='show') readonly @endif>


                            @error('name')

                                <div class="text-danger role-error">
                                    {{ $message }}
                                </div>

                            @enderror


                            @if(session('error'))

                                <div class="text-danger role-error">
                                    {{ session('error') }}
                                </div>

                            @endif

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="role-form-group">

                            <label class="role-form-label">

                                Description

                                <span class="text-danger"></span>

                            </label>


                            <input
                                type="text"
                                name="description"
                                class="form-control role-form-input"
                                placeholder="Enter description"
                                value="{{ old('description', $role->description ?? '') }}"
                                @if ($mode=='show') disabled @endif>


                            @error('description')

                                <div class="text-danger role-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    <!-- =========================
                         BUTTONS
                    ========================== -->

                    <div class="role-form-actions">

                        <a
                            href="{{ route('roles.index') }}"
                            class="btn btn-success role-back-btn">

                            Back

                        </a>


                        @if ($mode != 'show')

                            <button
                                type="submit"
                                class="btn btn-success role-submit-btn">

                                {{ $mode == 'edit' ? 'Update Role' : 'Save Role' }}

                            </button>

                        @endif

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<style>

/* =====================================================
   ROLE FORM CARD
===================================================== */

.role-form-card {

    border: 1px solid #e9ecef;

    border-radius: 14px;

    overflow: hidden;

    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);

}


/* =====================================================
   HEADER
===================================================== */

.role-form-header {

    display: flex;

    align-items: center;

    padding: 18px 22px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f2;

}


.role-form-title {

    margin: 0;

    color: #212529;

    font-size: 20px;

    font-weight: 600;

    line-height: 1.3;

}


.role-form-subtitle {

    margin: 3px 0 0;

    color: #8f969c;

    font-size: 11px;

    font-weight: 400;

}


/* =====================================================
   BODY
===================================================== */

.role-form-body {

    padding: 24px;

}


/* =====================================================
   FORM ROW
===================================================== */

.role-form-row {

    display: flex;

    gap: 20px;

    width: 100%;

}


.role-form-group {

    flex: 1;

    min-width: 0;

    margin-bottom: 20px;

}


/* =====================================================
   LABEL
===================================================== */

.role-form-label {

    display: block;

    margin-bottom: 7px;

    color: #343a40;

    font-size: 13px;

    font-weight: 600;

}


/* =====================================================
   INPUT
===================================================== */

.role-form-input {

    width: 100%;

    height: 42px;

    padding: 9px 13px;

    border: 1px solid #dfe3e6;

    border-radius: 7px;

    color: #343a40;

    background: #ffffff;

    font-size: 13px;

    box-shadow: none;

    transition: all 0.2s ease;

}


.role-form-input::placeholder {

    color: #adb5bd;

    font-size: 12px;

}


.role-form-input:focus {

    border-color: #198754;

    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.08);

}


.role-form-input:disabled,

.role-form-input[readonly] {

    background: #f8f9fa;

    color: #6c757d;

    cursor: default;

}


/* =====================================================
   ERROR
===================================================== */

.role-error {

    margin-top: 5px;

    font-size: 11px;

}


/* =====================================================
   ACTIONS
===================================================== */

.role-form-actions {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;

    margin-top: 8px;

    padding-top: 18px;

    border-top: 1px solid #edf0f2;

}


/* =====================================================
   BUTTONS
===================================================== */

.role-back-btn,

.role-submit-btn {

    min-height: 38px;

    padding: 8px 18px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: 500;

}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 768px) {

    .role-form-header {

        padding: 16px;

    }


    .role-form-body {

        padding: 18px;

    }


    .role-form-row {

        gap: 15px;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 576px) {

    .role-form-header {

        padding: 15px;

    }


    .role-form-title {

        font-size: 18px;

    }


    .role-form-subtitle {

        font-size: 11px;

    }


    .role-form-body {

        padding: 15px;

    }


    /* Fields one below another */

    .role-form-row {

        flex-direction: column;

        gap: 0;

    }


    .role-form-group {

        width: 100%;

        margin-bottom: 17px;

    }


    .role-form-input {

        height: 40px;

    }


    /* Buttons */

    .role-form-actions {

        flex-direction: column-reverse;

        align-items: stretch;

        gap: 8px;

    }


    .role-back-btn,

    .role-submit-btn {

        width: 100%;

        text-align: center;

    }

}


/* =====================================================
   SMALL MOBILE
===================================================== */

@media (max-width: 400px) {

    .role-form-body {

        padding: 13px;

    }


    .role-form-header {

        padding: 14px;

    }

}

</style>

@endsection
