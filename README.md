# Boolfolio

this is a project

```php

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

```

## Mailable

### Configure mailtrap
