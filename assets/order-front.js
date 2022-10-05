import $ from "jquery";
import CartItemRepository from "./cart/repository";
import Toastify from "toastify-js";

$(function () {

    $('[name="order[paymentDetails][type]"]').on('change', function(e){
        if(e.target.value === 'cash') {
            $('#creditCardDetails').addClass('d-none');
            $('#creditCardDetails').find('input').attr('disabled', 'disabled');
        }else{
            $('#creditCardDetails').removeClass('d-none');
            $('#creditCardDetails').find('input').removeAttr('disabled');
        }
    });

    $('[name="order[paymentDetails][type]"]').filter((i, t) => t.checked).trigger('change');

    let itemsContainer = $('.items-list');
    let itemsCounter = $('.items-counter');
    let totalPriceSelector = $('.total-order');
    let items = new CartItemRepository().getItemsAsArray();

    let totalPrice = items.reduce((acc, item) =>
        acc + parseFloat(item['price']) * parseInt(item['quantity']), 0);

    totalPriceSelector.text('$' + totalPrice.toFixed(2));

    itemsContainer
        .empty()
        .append(items.map( (element, index) => buildItem(element, index)).join("\n"));

    itemsCounter.text(items.length);

    /**
     * @param {{name:string, code:string, price: Number, imageUrl: string, quantity:number}} product
     **/
    function buildItem(product, index) {

        return `<li class="list-group-item d-flex justify-content-between lh-condensed align-items-center">
                            <div>
                                <h6 class="my-0">${product.name}</h6>
                                <small class="text-muted">${product.code}</small>
                            </div>
                            <div>
                                <span class="border m-1 p-1 text-black-50">${product.quantity}</span>
                                <span class="m-1 mr-2 text-black-50">x</span>
                                <span class="text-muted">$${product.price}</span>
                            </div>
                            <input type="hidden" id="form_items_${index}_product_code" name="order[items][${index}][productCode]" value="${product.code}"/>
                            <input type="hidden" id="form_items_${index}_product_code" name="order[items][${index}][amount]" value="${product.quantity}"/>
                            <input type="hidden" id="form_items_${index}_product_code" name="order[items][${index}][price]" value="${product.price}"/>
                        </li>`;
    }
    $('.cancel').on('click', (e) => {

        let id = $(e.target).data('order-id');
        console.log(id)
        $(e.target).toggleClass('d-none');
        $('.cancel', `[data-order-id=${id}][data-button="status"]`).toggleClass('d-none');

        let data = new FormData();
        data.append('status', 'canceled');

        fetch(`http://localhost:8083/cabinet/${id}/status`, {
            method: 'POST',
            body: data
        })
            .then((response) => {
                    $(`[data-order-id=${id}][data-field="status"]`).text('canceled')
                }
            )
            .catch((error) => {
                console.error('Error: ', error)
            })
        Toastify({
            text: "Order was canceled",
            className: "bg-primary",
            duration: 3000,
            position: "center",
            offset: {
                y: 0
            }
        }).showToast();
    });
});
