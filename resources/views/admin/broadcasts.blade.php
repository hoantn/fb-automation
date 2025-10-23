@extends('admin.layouts.master')
@section('title','Broadcasts')
@section('content')
@if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
<div class="row g-3">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Gửi broadcast</h5>
        <form method="post" action="/admin/broadcasts/send">
          @csrf
          <div class="mb-3">
            <label class="form-label">Chọn Page</label>
            <select class="form-select" name="page_id" required>
              @foreach($pages as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea class="form-control" name="content" rows="4" maxlength="1000" required placeholder="Nhập nội dung..."></textarea>
          </div>
          <button class="btn btn-dark">Gửi</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Lịch sử broadcast</h5>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>ID</th><th>Page</th><th>Status</th><th>Total</th><th>Sent</th><th>Failed</th><th>Time</th></tr></thead>
            <tbody>
              @foreach($items as $b)
                <tr>
                  <td>{{ $b->id }}</td>
                  <td>{{ $b->page_id }}</td>
                  <td>{{ $b->status }}</td>
                  <td>{{ $b->total }}</td>
                  <td>{{ $b->sent }}</td>
                  <td>{{ $b->failed }}</td>
                  <td>{{ $b->created_at }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {{ $items->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
