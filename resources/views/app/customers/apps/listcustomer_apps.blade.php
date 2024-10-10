@extends('layout.app')
@section('body-content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="{{ route('dashboard') }}">Dashboard</a> /</span> Customer's Apps</h4>
            <button type="button" class="btn btn-primary py-3 mb-4" data-bs-toggle="modal" data-bs-target="#customerModal"><i
                    class="menu-icon bx bx-layer-plus"></i>New Application</button>
        </div>

        <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Create a app</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('createCustomerApp') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="nameBasic" class="form-label">Name</label>
                                    <input type="text" id="nameBasic" class="form-control" name="name" placeholder="Enter Name" />
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="" class="form-label">Customer</label>
                                    <select name="customer_id" class="form-select" required>
                                        <option value="">** Choose a customer **</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="dobBasic" class="form-label">is Active ?</label>
                                    <select name="status" class="form-select" id="">
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
            <h5 class="card-header">All Customer's Apps</h5>
            <div class="card-body">
                <div class="text-nowrap">
                    <table class="table myTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code App</th>
                                <th>Status</th>
                                <th>Customer Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer_apps as $customer_app)
                                <tr>
                                    <td><strong>{{ $customer_app->name }}</strong></td>
                                    <td>{{ $customer_app->code_app }}</td>
                                    <td>
                                        @switch($customer_app->status)
                                            @case(1)
                                            <span class="badge bg-label-primary me-1">Active</span>
                                                @break
                                        
                                            @default
                                            <span class="badge bg-label-dark me-1">Inactive</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $customer_app->customer_name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (is_null($customer_app))
                                                    <a class="dropdown-item" href="{{ route('generate_api_key', $customer_app->id) }}"><i 
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
