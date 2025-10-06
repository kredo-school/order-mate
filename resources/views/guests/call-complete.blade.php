@extends('layouts.app')
@section('title', 'Call Complete')
@section('content')
<div class="container my-5 text-center page-center">
  <h2 class="text-brown mt-5">{{__('guest.call_success')}}</h2>

  {{-- Priority 表示 --}}
  <div class="text-center text-brown mt-4">
      <h4>{{__('guest.priority')}}: <span id="priority-display" class="fs-3 fw-bold">
        {{ $priority ?? '---' }}
    </span></h4>
      
  </div>

  <div class="mt-5">
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary btn-lg">{{__('guest.back_to_menu')}}</a>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const callId = @json($call->id); 
      const priorityElement = document.getElementById('priority-display');

      // ルートを Blade から生成
      const priorityUrl = "{{ route('guest.call.priority', [
          'storeName' => $storeName,
          'tableUuid' => $tableUuid,
          'call' => $call->id
      ]) }}";

      function fetchPriority() {
          fetch(priorityUrl)
              .then(response => response.json())
              .then(data => {
                  priorityElement.textContent = data.priority ?? '---';
              })
              .catch(() => {
                  priorityElement.textContent = '{{__('guest.acquisition_error')}}';
              });
      }

      setInterval(fetchPriority, 5000);
  });
</script>

@endpush
