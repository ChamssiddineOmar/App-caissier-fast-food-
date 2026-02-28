@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ENTÊTE --}}
    <div class="d-flex justify-content-between align-items-start mb-5">
        <div class="admin-nav">
            <h2 class="fw-800 mb-3" style="color: var(--dark-text);">Tableau de Bord du Patron</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.stats') }}" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold text-primary border bg-white">
                    <i class="fa-solid fa-chart-line me-2"></i> Statistiques
                </a>
                <a href="{{ route('admin.produits') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-utensils me-2"></i> Gérer le Menu
                </a>
                <a href="{{ route('admin.caissiers') }}" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold text-info border bg-white">
                    <i class="fa-solid fa-users-gear me-2"></i> Gérer le Personnel
                </a>
                <a href="{{ route('accueil') }}" class="btn btn-outline-dark shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-cash-register me-2"></i> Interface Caisse
                </a>
            </div>
        </div>

        <button class="btn btn-dark rounded-4 px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="fa-solid fa-plus me-2"></i> Nouveau Produit
        </button>
    </div>

    {{-- RECHERCHE --}}
    <div class="row mb-4 g-4 align-items-center">
        <div class="col-md-8"> 
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-list me-2"></i>Liste des Produits</h5>
        </div>
        <div class="col-md-4">
            <div class="position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 15px; top: 12px; color: var(--light-text);"></i>
                <input type="text" id="adminSearch" class="form-control border-0 shadow-sm ps-5 py-2 rounded-pill" 
                       placeholder="Rechercher un plat..." onkeyup="filtrerAdmin()">
            </div>
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small uppercase">
                        <th class="ps-4 py-3">Produit</th>
                        <th>Catégorie</th>
                        <th>Prix Actuel</th>
                        <th>Disponibilité</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produits as $produit)
                    <tr class="{{ !$produit->en_stock ? 'bg-light opacity-75' : '' }}">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $produit->image ? asset('storage/'.$produit->image) : 'https://cdn-icons-png.flaticon.com/512/1161/1161695.png' }}" 
                                     class="rounded-3 me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                <span class="fw-700 text-dark">{{ $produit->nom }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark rounded-pill px-3">{{ ucfirst($produit->categorie) }}</span></td>
                        <td><span class="fw-800 text-primary">{{ number_format($produit->prix, 0, ',', ' ') }} F</span></td>
                        <td>
                            <form action="{{ route('admin.produits.stock', $produit->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold {{ $produit->en_stock ? 'btn-success' : 'btn-danger' }}">
                                    <i class="fa-solid {{ $produit->en_stock ? 'fa-check' : 'fa-xmark' }} me-1"></i>
                                    {{ $produit->en_stock ? 'En Stock' : 'Rupture' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-3 me-2 fw-bold" 
                                    onclick='ouvrirEditeur(@json($produit))'>
                                <i class="fa-solid fa-pen me-1"></i> Modifier
                            </button>
                            <form action="{{ route('produits.destroy', $produit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL AJOUT --}}
<div class="modal fade" id="modalAjout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('produits.store') }}" method="POST" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf
            <div class="modal-header border-0">
                <h5 class="fw-bold">Nouveau Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nom" class="form-control rounded-3 mb-3" placeholder="Nom du plat" required>
                <input type="number" name="prix" class="form-control rounded-3 mb-3" placeholder="Prix (F)" required>
                <select name="categorie" class="form-control rounded-3 mb-3" required>
                    <option value="burgers">🍔 Burgers</option>
                    <option value="pizza">🍕 Pizza</option>
                    <option value="accompagnements">🍟 Accompagnements</option>
                    <option value="tacos_chawarma">🌮 Tacos & Chawarma</option>
                    <option value="boissons">🥤 Boissons</option>
                    <option value="desserts">🍦 Desserts</option>
                </select>
                <label class="small text-muted mb-1">Image du produit</label>
                <input type="file" name="image" class="form-control rounded-3">
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL MODIFIER --}}
<div class="modal fade" id="modalModifier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formModifier" method="POST" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h5 class="fw-bold">Modifier le produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nom" id="edit_nom" class="form-control rounded-3 mb-3" required>
                <input type="number" name="prix" id="edit_prix" class="form-control rounded-3 mb-3" required>
                <select name="categorie" id="edit_cat" class="form-control rounded-3 mb-3" required>
                    <option value="burgers">🍔 Burgers</option>
                    <option value="pizza">🍕 Pizza</option>
                    <option value="accompagnements">🍟 Accompagnements</option>
                    <option value="tacos_chawarma">🌮 Tacos & Chawarma</option>
                    <option value="boissons">🥤 Boissons</option>
                    <option value="desserts">🍦 Desserts</option>
                </select>
                <input type="file" name="image" class="form-control rounded-3">
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filtrerAdmin() {
        let val = document.getElementById('adminSearch').value.toLowerCase();
        document.querySelectorAll('table tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    }

    function ouvrirEditeur(p) {
        const form = document.getElementById('formModifier');
        form.action = "/admin/produits/" + p.id;
        document.getElementById('edit_nom').value = p.nom;
        document.getElementById('edit_prix').value = p.prix;
        document.getElementById('edit_cat').value = p.categorie;
        var myModal = new bootstrap.Modal(document.getElementById('modalModifier'));
        myModal.show();
    }
</script>
@endsection