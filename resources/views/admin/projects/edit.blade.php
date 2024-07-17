@extends('layouts.app')


@section('content')


<div class="p-3 mb-4 bg-light">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <h1 class="display-5 fw-bold">Edit {{$project->name}}</h1>

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


    <form action="{{route('projects.update', $project)}}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelper" placeholder="Boolfolio project name" value="{{old('name', $project->name)}}" />
            <small id="nameHelper" class="form-text text-muted">Type a name for your project</small>
            @error('name')
            <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 d-flex gap-2">

            @if (Str::startsWith($project->cover_image, 'http'))
            <img width="140" src="{{$project->cover_image}}" alt="">
            @else
            <img width="140" src="{{ asset('storage/' . $project->cover_image) }}" alt="">
            @endif

            <div>
                <label for="cover_image" class="form-label">Replace Image</label>
                <input type="file" class="form-control" name="cover_image" id="cover_image" placeholder="" aria-describedby="coverImageHelper" />
                <div id="coverImageHelper" class="form-text">Upload an image for the curret project</div>
                @error('cover_image')
                <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>


        </div>


        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="description" rows="3">{{old('description', $project->description)}}</textarea>
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