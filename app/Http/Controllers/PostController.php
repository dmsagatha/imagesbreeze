<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
  public function index(): Response
  {
    $posts = Post::all();

    return response()->view('posts.index', [
      'posts' => Post::orderBy('title')->get()
    ]);
  }

  /* public function create(): Response
  {
    return response()->view('posts.form');
  } */
  
  public function store(Request $request): RedirectResponse
  {
    $validator = Validator::make($request->all(), [
      'title' => ['required', 'unique:' . Post::class]
    ]);

    // FilePond
    $temporaryFile = TemporaryFile::where('folder', $request->photo)->first();

    if ($validator->fails() && $temporaryFile) {
      Storage::deleteDirectory('posts/tmp/' . $temporaryFile->folder);
      $temporaryFile->delete();

      return to_route('posts.index')->withErrors($validator)->withInput();
    } elseif ($validator->fails()) {
      return to_route('posts.index')->withErrors($validator)->withInput();
    }
    
    if ($temporaryFile) {
      Storage::copy('posts/tmp/' . $temporaryFile->folder . '/' . $temporaryFile->filename, 'posts/' . $temporaryFile->folder . '/' . $temporaryFile->filename);

      Post::create([
        'title' => $request->title,
        'photo' => $temporaryFile->folder . '/' . $temporaryFile->filename
      ]);
      
      // Eliminar directorio y archivo temporal
      // File::deleteDirectory(storage_path('app/public/posts/tmp/' . $request->photo));
      Storage::deleteDirectory('posts/tmp/' . $temporaryFile->folder);

      // Eliminar el archivo temporal del modelo asociado
      $temporaryFile->delete();

      return to_route('posts.index')->with('success', 'Post creado');
    }

    return to_route('posts.index')->with('danger', 'Por favor subir un archivo');
  }
  
  public function tmpUplaod(Request $request)
  {
    if ($request->hasFile('photo')) {
      $file = $request->file('photo');
      $filename = $file->getclientOriginalName();
      // $folder = uniqid() . '-' . now()->timestamp;
      $folder = uniqid('post', true);
      $file->storeAs('posts/tmp/' . $folder, $filename);

      TemporaryFile::create([
        'folder'   => $folder,
        'filename' => $filename
      ]);

      return $folder;
    }

    return '';
  }

  public function tmpDelete()
  {
    $temporaryFile = TemporaryFile::where('folder', request()->getContent())->first();
    
    if ($temporaryFile) {
      Storage::deleteDirectory('posts/tmp/' . $temporaryFile->folder);
      $temporaryFile->delete();

      return response('');
    }
  }

  /* public function show(string $id): Response
  {
    return response()->view('posts.show', [
      'post' => Post::findOrFail($id)
    ]);
  }

  public function edit(string $id): Response
  {
    return response()->view('posts.form', [
      'post' => Post::findOrFail($id),
    ]);
  }
  public function edit(Post $post): View
  {
    return view('posts.edit', compact('post'));
  }
  
  public function destroy(string $id): RedirectResponse
  {
    $post = Post::findOrFail($id);

    $currentImage = str_replace('/storage', '/public', $post->featured_image);
    Storage::delete($currentImage);

    $delete = $post->delete($id);

    if($delete) {
      session()->flash('notif.success', 'Post deleted successfully!');
      return redirect()->route('posts.index');
    }

    return abort(500);
  } */
}