<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Language;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Course::all()->map(function($course) {
            return collect($course)->except(['course_modules', 'author']);
        });
    }

    public function authorCourses(Request $request) {
        return Course::where('authorId', auth('sanctum')->user()->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'language' => 'required',
            'level' => 'required',
            'category' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif',
            'video' => 'file|mimes:mp4,avi,mov,wmv,flv'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $violations = [];
            foreach($errors->messages() as $field => $message) {
                $violations[$field] = [
                    'message' => $message[0]
                ];
            }

            return response()->invalid($violations);
        }

        $course = new Course();
        $basePath = '/uploads/users/'.auth('sanctum')->user()->id.'/courses/';

        $course->authorId = auth('sanctum')->user()->id;
        $course->title = $request->title ?? $course->title;
        $course->description = $request->description ?? $course->description;
        $course->price = $request->price ?? $course->price;
        $course->requirement = $request->requirement ?? $course->requirement;
        $course->about = $request->about ?? $course->about;
        $course->knowledge = $request->knowledge ?? $course->knowledge;
        $course->language_id = Language::where('name', $request->language)->first()->id;
        $course->level_id = Level::where('name', $request->level)->first()->id;
        $course->category_id = Category::where('name', $request->category)->first()->id;
        $course->save();

        if ($request->image) {
            $imageExtension = $request->image->getClientOriginalExtension();
            Storage::disk('local')->putFileAs($basePath.$course->id, $request->image, 'course_image.'.$imageExtension);
            $course->image = $basePath.$course->id.'/course_image.'.$imageExtension;
            $course->save();
        }

        if ($request->video) {
            $videoExtension = $request->video->getClientOriginalExtension();
            Storage::disk('local')->putFileAs($basePath.$course->id, $request->video, 'course_video.'.$videoExtension);
            $course->video = $basePath.$course->id.'/course_video.'.$videoExtension;
            $course->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'course created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        if ($course) {
            return collect($course)->except(['course_modules']);
        }

        return null;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {

        if (!$course) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Course not found'
            ], 404);
        }

        $basePath = '/uploads/users/'.auth('sanctum')->user()->id.'/courses/';

        $course->authorId = auth('sanctum')->user()->id;
        $course->title = $request->title ?? $course->title;
        $course->description = $request->description ?? $course->description;
        $course->price = $request->price ?? $course->price;
        $course->requirement = $request->requirement ?? $course->requirement;
        $course->about = $request->about ?? $course->about;
        $course->knowledge = $request->knowledge ?? $course->knowledge;
        $course->language_id = Language::where('name', $request->language)->first()->id ?? $course->language_id;
        $course->level_id = Level::where('name', $request->level)->first()->id ?? $course->level_id;
        $course->category_id = Category::where('name', $request->category)->first()->id ?? $course->category_id;
        $course->save();

        if ($request->image) {
            $imageExtension = $request->image->getClientOriginalExtension();
            Storage::disk('local')->putFileAs($basePath.$course->id, $request->image, 'course_image.'.$imageExtension);
            $course->image = $basePath.$course->id.'/course_image.'.$imageExtension;
            $course->save();
        }

        if ($request->video) {
            $videoExtension = $request->video->getClientOriginalExtension();
            Storage::disk('local')->putFileAs($basePath.$course->id, $request->video, 'course_video.'.$videoExtension);
            $course->video = $basePath.$course->id.'/course_video.'.$videoExtension;
            $course->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function uploads(User $user, Course $course, string $name) {
        // if (!$course) {
        //     return 'no course';
        // }
        // return $course;
        $basePath = 'uploads/users/'.$user->id.'/courses/'.$course->id;
        $absolutePath = Storage::disk('local')->path($basePath);

        return Storage::disk('local')->response($basePath.'/'.$name);
    }

    private function validateCourse(Request $request) {
        return Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'language' => 'required',
            'level' => 'required',
            'category' => 'required',
            'image' => 'required',
            'video' => 'required'
        ]);
    }
}
