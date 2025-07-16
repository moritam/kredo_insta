@if ($post->trashed())
    {{-- Activate --}}
    <div class="modal fade" id="activate-post-{{ $post->id }}">
        <div class="modal-dialog">
            <div class="modal-content border-primary">
                <div class="modal-header border-primary">
                    <h3 class="h5 modal-title text-primary">
                        <i class="fa-solid fa-eye"></i> Unhide Post
                    </h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to Unhide Post <span class="fw-bold">{{ $post->id }}</span>?</p>
                    <div class="text-start">
                        <img src="{{ $post->image }}" alt="{{ $post->name }}"
                            class="d-block mx-auto avatar-lg shadow text-start">
                    </div>
                    <p>{{ $post->description }}</p>
                </div>

                <div class="modal-footer border-0">
                    <form action="{{ route('admin.posts.activate', $post->id) }}" method="post">
                        @csrf
                        @method('PATCH')

                        <button type="button" class="btn btn-outline-primary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Unhide Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- Deactivate --}}
    <div class="modal fade" id="deactivate-post-{{ $post->id }}">
        <div class="modal-dialog">
            <div class="modal-content border-danger">
                <div class="modal-header border-danger">
                    <h3 class="h5 modal-title text-danger">
                        <i class="fa-solid fa-eye"></i> Hide Post
                    </h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to hide Post <span class="fw-bold">{{ $post->id }}</span>?</p>
                    <div class="text-start">
                        <img src="{{ $post->image }}" alt="{{ $post->name }}"
                            class="d-block mx-auto avatar-lg shadow text-start">
                    </div>
                    <p>{{ $post->description }}</p>
                </div>
                <div class="modal-footer border-0">
                    <form action="{{ route('admin.posts.deactivate', $post->id) }}" method="post">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">Hide</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
