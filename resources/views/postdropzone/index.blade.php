<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Posts Dropzone') }}
    </h2>
  </x-slot>

  <div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if (session()->has('success'))
        <div class="bg-green-400 text-sm text-green-700 m-2 p-2">
          {{ session('success') }}
        </div>
      @endif
      @if (session()->has('danger'))
        <div class="bg-red-400 text-sm text-red-700 m-2 p-2">
          {{ session('danger') }}
        </div>
      @endif

      <form method="POST" action="{{ route('dropzone.store') }}" enctype="multipart/form-data">
        @csrf

        <div>
          <x-input-label for="title" :value="__('Título')" />
          <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" autofocus
            autocomplete="title" />
          <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <!-- Imagen -->
        <div class="mt-4">
          <div class="dropzone" id="dropzone"></div>
          <input type="hidden" readonly class="newimage" name="photo" value="">

          <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
          <x-primary-button class="ml-4">
            {{ __('Guardar Datos') }}
          </x-primary-button>
        </div>
      </form>
    </div>
  </div>

  {{-- Listado de Posts --}}
  <div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 text-gray-900">
          <table class="border-collapse table-auto w-full text-sm">
            <thead>
              <tr>
                <th class="border-b font-medium p-2 pl-8 pt-0 pb-3 text-slate-400 text-left">Title</th>
                <th class="border-b font-medium p-2 pl-8 pt-0 pb-3 text-slate-400 text-left">Created At</th>
                <th class="border-b font-medium p-2 pl-8 pt-0 pb-3 text-slate-400 text-left">Updated At</th>
                <th class="border-b font-medium p-2 pl-8 pt-0 pb-3 text-slate-400 text-left">Updated At</th>
                <th class="border-b font-medium p-2 pl-8 pt-0 pb-3 text-slate-400 text-left">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white">
              @foreach ($posts as $post)
                <tr>
                  <td
                    class="border-b border-slate-100 dark:border-slate-700 p-2 pl-8 text-slate-500 dark:text-slate-400">
                    {{ $post->title }}
                  </td>
                  <td
                    class="border-b border-slate-100 dark:border-slate-700 p-2 pl-8 text-slate-500 dark:text-slate-400">
                    {{ $post->created_at }}
                  </td>
                  <td
                    class="border-b border-slate-100 dark:border-slate-700 p-2 pl-8 text-slate-500 dark:text-slate-400">
                    {{ $post->updated_at }}
                  </td>
                  <td class="m-5 p-5 flex flex-row items-center">
                    <img class="h-10 w-10 rounded-lg" src="{{ Storage::disk('public')->url('posts/' . $post->photo) }}" alt="" title="{{ $post->title }}" />
                  </td>
                  <td
                    class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                    <a href="#"
                      class="border border-blue-500 hover:bg-blue-500 hover:text-white px-4 py-2 rounded-md">SHOW</a>
                    <a href="#"
                      class="border border-yellow-500 hover:bg-yellow-500 hover:text-white px-4 py-2 rounded-md">EDIT</a>
                    {{-- add delete button using form tag --}}
                    <form method="post" action="#" class="inline">
                      @csrf
                      @method('delete')
                      <button
                        class="border border-red-500 hover:bg-red-500 hover:text-white px-4 py-2 rounded-md">DELETE</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
  @endpush

  @push('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <script>
      var newimage = [];
      Dropzone.autoDiscover = false;
  
      var myDropzone = new Dropzone("#dropzone", {
        url: '{{ route('dropzone.dropzonestore') }}',
        // type='post',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        // url:"/dropzonestore",
        parallelUploads: 1,
        uploadMultiple: true,
        acceptedFiles: '.png,.jpg,.jpeg',
        addRemoveLinks: true,
        dictDefaultMessage: "<h3 class='sbold'>Suelte los archivos aquí o haga clic para cargar el documento(s)<h3>"
      });
    </script>
  @endpush
</x-guest-layout>