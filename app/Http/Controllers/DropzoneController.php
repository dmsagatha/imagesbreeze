<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DropzoneController extends Controller
{
  public function index(): Response
  {
    return response()->view('postdropzone.index', [
      'posts' => Post::latest()->get()
    ]);
  }
  
  public function create(): Response
  {
  }
  
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'title' => ['required', 'unique:posts']
    ]);

    $post = new Post();
    $post->title = $request['title'];
    $post->photo = $request['photo'];
    $post->save();

    return to_route('dropzone.index')->with('success', 'Post creado');
  }
  
  public function dropzonestore(Request $request)
  {
    $image = $request->file('file');

    foreach ($image as $images) {
      $imagename = uniqid() . "." . $images->getClientOriginalExtension();
      $images->move(public_path('posts'), $imagename);
    }

    return $imagename;
  }
  
  public function show(Post $post): Response
  {
  }
  
  public function edit(Post $post): Response
  {
  }
  
  public function update(Request $request, Post $post): RedirectResponse
  {
  }
  
  public function destroy(Post $post): RedirectResponse
  {
  }
}