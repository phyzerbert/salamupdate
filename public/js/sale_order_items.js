
var app = new Vue({
    el: '#app',

    data: {
        order_items: [],
        products: [],
        selected_product: '',
        total: {
            quantity: 0,
            price: 0
        }
    },


    methods:{
        init() {
            // axios.get('/get_products')
            //     .then(response => {
            //         this.products = response.data;
            //     })
            //     .catch(error => {
            //         console.log(error);
            //     });
        },
        add_item() {
            this.order_items.push({
                product_id: "",
                price: 0,
                tax_name: "",
                tax_rate: 0,
                quantity: 0,
                sub_total: 0,
            })
        },
        calc_subtotal() {
            data = this.order_items
            let total_quantity = 0;
            let total_price = 0;
            for(let i = 0; i < data.length; i++) {
                this.order_items[i].sub_total = (parseInt(data[i].price) + (data[i].price*data[i].tax_rate)/100) * data[i].quantity
                total_quantity += parseInt(data[i].quantity)
                total_price += data[i].sub_total
            }

            this.total.quantity = total_quantity
            this.total.price = total_price
        },
        remove(i) {
            this.order_items.splice(i, 1)
        }
    },
    filters: {
        currency: function (value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },

    mounted:function() {
        this.init();
        // this.add_item();
        $("#app").css('opacity', 1);
    },
    updated: function() {
        this.calc_subtotal()
        $(".product").autocomplete({
            source : function( request, response ) {
                axios.post('/get_autocomplete_products', { keyword : request.term })
                    .then(resp => {
                        // response(resp.data);
                        response(
                            $.map(resp.data, function(item) {
                                return {
                                    label: item.name + "(" + item.code + ")",
                                    value: item.name + "(" + item.code + ")",
                                    id: item.id,
                                    price: item.price,
                                    tax_name: item.tax.name,
                                    tax_rate: item.tax.rate,
                                }
                            })
                        );
                    })
                    .catch(error => {
                        console.log(error);
                    }
                );
            }, 
            minLength: 1,
            select: function( event, ui ) {
                let index = $(".product").index($(this));
                app.order_items[index].product_id = ui.item.id
                app.order_items[index].price = ui.item.price
                app.order_items[index].tax_name = ui.item.tax_name
                app.order_items[index].tax_rate = ui.item.tax_rate
                app.order_items[index].quantity = 1
                app.order_items[index].sub_total = ui.item.price + (ui.item.price*ui.item.tax_rate)/100
            }
        });
    }
});
