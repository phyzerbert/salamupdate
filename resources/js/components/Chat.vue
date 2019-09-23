<template>
    <div id="chatbox">  
        <div class="side-bar right-bar nicescroll">
            <h4 class="text-center"><i class="fa fa-wechat"></i> Usuarios</h4>                    
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
                                <i class="fa fa-circle online" v-if="is_connected(friend.id)"></i>
                                <i class="fa fa-circle offline" v-else></i>
                            </div>
                            <span class="name">{{friend.name}}</span>
                        </a>
                        <span class="badge badge-pill badge-xs badge-danger float-right" style="margin-top: 10px" v-if="friend.unread_messages > 0">{{friend.unread_messages}}</span>
                        <span class="clearfix"></span>
                    </li>
                </ul>  
            </div>
        </div>

        <div class="card" id="msgArea" v-show="activeFriend">
            <div class="card-header bg-info py-2">
                <h4 class="card-title mb-0 mt-1 float-left" v-if="activeFriend">
                    <span class="status mr-1">
                        <i class="fa fa-circle online" v-if="is_connected(activeFriend)"></i>
                        <i class="fa fa-circle offline" v-else></i>
                    </span>
                    <span class="name text-white">{{activeFriendData[0].name}}</span>
                </h4>
                <div class="card-widgets mt-1 float-right">
                    <a href="#" id="box-hide" @click="removeActive"><i class="ion-close-round text-white"></i></a>
                </div>
                <span class="clearfix"></span>
            </div>
            <div class="card-body p-0 nicescroll" id="privateMessageBox">
                <message-list :user="user" :all-messages="allMessages" v-if="activeFriend && !msg_loading"></message-list>
                <div class="text-center" v-if="!activeFriend && !msg_loading">
                    <div><img src="/images/chat.png" width="250" style="margin-top:100px;" alt=""></div>
                </div>
                <div class="msg-loading text-center" v-if="msg_loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div id="card-footer">
                <!-- <img v-show="typing" ref="typing_indicator" src="/images/typing_indicator.gif" width="60" alt=""> -->
                <div class="progress progress-sm mb-0" v-show="uploading">
                    <div class="progress-bar progress-bar-success" role="progressbar" :aria-valuenow="uploadProgress" aria-valuemin="0" aria-valuemax="100" :style="{width: uploadProgress + '%'}">
                        <span class="sr-only">{{uploadProgress}}% Complete</span>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2">
                <div class="d-flex">
                    <file-upload
                        :post-action="'/chat/message/'+activeFriend"
                        ref='upload'
                        v-model="files"
                        @input-file="inputFile"
                        :headers="{'X-CSRF-TOKEN': token}"
                    ><span class="icon-attach text-primary"><i class="fa fa-paperclip"></i></span></file-upload>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="chat-input" ref="chat_input" v-model="message" placeholder="Enter Message" @keyup.enter="sendMessage" />
                        <span class="input-group-append">
                            <button type="button" class="btn btn-sm waves-effect waves-light btn-primary" id="btn-send" @click="sendMessage">Send</button>
                        </span>
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
                sending: false,
                msg_loading: false,
                emoStatus:false,
                uploadProgress: 0,
                uploading: false,
                users: [],
                unreads: {},
                total_unreads: 0,
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
                if(val == null) return false
                this.activeFirend = val
                this.activeFriendData = this.users.filter((user) => {
                    return user.id == this.activeFriend;
                })
                axios.post('/read_messages/' + val).then(response => {
                    if(response.data == 'success'){
                        this.getUnreadMessages();
                    }
                })
                this.fetchMessages();
            },
            '$refs.upload'(val){
                console.log(val);
            }
        },
        methods: {
            inputFile(newFile, oldFile){
                this.$refs.upload.active = true
                if (newFile && oldFile) {
                    if (newFile.active !== oldFile.active) {
                        this.uploading = true
                    }
                    if (newFile.progress !== oldFile.progress) {
                        // console.log('progress', newFile.progress)
                        this.uploadProgress = newFile.progress
                    }

                    // Uploaded error
                    if (newFile.error !== oldFile.error) {
                        alert('Sorry, upload is failed. Please try again');
                    }

                    // Uploaded successfully
                    if (newFile.success !== oldFile.success) {
                        setTimeout(function(){
                            // $(".progress").hide()
                            this.uploading = false;
                        }, 1000);
                    }
                }
            },
            onTyping(){
                Echo.private('chat.'+this.activeFriend).whisper('typing',{
                    user:this.user
                });
            },
            sendMessage(){
                if(!this.message){
                    return false;
                }
                if(!this.activeFirend){
                    return alert('Please select user');
                }
                if(this.sending) {
                    return false;
                }
                this.sending = true;
                axios.post('/chat/message/' + this.activeFirend, {message: this.message})
                    .then(response => {
                        this.sending = false;
                        this.message = null;
                        if(response.data.message.is_image == 1){
                            setTimeout(function(){ this.allMessages.push(response.data.message) }, 1000);
                        }else{
                            this.allMessages.push(response.data.message)
                        }                        
                        setTimeout(this.scrollToEnd, 50);
                    })
            },
            fetchMessages() {
                if(!this.activeFirend){
                    return alert('Please select user');
                }
                this.msg_loading = true
                axios.get('/chat/messages/' + this.activeFirend)
                    .then(response => {
                        this.allMessages = response.data;
                        this.msg_loading = false
                        this.uploading = false
                        this.uploadProgress = 0;
                        setTimeout(this.scrollToEnd, 50);
                    })
            },
            fetchUsers() {
                axios.get('/users').then(response => {
                    this.users = response.data
                    this.getUnreadMessages();
                    if(this.friends.length > 0){
                        this.activeFirend = this.friends[0].id;
                    }
                })
            },
            getUnreadMessages(){
                axios.get('/unread_messages').then(response1 => {
                    let unreads = response1.data; 
                    let total_unreads = 0                       
                    for (let i = 0; i < this.users.length; i++) {
                        let user_unreads = unreads[this.users[i].id]
                        this.users[i].unread_messages = user_unreads;
                        total_unreads += user_unreads
                    }
                    this.total_unreads = total_unreads
                    $("#total_unreads").text(total_unreads);
                })
            },
            scrollToEnd(){
                document.getElementById('privateMessageBox').scrollTo(0,99999);
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
            },
            removeActive() {
                this.activeFriend = null
            },
            is_connected(id) {
                for (let i = 0; i < this.onlineFriends.length; i++) {
                    const element = this.onlineFriends[i];
                    if(element.id == id) return true                                         
                }
                return false
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
                    // console.log('joining',user.name);
                })
                .leaving((user) => {
                    this.onlineFriends.splice(this.onlineFriends.indexOf(user),1);
                    // console.log('leaving',user.name);
                });
                
            Echo.private('chat.'+this.user.id)
                .listen('MessageSent',(e)=>{
                    let audio = new Audio('/Ring.wav')
                    audio.play()
                    if(!this.activeFriend){
                        this.activeFriend=e.message.user_id;
                    }
                    if(this.activeFriend != e.message.user_id){                        
                        for (let i = 0; i < this.users.length; i++) {
                            const element = this.users[i];
                            if(element.id == e.message.user_id){
                                element.unread_messages++;
                                this.total_unreads++;
                                $("#total_unreads").text(this.total_unreads);
                            }
                        }
                    }else{
                        this.allMessages.push(e.message)
                        setTimeout(this.scrollToEnd,50);
                        axios.post('/read_messages/' + e.message.user_id).then(response => {
                            if(response.data == 'success'){
                                this.getUnreadMessages();
                            }
                        })
                    }                    
                })
                // .listenForWhisper('typing', (e) => {
                //     if(e.user.id==this.activeFriend){
                //         this.typing = true;
                //         if(this.typingClock) clearTimeout();
                //         this.typingClock=setTimeout(()=>{
                //             this.typingFriend={};
                //             this.typing = false;
                //         },5000);
                //     }                 
                // });
        }
    }
