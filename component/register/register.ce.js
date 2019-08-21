
const registerForm = {
    name: 'register-form',
    mixins:[common],
    data:()=>{
        return {
            username:'',
            password:'',
            email:'',
            acceptTAC:false,
            duplicate:false,
            showModal:false,
            loggedIn:localStorage.token,
            passwordStrength:0,
            processing:false
        }
    },
    mounted(){
        if(this.loggedIn){
            // check if still valid token
            api.get('register').then(x=>{
                console.log(x.data);
            }).catch(e=>{
                console.log(e);
                this.logout();
            })
        }
    },
    watch:{
        password:function(value){
            this.passwordStrength = this.testPassword(value);
        }
    },
    methods:{
        logout(){
            console.log('test');
            delete localStorage.token;
            this.loggedIn = false;
        },
        register(){
            this.processing = true;
            this.duplicate = false;
            api.post('register',this._data).then((res)=>{
                localStorage.setItem('token',res.data.token);
                this.loggedIn = res.data.token;
                this.processing = false;
            }).catch((err)=>{
                this.duplicate = true;
                this.processing = false;
            })

        },

    },
    template:document.querySelector('#register').innerHTML
};
