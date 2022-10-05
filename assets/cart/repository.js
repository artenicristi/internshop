export default class CartItemRepository {

    getItems() {
        return JSON.parse(localStorage.getItem("products") || "{}");
    }

    getItemsAsArray() {
        return Object.values(this.getItems());
    }

    saveItems(products) {
        window.localStorage.setItem("products", JSON.stringify(products));
    }

    setItemQuantity(code, quantity) {
        let products = this.getItems();
        products[code].quantity = quantity;
        this.saveItems(products);
    }

    removeItemByCode(code) {
        let products = this.getItems();
        delete products[code];

        this.saveItems(products);
    }

    clear() {
        this.saveItems({});
    }

    addItem(product) {
        let products = this.getItems();
        if (products[product.code]) {
            products[product.code].quantity++;
            this.saveItems(products);

            return;
        }

        products[product.code] = product;
        products[product.code].quantity = 1;
        this.saveItems(products);
    }
}