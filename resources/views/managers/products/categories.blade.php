@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container">
  <div class="">
    <a href="{{url()->previous()}}" class="">
      <h3 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Category</h3>
    </a>
  </div>

  <div class="mx-auto w-50">
    <form action="{{route('manager.categories.store')}}" method="post" class="">
      @csrf
      <div class="row">
        <div class="col-9">
          <input type="text" name="name" id="name" class="form-control" placeholder="Category Name" required>
        </div>
        <div class="col-3">
          <button type="submit" class="btn btn-cat btn-primary text-white ms-2"><i class="fa-solid fa-plus"></i>Add</button>
        </div>
      </div>
    </form>

    <table class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      {{-- foreachでtbody --}}
      <tbody>
        @foreach($all_categories as $category)
        <tr>
          <td>{{ $category->name }}</td>
          <td>
            <a href="" class="btn-icon btn-icon-edit">
              <i class="fa-solid fa-pen-to-square"></i>
            </a>
          </td>
          <td>
            <form action="" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-icon btn-icon-delete">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        @endforeach
    </table>
  </div>
</div>
@endsection