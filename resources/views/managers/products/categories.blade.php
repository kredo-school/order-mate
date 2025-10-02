@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container">
  <div class="mb-3">
    <a href="{{url()->previous()}}" class="">
      <h3 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.category')}}</h3>
    </a>
  </div>

  <div class="mx-auto w-50">
    {{-- 新規カテゴリー追加フォーム --}}
    <form action="{{route('manager.categories.store')}}" method="post" class="">
      @csrf
      <div class="row">
        <div class="col-9">
          <input type="text" name="name" id="name" class="form-control" placeholder="{{__('manager.add_category')}}" autofocus required>
        </div>
        <div class="col-3">
          <button type="submit" class="btn btn-primary p-2 ms-2"><i class="fa-solid fa-plus"></i> {{__('manager.add')}}</button>
        </div>
      </div>
    </form>

    <table class="table table-hover mt-4">
      <thead>
        <tr>
          <th>{{__('manager.category_list')}}</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($all_categories as $category)
        <tr>
          <td>{{ $category->name }}</td>
          <td>
            {{-- 編集ボタン --}}
            <button type="button" class="btn-icon btn-icon-edit" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>

            {{-- 編集用モーダル --}}
            <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel{{ $category->id }}">{{__('manager.edit_category')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('manager.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-12">
                          <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline" data-bs-dismiss="modal">{{__('manager.cancel')}}</button>
                      <button type="submit" class="btn btn-cat btn-primary text-white">{{__('manager.save')}}</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </td>
          <td>
            <form action="{{route('manager.categories.destroy', $category->id)}}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-icon btn-icon-delete">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
