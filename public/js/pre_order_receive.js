var app = new Vue({
    el: '#app',

    data: {
        order_items: [],
        checked_items: [],
        filtered_items: [],
        products: [],
        total: {
            discount: 0,
            cost: 0
        },
        params: {
            id: $('#order_id').val()
        },
        grand_total: 0,
        keyword : '',
    },

    methods:{
        init() {
            axios.post('/get_pre_order',this.params)
                .then(response => {
                    for (let i = 0; i < response.data.items.length; i++) {
                        const element = response.data.items[i];
                        axios.post('/get_product', {id:element.product_id})
                            .then(response1 => {
                                axios.post('/get_received_quantity', {id:element.id})
                                    .then(response2 => {
                                        this.order_items.push({
                                            product_id: element.product_id,
                                            product_code: response1.data.code,
                                            product_name: response1.data.name,
                                            cost: element.cost,
                                            discount: element.discount,
                                            discount_string: element.discount_string,
                                            ordered_quantity: element.quantity,
                                            received_quantity: response2.data,
                                            balance: element.quantity - response2.data,
                                            receive_quantity: element.quantity - response2.data,
                                            sub_total: element.subtotal,
                                            item_id: element.id,
                                        })
                                        
                                    })
                                    .catch(error => {
                                        console.log(error);
                                    });
                            })
                            .catch(error => {
                                console.log(error);
                            });                    
                    }

                    Vue.nextTick(function() {
                        this.filtered_items = this.order_items
                    });
                })
                .catch(error => {
                    console.log(error);
                }); 
        },
        calc_subtotal() {
            data = this.order_items
            // console.log(data)
            let total_discount = 0;
            let total_cost = 0;

            for(let i = 0; i < data.length; i++) {
                if(this.checked_items.indexOf(data[i].item_id) == -1) continue;
                this.order_items[i].sub_total = (parseInt(data[i].cost) - parseInt(data[i].discount)) * data[i].receive_quantity
                total_discount += parseInt(data[i].discount) * data[i].receive_quantity
                total_cost += data[i].sub_total
            }
            this.total.discount = total_discount
            this.total.cost = total_cost
        },
        calc_grand_total() {
            this.grand_total = this.total.cost
        },
        formatPrice(value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        searchProduct() {
            const keyword = this.keyword;
            let data = this.order_items
            this.filtered_items = []
            for(let i = 0; i < data.length; i++) {
                if((data[i].product_name.indexOf(keyword) == -1) && (data[i].product_code.indexOf(keyword) == -1)) continue;
                this.filtered_items.push(data[i])
            }
        }
    },

    mounted:function() {
        this.init();
        $("#app").css('opacity', 1);
    },
    updated: function() {
        this.calc_subtotal()
        this.calc_grand_total()
    },
    created: function() {
        this.filtered_items = this.order_items
    }
});


