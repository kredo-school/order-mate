@extends('layouts.app')

@section('title', 'Customs')

@section('content')
<div class="container">
  <div class="">
    <a href="{{url()->previous()}}" class="">
      <h3 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Custom</h3>
    </a>
  </div>

  <div class="mx-auto">
    <div class="d-flex justify-content-end mx-5 form-underline">
      <input type="search" name="search_custom" id="search_custom" class="form-control">
    </div>

    <div class="mt-3">
      <div class="row">
        {{-- custom left side --}}
        <div class="col">
          <div class="card card-body mb-3">
            <form action="{{route('manager.custom.store')}}" method="post">
              @csrf
              {{-- custom title and add button --}}
              <div class="form-underline d-flex align-items-center">
                <input type="text" name="title" id="title" class="form-control me-2" placeholder="add title" style="flex: 1;" autofocus>
                <button type="submit" class="btn btn-primary text-white" id="add_custom_btn">
                  <i class="fa-solid fa-plus me-1"></i> Add
                </button>
              </div>
  
              {{-- custom option and custom price --}}
              <div id="custom-fields-wrapper">
                {{-- initial custom option and price input --}}
                <div class="form-underline my-3 d-flex align-items-center">
                  <input type="text" name="name[]" id="name" class="form-control me-2" placeholder="add option" style="flex: 1;">
                  <input type="number" name="extra_price[]" id="extra_price" class="form-control me-2" placeholder="price" style="width: 150px;">
                </div>
              </div>
  
              {{-- add input button --}}
              <button type="button" id="add-custom-field" class="btn-icon d-flex align-items-start"><i class="fa-solid fa-plus" style="color: #d9d3c8"></i></button>
            </form>

            <script>
              document.getElementById('add-custom-field').addEventListener('click', function () {
                  const wrapper = document.getElementById('custom-fields-wrapper');

                  // 新しいフィールドを作成（初期のものと全く同じクラス構造）
                  const newField = document.createElement('div');
                  newField.classList.add('form-underline', 'my-3', 'd-flex', 'align-items-center');

                  newField.innerHTML = `
                    <input type="text" name="name[]" class="form-control me-2" placeholder="add option" style="flex: 1;">
                    <input type="number" name="extra_price[]" class="form-control me-2" placeholder="price" style="width: 150px;">
                  `;

                  wrapper.appendChild(newField);
              });
              </script>

          </div>
        </div>

        {{-- custom right side --}}
        <div class="col">
          @foreach ($all_customGroups as $group)
            <div class="card card-body mb-2">
              {{-- タイトル部分 --}}
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ $group->title }}</h5>
                <div>
                  {{-- edit --}}
                  <button type="button" class="btn-icon btn-icon-edit" data-bs-toggle="modal" data-bs-target="#editGroupModal{{ $group->id }}">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>

                  {{-- edit modal --}}
                  <div class="modal fade" id="editGroupModal{{ $group->id }}" tabindex="-1" aria-labelledby="editGroupModalLabel{{ $group->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <form action="{{ route('manager.custom.update', $group->id) }}" method="POST">
                          @csrf
                          @method('PATCH')
                    
                          <div class="modal-header">
                            <h5 class="modal-title" id="editGroupModalLabel{{ $group->id }}">Edit Custom Group</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                    
                          <div class="modal-body">
                            {{-- Group title --}}
                            <div class="mb-3">
                              <label for="title{{ $group->id }}" class="form-label">Group Title</label>
                              <input type="text" name="title" id="title{{ $group->id }}" class="form-control" value="{{ $group->title }}" required>
                            </div>
                    
                            {{-- Options --}}
                            <div id="edit-custom-fields-wrapper-{{ $group->id }}">
                              @foreach($group->customOptions as $option)
                                <div class="d-flex mb-2 align-items-center price-option">
                                  <input type="hidden" name="option_ids[]" value="{{ $option->id }}">
                                  <input type="text" name="name[]" class="form-control me-2" value="{{ $option->name }}" required>
                                  <input type="number" name="extra_price[]" class="form-control me-2" value="{{ $option->extra_price }}">
                                  <button type="button" class="btn btn-danger btn-sm delete-row" data-id="{{ $option->id }}">
                                    <i class="fa-solid fa-xmark"></i>
                                  </button>
                                </div>
                              @endforeach
                            </div>
                    
                            {{-- Add option button --}}
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addOptionField({{ $group->id }})">
                              <i class="fa-solid fa-plus"></i> Add Option
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>


                  {{-- delete --}}
                  <form action="{{route('manager.custom.destroy', $group->id)}}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon btn-icon-delete">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </div>
              </div>

            {{-- オプション一覧 --}}
            @if($group->customOptions->count() > 0)
              <ul class="list-unstyled mb-0">
                @foreach ($group->customOptions as $option)
                  <li class="d-flex justify-content-between">
                    <span>{{ $option->name }}</span>
                    @if ($option->extra_price)
                      <span class="text-muted">
                          （{{ $option->extra_price > 0 ? '+' : ($option->extra_price < 0 ? '-' : '±') }}{{ number_format(abs($option->extra_price)) }}）
                      </span>
                    @endif
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-muted mb-0">No options added</p>
            @endif
            </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>
  <script>
    function addOptionField(groupId) {
      const wrapper = document.getElementById('edit-custom-fields-wrapper-' + groupId);
      const div = document.createElement('div');
      div.classList.add('d-flex', 'mb-2', 'align-items-center', 'price-option');
      div.innerHTML = `
        <input type="text" name="name[]" class="form-control me-2" placeholder="Option name" required>
        <input type="number" name="extra_price[]" class="form-control me-2" placeholder="Price">
        <button type="button" class="btn btn-danger btn-sm delete-row">
          <i class="fa-solid fa-xmark"></i>
        </button>
      `;
      wrapper.appendChild(div);
    }
    
    // 行削除イベント（モーダルは閉じない）
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.delete-row');
      if (btn) {
        e.preventDefault();
        const optionId = btn.getAttribute('data-id');
        if (optionId) {
          const form = btn.closest('form');
          const hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'delete_ids[]';
          hiddenInput.value = optionId;
          form.appendChild(hiddenInput);
        }
        btn.closest('.price-option').remove();
      }
    });

    // 行削除イベント（モーダルは閉じる）
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.delete-row');
      if (btn) {
        e.preventDefault();
        const optionId = btn.getAttribute('data-id');
        if (optionId) {
          const form = btn.closest('form');
          const hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'delete_ids[]';
          hiddenInput.value = optionId;
          form.appendChild(hiddenInput);
        }
        btn.closest('.price-option').remove();
      }
    });
  </script>
    
</div>
@endsection
