@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Role</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    </div>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="name" class="form-label">Role Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter role name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Assign Permissions</label>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" class="form-check-input" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('permissions')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create Role</button>
    </form>
</div>
@endsection
