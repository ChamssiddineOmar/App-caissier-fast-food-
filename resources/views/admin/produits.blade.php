@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="admin-nav">
            <h2 class="fw-800 mb-3" style="color: var(--dark-text);">Tableau de Bord du Patron</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.stats') }}" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold text-primary border bg-white">
                    <i class="fa-solid fa-chart-line me-2"></i> Statistiques
                </a>
                <a href="{{ route('admin.produits') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-utensils me-2"></i> Gérer le Menu
                </a>
            </div>
        </div>

        <button class="btn btn-dark rounded-4 px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="fa-solid fa-plus me-2"></i> Nouveau Produit
        </button>
    </div>

    <hr class="mb-5 opacity-25">

    <div class="row mb-4">
        <div class="col-md-5">
            <div class="position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 15px; top: 12px; color: var(--light-text);"></i>
                <input type="text" id="adminSearch" class="form-control border-0 shadow-sm ps-5 py-2 rounded-pill" 
                       placeholder="Rechercher un plat ou une catégorie..." onkeyup="filtrerAdmin()">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small uppercase">
                        <th class="ps-4 py-3">Produit</th>
                        <th>Catégorie</th>
                        <th>Prix Actuel</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produits as $produit)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $produit->image ? asset('storage/'.$produit->image) : 'https://cdn-icons-png.flaticon.com/512/1161/1161695.png' }}" 
                                     class="rounded-3 me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                <span class="fw-700 text-dark">{{ $produit->nom }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark rounded-pill px-3">{{ ucfirst($produit->categorie) }}</span></td>
                        <td><span class="fw-800 text-primary">{{ number_format($produit->prix, 0, ',', ' ') }} F</span></td>
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

<div class="modal fade" id="modalAjout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; padding: 20px;">
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-800">Nouveau Produit</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('produits.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="text" name="nom" class="form-control mb-3 border-0 bg-light p-3 rounded-4" placeholder="Nom du plat" required>
                    <input type="number" name="prix" class="form-control mb-3 border-0 bg-light p-3 rounded-4" placeholder="Prix" required>
                    <select name="categorie" class="form-select mb-3 border-0 bg-light p-3 rounded-4" required>
                        <option value="burgers">Burgers</option>
                        <option value="pizza">Pizza</option>
                        <option value="accompagnements">Accompagnements</option>
                        <option value="boissons">Boissons</option>
                    </select>
                    <input type="file" name="image" class="form-control border-0 bg-light p-3 rounded-4">
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="border-radius:15px;">AJOUTER AU MENU</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModifier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; padding: 20px;">
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-800">Modifier Produit</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModifier" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="text" name="nom" id="edit_nom" class="form-control mb-3 border-0 bg-light p-3 rounded-4" required>
                    <input type="number" name="prix" id="edit_prix" class="form-control mb-3 border-0 bg-light p-3 rounded-4" required>
                    <select name="categorie" id="edit_cat" class="form-select mb-3 border-0 bg-light p-3 rounded-4" required>
                        <option value="burgers">Burgers</option>
                        <option value="pizza">Pizza</option>
                        <option value="accompagnements">Accompagnements</option>
                        <option value="boissons">Boissons</option>
                    </select>
                    <input type="file" name="image" class="form-control border-0 bg-light p-3 rounded-4">
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm" style="border-radius:15px;">METTRE À JOUR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Recherche dynamique dans le tableau
    function filtrerAdmin() {
        let val = document.getElementById('adminSearch').value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    }

    // Fonction pour ouvrir le modal de modification avec les données
    function ouvrirEditeur(p) {
        const form = document.getElementById('formModifier');
        // Route Laravel : /admin/produits/{id}
        form.action = "/admin/produits/" + p.id;
        
        document.getElementById('edit_nom').value = p.nom;
        document.getElementById('edit_prix').value = p.prix;
        document.getElementById('edit_cat').value = p.categorie;
        
        let myModal = new bootstrap.Modal(document.getElementById('modalModifier'));
        myModal.show();
    }
</script>
@endsection