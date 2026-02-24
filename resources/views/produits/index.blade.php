@extends('layouts.app')

@section('content')
<style>
    .cat-container { display: flex; gap: 12px; margin-bottom: 30px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
    .cat-pill { background: white; border-radius: 20px; padding: 12px 22px; font-weight: 700; color: var(--light-text); cursor: pointer; border: none; box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.05); flex: 0 0 auto; transition: 0.3s; font-size: 14px; }
    .cat-pill.active { background: var(--primary); color: white; }
    .product-card { background: white; border-radius: 30px; padding: 20px; transition: 0.4s; cursor: pointer; text-align: center; box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); height: 100%; position: relative; border: 1px solid transparent; }
    .product-card:hover { transform: translateY(-10px); border-color: var(--primary); }
    .product-card img { width: 100%; height: 110px; object-fit: contain; margin-bottom: 15px; }
    .price-tag { background: #F4F7FE; color: var(--primary); border-radius: 12px; font-weight: 800; padding: 5px 15px; display: inline-block; }
    .btn-new { background: var(--dark-text); color: white; border-radius: 18px; padding: 12px 25px; font-weight: 700; border: none; transition: 0.3s; display: inline-flex; align-items: center; text-decoration: none; }
    .btn-new:hover { background: #000; transform: scale(1.05); color: white; }
    .btn-stats { background: var(--primary); color: white; } /* Couleur bleue pour les stats */
    .btn-circle { width: 35px; height: 35px; border-radius: 12px; border: none; display: flex; align-items: center; justify-content: center; z-index: 30; }
    .action-btns-wrapper { position: absolute; top: 15px; width: calc(100% - 30px); display: flex; justify-content: space-between; opacity: 0; transition: 0.3s; padding: 0 10px; }
    .product-card:hover .action-btns-wrapper { opacity: 1; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="cat-container">
        <button class="cat-pill active filter-btn" data-filter="all">✨ Tout</button>
        <button class="cat-pill filter-btn" data-filter="burgers">🍔 Burgers</button>
        <button class="cat-pill filter-btn" data-filter="pizza">🍕 Pizza</button>
        <button class="cat-pill filter-btn" data-filter="accompagnements">🍟 Accompagnements</button>
        <button class="cat-pill filter-btn" data-filter="tacos_chawarma">🌮 Tacos & Chawarma</button>
        <button class="cat-pill filter-btn" data-filter="boissons">🥤 Boissons</button>
        <button class="cat-pill filter-btn" data-filter="desserts">🍦 Desserts</button>
        <button class="cat-pill filter-btn" data-filter="menus">🍱 Menus combinés</button>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('stats.index') }}" class="btn-new btn-stats shadow-lg">
            <i class="fa-solid fa-chart-line me-2"></i>Stats
        </a>
        <button type="button" class="btn-new shadow-lg" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="fa-solid fa-plus me-2"></i>Nouveau
        </button>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4" id="grid-produits">
    @foreach($produits as $produit)
    <div class="col product-item" data-category="{{ $produit->categorie }}" data-name="{{ strtolower($produit->nom) }}">
        <div class="product-card">
            <div class="action-btns-wrapper">
                <button type="button" class="btn-circle" style="background:#e0e7ff; color:var(--primary)" onclick="ouvrirEditeur({{ json_encode($produit) }})">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <form action="{{ route('produits.destroy', $produit->id) }}" method="POST" onsubmit="return confirm('Supprimer ce produit ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-circle" style="background:#fee2e2; color:var(--danger)"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
            <div onclick="ajouterAuPanier({{ $produit->id }}, '{{ addslashes($produit->nom) }}', {{ $produit->prix }})">
                <img src="{{ $produit->image ? asset('storage/'.$produit->image) : 'https://cdn-icons-png.flaticon.com/512/1161/1161695.png' }}">
                <h5 class="fw-800 mb-1" style="font-size:15px">{{ $produit->nom }}</h5>
                <div class="price-tag">{{ number_format($produit->prix, 0, ',', ' ') }} F</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="modal fade" id="modalAjout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; padding: 20px;">
            <div class="modal-header border-0 pb-0"><h4 class="fw-800">Nouveau Produit</h4><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('produits.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="text" name="nom" class="form-control mb-3 border-0 bg-light p-3 rounded-4" placeholder="Nom du plat" required>
                    <input type="number" name="prix" class="form-control mb-3 border-0 bg-light p-3 rounded-4" placeholder="Prix" required>
                    <select name="categorie" class="form-select mb-3 border-0 bg-light p-3 rounded-4" required>
                        <option value="burgers">Burgers</option>
                        <option value="accompagnements">Accompagnements</option>
                        <option value="tacos_chawarma">Tacos & Chawarma</option>
                        <option value="boissons">Boissons</option>
                        <option value="pizza">Pizza</option>
                        <option value="desserts">Desserts</option>
                        <option value="menus">Menus combinés</option>
                    </select>
                    <input type="file" name="image" class="form-control border-0 bg-light p-3 rounded-4">
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn w-100 py-3 fw-bold" style="background:var(--success); color:white; border-radius:15px;">ENREGISTRER</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModifier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; padding: 20px;">
            <div class="modal-header border-0 pb-0"><h4 class="fw-800">Modifier Produit</h4><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formModifier" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="text" name="nom" id="edit_nom" class="form-control mb-3 border-0 bg-light p-3 rounded-4" required>
                    <input type="number" name="prix" id="edit_prix" class="form-control mb-3 border-0 bg-light p-3 rounded-4" required>
                    <select name="categorie" id="edit_cat" class="form-select mb-3 border-0 bg-light p-3 rounded-4" required>
                        <option value="burgers">Burgers</option>
                        <option value="accompagnements">Accompagnements</option>
                        <option value="tacos_chawarma">Tacos & Chawarma</option>
                        <option value="boissons">Boissons</option>
                        <option value="pizza">Pizza</option>
                        <option value="desserts">Desserts</option>
                        <option value="menus">Menus combinés</option>
                    </select>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius:15px;">METTRE À JOUR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Gestion du filtrage
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.dataset.filter;
            document.querySelectorAll('.product-item').forEach(item => {
                item.style.display = (filter === 'all' || item.dataset.category === filter) ? 'block' : 'none';
            });
        });
    });

    // Recherche dynamique
    const posSearch = document.getElementById('main-search');
    if(posSearch) {
        posSearch.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.dataset.name;
                item.style.display = name.includes(val) ? 'block' : 'none';
            });
        });
    }

    // Ouvrir l'éditeur
    function ouvrirEditeur(p) {
        document.getElementById('formModifier').action = "/produits/" + p.id;
        document.getElementById('edit_nom').value = p.nom;
        document.getElementById('edit_prix').value = p.prix;
        document.getElementById('edit_cat').value = p.categorie;
        var modal = new bootstrap.Modal(document.getElementById('modalModifier'));
        modal.show();
    }
</script>
@endsection