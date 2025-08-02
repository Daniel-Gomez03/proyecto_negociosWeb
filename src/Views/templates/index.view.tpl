<section class="hero-panel">
    <img src="public/imgs/hero/hero_gunpla.png" alt="Modelos a escala en un taller de trabajo"
        class="hero-background-image">

    <div class="hero-content">
        <div class="hero-text">
            <h1 class="hero-title">
                Descubre el Mundo del
                <span class="hero-highlight"> Modelismo </span>
                y Coleccionables
            </h1>

            <p class="hero-description">
                Encuentra las mejores figuras, modelos y coleccionables para tu pasión.
            </p>

            <div class="hero-actions">
                <a href="{{catalogUrl}}" class="hero-btn primary">
                    <i class="fas fa-table"></i> Explorar Catálogo
                </a>
            </div>
        </div>
    </div>
</section>

<section class="product-section">
    <div class="section-header">
        <h2 class="section-title featured">Productos Destacados</h2>
    </div>

    <div class="product-list">
        {{foreach productsHighlighted}}
        <div class="product" data-productId="{{productId}}">
            <img src="{{productImgUrl}}" alt="{{productName}}">

            <div class="product-info">
                <h2>{{productName}}</h2>
                <p>{{productDescription}} </p>
                <p>{{productDetails}}</p>

                <span class="price">${{productPrice}}</span>
                <span class="stock">Disponibles: {{productStock}}</span>

                {{if enCarretilla}}
                    <div class="add-to-cart in-cart">
                        <i class="fa-solid fa-check"></i>
                        <span>En el Carrito</span>
                    </div>
                {{endif enCarretilla}}
                
                {{ifnot enCarretilla}}
                    {{if productStock}}
                        <form action="{{formAction}}" method="post">
                            <input type="hidden" name="productId" value="{{productId}}">
                            <button type="submit" name="addToCart" class="add-to-cart">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span>Agregar al Carrito</span>
                            </button>
                        </form>
                    {{endif productStock}}

                    {{ifnot productStock}}
                        <div class="add-to-cart in-cart">
                            <i class="fa-solid fa-times-circle"></i>
                            <span>Sin Stock</span>
                        </div>
                    {{endifnot productStock}}
                {{endifnot enCarretilla}}
            </div>
        </div>
        {{endfor productsHighlighted}}
    </div>
</section>

<section class="product-section">
    <div class="section-header">
        <h2 class="section-title new">Nuevos Productos</h2>
    </div>

    <div class="product-list">
        {{foreach productsNew}}
        <div class="product" data-productId="{{productId}}">
            <img src="{{productImgUrl}}" alt="{{productName}}">

            <div class="product-info">
                <h2>{{productName}}</h2>
                <p>{{productDescription}} </p>
                <p>{{productDetails}}</p>

                <span class="price">${{productPrice}}</span>
                <span class="stock">Disponibles: {{productStock}}</span>

                {{if enCarretilla}}
                    <div class="add-to-cart in-cart">
                        <i class="fa-solid fa-check"></i>
                        <span>En el Carrito</span>
                    </div>
                {{endif enCarretilla}}
                
                {{ifnot enCarretilla}}
                    {{if productStock}}
                        <form action="{{formAction}}" method="post">
                            <input type="hidden" name="productId" value="{{productId}}">
                            <button type="submit" name="addToCart" class="add-to-cart">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span>Agregar al Carrito</span>
                            </button>
                        </form>
                    {{endif productStock}}

                    {{ifnot productStock}}
                        <div class="add-to-cart in-cart">
                            <i class="fa-solid fa-times-circle"></i>
                            <span>Sin Stock</span>
                        </div>
                    {{endifnot productStock}}
                {{endifnot enCarretilla}}
            </div>
        </div>
        {{endfor productsNew}}
    </div>
</section>