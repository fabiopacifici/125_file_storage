<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $projects = Project::orderByDesc('id')->paginate();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $technologies = Technology::all();
        return view('admin.projects.create', compact('technologies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        //dd($request->all());

        $val_data = $request->validated();

        if ($request->has('cover_image')) {
            // save the image

            $image_path = Storage::put('uploads', $request->cover_image);
            $val_data['cover_image'] = $image_path;
            //dd($image_path, $val_data);
        }
        //dd($val_data);

        $proj = Project::create($val_data);

        if ($request->has('technologies')) {
            $proj->technologies()->attach($request->technologies);
        }

        return to_route('projects.index')->with('message', 'Project created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        //dd($request->all());


        $val_data = $request->validated();

        if ($request->has('cover_image')) {
            // save the image
            $image_path = Storage::put('uploads', $request->cover_image);
            $val_data['cover_image'] = $image_path;

            if ($project->cover_image && !Str::startsWith($project->cover_image, 'http')) {

                // not null and not startingn with http
                Storage::delete($project->cover_image);
            }

            //dd($image_path, $val_data);
        }


        //dd($val_data);

        $project->update($val_data);

        return to_route('projects.index')->with('message', 'Project updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {


        if ($project->cover_image && !Str::startsWith($project->cover_image, 'http')) {
            // not null and not startingn with http
            Storage::delete($project->cover_image);
        }


        $project->delete();

        return to_route('projects.index')->with('message', 'Project Deleted');
    }
}
