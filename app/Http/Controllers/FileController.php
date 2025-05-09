<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller {
  /**
   * Store a newly uploaded file for a project.
   */
  public function store(Request $request, string $model, string $uuid) {
    $this->authorize('files.create', File::class);

    $validated = $request->validate([
      'file' => 'required|file|max:10240', // Max 10MB file size
    ]);

    $model = $this->findModelByNameAndUuid($model, $uuid);

    if (!$model) {
      return response()->json(['error' => 'Model not found'], 404);
    }

    $uploadedFile = $validated['file'];

    // Store the file in storage/app/public/projects/{project_id}/files
    // The putFile method automatically generates a unique filename
    $path = Storage::putFile('public/' . $uuid . '/files', $uploadedFile);

    // Create a database record for the file
    $file = new File([
      'name' => $uploadedFile->getClientOriginalName(),
      'path' => $path, // Store the path returned by Storage::putFile
      'mime_type' => $uploadedFile->getMimeType(),
      'size' => $uploadedFile->getSize(),
    ]);

    $model->files()->save($file);

    return redirect()->back()->with('success', 'File uploaded successfully.');
  }

  /**
   * Remove the specified file from storage.
   */
  public function destroy(File $file) {
    $this->authorize('project.edit', Project::class);

    // Delete the file from storage
    Storage::delete($file->path);

    // Delete the file record from the database
    $file->delete();

    return redirect()->back()->with('success', 'File deleted successfully.');
  }

  private function findModelByNameAndUuid(string $modelName, string $uuid): ?Model {
    // Validate UUID
    if (!Str::isUuid($uuid)) {
      return null;
    }

    // Check if the model class exists
    if (!class_exists($modelName)) {
      return null;
    }

    // Ensure the class is a valid Eloquent model
    $modelInstance = app($modelName);
    if (!$modelInstance instanceof Model) {
      return null;
    }

    // Query the model by UUID
    return $modelInstance->newQuery()->where('uuid', $uuid)->first();
  }
}
