import $ from 'jquery';

$(function () {
    $('.status').on('click', (e) => {

        let arrStatus = ['new', 'in-progress', 'shipping', 'closed'];
        let arrBtnStatus = ['Take into work', 'Ship to customer', 'Close'];

        let buttonStatus = $(e.target).text().trim();
        let id = $(e.target).data('order-id');
        let fieldStatus = $(`[data-order-id=${id}][data-field='status']`).text();

        let newFieldStatus = arrStatus[arrStatus.findIndex(status => status === fieldStatus) + 1];
        let newBtnStatus = arrBtnStatus[arrBtnStatus.findIndex(status => status === buttonStatus) + 1];

        if (!newBtnStatus) {
            $(e.target).addClass('d-none');
        }

        let data = new FormData();
        data.append('status', newFieldStatus);

        fetch(`http://localhost:8083/admin/order/${id}/status`, {
            method: 'POST',
            body: data
        })
            .then((response) => {
                    $(`[data-order-id=${id}][data-field="status"]`).text(newFieldStatus)
                    $(`[data-order-id=${id}][data-button="status"]`).text(newBtnStatus)
                }
            )
            .catch((error) => {
                console.error('Error: ', error)
            })
    });

    $('.cancel').on('click', (e) => {

        let id = $(e.target).data('order-id');
        $(e.target).addClass('d-none');
        $(`[data-order-id=${id}][data-button="status"]`).addClass('d-none');

        let data = new FormData();
        data.append('status', 'canceled');

        fetch(`http://localhost:8083/admin/order/${id}/status`, {
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
    });
})