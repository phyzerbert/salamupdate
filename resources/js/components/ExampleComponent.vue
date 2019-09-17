<template>
    <h1>Hello...</h1>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')    
            $("#app").css('opacity', 1);       
        }
    }
</script>
