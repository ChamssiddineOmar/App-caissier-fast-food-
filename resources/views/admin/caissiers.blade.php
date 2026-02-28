@extends('layouts.app')

@section('content')
<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-800 text-dark mb-0">👥 Gestion du Personnel</h2>
        <a href="{{ route('admin.produits') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
            <i class="fa-solid fa-arrow-left me-2"></i> Retour au Menu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
        <h5 class="fw-bold mb-3 text-primary">Ajouter un nouveau caissier</h5>
        <form action="{{ route('admin.caissiers.store') }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="text" name="nom" class="form-control rounded-pill px-4 border-0 bg-light shadow-none" placeholder="Nom complet (ex: Jean Dupont)..." required>
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                <i class="fa-solid fa-plus me-2"></i> Ajouter au système
            </button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <table class="table align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Nom de l'employé</th>
                    <th>État</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($caissiers as $c)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark">{{ $c->nom }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.caissiers.toggle', $c->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold {{ $c->actif ? 'btn-success' : 'btn-secondary' }}">
                                <i class="fa-solid {{ $c->actif ? 'fa-check' : 'fa-pause' }} me-1"></i>
                                {{ $c->actif ? 'Actif' : 'Désactivé' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <form action="{{ route('admin.caissiers.destroy', $c->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Attention : supprimer cet employé effacera son nom. Préfère la désactivation si possible. Continuer ?')">
                                <i class="fa-solid fa-trash me-1"></i> Effacer
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-users fa-3x mb-3 opacity-25"></i>
                        <p>Aucun caissier enregistré pour le moment.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection