@extends('admin.layouts.master')
@section('title','Pages')
@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="card-title">Pages</h5>
    <div class="table-responsive">
      <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Meta Page ID</th><th>Created</th></tr></thead>
        <tbody>
          @foreach($pages as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->meta_page_id }}</td>
            <td>{{ $p->created_at }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