</script>

<style scoped>
    #privateMessageBox {
        height:calc(100% - 126px);
        padding-bottom: 0;
    }

    #msgArea {
        width: 380px;
        height: 58vh;
        position: fixed;
        bottom: -18px;
        z-index: 10;
        right: 10px;
        transition-duration: 0.3s;
    }
    
    .right-bar-enabled #msgArea {
        right: 250px;
    }

    @media(max-width: 768px) {
        #msgArea {
            width: 95%;
            height: 80vh;
            height: calc(100vh - 75px);
        }
    }
    #msgArea .status i.online {
        color: #a0d269;
    }

    #msgArea .status i.offline {
        color: #ef5350;
    }

    #msgArea .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
    }

    #chat-input:focus {
        box-shadow: none;
    }

    #btn-send {
        height: 31px;
    }
    

    .icon-attach {
        font-size: 20px;
        margin-right: 10px;
        cursor: pointer;
    }
    .card-header .name {
        font-size: 16px;
        color: #444444;
        font-weight: 500;
    }
    #chatbox-footer {
        height: 15px;
        position: absolute;
        bottom: 75px;
        padding-left: 60px;
    }
    .msg-loading {
        margin-top: 47%;
    }
    .msg-loading .spinner-border {
        width: 80px;
        height: 80px;
    }
</style>