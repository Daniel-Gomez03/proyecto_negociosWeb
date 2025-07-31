<!-- Hero Panel -->
<section class="hero-panel">
    <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    
    <div class="hero-content">
        <div class="hero-text">
            <span class="hero-badge">
                <i class="fa-solid fa-star"></i>
                Especialistas en Modelismo
            </span>
            
            <h1 class="hero-title">
                Descubre el Mundo del
                <span class="hero-highlight">Modelismo</span>
                y Coleccionables
            </h1>
            
            <p class="hero-description">
                Encuentra las mejores figuras, modelos y coleccionables para tu pasión. 
                Calidad premium, precios increíbles y envío rápido a toda Honduras.
            </p>
            
            <div class="hero-actions">
                <a href="index.php?page=Catalogo" class="hero-btn primary">
                    <i class="fa-solid fa-rocket"></i>
                    Explorar Catálogo
                </a>
            </div>
        </div>
    </div>
    
    <div class="hero-scroll">
        <span>Scroll para explorar</span>
        <i class="fa-solid fa-chevron-down"></i>
    </div>
</section>
<div class="product-list">
    <div class="section-header">
        <h2 class="section-title featured">
            <i class="fa-solid fa-star"></i>
            Productos Destacados
            <span class="section-badge">PREMIUM</span>
        </h2>
    </div>
    {{foreach productsHighlighted}}
    <div class="product" data-productId="{{productId}}">
        <img src="{{productImgUrl}}" alt="{{productName}}">
        <h2>{{productName}}</h2>
        <p>{{productDescription}}</p>
        <p>{{productDetails}}</p>
        
        <span class="price">{{productPrice}}</span>
        <span class="stock">Disponible {{productStock}}</span>
        <form action="index.php?page=index" method="post">
            <input type="hidden" name="productId" value="{{productId}}">
            <button type="submit" name="addToCart" class="add-to-cart">
                <i class="fa-solid fa-cart-plus"></i>Agregar al Carrito
            </button>
        </form>
    </div>
    {{endfor productsHighlighted}}
</div>

<div class="product-list">
    <div class="section-header">
        <h2 class="section-title new">
            <i class="fa-solid fa-sparkles"></i>
            Nuevos Productos
            <span class="section-badge">NUEVO</span>
        </h2>
    </div>
    {{foreach productsNew}}
    <div class="product" data-productId="{{productId}}">
        <img src="{{productImgUrl}}" alt="{{productName}}">
        <h2>{{productName}}</h2>
        <p>{{productDescription}}</p>
        <p>{{productDetails}}</p>
        
        <span class="price">{{productPrice}}</span>
        <span class="stock">Disponible {{productStock}}</span>
        <form action="index.php?page=index" method="post">
            <input type="hidden" name="productId" value="{{productId}}">
            <button type="submit" name="addToCart" class="add-to-cart">
                <i class="fa-solid fa-cart-plus"></i>Agregar al Carrito
            </button>
        </form>
    </div>
    {{endfor productsNew}}
</div>