@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="glass p-5">
                <h2 class="text-white mb-4">Edit Profile</h2>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Surname</label>
                            <input type="text" class="form-control" name="surname" value="{{ auth()->user()->surname }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Avatar</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-glass">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

