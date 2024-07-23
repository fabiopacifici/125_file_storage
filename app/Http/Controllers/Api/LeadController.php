<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NewLeadMarkdownMessage;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    //

    public function store(Request $request)
    {
        //dd($request->all());
        $data = $request->all();

        // validate the user inputs
        $validator = Validator::make($data, [
            'name' => 'required|min:5',
            'email' => 'required|email',
            'message' => 'required|min:30'
        ]);

        //dd($validator->fails());
        // check if you have validation errors and return them as json
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // save the lead into the database
        $newLead = new Lead();
        $newLead->fill($data);
        $newLead->save();

        // send the email to the site adming
        Mail::to('info@boolean.com')->send(new NewLeadMarkdownMessage($newLead));

        // return a success json response
        return response()->json([
            'success' => true
        ]);
    }
}
