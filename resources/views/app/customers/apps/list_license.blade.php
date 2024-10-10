@extends('layout.app')
@section('body-content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="{{ route('dashboard') }}">Dashboard</a> /</span> License Keys</h4>
            <button type="button" class="btn btn-primary py-3 mb-4" data-bs-toggle="modal" data-bs-target="#customerModal"><i
                    class="menu-icon bx bxs-key"></i>New License Key</button>
        </div>

        <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Create a license key</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('createLicenseKey') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="nameBasic" class="form-label">Start Date</label>
                                    <input type="date" id="nameBasic" class="form-control" name="start_at" min="{{ date('Y-m-d') }}" />
                                </div>
                                <div class="col mb-3">
                                    <label for="nameBasic" class="form-label">End Date</label>
                                    <input type="date" id="nameBasic" class="form-control" name="end_at" />
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="" class="form-label">Customer's app</label>
                                    <select name="customer_app_id" class="form-select" required>
                                        <option value="">** Choose a customer's app **</option>
                                        @foreach ($customer_apps as $customer_app)
                                            <option value="{{ $customer_app->id }}">{{ $customer_app->name.' - '.$customer_app->customer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="dobBasic" class="form-label">is Active ?</label>
                                    <select name="isActive" class="form-select" id="">
                                        <option value="">** Choose an option **</option>
                                        <option value="0">Inactive</option>
                                        <option value="1">Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if (Session::get('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (Session::get('fail'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ Session::get('fail') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">All License Keys</h5>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table myTable">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>License Key</th>
                                <th>App Name</th>
                                <th>Start date</th>
                                <th>End date</th>
                                <th>Remaining Time</th>
                                <th>Status</th>
                                <th>Auto Renew</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($licenses as $license)
                                <tr>
                                    <td><center>{{ $i.'.' }}</center></td>
                                    <td><i class="menu-icon bx bxs-key"></i><strong>{{ $license->key }}</strong></td>
                                    <td>{{ $license->customer_app_name.' - '.$license->customer_name }}</td>
                                    @php
                                        $start = \Carbon\Carbon::parse($license->start_at);
                                        $end = \Carbon\Carbon::parse($license->end_at);
                                        if (date('Y-m-d') <= $end) {
                                            $remain = $end->diffInDays(date('Y-m-d'));
                                        } else {
                                            $remain = 0;
                                        }
                                        
                                    @endphp
                                    <td>{{ $start->format('d/m/Y') }}</td>
                                    <td>{{ $end->format('d/m/Y') }}</td>
                                    <td><center>{{ $remain.' days' }}</center></td>
                                    <td>
                                        @switch($license->isActive)
                                            @case(1)
                                            <center><span class="badge bg-label-primary me-1">Active</span></center>
                                                @break
                                                
                                            @case(2)
                                            <center><span class="badge bg-label-warning me-1">Suspended</span></center>
                                                @break

                                            @case(3)
                                            <center><span class="badge bg-label-danger me-1">Expired</span></center>
                                                @break
                                        
                                            @default
                                            <center><span class="badge bg-label-dark me-1">Inactive</span></center>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($license->renew)
                                            @case(1)
                                            <center><span class="badge bg-label-primary me-1">Yes</span></center>
                                                @break
                                        
                                            @default
                                            <center><span class="badge bg-label-dark me-1">No</span></center>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (is_null($license))
                                                    <a class="dropdown-item" href="{{ route('generate_api_key', $license->id) }}"><i 
                                                        class="bx bxs-key me-1"></i> Generate API Key</a>
                                                @endif
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-trash me-1"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
