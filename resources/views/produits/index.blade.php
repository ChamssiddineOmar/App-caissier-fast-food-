@extends('layouts.app')

@section('content')
<style>
    /* Catégories Premium */
    .cat-container { 
        display: flex; gap: 15px; margin-bottom: 40px; 
        overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; 
    }
    .cat-container::-webkit-scrollbar { display: none; }

    .cat-pill {
        background: white; border-radius: 20px; padding: 12px 25px;
        font-weight: 700; color: var(--light-text); cursor: pointer;
        transition: 0.3s; white-space: nowrap; border: none;
        box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.05);
        flex: 0 0 auto;
    }
    .cat-pill.active { background: var(--primary); color: white; box-shadow: 0px 20px 30px rgba(67, 24, 255, 0.2); }

    /* Card Produit */
    .product-card {
        background: white; border-radius: 30px; border: none;
        padding: 20px; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer; position: relative; text-align: center;
        box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
        height: 100%;
    }
    .product-card:hover { transform: translateY(-10px); box-shadow: 0px 30px 45px rgba(112, 144, 176, 0.2); }
    .product-card img { width: 100%; height: 140px; object-fit: contain; margin-bottom: 15px; }
    
    .price-tag {
        background: #F4F7FE; color: var(--primary);
        border-radius: 12px; font-weight: 800; padding: 5px 15px; display: inline-block;
    }

    /* Boutons d'action */
    .action-btns-wrapper {
        position: absolute; top: 15px; width: calc(100% - 30px);
        display: flex; justify-content: space-between; z-index: 10;
        opacity: 0; transition: 0.3s;
    }
    .product-card:hover .action-btns-wrapper { opacity: 1; }

    .btn-circle {
        width: 35px; height: 35px; border-radius: 12px; border: none;
        display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .btn-edit { background: #e0e7ff; color: var(--primary); }
    .btn-delete { background: #fee2e2; color: var(--danger); }
    .btn-circle:hover { transform: scale(1.1); color: white; }
    .btn-edit:hover { background: var(--primary); }
    .btn-delete:hover { background: var(--danger); }

    .btn-new {
        background: var(--dark-text); color: white; border-radius: 18px;
        padding: 12px 25px; font-weight: 700; border: none;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="cat-container">
        <button class="cat-pill active filter-btn" data-filter="all">✨ Tout</button>
        <button class="cat-pill filter-btn" data-filter="burgers">🍔 Burgers</button>
        <button class="cat-pill filter-btn" data-filter="pizzas">🍕 Pizzas</button>
        <button class="cat-pill filter-btn" data-filter="tacos_chawarma">🌯 Tacos & Chawarma</button>
        <button class="cat-pill filter-btn" data-filter="accompagnements">🍟 Accompagnements</button>
        <button class="cat-pill filter-btn" data-filter="boissons">🥤 Boissons</button>
        <button class="cat-pill filter-btn" data-filter="desserts">🍦 Desserts</button>
        <button class="cat-pill filter-btn" data-filter="menus">🍱 Menus</button>
    </div>
    <button class="btn-new shadow-lg" data-bs-toggle="modal" data-bs-target="#modalAjout">
        <i class="fa-solid fa-plus me-2"></i>Nouveau
    </button>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4" id="grid-produits">
    @foreach($produits as $produit)
    <div class="col product-item" data-category="{{ $produit->categorie }}" data-name="{{ strtolower($produit->nom) }}">
        <div class="product-card">
            <div class="action-btns-wrapper">
                <button type="button" class="btn-circle btn-edit" onclick="ouvrirEditeur({{ json_encode($produit) }})">
                    <i class="fa-solid fa-pen"></i>
                </button>
                
                <form action="{{ route('produits.destroy', $produit->id) }}" method="POST" onsubmit="return confirm('Supprimer ce délice ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-circle btn-delete"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>

            <div onclick="ajouterAuPanier({{ $produit->id }}, '{{ addslashes($produit->nom) }}', {{ $produit->prix }})">
                @if($produit->image)
                    <img src="{{ asset('storage/'.$produit->image) }}" alt="">
                @else
                    <div style="height:140px; display:flex; align-items:center; justify-content:center; background:#F4F7FE; border-radius:20px; margin-bottom:15px">
                        <i class="fa-solid fa-utensils fa-3x text-muted opacity-20"></i>
                    </div>
                @endif
                <h5 class="product-title fw-800 mb-2" style="font-size:16px">{{ $produit->nom }}</h5>
                <div class="price-tag">{{ number_format($produit->prix, 0, ',', ' ') }} F</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="modal fade" id="modalAjout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 30px; padding: 20px;">
            <div class="modal-header border-0 pb-0"><h4 class="fw-800">Nouveau Produit</h4><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('produits.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="text" name="nom" class="form-control mb-3 border-0 bg-light p-3 rounded-4" placeholder="Nom" required>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><input type="number" name="prix" class="form-control border-0 bg-light p-3 rounded-4" placeholder="Prix" required></div>
                        <div class="col-6">
                            <select name="categorie" class="form-select border-0 bg-light p-3 rounded-4" required>
                                <option value="burgers">Burgers</option>
                                <option value="pizzas">Pizzas</option>
                                <option value="tacos_chawarma">Tacos & Chawarma</option>
                                <option value="accompagnements">Accompagnements</option>
                                <option value="boissons">Boissons</option>
                                <option value="desserts">Desserts</option>
                                <option value="menus">Menus</option>
                            </select>
                        </div>
                    </div>
                    <input type="file" name="image" class="form-control border-0 bg-light p-3 rounded-4">
                </div>
                <div class="modal-footer border-0"><button type="submit" class="btn btn-pay py-3 w-100 fw-bold" style="background:var(--success); color:white; border-radius:15px; border:none;">ENREGISTRER</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModifier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 30px; padding: 20px;">
            <div class="modal-header border-0 pb-0"><h4 class="fw-800">Modifier l'article</h4><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formModifier" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="text" name="nom" id="edit_nom" class="form-control mb-3 border-0 bg-light p-3 rounded-4" required>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><input type="number" name="prix" id="edit_prix" class="form-control border-0 bg-light p-3 rounded-4" required></div>
                        <div class="col-6">
                            <select name="categorie" id="edit_categorie" class="form-select border-0 bg-light p-3 rounded-4" required>
                                <option value="burgers">Burgers</option>
                                <option value="pizzas">Pizzas</option>
                                <option value="tacos_chawarma">Tacos & Chawarma</option>
                                <option value="accompagnements">Accompagnements</option>
                                <option value="boissons">Boissons</option>
                                <option value="desserts">Desserts</option>
                                <option value="menus">Menus</option>
                            </select>
                        </div>
                    </div>
                    <input type="file" name="image" class="form-control border-0 bg-light p-3 rounded-4">
                </div>
                <div class="modal-footer border-0"><button type="submit" class="btn btn-pay py-3 w-100 fw-bold" style="background:var(--primary); color:white; border-radius:15px; border:none;">METTRE À JOUR</button></div>
            </form>
        </div>
    </div>
</div>

<script>
    // Filtrage par Catégorie
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

    // Recherche en temps réel
    const searchBar = document.querySelector('.search-bar');
    if(searchBar) {
        searchBar.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.dataset.name;
                item.style.display = name.includes(term) ? 'block' : 'none';
            });
        });
    }

    // Gestion de l'édition
    function ouvrirEditeur(produit) {
        const form = document.getElementById('formModifier');
        form.action = "{{ url('produits') }}/" + produit.id;

        document.getElementById('edit_nom').value = produit.nom;
        document.getElementById('edit_prix').value = produit.prix;
        document.getElementById('edit_categorie').value = produit.categorie;

        let editModal = new bootstrap.Modal(document.getElementById('modalModifier'));
        editModal.show();
    }
</script>
@endsection