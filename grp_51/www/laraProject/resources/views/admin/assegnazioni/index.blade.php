php
@extends('layouts.admin')

@section('title', 'Gestione Assegnazioni Prodotti')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-person-gear me-2"></i>
                    Gestione Assegnazioni Prodotti
                </h1>
            </div>
            
            {{-- Statistiche --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4>{{ $statistiche['totale_prodotti'] }}</h4>
                            <p class="mb-0">Prodotti Totali</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h4>{{ $statistiche['prodotti_assegnati'] }}</h4>
                            <p class="mb-0">Prodotti Assegnati</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h4>{{ $statistiche['prodotti_non_assegnati'] }}</h4>
                            <p class="mb-0">Non Assegnati</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h4>{{ $statistiche['totale_staff'] }}</h4>
                            <p class="mb-0">Staff Membri</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista prodotti e controlli assegnazione --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Lista Prodotti e Assegnazioni</h5>
                    
                    {{-- Qui aggiungerai la tabella dei prodotti --}}
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Prodotto</th>
                                    <th>Categoria</th>
                                    <th>Staff Assegnato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prodotti as $prodotto)
                                <tr>
                                    <td>{{ $prodotto->nome }}</td>
                                    <td>{{ $prodotto->categoria }}</td>
                                    <td>
                                        @if($prodotto->staffAssegnato)
                                            <span class="badge bg-success">
                                                {{ $prodotto->staffAssegnato->nome_completo }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Non Assegnato</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            Gestisci
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nessun prodotto trovato</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $prodotti->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection