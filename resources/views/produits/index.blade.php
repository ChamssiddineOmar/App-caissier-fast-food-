@extends('layouts.app')

@section('content')
<style>
    .cat-container { display: flex; gap: 12px; margin-bottom: 30px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
    .cat-pill { background: white; border-radius: 20px; padding: 12px 22px; font-weight: 700; color: var(--light-text); cursor: pointer; border: none; box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.05); flex: 0 0 auto; transition: 0.3s; font-size: 14px; }
    .cat-pill.active { background: var(--primary); color: white; }
    
    .product-card { 
        background: white; 
        border-radius: 30px; 
        padding: 20px; 
        transition: 0.4s; 
        cursor: pointer; 
        text-align: center; 
        box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); 
        height: 100%; 
        position: relative; 
        border: 1px solid transparent; 
    }
    .product-card:hover:not(.out-of-stock) { transform: translateY(-10px); border-color: var(--primary); }
    
    .product-card.out-of-stock { 
        opacity: 0.6; 
        cursor: not-allowed; 
        filter: grayscale(0.5);
    }
    .badge-rupture {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #EE5D50;
        color: white;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .product-card img { width: 100%; height: 110px; object-fit: contain; margin-bottom: 15px; }
    .price-tag { background: #F4F7FE; color: var(--primary); border-radius: 12px; font-weight: 800; padding: 5px 15px; display: inline-block; }
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
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4" id="grid-produits">
    @foreach($produits as $produit)
    <div class="col product-item" data-category="{{ $produit->categorie }}" data-name="{{ strtolower($produit->nom) }}">
        <div class="product-card {{ !$produit->en_stock ? 'out-of-stock' : '' }}" 
             @if($produit->en_stock) onclick="ajouterAuPanier({{ $produit->id }}, '{{ addslashes($produit->nom) }}', {{ $produit->prix }})" @endif>
            
            @if(!$produit->en_stock)
                <span class="badge-rupture">Rupture</span>
            @endif

            <img src="{{ $produit->image ? asset('storage/'.$produit->image) : 'https://cdn-icons-png.flaticon.com/512/1161/1161695.png' }}">
            <h5 class="fw-800 mb-1" style="font-size:15px">{{ $produit->nom }}</h5>
            <div class="price-tag">{{ number_format($produit->prix, 0, ',', ' ') }} F</div>
        </div>
    </div>
    @endforeach
</div>

<script>
    // Filtrage catégories
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

    // Fonction de validation avant commande (Optionnel mais recommandé)
    function validerCommande() {
        const caissier = document.getElementById('caissier_select').value;
        if(!caissier) {
            alert("Veuillez sélectionner un caissier avant de valider la vente !");
            return false;
        }
        return true;
    }
</script>
@endsection