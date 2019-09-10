
var app = new Vue({
    el: '#app',

    data: {
        order_items: [],
        products: [],
        selected_product: '',
        total: {
            quantity: 0,
            price: 0
        },
        params: {
            type: $('#data').data('type'),
            id: $('#data').data('id')
        }
    },


    methods:{
        init() {
            axios.get('/get_products')
                .then(response => {
                    this.products = response.data;
                })
                .catch(error => {
                    console.log(error);
                });            
        },
        get_product(i) {
            const data = new FormData();
            data.append('id', this.order_items[i].product_id);

            axios.post('/get_product', data)
                .then(response => {
                    let tax_name = (response.data.tax) ? response.data.tax.name : ''
                    let tax_rate = (response.data.tax) ? response.data.tax.rate : 0
                    this.order_items[i].price = response.data.price
                    this.order_items[i].tax_name = tax_name
                    this.order_items[i].tax_rate = tax_rate
                    this.order_items[i].quantity = 1
                    this.order_items[i].sub_total = response.data.price + (response.data.price*response.data.tax.rate)/100
                })
                .catch(error => {
                    console.log(error);
                });
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
        console.log(this.params)
        axios.post('/get_orders', this.params)
            .then(response => {
                // console.log(response.data)
                for (let i = 0; i < response.data.length; i++) {
                    const element = response.data[i];
                    axios.post('/get_product', {id:element.product_id})
                    .then(response1 => {
                        let tax_name = (response1.data.tax) ? response1.data.tax.name : ''
                        let tax_rate = (response1.data.tax) ? response1.data.tax.rate : 0
                        this.order_items.push({
                            product_id: element.product_id,
                            price: response1.data.price,
                            tax_name: tax_name,
                            tax_rate: tax_rate,
                            quantity: element.quantity,
                            sub_total: element.subtotal,
                            order_id: element.id,
                        })
                    })
                    .catch(error => {
                        console.log(error);
                    });                    
                }
            })
            .catch(error => {
                console.log(error);
            });
        
        $("#app").css('opacity', 1);            
    },
    updated: function() {
        this.calc_subtotal()
        // $(".product").select2();
    }
});
