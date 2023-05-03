<script>
    import axios from 'axios';   
    import Footer from '../../components/Footer.vue';     
    import { articleService } from '../../services/article.services';

    export default {
        name: 'articles',
        data: () => ({ 
            articles: [],
        }),       
        computed: {            
        },
        methods: {      
        },
        mounted() { 
            articleService.getArticles().then((articles) => { 
                articles.data.forEach(article => {
                    if(!article.isValidate){
                        this.articles.push(article);
                    }
                }); 
            });                 
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>News</h1>
        <div class="allArticles">
            <div v-for="article in this.articles" class="article">
                
                <div class="divTitle">
                    <h3>{{ article.title }}</h3>
                </div>
                <div class="divContentAndImage">
                    <div class="divContent">
                        <p class="content">{{ article.content }}</p>
                    </div>
                    <div v-if="article.media">
                        <img src={{ article.media }}>
                    </div>
                </div>
                <div class="divDate"><!-- v-if="article.date" -->
                    <p></p>
                </div>
                
                
            </div>
        </div>
    </main>
    
    <Footer class="footer"/>   
</template>

<style scoped>
p.content{
    margin: 0;
    padding: 10px;
    text-align: justify;
}
.footer{
    position: inherit;
}

.divDate{
    display: flex;
    flex-direction: row-reverse;
    margin: 0 10px 10px 0;
}
.article{
    display: flex;
    flex-direction: column;
    border: solid 1px black;
    border-radius: 10px;
    margin: 20px 0;
}

.article:nth-child(even){
    background-color: #f6f6f6;
}

.article:nth-child(odd){
    background-color: #ececec;
}

.divTitle{
    display: flex;
}

.divTitle h3{
    margin: 10px 0 0 10px;
    font-weight: bold;
}

</style>