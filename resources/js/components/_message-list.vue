<template>
    <div class="chat-conversation">
        <ul class="conversation-list nicescroll">
            <li class="clearfix" :class="[message.user.id==user.id ? 'odd' : '']" v-for="(message, index) in allMessages" :key="index" >
                <div class="chat-avatar">
                    <img src="/images/avatar.png" alt="male">
                </div>
                <div class="conversation-text">
                    <div class="ctext-wrap">
                        <p>{{message.message}}</p>
                        <div v-if="message.attachment" class="image-container">
                            <img v-if="message.is_image" width="100" :src="'/'+message.attachment" alt="">
                            <a v-if="!message.is_image" width="100" :href="'/'+message.attachment" download="">Click Here</a>
                        </div>
                    </div><br />
                    <i>{{message.created_at}}</i>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['user', 'allMessages'],
        created() {
            // console.log(this.user)
        }
    }
</script>


<style scoped>
</style>