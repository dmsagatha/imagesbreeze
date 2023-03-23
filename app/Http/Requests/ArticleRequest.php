<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    if (request()->routeIs('articles.store'))
    {
      $name  = 'required|min:3|unique:articles';
    } else {
      $name  = ['required', 'min:3', Rule::unique('articles')->ignore($this->route('article'))];
    }

    return [
      'name'        => $name,
      'description' => 'required',
      'image'       => 'required',
    ];
  }
}