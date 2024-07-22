@extends('layouts.app')


@section('content')


<div class="p-3 mb-4 bg-light">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <h1 class="display-5 fw-bold">Projects</h1>

        <a href="{{route('projects.create')}}" class="btn btn-dark">
            <i class="bi bi-pencil-square"></i>
        </a>

    </div>
</div>



<div class="container">

    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>{{session('message')}}</strong>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-primary">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Cover Image</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>

                @forelse ($projects as $project )
                <tr class="">
                    <td scope="row">{{$project->id}}</td>
                    <td>{{$project->name}}</td>
                    <td>


                        @if (Str::startsWith($project->cover_image, 'http'))
                        <img width="140" src="{{$project->cover_image}}" alt="">
                        @else
                        <img width="140" src="{{ asset('storage/' . $project->cover_image) }}" alt="">
                        @endif

                    </td>
                    <td>view/

                        <a class="btn btn-dark" href="{{route('projects.edit', $project)}}">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <!-- Modal trigger button -->
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-{{$project->id}}">
                            <i class="bi bi-trash"></i>
                        </button>

                        <!-- Modal Body -->
                        <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                        <div class="modal fade" id="modal-{{$project->id}}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitle-{{$project->id}}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitle-{{$project->id}}">
                                            Delete current project
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        Deleting project name: {{$project->name}}
                                        ⚡Danger, you cannot undo this operation
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>

                                        <form action="{{route('projects.destroy', $project)}}" method="post">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger">
                                                Confirm
                                            </button>

                                        </form>


                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>

                @empty
                <tr class="">
                    <td scope="row" colspan="3">Nothing to show</td>
                </tr>
                @endforelse



                {{$projects->links('pagination::bootstrap-5')}}

            </tbody>
        </table>
    </div>

</div>


@endsection