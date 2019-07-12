new Vue({
    el: '#posting', data: {
        article:{
            category_id: '',
            public:true,
            isDraft:true,
            name:'',
            teaser:'',
            image:{
                path:''
            },
            content:[
                {content:''}
            ],
            keywords:'',
        },
        categories: [
            {name: 'Other'}
        ]
    },
    components:{
        'editor':Editor
    },
    created(){
        if('{{loadedArticleId}}' !== ''){
            this.loadArticle('{{loadedArticleId}}');
        }
        api.get('categories?all').then(res => {this.categories = res.data});
    },
    methods:{
        loadArticle(id){
            api.get('write?id='+id).then(res=>{
                this.article = res.data;
                if(Array.isArray(res.data.image)){
                    this.article.image = {path:''}
                } else if(res.data.image.path.substring(0,4) !== 'http'){
                    this.article.image.path = '{{base}}'+res.data.image.path;
                }
                this.article.isDraft = !res.data.publish_date;
                this.article.public = res.data.is_public;
            }).catch(err=>{
                // not allowed
            })
        },
        changePic(imgId){
            this.article.image.id = imgId;
            api.get('uploadImage?id='+imgId).then(res=>{
                if(res.data.path.substring(0,4) !== 'http'){
                    this.article.image.path = '{{base}}'+res.data.path;
                } else {
                    this.article.image = res.data;
                }

            })

        },
        create(){
            let obj = this.article;
            console.log(this.article);
            api.post('article',obj).then(res=>{
                this.loadArticle(res.data.id);
            }).catch(err=>{

            })
        }
    }

});
