@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <form action="{{ route('post.update', $post->id) }}" method="post" class="" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <div class="col-6">
                <label for="title" class="form-label fw-bolder">Category <span class="text-muted fw-normal">(up to
                        3)</span></label>
            </div>
            @foreach ($all_categories as $category)
                <div class="form-check form-check-inline">
                    @if (in_array($category->id, $selected_categories))
                        <input class="form-check-input" name="category[]" id="{{ $category->name }}" type="checkbox"
                            value="{{ $category->id }}" id="{{ $category->name }}"
                            {{ in_array($category->id, old('category', [])) ? 'checked' : '' }} checked>
                    @else
                        <input class="form-check-input" name="category[]" id="{{ $category->name }}" type="checkbox"
                            value="{{ $category->id }}" id="{{ $category->name }}"
                            {{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
                    @endif
                    <label class="form-check-label" for="{{ $category->name }}">{{ $category->name }}</label>
                </div>
            @endforeach

            {{-- Error --}}
            @error('category')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label fw-bolder">Description</label>
            <textarea name="description" id="description" class="form-control" cols="4" rows="3"
                placeholder="What's on your mind...">{{ old('description', $post->description) }}</textarea>
            {{-- Error --}}
            @error('description')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <label for="image" class="form-label fw-bolder">Image</label>
                <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="w-100 img-thumbnail">
                <input type="file" name="image" id="image" class="form-control" aria-describedby="image-info">
                <div class="form-text" id="image-info">
                    Acceptable formats are jpeg,jpg,png,gif only. <br>
                    Maximum file size is 1048kb.
                </div>
                {{-- Error --}}
                @error('image')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <button type="submit" class="btn btn-primary px-5">Save</button>
    </form>

@endsection
