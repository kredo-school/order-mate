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
          <div class="card card-body">
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
          <div class="card card-body">
            <div class="">
              <h5></h5>
            </div>
            <div class="">
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
