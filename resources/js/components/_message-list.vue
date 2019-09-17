<template>
    <div>
        <div class="p-3" v-for="(message, index) in allMessages" :key="index">
            <div>
                <span class="small font-italic">{{message.user.name}}</span>
            </div>
            <div>
                <span>{{message.message}}</span>
            </div>
            <div v-if="message.attachment" class="image-container">
                <img v-if="message.is_image" width="100" :src="'/'+message.attachment" alt="">
                <a v-if="!message.is_image" width="100" :href="'/'+message.attachment" download="">Click Here</a>
            </div>
            <div>
                <span>{{message.created_at}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user', 'allMessages'],
    }
</script>

<style scoped>

</style>