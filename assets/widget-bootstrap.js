import Cart from "./cart/cart";
import $ from "jquery";

// document ready
$(function () {
    const cart = new Cart();

    document.addEventListener('cart.clear', () => cart.clear())

    EventsToDispatch.forEach(eventName => {
        console.log("Event dispatched -> "+ eventName);
        document.dispatchEvent(new Event(eventName));
    });
});
