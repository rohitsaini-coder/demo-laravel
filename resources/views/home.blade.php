@extends('layouts.app')
@section('title', 'Users')

@section('custom_css')

@endsection

@section('main-content')


    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box  d-flex justify-content-between">
                <h4 class="page-title">Users</h4>
                <div>
                    <button type="button" class="btn btn-primary  mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap">Add</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="user-table" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Profile Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Description</th>
                                <th>Created At</th>
                            </tr>
                        </thead>

                        <tbody>


                        </tbody>
                    </table>

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div> <!-- end row-->



    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm" enctype="multipart/form-data">

                <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                            <label for="name">Name</label>
                            <span class="error-message text-danger" id="error-name"></span>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                            <label for="email">Email</label>
                            <span class="error-message text-danger" id="error-email"></span>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                            <label for="phone">Phone</label>
                            <span class="error-message text-danger" id="error-phone"></span>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Description" name="description" id="description" style="height: 100px" ></textarea>
                            <label for="description">Description</label>
                            <span class="error-message text-danger" id="error-description"></span>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" name="role_id" id="role_id" aria-label="Floating label select example">
                                <option>Select Role</option>
                                @foreach ( $roles as $role )
                                    <option value="{{$role->id}}"> {{$role->name}}</option>
                                @endforeach
                            </select>
                            <label for="role_id">Role</label>

                            <span class="error-message text-danger" id="error-role_id"></span>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="file" name="profile_image" id="profile_image" class="form-control">
                            <label for="profile_image">Profile Image</label>
                            <span class="error-message text-danger" id="error-profile_image"></span>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>

            </div>
        </div>
    </div>
@endsection

@section('custom_JS')

@parent
<script>
    var userTable = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/users',
            type: 'GET'
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'profile_image',
                className:'table-user',
                render: function(data, type, row) {
                    return '<img src="' + data + '" alt="User Image" class="me-2 rounded-circle">';
                }
            },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'role' },
            { data: 'description' },
            { data: 'created_at' },

        ],
        pageLength: 10,
        order: [[7, 'desc']]
    });


    document.getElementById('userForm').addEventListener('submit', function (e) {
        e.preventDefault();

        clearErrors();

        let isValid = validateForm();

        if (!isValid) {
            return;
        }

        let formData = new FormData(this);
        fetch('/api/users/create', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (!data.status) {

                toasterAlert('error',data.message);

            } else if(data.errors) {

                displayErrors(data.errors);

            }else{
                document.getElementById('userForm').reset();
                $('#exampleModal').modal('hide');
                userTable.ajax.reload(null, false);
                toasterAlert('success',data.message);

            }
        });
    });

    function validateForm() {
        let name = document.getElementById('name').value;
        let email = document.getElementById('email').value;
        let phone = document.getElementById('phone').value;
        let role = document.getElementById('role_id').value;

        let description = document.getElementById('description').value;

        let image = document.getElementById('profile_image').files[0];

        let isValid = true;

        if (!name) {
            showError('name', 'The name field is required.');
            isValid = false;
        }


        if (!email) {
            showError('email', 'The email field is required.');
            isValid = false;
        }else if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            showError('email', 'The email is invalid');
            isValid = false;
        }

        if (!phone) {
            showError('phone', 'The phone field is required.');
            isValid = false;
        }else if (!/^\d{10}$/.test(phone)) {
            showError('phone','Please enter a valid 10-digit phone number');
            isValid = false;
        }


        if (isNaN(role)) {
            showError('role_id', 'The role field is required.');
            isValid = false;
        }

        if (!description) {
            showError('description', 'The description field is required.');
            isValid = false;
        }

        if (image == undefined) {
            showError('profile_image', 'The profile image field is required.');
            isValid = false;
        }else if(!['image/jpeg', 'image/png'].includes(image.type)){
            showError('profile_image', 'Only JPG and PNG images are allowed.');
            isValid = false;
        }

        return isValid;
    }

    function showError(field, message) {
        let errorElement = document.getElementById(`error-${field}`);
        if (errorElement) {
            errorElement.innerText = message;
        }
    }

    function displayErrors(errors) {
        for (let field in errors) {
            let errorElement = document.getElementById(`error-${field}`);
            if (errorElement) {
                errorElement.textContent = errors[field][0];
            }
        }
    }

    function clearErrors() {
        let errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(el => el.textContent = '');
    }




</script>
@endsection
