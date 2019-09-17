<template>
    <div class="row">
        <div class="col-md-3 online-users">
            <div class="side-bar nicescroll" id="user_sidebar">
                <h4 class="text-center">Users</h4>
                <hr />
                <div class="contact-list nicescroll">
                    <ul class="list-group contacts-list">
                        <li class="list-group-item"
                            :class="[activeFriend == friend.id ? 'active' : '']"
                            v-for="friend in friends" 
                            :color="((friend.id==activeFriend) ? 'green' : '')"
                            :key="friend.id"
                            @click="activeFriend=friend.id"
                        >
                            <a href="#">
                                <div class="avatar">
                                    <img src="/images/avatar.png" alt="">
                                </div>
                                <span class="name">{{friend.name}}</span>
                            </a>
                            <span class="clearfix"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9 messages">
            <div class="card" id="msgArea">
                <div class="card-header" style="min-height: 62px">
                    <a href="#" v-if="this.activeFriend">
                        <div class="avatar">
                            <img src="/images/avatar.png" width="42" class="rounded-circle" alt="">
                            <i class="fa fa-circle icon-online"></i>
                            <span class="name ml-2">{{this.activeFriendData[0].name}}</span>
                        </div>
                    </a>
                    <span class="clearfix"></span>
                </div>
                <div class="card-body" id="privateMessageBox">
                    <message-list :user="user" :all-messages="allMessages" v-if="this.activeFriend"></message-list>
                    <div class="text-center" v-if="!this.activeFriend">
                        <h2>Please start chat here !!!</h2>
                    </div>
                </div>
                <div id="chatbox-footer">
                    <img v-show="typing" ref="typing_indicator" src="/images/typing_indicator.gif" width="60" alt="">
                    <!-- <img ref="typing_indicator" src="/images/typing_indicator.gif" width="60" alt=""> -->
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <file-upload
                            :post-action="'/chat/message/'+activeFriend"
                            ref='upload'
                            v-model="files"
                            @input-file="$refs.upload.active = true"
                            :headers="{'X-CSRF-TOKEN': token}"
                        ><span class="icon-attach text-primary"><i class="fa fa-paperclip"></i></span></file-upload>
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="message" placeholder="Enter Message" @keydown="onTyping" @keyup.enter="sendMessage" />
                            <span class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-primary" @click="sendMessage">Send</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</template>

<script>
    import { setTimeout } from 'timers';
    import MessageList from './_message-list'
    export default {
        props: ['user'],
        components:{
            MessageList
        },
        data(){
            return {
                message: null,
                files: [],
                activeFriend: null,
                activeFriendData: {},
                typingFriend: {},
                onlineFriends: [],
                allMessages: [],
                typingClock: null,
                typing: false,
                emoStatus:false,
                users: [],
                token: document.head.querySelector('meta[name="csrf-token"]').content
            }
        },
        computed: {
            friends(){
                return this.users.filter((user) => {
                    return user.id != this.user.id;
                })
            }
        },
        watch:{
            files:{
                deep:true,
                handler(){
                    let success=this.files[0].success;
                    if(success){
                        this.fetchMessages();
                    }
                }
            },
            activeFriend(val){
                this.activeFirend = val
                this.activeFriendData = this.users.filter((user) => {
                    return user.id == this.activeFriend;
                })
                this.fetchMessages();
            },
            '$refs.upload'(val){
                console.log(val);
            }
        },
        methods: {
            onTyping(){
                Echo.private('chat.'+this.activeFriend).whisper('typing',{
                    user:this.user
                });
            },
            sendMessage(){
                if(!this.message){
                    return alert('Please enter message');
                }
                if(!this.activeFirend){
                    return alert('Please select friend');
                }
                axios.post('/chat/message/' + this.activeFirend, {message: this.message})
                    .then(response => {
                        this.message = null;
                        this.allMessages.push(response.data.message)
                        setTimeout(this.scrollToEnd, 50);
                    })
            },
            fetchMessages() {
                if(!this.activeFirend){
                    return alert('Please select friend');
                }
                axios.get('/chat/messages/' + this.activeFirend)
                    .then(response => {
                        this.allMessages = response.data;
                        setTimeout(this.scrollToEnd, 50);
                    })
            },
            fetchUsers() {
                axios.get('/users').then(response => {
                    this.users = response.data
                    if(this.friends.length > 0){
                        this.activeFirend = this.friends[0].id;
                    }
                })
            },
            scrollToEnd(){
                document.getElementById('privateMessageBox').scrollTo(0,99999);
            },
            toggleEmo(){
                this.emoStatus= !this.emoStatus;
            },
            onInput(e){
                if(!e){
                    return false;
                }
                if(!this.message){
                    this.message=e.native;
                }else{
                    this.message=this.message + e.native;
                }
                this.emoStatus=false;
            },
            onResponse(e){
                console.log('onrespnse file up',e);
            }
        },
        mounted() {
            $("#app").css('opacity', 1)
        },
        created() {            
            this.fetchUsers();
            Echo.join('plchat')
                .here((users) => {
                    this.onlineFriends=users;
                })
                .joining((user) => {
                    this.onlineFriends.push(user);
                    console.log('joining',user.name);
                })
                .leaving((user) => {
                    this.onlineFriends.splice(this.onlineFriends.indexOf(user),1);
                    console.log('leaving',user.name);
                });
                
            Echo.private('chat.'+this.user.id)
                .listen('MessageSent',(e)=>{
                    console.log('Message Sent')
                    this.activeFriend=e.message.user_id;
                    this.allMessages.push(e.message)
                    setTimeout(this.scrollToEnd,50);
                })
                .listenForWhisper('typing', (e) => {
                    if(e.user.id==this.activeFriend){
                        this.typing = true;
                        if(this.typingClock) clearTimeout();
                        this.typingClock=setTimeout(()=>{
                            this.typingFriend={};
                            this.typing = false;
                        },5000);
                    }                 
                });
        }
    }
</script>

<style scoped>
    #privateMessageBox {        
        overflow: auto;
        position: relative;
    }

    #msgArea {
        height: 73.6vh;
    }

    #user_sidebar {
        height: 73.6vh;
        position: relative;
    }

    .icon-attach {
        font-size: 25px;
        margin-right: 10px;
        cursor: pointer;
    }
    .card-header .avatar {
        position: relative;
    }
    .card-header .icon-online {
        color: #a0d269;
        position: absolute;
        bottom: 0;
        left: 31px;
    }
    .card-header .name {
        font-size: 18px;
        color: #444444;
        font-weight: 500;
    }
    #chatbox-footer {
        height: 15px;
        position: absolute;
        bottom: 75px;
        padding-left: 60px;
    }
</style>