@extends('layouts.app')

@section('title', 'Admin: Users')

@section('content')
    <form action="{{ route('admin.categories.store') }}" method="post">
        @csrf
        @method('POST')
        <div class="row">
            <div class="col-6">
                <input type="text" name="name" id="name" class="form-control" placeholder="Add a category">
            </div>
            <div class="col-3 text-start">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add</button>
            </div>
        </div>
    </form>
    <table class="table table-hover align-middle bg-white border text-secondary my-3">
        <thead class="small table-secondary text-secondary">
            <tr>
                <th></th>
                <th>NAME</th>
                <th>COUNT</th>
                <th>LAST UPDATED</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($all_categories as $category)
                {{ $category }}
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->categoryPost->count() }}</td>
                    <td>{{ $category->updated_at }}</td>
                    <td>
                        <button class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#edit-category-{{ $category->id }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#delete-category-{{ $category->id }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        </a>
                        {{-- Include modal here  --}}
                        @include('admin.categories.modals.status')
                    </td>
                </tr>
            @endforeach
            @if ($unrecognizedCount > 0)
                <tr>
                    <td></td>
                    <td>
                        <p>Uncategorized</p>
                        <p><span class="small text-muted">Hidden posts are not included</span></p>
                    </td>
                    <td>{{ $unrecognizedCount }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $all_categories->links() }}
    </div>
@endsection
