import CartItemRepository from "./repository";
import $ from "jquery";
import Toastify from 'toastify-js'
import "toastify-js/src/toastify.css"

export default class Cart {

    cart;
    repository;
    options;

    constructor(options = {
        cartSelector: '.cart',
        togglerSelector: '.cart-icon',
        totalPriceSelector: '.total-price',
        removeBtnSelector: '.cart-remove',
        addToCartBtnSelector: '.add-to-cart'
    }) {
        this.options = options;
        this.cart = $(options.cartSelector);
        this.repository = new CartItemRepository();

        $(options.togglerSelector).on('click', (e) => this.toggleWidget(e));

        $(document).on('click', (e) => this.toggleWidget(e))

        $(options.addToCartBtnSelector).on("click", (e) => {
            let $el = $(e.target);
            if($el.is('i')){
                $el = $el.parent('.btn');
            }

            if (User.isLoggedIn) {
                this.addProduct($el.data('storage'));
            } else {
                Toastify({
                    text: "Please login",
                    className: "bg-primary",
                    duration: 3000,
                    position: "center",
                    offset: {
                        y: 0
                    }
                }).showToast();

            }
        });

        this.updateContent();
    }


    updateTotalPrice() {

        const totalPrice = this.repository
            .getItemsAsArray()
            .map((product) => product.quantity * product.price)
            .reduce((acc, cartItemPrice) => acc + cartItemPrice, 0);

        $(this.options.totalPriceSelector).text(`${totalPrice}$`)
    }

    removeCartItem(event) {

        let $btn = $(event.target);
        if (!$btn.is(this.options.removeBtnSelector)) {
            $btn = $btn.closest(this.options.removeBtnSelector);
        }

        this.repository.removeItemByCode($btn.data('code'));
        this.updateContent();
    }

    clear() {
        this.repository.clear();
        this.updateContent();
    }

    onQuantityChange(event) {
        let input = event.target;
        if (isNaN(input.value) || input.value <= 0) {
            input.value = 1;
        }
        const productCode = $(event.target).data('code');

        this.repository.setItemQuantity(productCode, input.value);
        this.updateTotalPrice()
    }


    /**
     * @param {{name:string, code:string, price: Number, imageUrl: string}} product
     **/
    buildCartItem(product) {

        let imagePath = Config.defaultImagePath;
        if (product.imageUrl) {
            imagePath = Config.imagesBasePath + "/" + product.imageUrl;
        }

        return `<div class="cart-item" data-code="${product.code}">
                <img src="${imagePath}" alt="productImage" class="cart-image"> 
                <div class="detail-box"> 
                    <div class="cart-product-title">${product.name}</div>
                    <span class="cart-price">${product.price} x </span> 
                    <input type="number" data-code="${product.code}" value="${product.quantity}" class="cart-quantity ml-1"> 
                </div> 
                <a class="cart-remove" data-code="${product.code}">
                    <i class="gg-trash"></i>
                </a>
            </div>`;
    }


    toggleWidget(e) {
        const $el = $(e.target);

        let togglerSelector = this.options.togglerSelector;
        let isCartBtn = $el.is(togglerSelector) || $el.closest(togglerSelector).length;
        let isWithinCartWidget = $el.closest(this.options.cartSelector).length !== 0;

        if (this.cart.hasClass('active')) {
            if (isWithinCartWidget || $el.is('.cart-remove')) return;

            this.cart.removeClass("active");
            return false;
        } else {
            if (isCartBtn) {
                this.cart.addClass("active");
                return false;
            }
        }

    }

    updateContent() {
        let products = this.repository.getItemsAsArray();
        const $cartItems = this.cart.find('.cart-items');

        if (products.length === 0) {
            this.cart.find('.cart-content').hide();
            this.cart.find('.no-items').show();
            return;
        }
        this.cart.find('.no-items').hide();
        this.cart.find('.cart-content').show();


        $cartItems
            .empty() // make .cart-items empty
            .append(products.map(this.buildCartItem).join("\n")) // put all products as templates into .cart-items

        this.cart.find('.cart-remove').on('click', (e) => this.removeCartItem(e))
        this.cart.find('.cart-quantity').on('change', (e) => this.onQuantityChange(e))

        this.updateTotalPrice()
    }

    addProduct(product) {
        this.repository.addItem(product);
        this.updateContent();
        Toastify({
            text: "Product was added to the cart",
            className: "bg-primery",
            duration: 3000,
            position: "center",
            offset: {
                y: 0
            }
        }).showToast();
    }
}