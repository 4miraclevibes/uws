<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Retrieve all records by type
     */
    public function index(): JsonResponse
    {
        $type = request()->query('type');

        $modelMap = [
            'image'    => Image::class,
            'document' => Document::class,
        ];

        if (! isset($modelMap[$type])) {
            return response()->json([
                'message' => 'Invalid type',
            ], 400);
        }

        $items = $modelMap[$type]::all();

        return response()->json([
            'data'    => $items,
            'message' => 'success',
        ]);
    }

    /**
     * Store multiple uploads and dispatch to models by extension
     */
    public function store(Request $request): JsonResponse
    {
        // Validation rules and custom messages
        $rules = [
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB
            'descriptions' => 'sometimes|array',
            'descriptions.*'=> 'sometimes|string|max:255',
        ];

        $messages = [
            'files.required' => 'Please upload at least one file.',
            'files.array' => 'Files must be an array.',
            'files.*.file' => 'Each item must be a valid file.',
            'files.*.mimes' => 'Allowed file types: jpeg, jpg, png, gif, svg, pdf, doc, docx, xls, xlsx, ppt, pptx.',
            'files.*.max' => 'Each file size must not exceed 10MB.',
            'descriptions.array' => 'Descriptions must be an array.',
            'descriptions.*.string' => 'Each description must be a string.',
            'descriptions.*.max' => 'Description max length is 255 characters.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Configuration for dispatching
        $config = [
            'image' => ['ext' => ['jpg','jpeg','png','gif','svg'], 'path' => 'images',    'model' => Image::class,    'field' => 'images'],
            'document' => ['ext' => ['pdf','doc','docx','xls','xlsx','ppt','pptx'], 'path' => 'documents', 'model' => Document::class, 'field' => 'document'],
        ];

        $files        = collect($request->file('files'));
        $descriptions = $request->input('descriptions', []);

        $results = $files->map(function ($file, $index) use ($config, $descriptions) {
            $ext = Str::lower($file->getClientOriginalExtension());

            foreach ($config as $type => $cfg) {
                if (in_array($ext, $cfg['ext'], true)) {
                    $path = $file->store($cfg['path'], 'public');

                    // Prepare data for model
                    $data = [
                        $cfg['field']   => url(Storage::url($path)),
                        'description'  => $descriptions[$index] ?? 'no description',
                    ];

                    // Create record
                    $model = $cfg['model']::create($data);

                    return [
                        'type' => $type,
                        'record' => $model,
                        'url' => url(Storage::url($path)),
                    ];
                }
            }

            return null; // unsupported file
        })->filter()->values();

        if ($results->isEmpty()) {
            return response()->json([
                'message' => 'No valid files to store',
            ], 422);
        }

        return response()->json([
            'message' => 'Files uploaded successfully',
            'data'    => $results,
        ], 201);
    }
}
