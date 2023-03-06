<script>
    import Loader from './components/Loader.vue';
    import Header from './components/Header.vue';

    export default {
        name: 'app',
        data: () => ({}),       
        computed: {            
        },
        methods: {      
        },
        mounted() {        
          /*this.user = {
            name: "Marie"
          };*/
        },
        components: {
          Loader,
          Header
        }
    }
</script>

<template>
  <Header/>  
  <router-view></router-view> 
</template>



<style>
:root {
    --primary : #e75112;
    --secondary: #FDAF17;    
    --third: #9B9B9B;
    --loading-secondary: #ff8d5c;
    --loading-fourth: #ffda8f;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F7F7;
    --bg-third: #CCCCCC;
    --bg-navbar: #403E3F;
    --subtitle: #666;
    --button-bg-disable: #cecece;
    --button-color-disable: #ececec;
    --input-bg: #f2f2f2;
    --input-placeholder:#aaaaaa; 
}

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  --color_foeee: #fff;
}

main {
  min-height: 80vh !important;
  padding:32px;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  min-height: 100%;
  max-width: 100%;
}

body {
	margin: 0;
	min-height: 100vh;
	display: grid;
	grid-template-rows: auto 1fr auto;
  background-image: linear-gradient(12deg, var(--bg-secondary) 0%, var(--bg-third) 100%);
}

main {
  min-height:100%;
  position:relative;
}

.link{
  text-decoration: none;
}

h1 {
  padding-top: 40px!important;
}
</style>
