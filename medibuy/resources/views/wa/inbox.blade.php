@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
  body { font-family:'Inter', sans-serif; background:#f5f7fa; }
  .inbox-wrap{
    max-width:900px; margin:40px auto; 
  }
  .inbox-card{
    background:#fff; border-radius:18px; overflow:hidden;
    box-shadow:0 6px 20px rgba(0,0,0,.06);
  }
  .inbox-card h3{
    padding:18px 22px; margin:0; font-size:1.2rem; font-weight:600;
    border-bottom:1px solid #eee; background:#fafbfc;
  }
  .inbox-list a{
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 20px; text-decoration:none; color:#333;
    border-bottom:1px solid #f1f1f1; transition:.2s;
  }
  .inbox-list a:hover{ background:#f9fafb; }
  .inbox-list .info{ flex:1; min-width:0; }
  .inbox-list .name{ font-weight:500; font-size:.95rem; }
  .inbox-list .preview{ font-size:.82rem; color:#777; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .inbox-list .time{ font-size:.75rem; color:#999; margin-left:12px; white-space:nowrap; }
  .inbox-list .avatar{
    width:46px; height:46px; border-radius:50%; background:#e3e9f0;
    display:flex; align-items:center; justify-content:center;
    font-weight:600; font-size:.9rem; color:#555; margin-right:14px;
    flex-shrink:0;
  }
</style>

<div class="inbox-wrap">
  <div class="inbox-card">
    <h3>Bandeja de entrada</h3>
    <div class="inbox-list">
      @forelse($threads as $t)
        <a href="{{ route('wa.chat', $t->from) }}">
          <div class="avatar">{{ substr($t->from, -2) }}</div>
          <div class="info">
            <div class="name">{{ $t->from }}</div>
            <div class="preview">{{ $t->last_in_text ?? '—' }}</div>
          </div>
          <div class="time">{{ \Carbon\Carbon::parse($t->last_at)->diffForHumans() }}</div>
        </a>
      @empty
        <div class="p-4 text-muted">Aún no hay conversaciones.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
