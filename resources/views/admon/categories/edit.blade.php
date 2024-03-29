<x-app-layout>
  <x-slot:header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ isset($category) ? __('Edit') : __('Create') }}
    </h2>
  </x-slot>

  <div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="py-1 px-4 text-gray-900">
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

          <form method="post" action="{{ route('categories.update', $category->id) }}" class="mt-6 space-y-6" enctype="multipart/form-data">
            @csrf @method('put')

            <div>
              <x-input-label for="name" :value="__('Name')" />
              <x-text-input type="text" id="name" name="name" class="block mt-1 w-full" :value="$category->name ?? old('name')" autofocus autocomplete="name" />
              <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            
            <div class="col-md-8 mb-4">
              <div class="dropzone" id="dropzone">
                @foreach ($featured_image as $item)
                  <img class="" src="{{ asset('categories/' . $item) }}" class="img-thumbnail" height="90px" width="150px" />
                @endforeach
              </div>
            </div>
            
            {{-- <img src="{{ isset($item) ? asset('/categories/'.$item->featured_image) : '' }}" class="w-10 h-10 rounded-lg" alt="{{ ($item->featured_image) }}" /> --}}

            <div class="py-2 bg-gray-50 text-center space-y-2">
              <x-primary-button class="ml-4">
                {{ __('Update') }}
              </x-primary-button>
              <x-blue-button class="ml-4">
                <a href="{{ route('categories.index') }}">{{ __('Cancel') }}</a>
              </x-blue-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
  @endpush

  @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    
    <script>
      let newimage = [];
      Dropzone.autoDiscover = false;

      let myDropzone = new Dropzone("#dropzone", {
        url: '{{ route('dropzone.store') }}',
        maxFilesize: 2, // MB
        maxFiles: 1,
        addRemoveLinks: true,
        acceptedFiles: 'image/*',
        parallelUploads: 1,
        uploadMultiple: true,
        paramName: 'featured_image', // Cambiar 'file' por 'featured_image'
        dictDefaultMessage: "<h3 class='sbold'>Suelte los archivos aquí o haga clic para cargar el documento<h3>",
        dictRemoveFile:'Quitar',
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },

        success: function(file, response) {
          console.log(file);
          newimage.push(response);
          console.log(newimage);
          $(".newimage").val(newimage);
          $(file.previewTemplate).find('.dz-filename span').data('dz-name', response);
          $(file.previewTemplate).find('.dz-filename span').html(response);
        },

        removedfile: function(file) {
          let removeimageName = $(file.previewElement).find('.dz-filename span').data('dz-name');

          $.ajax({
            type: 'POST',
            url: "{{ route('remove.file') }}",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
              removeimageName: removeimageName
            },
            success: function(data) {
              console.log(data);
              for (var i = 0; i < newimage.length; i++) {
                if (newimage[i] === data) {
                  newimage.splice(i, 1);
                }
              }
              $(".newimage").val(newimage);
            }
          });
          var _ref;
          return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },

        
        /* init: function() 
        {
            this.on("success", function(file, response)
            {
                var path = "storage/categories/" + response;
                $("#user-avatar").attr("src", path );
            });
        }, */
        
        
    init: function () {
      @if(isset($category) && $category->featured_image)
        var files =
          {!! json_encode($category->featured_image) !!}
        for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="featured_image[]" value="' + file.file_name + '">')
        }
      @endif
    }

        /* init: function() {
          if (document.querySelector('[name="featured_image"]').value.trim()) { // si hay algo
            const imagenPublicada = {}
            imagenPublicada.size = 1234;
            imagenPublicada.name = document.querySelector('[name="featured_image"]').value;


            this.options.addedfile.call(this, imagenPublicada);
            this.options.thumbnail.call(this, imagenPublicada, "/public/storage/categories/"+imagenPublicada.name);
            imagenPublicada.previewElement.classList.add("dz-success", "dz-complete");
          }
        } */

        /* init:function (){
          $.ajax({
            url: "{{ route('getCategoryImage', $category->id) }}",
            type: "get",
            datatype: 'json',
            success: function (data) {
              $.each(data,function (key,value) {
                let mockFile = {name:value.id,size:value.original_name};
                drop.emit("addedfile",mockFile);
                drop.emit("thumbnail",mockFile,value.path);
                drop.emit("complete",mockFile);
              })
            }
          });
        } */

        /* init: function() {
          @if (isset($category->featured_image))
            var files = {!! json_encode($featured_image) !!}
            for (var i in files) {
              var file = files[i]
              console.log(file);
              file = {
                ...file,
                width: 226,
                height: 324
              }
              this.options.addedfile.call(this, file)
              this.options.thumbnail.call(this, file, file.original_url)
              file.previewElement.classList.add('dz-complete')

              $('form').append('<input type="hidden" name="featured_image[]" value="' + file.file_name + '">')
            }
          @endif
        } */
      });
    </script>
  @endpush
</x-app-layout>
