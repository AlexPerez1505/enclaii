@extends('layouts.app')

@section('title', 'Checklists')
@section('titulo', 'Checklists')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-green: #d3f9d8;
        --accent-green: #38b000;
        --pastel-pink: #ffe0e9;
        --accent-pink: #d6336c;
        --pastel-gray: #f1f3f5;
    }
    body {
        background: var(--pastel-gray);
    }
    .main-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(100,100,100,.09);
        padding: 32px 24px 24px 24px;
        margin-bottom: 32px;
        margin-left: auto;
        margin-right: auto;
        max-width: 500px;
        animation: fadeIn .7s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px);}
        to { opacity: 1; transform: none;}
    }
    h1 {
        color: var(--accent-blue);
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 1.3rem;
        letter-spacing: -1px;
    }
    .detalle-label {
        color: var(--accent-blue);
        font-weight: 600;
        font-size: 1.11rem;
        margin-bottom: 0;
    }
    .detalle-value {
        color: var(--accent-gray);
        font-size: 1.09rem;
        margin-bottom: 1rem;
    }
    .botones-detalle {
        margin-top: 16px;
    }
    .btn-pastel-edit {
        background: var(--pastel-green);
        color: var(--accent-green);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 24px;
        font-size: 1.12rem;
        margin-right: 8px;
        box-shadow: 0 2px 8px rgba(56,176,0,0.06);
        transition: box-shadow .17s, filter .17s, transform .17s;
    }
    .btn-pastel-edit:focus,
    .btn-pastel-edit:hover {
        filter: brightness(1.06);
        box-shadow: 0 4px 16px rgba(56,176,0,0.16);
        transform: scale(1.06);
    }
    .btn-pastel-back {
        background: var(--pastel-pink);
        color: var(--accent-pink);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 22px;
        font-size: 1.12rem;
        transition: box-shadow .17s, filter .17s, transform .17s;
    }
    .btn-pastel-back:focus,
    .btn-pastel-back:hover {
        filter: brightness(1.06);
        box-shadow: 0 4px 14px rgba(214,51,108,0.15);
        transform: scale(1.06);
    }
</style>

<div class="container mt-4">
    <div class
