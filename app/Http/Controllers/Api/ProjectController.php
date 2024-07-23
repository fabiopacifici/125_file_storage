<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {

        // option 1 (shortcut)
        // if you return a collection or an object they will be converted by laravel into a json.
        //return Project::all();

        // option 2 (return/response/json)

        return  response()->json([
            'success' => true,
            'projects' => Project::with('technologies')->orderByDesc('id')->paginate()
        ]);
    }

    public function latest()
    {
        return response()->json([
            'success' => true,
            'projects' => Project::with('technologies')->orderByDesc('id')->take(3)->get()
        ]);
    }

    public function show($slug)
    {


        //dd($slug);
        $project = Project::with('technologies')->where('slug', $slug)->first();
        //dd($project);


        if ($project) {
            return response()->json([
                'success' => true,
                'project' => $project
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'not found'
            ]);
        }
    }
}
