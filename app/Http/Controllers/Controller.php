<?php

namespace App\Http\Controllers;

use App\Application;
use App\Job;
use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'profile' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }
        $user = new user;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->profile = $request->input('profile');
        $user->token = uniqid();
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
        ]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }
        $email = $request->input('email');

        if (User::where('email', $email)->first()) {
            $token = uniqid();
            User::where('email', $email)->update(['token' => $token]);
            $user = User::where('email', $email)->first();
            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login unsuccessful.',
            ], 400);
        }
    }
    public function get_jobs()
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => Job::all(),
        ], 200);
    }
    public function add_job($token, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required',
            'company' => 'required',
            'job_title' => 'required',
            'skills' => 'required',
            'description' => 'required',
            'salary_range' => 'required',
            'location' => 'required',
            'experience' => 'required',
            'education' => 'required',
            'stream' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);

        }
        if ($this->validUser($token, 'job provider') == true) {
            $job = new Job;
            $job->provider_id = $request->input('provider_id');
            $job->company = $request->input('company');
            $job->job_title = $request->input('job_title');
            $job->skills = $request->input('skills');
            $job->description = $request->input('description');
            $job->salary_range = $request->input('salary_range');
            $job->location = $request->input('location');
            $job->experience = $request->input('experience');
            $job->education = $request->input('education');
            $job->stream = $request->input('stream');
            $job->created_at = date('Y-m-d H:i:s');
            $job->save();
            return response()->json([
                'success' => true,
                'message' => 'Job added successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No access to the user.',
            ], 400);
        }
    }
    public function apply_job($token, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
            'provider_id' => 'required',
            'seeker_id' => 'required',
            'contact' => 'required',
            'message' => 'required',
            'file' => 'required|mimes:pdf,xlx,csv|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        if ($this->validUser($token, 'job seeker') == true) {
            $fileName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('uploads'), $fileName);
            $application = new Application;
            $application->job_id = $request->input('job_id');
            $application->provider_id = $request->input('provider_id');
            $application->seeker_id = $request->input('seeker_id');
            $application->contact = $request->input('contact');
            $application->message = $request->input('message');
            $application->status = 'Pending';
            $application->resume = $fileName;
            $application->save();
            return response()->json([
                'success' => true,
                'message' => 'Job applied successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No access to the user.',
            ], 400);
        }
    }
    public function get_applications($token, $provider_id)
    {
        if ($this->validUser($token, 'job provider') == true) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => Application::all()->where('provider_id', $provider_id),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No access to the user.',
            ], 400);
        }
    }
    public function update_status($token, $app_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }
        if ($this->validUser($token, 'job provider') == true) {
            if (Application::where('id', $app_id)->update(['status' => $request->input('status')])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Updation failed.',
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No access to the user.',
            ], 400);
        }
    }
    public function validUser($token, $profile)
    {
        $user = User::where('token', $token)->where('profile', $profile)->first();
        if ($user) {
            return true;
        } else {
            return false;
        }

    }
}
