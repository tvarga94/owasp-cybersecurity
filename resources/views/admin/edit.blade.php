@extends('layouts.app')

@section('content')

    <div class="card mt-5-new">
        <h2 class="card-header">Edit User</h2>
        <div class="card-body">

            <a class="btn btn-primary btn-sm mb-3" href="{{ url()->previous() }}"><i class="fa fa-arrow-left"></i> Back</a>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label"><strong>Name:</strong></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ $user->name }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter user's name"
                        required autofocus>
                    @error('name')
                    <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"><strong>Email Address:</strong></label>
                    <input
                        name="email"
                        id="email"
                        value="{{ $user->email }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Enter user's email"
                        required>
                    @error('email')
                    <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Update User</button>
            </form>

        </div>
    </div>

@endsection
