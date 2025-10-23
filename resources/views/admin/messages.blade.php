@extends('admin.layouts.master')
@section('title','Messages')
@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="card-title">Messages</h5>
    <form class="row g-2 mb-3">
      <div class="col-auto">
        <select class="form-select" name="page_id" onchange="this.form.submit()">
          <option value="">-- All pages --</option>
          @foreach($pages as $p)
            <option value="{{ $p->id }}" @if($pageId==$p->id) selected @endif>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
    </form>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead><tr><th>ID</th><th>Page</th><th>Customer</th><th>Dir</th><th>Text</th><th>Status</th><th>Time</th></tr></thead>
        <tbody>
          @foreach($messages as $m)
          <tr>
            <td>{{ $m->id }}</td>
            <td>{{ $m->page_id }}</td>
            <td>{{ $m->customer?->name ?? ('PSID '.$m->customer?->psid) }}</td>
            <td>{{ $m->direction }}</td>
            <td>{{ Str::limit($m->text, 80) }}</td>
            <td>{{ $m->status }}</td>
            <td>{{ $m->created_at }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $messages->withQueryString()->links() }}
  </div>
</div>
@endsection
