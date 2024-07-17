@extends('layouts.app')


@section('content')


<div class="p-3 mb-4 bg-light">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <h1 class="display-5 fw-bold">Add Project</h1>

        <a href="{{route('projects.index')}}" class="btn btn-dark">
            <i class="bi bi-arrow-left"></i> projects
        </a>

    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<div class="container">


    <form action="{{route('projects.store')}}" method="post" enctype="multipart/form-data">
        @csrf


        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelper" placeholder="Boolfolio project name" value="{{old('name')}}" />
            <small id="nameHelper" class="form-text text-muted">Type a name for your project</small>
            @error('name')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="cover_image" class="form-label">Choose file</label>
            <input type="file" class="form-control" name="cover_image" id="cover_image" placeholder="" aria-describedby="coverImageHelper" />
            <div id="coverImageHelper" class="form-text">Upload an image for the curret project</div>
            @error('cover_image')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>



        <div class="mb-3">
            <label for="technologies" class="form-label">Technologies</label>
            <select class="form-select form-select-lg" name="technologies[]" id="technologies" multiple>

                @forelse ($technologies as $technology )
                <option value="{{$technology->id}}">{{$technology->name}}</option>

                @empty

                <option selected>Select one</option>
                @endforelse

            </select>
        </div>


        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="description" rows="3">{{old('description')}}</textarea>
            @error('description')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-dark">
            <i class="bi bi-floppy"></i> Save
        </button>


    </form>



</div>


@endsection