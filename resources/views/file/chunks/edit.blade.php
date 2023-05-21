@extends('layouts.app')

@section('css')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Core Css Quill editor -->
    <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        .ql-editor {
            max-height: 50vh;
            overflow: auto;
        }
    </style>

@endsection

@section('content')

<a class="btn btn-primary mb-4" href="{{ route('web::library::index') }}" title="Back button">Back</a>
<div class="mb-3 mb-lg-5">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Edit file by chunks</h5>
        </div>
        <div class="card-body">
            <form id="chunks-form" method="POST" class="needs-validation" action="{{ route('web::file::chunks::update', ['library_file' => $libraryFile->id])  }}" novalidate>
                @csrf
                @method('put')

                <div id="quill-area">
                    @foreach($chunks['html'] as $key => $chunk)
                        <div class="d-flex">
                            <div class="d-flex flex-column w-100">
                                <div class="d-flex flex-column">
                                    <!--Quill editor-->
                                    <div data-quill='{"placeholder": "Quill WYSIWYG"}'>
                                        <p>
                                            {!! $chunk !!}
                                        </p>
                                    </div>
                                    <textarea name="chunks[{{$key}}]" style="display:none" id="hidden-chunk-{{$key}}"></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="w-100 text-center">
                                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$key}}" aria-expanded="false" aria-controls="collapse{{$key}}">
                                            Show more
                                        </button>
                                        <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            Join chunks
                                        </button>
                                    </div>
                                    <div class="collapse" id="collapse{{$key}}">
                                        <div class="card card-body rounded-0">
                                            @if(!empty($chunks['texts'][$key]))
                                                <div >{!! $chunks['texts'][$key] !!}</div>
                                            @endif
{{--                                            Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start pt-3 pl-3">
                                <input name="chunked_list[{{$key}}]" type="checkbox" value="{{$key}}"
                                   @if(!empty($libraryFile->chunked_list))
                                       @if( isset($libraryFile->chunked_list[$key])) checked @endif
                                   @else
                                       checked
                                   @endif
                                   id="checkChunk-{{$key}}"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>
                <input class="btn btn-success" type="submit" value="Save" />
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title h4" id="approveModalLabel">Join chunks</h1>
                <button type="button" class="btn btn-link text-secondary p-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x h3"></i>
                </button>
            </div>
            <div class="modal-body">
                Do you really want to join chunks?
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Join</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

    <!-- Include the Quill library -->
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    var initQuill = document.querySelectorAll("[data-quill]");
    initQuill.forEach((qe) => {
        const qt = {
            ...(qe.dataset.quill ? JSON.parse(qe.dataset.quill) : {}),
            modules: {
                toolbar: [
                    [{ 'background': ["transparent", "#e60000", "#008a00"] }]
                ]
            },
            theme: "snow"
        };
        new Quill(qe, qt);
    });

    $("#chunks-form").on("submit",function(event) {
        $chunks = $('#quill-area .ql-editor');
        $.each($chunks, function (index, value) {
            $("#hidden-chunk-" + index).html($(value).html());
        });
    })

    const triggerTabList = document.querySelectorAll('#myTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)

        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>

<script>
    @if (session('status') == 'file-updated-successfully')
    Swal.fire({
        title: 'Success!',
        text: 'Library file was successfully saved! {!! (!empty(session('message')) ? session('message') : '') !!}',
        icon: 'success',
        confirmButtonText: 'OK',
        timer: 2000
    });
    @elseif (session('status') == 'file-updated-error')
    Swal.fire({
        title: 'Error!',
        text: 'The library file was unable to be saved due to an error. {{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
    @endif
</script>

@endsection
