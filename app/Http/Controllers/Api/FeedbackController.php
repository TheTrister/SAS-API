<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ToJsonResource;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::latest()->paginate(1000);
        return new ToJsonResource(true, 'Data Feedback', $feedback);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NIS' => 'required',
            'EMAIL' => 'required',
            'FEEDBACK' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $feedback = Feedback::create([
            'NIS' => $request->NIS,
            'EMAIL' => $request->EMAIL,
            'FEEDBACK' => $request->FEEDBACK,
        ]);
        return new ToJsonResource(true, 'Feedback Telah dikirim', $feedback);
    }
}
