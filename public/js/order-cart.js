window.addEventListener('showCartQty', event => {
    $('.cartCount').text(event.detail.qty);
});

window.addEventListener('openFormModalCart', event => {
    console.log(event);
    $('#cartModal').modal('show');
});

window.addEventListener('closeFormModalCart', event => {
    console.log(event);
    $('#cartModal').modal('hide')
});

