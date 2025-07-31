<h2 class="catalog-title">{{page_title}}</h2>
<div class="product-list">
    {{foreach products}}
    <div class="product" data-productId="{{productId}}">
        <img src="{{productImgUrl}}" alt="{{productName}}">
        <h2>{{productName}}</h2>
        <p>{{productDescription}}</p>
        <p>{{productDetails}}</p>
        
        <span class="price">${{productPrice}}</span>
        <span class="stock">Disponible {{productStock}}</span>
        
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
                <div class="add-to-cart in-cart" style="background-color: #6b7280; border-color: #6b7280; cursor: default;">
                    <i class="fa-solid fa-times-circle"></i>
                    <span>Sin Stock</span>
                </div>
            {{endifnot productStock}}

        {{endifnot enCarretilla}}
    </div>
    {{endfor products}}
</div>