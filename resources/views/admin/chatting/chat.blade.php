@extends('admin.layouts.master')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet">
  <style scoped="">
  .container{max-width:1170px; margin:auto;}
  img{ max-width:100%;}
  .inbox_people {
    background: #f8f8f8 none repeat scroll 0 0;
    float: left;
    overflow: hidden;
    width: 35%; border-right:1px solid #c4c4c4;
  }
  .inbox_msg {
    border: 1px solid #c4c4c4;
    clear: both;
    overflow: hidden;
  }
  .top_spac{ margin: 20px 0 0;}
  

  .recent_heading {float: left; width:40%;}
  .srch_bar {
    display: inline-block;
    text-align: right;
    width: 60%;
  }
  .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

  .recent_heading h4 {
    color: #05728f;
    font-size: 21px;
    margin: auto;
  }
  .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
  .srch_bar .input-group-addon button {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    padding: 0;
    color: #707070;
    font-size: 18px;
  }
  .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

  .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
  .chat_ib h5 span{ font-size:13px; float:right;}
  .chat_ib p{ font-size:14px; color:#989898; margin:auto}
  .chat_img {
    float: left;
    width: 11%;
  }
  .chat_ib {
    float: left;
    padding: 0 0 0 15px;
    width: 88%;
  }

  .chat_people{ overflow:hidden; clear:both;}
  .chat_list {
    border-bottom: 1px solid #c4c4c4;
    margin: 0;
    padding: 18px 16px 10px;
  }
  .inbox_chat { height: 550px; overflow-y: scroll;}

  /*.active_chat{ background:#ebebeb;}*/

  .incoming_msg_img {
    display: inline-block;
    width: 6%;
  }
  .received_msg {
    display: inline-block;
    padding: 0 0 0 10px;
    vertical-align: top;
    width: 92%;
   }
   .received_withd_msg p {
    background: #ebebeb none repeat scroll 0 0;
    border-radius: 3px;
    color: #646464;
    font-size: 14px;
    margin: 0;
    padding: 5px 10px 5px 12px;
    width: 100%;
  }
  .time_date {
    color: #747474;
    display: block;
    font-size: 12px;
    margin: 8px 0 0;
  }
  .received_withd_msg { width: 57%;}
  .mesgs {
    float: left;
    padding: 30px 15px 0 25px;
    width: 65%;
  }

   .sent_msg p {
    background: #05728f none repeat scroll 0 0;
    border-radius: 3px;
    font-size: 14px;
    margin: 0; 
    color:#fff;
    padding: 5px 10px 5px 12px;
    width:100%;
  }
  .outgoing_msg{ overflow:hidden;/* margin:26px 0 26px;*/}
  .sent_msg {
    float: right;
    /*width: 46%;*/
    /*padding: 0 0 0 10px;*/

  }
  .input_msg_write input {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    color: #4c4c4c;
    font-size: 15px;
    min-height: 48px;
    width: 100%;
  }

  .type_msg {border-top: 1px solid #c4c4c4;position: relative;}
  .msg_send_btn {
    background: #05728f none repeat scroll 0 0;
    border: medium none;
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    font-size: 17px;
    height: 33px;
    position: absolute;
    right: 0;
    top: 11px;
    width: 33px;
  }
  .messaging { padding: 0 0 50px 0;}
  .msg_history {
    height: 516px;
    overflow-y: auto;
  }
</style>

@endsection

@section('title','Dashboard')

@section('content')    
  <div class="container" style="padding: 5px;">
    <div id="app">
    
      <div class="messaging">
        <div class="inbox_msg">
          <div class="inbox_people">
            <div class="headind_srch">
              <div class="recent_heading">
                <h4>Recent</h4>
              </div>
              <div class="srch_bar">
                <div class="stylish-input-group">
                  <input type="text" class="search-bar"  placeholder="Search" >
                  <span class="input-group-addon">
                  <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                  </span> 
                </div>
              </div>
            </div>
            <div  class="inbox_chat nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <div >
            

            <a v-for="(user , index) in users" v-if="index !='Mdp_PMRrqyUt5NJ-E6b' " :class="{'nav-link active' : activeIndex === user.id }" :key="user.id" v-on:click="setActive(index)" data-toggle="pill" :href="'#'+user.id" role="tab" aria-controls="">
            <div class="chat_list active_chat">
              <div class="chat_people">
                <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                <div class="chat_ib">
                   
                    <h5>@{{ user.name }}<span class="chat_date">Dec 25</span></h5>
                  
                </div>
              </div>
            </div>
              
            </a>

           
            </div>

            
            
          </div>
          </div>
          <div class="tab-content" id="v-pills-tabContent">

            <div class="mesgs tab-pane fade show active" :id="activeIndex" role="tabpanel" aria-labelledby="v-pills-home-tab">
                <div class="msg_history">
                  <div v-for="(message,index) in messages" :class="[message.adminName=='Admin'? 'outgoing_msg':'incoming_msg']" v-if="activeIndex==message.chatId">
                        <div :class="[message.adminName ==='Admin'? 'sent_msg':'received_withd_msg']">
                          <p v-if="message.imagelist==null">@{{message.message}}</p>
                          <span class="time_date">{{-- @{{message.username}} --}}</span>
                        </div>          
                    </div>
                </div>
                <form @submit.prevent="sendMessage()">
                <div class="type_msg">
                  <div class="input_msg_write">
                    <input @keyup.enter="sendMessage" type="text" v-model="message" class="write_msg" placeholder="Type a message" />
                    <button class="msg_send_btn" type="submit"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                  </div>
                </div>
                </form>

              
            </div>

            
        </div>
      </div>
    
    </div>
      
    </div>
  </div>
  
@endsection 
@section('script')
  
  <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

  {{-- <script src="https://unpkg.com/vue-router@2.0.0/dist/vue-router.js"></script> --}}
  <script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-database.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js"></script>


<script>
  //Your web app's Firebase configuration
  //For Firebase JS SDK v7.20.0 and later, measurementId is optional
  var firebaseConfig = {
        apiKey: "AIzaSyDFh-4zmDNZVDMzK738w0PqWtR-l5Yg1Eg",
        authDomain: "uboayecosmetic.firebaseapp.com",
        databaseURL: "https://uboayecosmetic.firebaseio.com",
        projectId: "uboayecosmetic",
        storageBucket: "uboayecosmetic.appspot.com",
        messagingSenderId: "1088945078478",
        appId: "1:1088945078478:web:451e6c6379bf14dded909f",
        measurementId: "G-CPV5TBFFYW"
      };
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();

  new Vue({
    el:'#app',
    

    data(){

      return {
        message:null,
        messages:[],
        users:{},
        userId:{},
        activeIndex: undefined,

      }
    },
    
    methods:{
       setActive(index) { 
        this.activeIndex = index 

      },
      scrollToBottom(){
        let box=document.querySelector('.msg_history');
        box.scrollTop=box.scrollHeight;
      },

      sendMessage(activeIndex){
        if(this.message.length > 0){

        firebase.database().ref("Chats").push().set({
            // adminName:"UBoAye Admin",
            isseen:false,
            message: this.message,
            // receiver:this.activeIndex,
            // sender:"Mdp_PMRrqyUt5NJ-E6bd",
            chatId:this.activeIndex,
            adminName:"Admin"
            // username:"Admin",
            // date: moment().format()
        }).then(()=>{
          this.scrollToBottom();
        })
        }else{
          alert('You first write text')
        }


        this.message = null;
      },
     
      getUserName(){
        
        firebase.database().ref("Users").on('value',(snapshot) => {
              this.users=snapshot.val();            

          })
      },
      getUserId(id){
        firebase.database().ref("Users").orderByKey().once("value")
          .then(function(snapshot) {
            snapshot.forEach(function(childSnapshot) {
              var userId = childSnapshot.val().id;
              
          });
        });
      },
      fetchMessages(activeIndex){

        firebase.database().ref('Chats').on('value', (snapshot) => {


            this.messages= snapshot.val();
          

            setTimeout(()=>{
              this.scrollToBottom();
            },1000);
        })
      } 

    

    },
      
     created(){

       this.fetchMessages();
       this.getUserName();
       this.getUserId();
       this.setActive();
     }  
});

  
</script>

@endsection



